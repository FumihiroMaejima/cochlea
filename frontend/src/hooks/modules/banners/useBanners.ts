// eslint-disable-next-line @typescript-eslint/no-unused-vars
import { useState, useReducer, useCallback } from 'react'
import { useRequest } from '@/hooks/useRequest'
import { appConfig } from '@/config/data'
import {
  // IAppConfig,
  // eslint-disable-next-line @typescript-eslint/no-unused-vars
  BaseAddHeaderResponse,
  ServerRequestType,
  AuthAppHeaderOptions,
} from '@/types'
import { makeDataUrl, downloadFile } from '@/util'
/* import { ToastData, SelectBoxType } from '@/types/applications/index'
import {
  validateName,
  validateEmail,
  validateSelectBoxNumberValue,
  validatePassword,
  validateConfirmPassword,
} from '@/util/validation'
import { makeDataUrl, downloadFile } from '@/util' */

const config = { ...appConfig }

export const editableRole = ['master', 'administrator']

export const bannerData = {
  id: 0,
  name: '',
  detail: '',
  location: 1,
  pc_height: 100,
  pc_width: 100,
  sp_height: 100,
  sp_width: 100,
  start_at: '',
  end_at: '',
  url: '',
  image: '',
  created_at: '',
  updated_at: '',
  deleted_at: '',
}

export type BannerType = typeof bannerData
export type BannerTypeKeys = keyof BannerType
export type BannerTextKeys = Extract<
  BannerTypeKeys,
  'name' | 'detail' | 'start_at' | 'end_at' | 'url' | 'image'
>
export type BannerSelectKeys = Extract<BannerTypeKeys, 'location'>

export type StateType = {
  banners: BannerType[]
}

export const initialData: StateType = {
  banners: [...[]],
}

