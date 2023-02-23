import axios, { AxiosResponse, AxiosError, AxiosRequestConfig } from 'axios'
import { ServerRequestResponseType, ServerErrorResponseType } from '@/types'

/* eslint-disable-next-line @typescript-eslint/explicit-module-boundary-types */
export const useRequest = () => {
  const state = {}
  /**
   * GET request
   * @param {string} url
   * @param {AxiosRequestConfig | undefined} options
   * @return {Promise<ServerRequestResponseType<T>>}
   */
  const getRequest = async <T = any, D = any>(
    url: string,
    options?: AxiosRequestConfig
  ): Promise<ServerRequestResponseType<T>> => {
    axios.defaults.withCredentials = true
    return await axios
      .get<T, AxiosResponse<T, D>, D>(url, options)
      .then((response: AxiosResponse<T, D>) => {
        return { data: response.data, status: response.status }
      })
      .catch((error: AxiosError<ServerErrorResponseType>) => {
        /* eslint-disable-next-line no-console */
        console.error('axios error' + JSON.stringify(error.message, null, 2))
        throw {
          data: error.response ? error.response.data : error,
          status: error.response ? error.response.status : 401,
        }
      })
  }

  /**
   * Delete request
   * @param {string} url
   * @param {AxiosRequestConfig | undefined} options
   * @return {Promise<ServerRequestResponseType<T>>}
   */
  const deleteRequest = async <T = unknown, D = any>(
    url: string,
    options?: AxiosRequestConfig
  ): Promise<ServerRequestResponseType<T>> => {
    axios.defaults.withCredentials = true
    return await axios
      .delete<T, AxiosResponse<T, D>, D>(url, options)
      .then((response: AxiosResponse<T, D>) => {
        return { data: response.data, status: response.status }
      })
      .catch((error: AxiosError<ServerErrorResponseType>) => {
        /* eslint-disable-next-line no-console */
        console.error('axios error' + JSON.stringify(error.message, null, 2))
        throw {
          data: error.response ? error.response.data : error,
          status: error.response ? error.response.status : 401,
        }
      })
  }

  /**
   * POST request
   * @param {string} url
   * @param {any} data
   * @param {AxiosRequestConfig | undefined} options
   * @return {Promise<ServerRequestResponseType<T>>}
   */
  const postRequest = async <T = unknown, D = any>(
    url: string,
    data: any,
    options?: AxiosRequestConfig
  ): Promise<ServerRequestResponseType<T>> => {
    axios.defaults.withCredentials = true
    return await axios
      .post<T, AxiosResponse<T, D>, D>(url, data, options)
      .then((response: AxiosResponse<T, D>) => {
        return { data: response.data, status: response.status }
      })
      .catch((error: AxiosError<ServerErrorResponseType>) => {
        /* eslint-disable-next-line no-console */
        console.error('axios error' + JSON.stringify(error.message, null, 2))
        throw {
          data: error.response ? error.response.data : error,
          status: error.response ? error.response.status : 401,
        }
      })
  }

  /**
   * PUT request
   * @param {string} url
   * @param {any} data
   * @param {AxiosRequestConfig | undefined} options
   * @return {Promise<ServerRequestResponseType<T>>}
   */
  const putRequest = async <T = unknown, D = any>(
    url: string,
    data: any,
    options?: AxiosRequestConfig
  ): Promise<ServerRequestResponseType<T>> => {
    axios.defaults.withCredentials = true
    return await axios
      .put<T, AxiosResponse<T, D>, D>(url, data, options)
      .then((response: AxiosResponse<T, D>) => {
        return { data: response.data, status: response.status }
      })
      .catch((error: AxiosError<ServerErrorResponseType>) => {
        /* eslint-disable-next-line no-console */
        console.error('axios error' + JSON.stringify(error.message, null, 2))
        throw {
          data: error.response ? error.response.data : error,
          status: error.response ? error.response.status : 401,
        }
      })
  }

  /**
   * PATCH request
   * @param {string} url
   * @param {any} data
   * @param {AxiosRequestConfig | undefined} options
   * @return {Promise<ServerRequestResponseType<T>>}
   */
  const patchRequest = async <T = unknown, D = any>(
    url: string,
    data: any,
    options?: AxiosRequestConfig
  ): Promise<ServerRequestResponseType<T>> => {
    axios.defaults.withCredentials = true
    return await axios
      .patch<T, AxiosResponse<T, D>, D>(url, data, options)
      .then((response: AxiosResponse<T, D>) => {
        return { data: response.data, status: response.status }
      })
      .catch((error: AxiosError<ServerErrorResponseType>) => {
        /* eslint-disable-next-line no-console */
        console.error('axios error' + JSON.stringify(error.message, null, 2))
        throw {
          data: error.response ? error.response.data : error,
          status: error.response ? error.response.status : 401,
        }
      })
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

// make provide/inject key
export type UseRequestType = ReturnType<typeof useRequest>
export const UseRequestStateKey = Symbol('useRequestState')
