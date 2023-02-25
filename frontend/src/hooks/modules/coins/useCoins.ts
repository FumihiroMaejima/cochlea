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
  admins: CoinType[]
}

export const initialData: StateType = {
  admins: [...[]],
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
    admins: currentValue.admins.map((coin, i) => {
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
      admins: coinsState.admins.map((coin, i) => {
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
      admins: coinsState.admins.map((coin, i) => {
        if (i === index) {
          coin[key as CoinSelectKeys] = value
        }
        return coin
      }),
    })
  }

  /**
   * set coin's data.
   * @param {CoinType[]} admins
   * @return {void}
   */
  const setAdmins = (admins: CoinType[]) => {
    coinsState.admins = admins
    dispatch(coinsState)
  }

  /**
   * get admins data.
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
        // setAdmins(response.data.data)
        // TODO fix to above
        // setAdmins(response.data as unknown as CoinType[])
        const data = response.data as ServerRequestType<CoinType[]>
        setAdmins(data.data as CoinType[])
        return { data: response.data, status: 200 }
        // setAdmins(response.data.data)
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
  } as const
}

export type UseToastType = ReturnType<typeof useCoins>