export function useBanners() {
  /* const [bannersState, dispatch] = useReducer(reducer, {
    ...initialData,
  }) */
  const [bannersState, dispatch] = useState({ ...initialData })

  /**
   * update banner's text value.
   * @param {number} index
   * @param {BannerTextKeys} key
   * @param {string} value
   * @return {void}
   */
  const updateBannerTextData = (
    index: number,
    key: BannerTextKeys,
    value: string
  ) => {
    dispatch({
      banners: bannersState.banners.map((banner, i) => {
        if (i === index) {
          banner[key as BannerTextKeys] = value
        }
        return banner
      }),
    })
  }

  /**
   * update banner's number value.
   * @param {number} index
   * @param {BannerSelectKeys} key
   * @param {number} value
   * @return {void}
   */
  const updateBannerNumberData = (
    index: number,
    key: BannerSelectKeys,
    value: number
  ) => {
    dispatch({
      banners: bannersState.banners.map((banner, i) => {
        if (i === index) {
          banner[key as BannerSelectKeys] = value
        }
        return banner
      }),
    })
  }

  /**
   * set banner's data.
   * @param {BannerType[]} banners
   * @return {void}
   */
  const setBanners = (banners: BannerType[]) => {
    bannersState.banners = banners
    dispatch(bannersState)
  }

  /**
   * get banners data.
   * @param {BaseAddHeaderResponse} header
   * @return {void}
   */
  const getBannersRequest = async (
    options: AuthAppHeaderOptions
  ): Promise<ServerRequestType> => {
    // axios.defaults.withCredentials = true
    return await useRequest()
      .getRequest<ServerRequestType<BannerType[]>>(
        config.endpoint.banners.banners,
        {
          headers: options.headers,
        }
      )
      .then((response) => {
        const data = response.data as ServerRequestType<BannerType[]>
        setBanners(data.data as BannerType[])
        return { data: response.data, status: 200 }
      })
      .catch((error) => {
        return { data: error, status: 404 | 500 }
      })
      .finally(() => {
        options.callback()
      })
  }

  /**
   * get banners csv file.
   * @param {BaseAddHeaderResponse} header
   * @return {void}
   */
  const getBannersCsvFileRequest = async (
    options: AuthAppHeaderOptions
  ): Promise<ServerRequestType> => {
    // axios.defaults.withCredentials = true
    return await useRequest()
      .getRequest<ServerRequestType<BlobPart>>(config.endpoint.banners.csv, {
        headers: options.headers,
      })
      .then((response) => {
        const data = response.data as unknown as BlobPart
        // download
        downloadFile(
          makeDataUrl(data, response.headers['content-type']),
          response.headers['content-disposition'].replace(
            'attachment; filename=',
            ''
          )
        )
        return { data: response.data, status: 200 }
      })
      .catch((error) => {
        return { data: error, status: 404 | 500 }
      })
      .finally(() => {
        options.callback()
      })
  }

  /**
   * downlaoad template file request.
   * @param {BaseAddHeaderResponse} header
   * @return {void}
   */
  const getBannerTemplateRequest = async (
    options: AuthAppHeaderOptions
  ): Promise<ServerRequestType> => {
    // axios.defaults.withCredentials = true
    return await useRequest()
      .getRequest<ServerRequestType<BlobPart>>(
        config.endpoint.banners.fileTemplate,
        {
          headers: options.headers,
          responseType: 'blob',
        }
      )
      .then((response) => {
        const data = response.data as unknown as BlobPart
        // download
        downloadFile(
          makeDataUrl(data, response.headers['content-type']),
          response.headers['content-disposition'].replace(
            'attachment; filename=',
            ''
          )
        )
        return { data: response.data, status: 200 }
      })
      .catch((error) => {
        return { data: error, status: 404 | 500 }
      })
      .finally(() => {
        options.callback()
      })
  }

  /**
   * update banners request.
   * @param {BannerType} banner banner record
   * @param {BaseAddHeaderResponse} header
   * @return {void}
   */
  const updateBannerRequest = async (
    banner: BannerType,
    options: AuthAppHeaderOptions
  ): Promise<ServerRequestType> => {
    // axios.defaults.withCredentials = true
    const body = {
      name: banner.name,
      detail: banner.detail,
      location: banner.location,
      pc_height: banner.pc_height,
      pc_width: banner.pc_width,
      sp_height: banner.sp_height,
      sp_width: banner.sp_width,
      start_at: banner.start_at,
      end_at: banner.end_at,
      url: banner.url,
    }
    return await useRequest()
      .patchRequest<ServerRequestType<BannerType[]>>(
        config.endpoint.banners.banner.replace(':id', String(banner.id)),
        body,
        {
          headers: options.headers,
        }
      )
      .then((response) => {
        return { data: response.data, status: 200 }
      })
      .catch((error) => {
        return { data: error, status: 404 | 500 }
      })
      .finally(() => {
        options.callback()
      })
  }

  /**
   * delete banners request.
   * @param {number[]} bannerIds banner id list
   * @param {BaseAddHeaderResponse} header
   * @return {void}
   */
  const deleteBannerRequest = async (
    bannerIds: number[],
    options: AuthAppHeaderOptions
  ): Promise<ServerRequestType> => {
    // axios.defaults.withCredentials = true
    const body = { banners: bannerIds }
    return await useRequest()
      .deleteRequest<ServerRequestType<BannerType[]>>(
        config.endpoint.banners.delete,
        {
          headers: options.headers,
          data: body,
        }
      )
      .then((response) => {
        return { data: response.data, status: 200 }
      })
      .catch((error) => {
        return { data: error, status: 404 | 500 }
      })
      .finally(() => {
        options.callback()
      })
  }

  // ------------------ useReducer() version

  /**
   * update banner's text value.
   * @param {number} index
   * @param {BannerTextKeys} key
   * @param {string} value
   * @return {void}
   */
  /* const updateBannerTextData = useCallback(
    (index: number, key: BannerTextKeys, value: string) => {
      dispatch({ type: key, index, value })
    },
    [dispatch]
  ) */

  /**
   * update banner's number value.
   * @param {number} index
   * @param {BannerSelectKeys} key
   * @param {number} value
   * @return {void}
   */
  /* const updateBannerNumberData = useCallback(
    (index: number, key: BannerSelectKeys, value: number) => {
      dispatch({ type: key, index, value })
    },
    [dispatch]
  ) */

  return {
    bannersState,
    updateBannerTextData,
    updateBannerNumberData,
    getBannersRequest,
    getBannersCsvFileRequest,
    getBannerTemplateRequest,
    updateBannerRequest,
    deleteBannerRequest,
  } as const
}

export type UseToastType = ReturnType<typeof useBanners>
