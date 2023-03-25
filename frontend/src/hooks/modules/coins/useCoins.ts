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
// import { TableColumnSetting } from '@/types/config/data'
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

export const coinData = {
  id: 0,
  name: '',
  detail: '',
  price: 0,
  cost: 0,
  start_at: '',
  end_at: '',
  image: '',
  created_at: '',
  updated_at: '',
  deleted_at: '',
}

export type CoinType = typeof coinData
export type CoinTypeKeys = keyof CoinType
export type CoinTextKeys = Extract<
  CoinTypeKeys,
  'name' | 'detail' | 'start_at' | 'end_at' | 'image'
>
export type CoinSelectKeys = Extract<CoinTypeKeys, 'price' | 'cost'>

export type StateType = {
  coins: CoinType[]
}

export const initialData: StateType = {
  coins: [...[]],
}

/* type ReducerActionType = {
  index: number
  type: CoinTypeKeys
  value: number | string
} */

/**
 * reducer function.
 * @param {StateType} currentValue
 * @param {ReducerActionType} action
 * @return {void}
 */
/* const reducer = (currentValue: StateType, action: ReducerActionType) => {
  return {
    ...currentValue,
    coins: currentValue.coins.map((coin, i) => {
      if (i === action.index) {
        if (typeof action.value === 'string') {
          coin[action.type as CoinTextKeys] = action.value
        } else if (typeof action.value === 'number') {
          coin[action.type as CoinSelectKeys] = action.value
        }
      }
      return coin
    }),
  }
} */

export function useCoins() {
  /* const [coinsState, dispatch] = useReducer(reducer, {
    ...initialData,
  }) */
  const [coinsState, dispatch] = useState({ ...initialData })

  /**
   * update coin's text value.
   * @param {number} index
   * @param {CoinTextKeys} key
   * @param {string} value
   * @return {void}
   */
  const updateCoinTextData = (
    index: number,
    key: CoinTextKeys,
    value: string
  ) => {
    dispatch({
      coins: coinsState.coins.map((coin, i) => {
        if (i === index) {
          coin[key as CoinTextKeys] = value
        }
        return coin
      }),
    })
  }

  /**
   * update coin's number value.
   * @param {number} index
   * @param {CoinSelectKeys} key
   * @param {number} value
   * @return {void}
   */
  const updateCoinNumberData = (
    index: number,
    key: CoinSelectKeys,
    value: number
  ) => {
    dispatch({
      coins: coinsState.coins.map((coin, i) => {
        if (i === index) {
          coin[key as CoinSelectKeys] = value
        }
        return coin
      }),
    })
  }

  /**
   * set coin's data.
   * @param {CoinType[]} coins
   * @return {void}
   */
  const setCoins = (coins: CoinType[]) => {
    coinsState.coins = coins
    dispatch(coinsState)
  }

  /**
   * get coins data.
   * @param {BaseAddHeaderResponse} header
   * @return {void}
   */
  const getCoinsRequest = async (
    options: AuthAppHeaderOptions
  ): Promise<ServerRequestType> => {
    // axios.defaults.withCredentials = true
    return await useRequest()
      .getRequest<ServerRequestType<CoinType[]>>(config.endpoint.coins.coins, {
        headers: options.headers,
      })
      .then((response) => {
        // TODO remove comment out
        // setCoins(response.data.data)
        // TODO fix to above
        // setCoins(response.data as unknown as CoinType[])
        const data = response.data as ServerRequestType<CoinType[]>
        setCoins(data.data as CoinType[])
        return { data: response.data, status: 200 }
        // setCoins(response.data.data)
        // return { data: response.data.data, status: response.status }
      })
      .catch((error) => {
        return { data: error, status: 404 | 500 }
        /* return {
        data: error,
        status: error.response ? error.response.status : 401
      } */
      })
      .finally(() => {
        options.callback()
      })
  }

  /**
   * get coins csv file.
   * @param {BaseAddHeaderResponse} header
   * @return {void}
   */
  const getCoinsCsvFileRequest = async (
    options: AuthAppHeaderOptions
  ): Promise<ServerRequestType> => {
    // axios.defaults.withCredentials = true
    return await useRequest()
      .getRequest<ServerRequestType<BlobPart>>(config.endpoint.coins.csv, {
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
  const getCoinTemplateRequest = async (
    options: AuthAppHeaderOptions
  ): Promise<ServerRequestType> => {
    // axios.defaults.withCredentials = true
    return await useRequest()
      .getRequest<ServerRequestType<BlobPart>>(
        config.endpoint.coins.fileTemplate,
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
   * update coins request.
   * @param {CoinType} coin coin record
   * @param {BaseAddHeaderResponse} header
   * @return {void}
   */
  const updateCoinRequest = async (
    coin: CoinType,
    options: AuthAppHeaderOptions
  ): Promise<ServerRequestType> => {
    // axios.defaults.withCredentials = true
    const body = {
      name: coin.name,
      detail: coin.detail,
      price: coin.price,
      cost: coin.cost,
      start_at: coin.start_at,
      end_at: coin.end_at,
    }
    return await useRequest()
      .patchRequest<ServerRequestType<CoinType[]>>(
        config.endpoint.coins.coin.replace(':id', String(coin.id)),
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
   * delete coins request.
   * @param {number[]} coinIds coin id list
   * @param {BaseAddHeaderResponse} header
   * @return {void}
   */
  const deleteCoinRequest = async (
    coinIds: number[],
    options: AuthAppHeaderOptions
  ): Promise<ServerRequestType> => {
    // axios.defaults.withCredentials = true
    const body = { coins: coinIds }
    return await useRequest()
      .deleteRequest<ServerRequestType<CoinType[]>>(
        config.endpoint.coins.delete,
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
   * update coin's text value.
   * @param {number} index
   * @param {CoinTextKeys} key
   * @param {string} value
   * @return {void}
   */
  /* const updateCoinTextData = useCallback(
    (index: number, key: CoinTextKeys, value: string) => {
      dispatch({ type: key, index, value })
    },
    [dispatch]
  ) */

  /**
   * update coin's number value.
   * @param {number} index
   * @param {CoinSelectKeys} key
   * @param {number} value
   * @return {void}
   */
  /* const updateCoinNumberData = useCallback(
    (index: number, key: CoinSelectKeys, value: number) => {
      dispatch({ type: key, index, value })
    },
    [dispatch]
  ) */

  return {
    coinsState,
    updateCoinTextData,
    updateCoinNumberData,
    getCoinsRequest,
    getCoinsCsvFileRequest,
    getCoinTemplateRequest,
    updateCoinRequest,
    deleteCoinRequest,
  } as const
}

export type UseToastType = ReturnType<typeof useCoins>
