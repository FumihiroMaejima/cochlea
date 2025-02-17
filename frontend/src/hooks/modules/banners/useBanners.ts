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
import { getFileObjectByUrl, createFileRequestFormData } from '@/util/file'
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
  uuid: '',
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
  images: Record<number, File | undefined> | undefined
}

export const initialData: StateType = {
  banners: [...[]],
  images: undefined,
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
    value: string,
  ) => {
    dispatch({
      banners: bannersState.banners.map((banner, i) => {
        if (i === index) {
          banner[key as BannerTextKeys] = value
        }
        return banner
      }),
      images: bannersState.images,
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
    value: number,
  ) => {
    dispatch({
      banners: bannersState.banners.map((banner, i) => {
        if (i === index) {
          banner[key as BannerSelectKeys] = value
        }
        return banner
      }),
      images: bannersState.images,
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
   * set banner imgae file object.
   * @param {BannerType[]} banners
   * @return {void}
   */
  const setBannerFileObjectList = async (
    banners: BannerType[],
  ): Promise<void> => {
    const fileObjectList: Record<number, File> = {}
    // 直列でファイルオブジェクトを取得
    for (const banner of banners) {
      const fileObject = await getFileObjectByUrl(banner.image)
      fileObjectList[banner.id] = fileObject
    }
    bannersState.banners = banners
    bannersState.images = fileObjectList
    dispatch(bannersState)
  }

  /**
   * update banner's fileobject value.
   * @param {number} index
   * @param {File | undefined} file
   * @return {void}
   */
  const updateBannerFileObject = (
    index: number,
    file: File | undefined,
  ): void => {
    const banner = bannersState.banners[index]
    if (!banner || !bannersState.images) {
      return
    }

    if (!file) {
      bannersState.images[banner.id] = undefined
      // TODO 一旦削除ボタンを押した段階で既存のものは削除される様にする
      /* if (bannersState.images[banner.id]) {
        const fileObject = await getFileObjectByUrl(banner.image)
        bannersState.images[banner.id] = fileObject
      } else {
        bannersState.images[banner.id] = undefined
      } */
    } else {
      bannersState.images[banner.id] = file
    }

    dispatch({
      banners: bannersState.banners,
      images: bannersState.images,
    })
  }

  /**
   * get banners data.
   * @param {BaseAddHeaderResponse} header
   * @return {void}
   */
  const getBannersRequest = async (
    options: AuthAppHeaderOptions,
  ): Promise<ServerRequestType> => {
    // axios.defaults.withCredentials = true
    const response = await useRequest()
      .getRequest<ServerRequestType<BannerType[]>>(
        config.endpoint.banners.banners,
        {
          headers: options.headers,
        },
      )
      .then((response) => {
        const data = response.data as ServerRequestType<BannerType[]>
        const banners = data.data as BannerType[]
        setBanners(banners)
        return { data: response.data, status: 200 }
      })
      .catch((error) => {
        return { data: error, status: 404 | 500 }
      })
      .finally(() => {
        options.callback()
      })

    // ファイルオブジェクトの設定
    // TODO 全て更新をかけてから追えるのに時間がかかる為、個別に取得したものを画面に更新出来る様にさせる
    await setBannerFileObjectList(bannersState.banners)
    return response
  }

  /**
   * get banners csv file.
   * @param {BaseAddHeaderResponse} header
   * @return {void}
   */
  const getBannersCsvFileRequest = async (
    options: AuthAppHeaderOptions,
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
            '',
          ),
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
    options: AuthAppHeaderOptions,
  ): Promise<ServerRequestType> => {
    // axios.defaults.withCredentials = true
    return await useRequest()
      .getRequest<ServerRequestType<BlobPart>>(
        config.endpoint.banners.fileTemplate,
        {
          headers: options.headers,
          responseType: 'blob',
        },
      )
      .then((response) => {
        const data = response.data as unknown as BlobPart
        // download
        downloadFile(
          makeDataUrl(data, response.headers['content-type']),
          response.headers['content-disposition'].replace(
            'attachment; filename=',
            '',
          ),
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
    options: AuthAppHeaderOptions,
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
        },
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
   * update banner image request.
   * @param {BannerType} banner banner record
   * @param {BaseAddHeaderResponse} header
   * @return {void}
   */
  const updateImageRequest = async (
    banner: BannerType,
    options: AuthAppHeaderOptions,
  ): Promise<ServerRequestType> => {
    // axios.defaults.withCredentials = true
    if (!bannersState.images) {
      throw new Error('No Image Setting')
    }

    const file = bannersState.images[banner.id]
    if (!file) {
      throw new Error('No File')
    }
    const form = createFileRequestFormData(file, 'image')

    return await useRequest()
      .postRequest<ServerRequestType<BannerType[]>>(
        config.endpoint.banners.image.replace(':uuid', String(banner.uuid)),
        form,
        {
          headers: options.headers,
        },
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
    options: AuthAppHeaderOptions,
  ): Promise<ServerRequestType> => {
    // axios.defaults.withCredentials = true
    const body = { banners: bannerIds }
    return await useRequest()
      .deleteRequest<ServerRequestType<BannerType[]>>(
        config.endpoint.banners.delete,
        {
          headers: options.headers,
          data: body,
        },
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
    updateBannerFileObject,
    setBannerFileObjectList,
    getBannersRequest,
    getBannersCsvFileRequest,
    getBannerTemplateRequest,
    updateBannerRequest,
    updateImageRequest,
    deleteBannerRequest,
  } as const
}

export type UseToastType = ReturnType<typeof useBanners>
