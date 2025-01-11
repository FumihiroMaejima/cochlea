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
  admins: AdminType[]
}

export const initialData: StateType = {
  admins: [...[]],
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
    admins: currentValue.admins.map((admin, i) => {
      if (i === action.index) {
        if (typeof action.value === 'string') {
          admin[action.type as AdminTextKeys] = action.value
        } else if (typeof action.value === 'number') {
          admin[action.type as AdminSelectKeys] = action.value
        }
      }
      return admin
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
   * update admin's text value.
   * @param {number} index
   * @param {AdminTextKeys} key
   * @param {string} value
   * @return {void}
   */
  const updateAdminTextData = (
    index: number,
    key: AdminTextKeys,
    value: string,
  ) => {
    dispatch({
      admins: adminsState.admins.map((admin, i) => {
        if (i === index) {
          admin[key as AdminTextKeys] = value
        }
        return admin
      }),
    })
  }

  /**
   * update admin's number value.
   * @param {number} index
   * @param {AdminSelectKeys} key
   * @param {number} value
   * @return {void}
   */
  const updateAdminNumberData = (
    index: number,
    key: AdminSelectKeys,
    value: number,
  ) => {
    dispatch({
      admins: adminsState.admins.map((admin, i) => {
        if (i === index) {
          admin[key as AdminSelectKeys] = value
        }
        return admin
      }),
    })
  }

  /**
   * set admin's data.
   * @param {AdminType[]} admins
   * @return {void}
   */
  const setAdmins = (admins: AdminType[]) => {
    adminsState.admins = admins
    dispatch(adminsState)
  }

  /**
   * get admins data.
   * @param {BaseAddHeaderResponse} header
   * @return {void}
   */
  const getAdminsRequest = async (
    options: AuthAppHeaderOptions,
  ): Promise<ServerRequestType> => {
    // axios.defaults.withCredentials = true
    return await useRequest()
      .getRequest<ServerRequestType<AdminType[]>>(
        config.endpoint.admins.admins,
        {
          headers: options.headers,
        },
      )
      .then((response) => {
        // TODO remove comment out
        // setAdmins(response.data.data)
        // TODO fix to above
        // setAdmins(response.data as unknown as AdminType[])
        const data = response.data as ServerRequestType<AdminType[]>
        setAdmins(data.data as AdminType[])
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
   * update admin's text value.
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
   * update admin's number value.
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
