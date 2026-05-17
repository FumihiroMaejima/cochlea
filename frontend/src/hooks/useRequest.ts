import { ServerRequestResponseType, ServerErrorResponseType } from '@/types'

type QueryParams = Record<string, string | number | boolean | null | undefined>

type RequestOptions = Omit<RequestInit, 'method' | 'body'> & {
  params?: QueryParams
  timeout?: number
}

/**
 * Builds a URL with query parameters.
 * @param {string} url - The base URL.
 * @param {QueryParams} [params] - The query parameters to append.
 * @returns {string} The URL with query parameters.
 */
const buildUrl = (url: string, params?: QueryParams): string => {
  if (!params) return url

  const query = new URLSearchParams()
  Object.entries(params).forEach(([key, value]) => {
    if (value !== null && value !== undefined) {
      query.append(key, String(value))
    }
  })

  const queryString = query.toString()
  if (!queryString) return url

  const separator = url.includes('?') ? '&' : '?'
  return url + separator + queryString
}

/**
 * Converts a Headers object to a plain object.
 * @param {Headers} headers - The Headers object to convert.
 * @returns {Record<string, string>} A plain object representing the headers.
 */
const headersToObject = (headers: Headers): Record<string, string> => {
  const result: Record<string, string> = {}
  headers.forEach((value, key) => {
    result[key] = value
  })
  return result
}
/**
 * Parses the response body based on the content type.
 * @param {Response} response - The fetch response object.
 * @returns {Promise<T>} A promise that resolves to the parsed response body.
 */
const parseResponseBody = async <T>(response: Response): Promise<T> => {
  if (response.status === 204 || response.status === 205) {
    return undefined as T
  }

  const contentType = response.headers.get('content-type') || ''
  if (contentType.includes('application/json')) {
    return (await response.json()) as T
  }

  const text = await response.text()
  return text as unknown as T
}

/**
 * Performs an HTTP request using the Fetch API with support for various methods, headers, query parameters, and timeouts.
 *
 * @param {string} method - The HTTP method to use for the request (e.g., 'GET', 'POST').
 * @param {string} url - The URL to which the request is sent.
 * @param {unknown} data - The data to be sent as the request body (for methods like POST, PUT, PATCH).
 * @param {RequestOptions} options - Additional options for the request, including headers, query parameters, and timeout.
 * @returns {Promise<ServerRequestResponseType<T>>} A promise that resolves to the server response, including data, status, and headers.
 */
const createRequestInit = (
  method: string,
  options?: RequestOptions,
  data?: unknown,
): RequestInit => {
  const { params, timeout, ...rest } = options || {}
  const headers = new Headers(rest.headers)

  const init: RequestInit = {
    ...rest,
    method,
    credentials: rest.credentials ?? 'include',
    headers,
  }

  // Handle request body based on the type of data provided
  if (data !== undefined) {
    const isFormData = typeof FormData !== 'undefined' && data instanceof FormData
    const isBlob = typeof Blob !== 'undefined' && data instanceof Blob
    const isUrlSearchParams =
      typeof URLSearchParams !== 'undefined' && data instanceof URLSearchParams
    const isArrayBuffer = data instanceof ArrayBuffer
    const isArrayBufferView = ArrayBuffer.isView(data)
    const isString = typeof data === 'string'

    const isRawBody =
      isString || isFormData || isBlob || isUrlSearchParams || isArrayBuffer || isArrayBufferView

    // If the data is a raw body type, set it directly. Otherwise, stringify it as JSON.
    // check option content-type header, if not set and data is not raw body, set it to application/json
    if (isRawBody) {
      init.body = data as BodyInit
    } else {
      if (!headers.has('Content-Type')) {
        headers.set('Content-Type', 'application/json')
      }
      init.body = JSON.stringify(data)
    }
  }

  return init
}

const request = async <T = unknown>(
  method: string,
  url: string,
  data?: unknown,
  options?: RequestOptions,
): Promise<ServerRequestResponseType<T>> => {
  const requestUrl = buildUrl(url, options?.params)
  const init = createRequestInit(method, options, data)
  const timeout = options?.timeout

  let timeoutId: ReturnType<typeof setTimeout> | undefined
  const controller = typeof AbortController !== 'undefined' ? new AbortController() : undefined

  if (controller) {
    init.signal = controller.signal
    if (timeout && timeout > 0) {
      timeoutId = setTimeout(() => controller.abort(), timeout)
    }
  }

  try {
    const response = await fetch(requestUrl, init)
    const responseData = await parseResponseBody<T | ServerErrorResponseType>(response)

    if (!response.ok) {
      throw {
        data: responseData,
        status: response.status,
        headers: headersToObject(response.headers),
      }
    }

    return {
      data: responseData as T,
      status: response.status,
      headers: headersToObject(response.headers),
    }
  } catch (error: any) {
    console.error('fetch error' + JSON.stringify(error?.message ?? error, null, 2))

    if (error?.status) {
      throw error
    }

    if (error?.name === 'AbortError') {
      throw {
        data: {
          status: 408,
          errors: ['Request timeout'],
          message: 'Request timeout',
        } as ServerErrorResponseType,
        status: 408,
        headers: {},
      }
    }

    throw {
      data: error,
      status: 401,
      headers: {},
    }
  } finally {
    if (timeoutId) {
      clearTimeout(timeoutId)
    }
  }
}

/* eslint-disable-next-line @typescript-eslint/explicit-module-boundary-types */
export const useRequest = () => {
  const state = {}

  const getRequest = async <T = any>(
    url: string,
    options?: RequestOptions,
  ): Promise<ServerRequestResponseType<T>> => {
    return await request<T>('GET', url, undefined, options)
  }

  const deleteRequest = async <T = unknown>(
    url: string,
    options?: RequestOptions,
  ): Promise<ServerRequestResponseType<T>> => {
    return await request<T>('DELETE', url, undefined, options)
  }

  const postRequest = async <T = unknown>(
    url: string,
    data: any,
    options?: RequestOptions,
  ): Promise<ServerRequestResponseType<T>> => {
    return await request<T>('POST', url, data, options)
  }

  const putRequest = async <T = unknown>(
    url: string,
    data: any,
    options?: RequestOptions,
  ): Promise<ServerRequestResponseType<T>> => {
    return await request<T>('PUT', url, data, options)
  }

  const patchRequest = async <T = unknown>(
    url: string,
    data: any,
    options?: RequestOptions,
  ): Promise<ServerRequestResponseType<T>> => {
    return await request<T>('PATCH', url, data, options)
  }

  return {
    state,
    getRequest,
    deleteRequest,
    postRequest,
    putRequest,
    patchRequest,
  }
}

export type UseRequestType = ReturnType<typeof useRequest>
export const UseRequestStateKey = Symbol('useRequestState')
