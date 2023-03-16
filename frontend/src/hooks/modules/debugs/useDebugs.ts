// eslint-disable-next-line @typescript-eslint/no-unused-vars
import { useState, useReducer, useCallback } from 'react'
import { useRequest } from '@/hooks/useRequest'
import { appConfig } from '@/config/data'
import { getTimeStamp } from '@/util/time'
import {
  // IAppConfig,
  // eslint-disable-next-line @typescript-eslint/no-unused-vars
  BaseAddHeaderResponse,
  ServerRequestType,
  AuthAppHeaderOptions,
} from '@/types'

const config = { ...appConfig }

export const editableRole = ['master', 'administrator']

export const debugData = {
  userId: 0,
  sessionId: '',
  email: '',
  name: '',
  fakerTimeStamp: 0,
  host: '',
  clinetIp: '',
  userAgent: '',
}

export type DebugType = typeof debugData
export type DebugTypeKeys = keyof DebugType
export type DebugTextKeys = Extract<DebugTypeKeys, 'email'>
export type DebugSelectKeys = Extract<DebugTypeKeys, 'fakerTimeStamp'>

export type StateType = {
  status: DebugType
  fakerTime?: string
}

export const initialData: StateType = {
  status: { ...debugData },
  fakerTime: undefined,
}

export function useDebugs() {
  const [debugsState, dispatch] = useState({ ...initialData })

  /**
   * set admin's data.
   * @param {DebugType[]} status
   * @return {void}
   */
  const setDebugs = (status: DebugType) => {
    debugsState.status = status
    dispatch(debugsState)
  }

  /**
   * get debugs data.
   * @param {BaseAddHeaderResponse} header
   * @return {void}
   */
  const getDebugStatusRequest = async (
    options: AuthAppHeaderOptions
  ): Promise<ServerRequestType> => {
    // axios.defaults.withCredentials = true
    return await useRequest()
      .getRequest<ServerRequestType<DebugType>>(config.endpoint.debugs.status, {
        headers: options.headers,
      })
      .then((response) => {
        const data = response.data as ServerRequestType<DebugType>
        setDebugs(data.data as DebugType)
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
   * update local faker time value.
   * @param {string} value
   * @return {void}
   */
  const updateLocalFakerTime = (value: string): void => {
    dispatch({
      status: debugsState.status,
      fakerTime: value,
    })
  }

  return {
    debugsState,
    getDebugStatusRequest,
    updateLocalFakerTime,
  } as const
}

export type UseToastType = ReturnType<typeof useDebugs>
