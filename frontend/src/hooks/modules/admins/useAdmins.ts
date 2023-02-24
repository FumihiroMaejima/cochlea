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

export const adminData = {
  id: 0,
  name: '',
  email: '',
  roleId: 0,
}

export type AdminType = typeof adminData
export type AdminTypeKeys = keyof AdminType
export type AdminTextKeys = Exclude<AdminTypeKeys, 'roleId' | 'id'>
export type AdminSelectKeys = Exclude<AdminTypeKeys, AdminTextKeys | 'id'>

export type StateType = {
  members: AdminType[]
}

export const initialData: StateType = {
  members: [...[]],
}

/* type ReducerActionType = {
  index: number
  type: AdminTypeKeys
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
    members: currentValue.members.map((member, i) => {
      if (i === action.index) {
        if (typeof action.value === 'string') {
          member[action.type as AdminTextKeys] = action.value
        } else if (typeof action.value === 'number') {
          member[action.type as AdminSelectKeys] = action.value
        }
      }
      return member
    }),
  }
} */

//export function useAdmins(): UseToastType {
export function useAdmins() {
  /* const [adminsState, dispatch] = useReducer(reducer, {
    ...initialData,
  }) */
  const [adminsState, dispatch] = useState({ ...initialData })

  /**
   * update member's text value.
   * @param {number} index
   * @param {AdminTextKeys} key
   * @param {string} value
   * @return {void}
   */
  const updateAdminTextData = (
    index: number,
    key: AdminTextKeys,
    value: string
  ) => {
    dispatch({
      members: adminsState.members.map((member, i) => {
        if (i === index) {
          member[key as AdminTextKeys] = value
        }
        return member
      }),
    })
  }

  /**
   * update member's number value.
   * @param {number} index
   * @param {AdminSelectKeys} key
   * @param {number} value
   * @return {void}
   */
  const updateAdminNumberData = (
    index: number,
    key: AdminSelectKeys,
    value: number
  ) => {
    dispatch({
      members: adminsState.members.map((member, i) => {
        if (i === index) {
          member[key as AdminSelectKeys] = value
        }
        return member
      }),
    })
  }

  /**
   * set member's data.
   * @param {AdminType[]} members
   * @return {void}
   */
  const setMembers = (members: AdminType[]) => {
    dispatch({
      members: [...members],
    })
  }

  /**
   * get members data.
   * @param {BaseAddHeaderResponse} header
   * @return {void}
   */
  const getAdminsRequest = async (
    options: AuthAppHeaderOptions
  ): Promise<ServerRequestType> => {
    // axios.defaults.withCredentials = true
    return await useRequest()
      .getRequest<ServerRequestType<any>>(config.endpoint.admins.admins, {
        headers: options.headers,
      })
      .then((response) => {
        // TODO remove comment out
        // setMembers(response.data.data)
        // TODO fix to above
        setMembers(response.data as unknown as AdminType[])
        return { data: response.data, status: 200 }
        // setMembers(response.data.data)
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
   * update member's text value.
   * @param {number} index
   * @param {AdminTextKeys} key
   * @param {string} value
   * @return {void}
   */
  /* const updateAdminTextData = useCallback(
    (index: number, key: AdminTextKeys, value: string) => {
      dispatch({ type: key, index, value })
    },
    [dispatch]
  ) */

  /**
   * update member's number value.
   * @param {number} index
   * @param {AdminSelectKeys} key
   * @param {number} value
   * @return {void}
   */
  /* const updateAdminNumberData = useCallback(
    (index: number, key: AdminSelectKeys, value: number) => {
      dispatch({ type: key, index, value })
    },
    [dispatch]
  ) */

  return {
    adminsState,
    updateAdminTextData,
    updateAdminNumberData,
    getAdminsRequest,
  } as const
}

export type UseToastType = ReturnType<typeof useAdmins>
