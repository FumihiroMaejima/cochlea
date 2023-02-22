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

export const memberData = {
  id: 0,
  name: '',
  email: '',
  roleId: 0,
}

export type MemberType = typeof memberData
export type MemberTypeKeys = keyof MemberType
export type MemberTextKeys = Exclude<MemberTypeKeys, 'roleId' | 'id'>
export type MemberSelectKeys = Exclude<MemberTypeKeys, MemberTextKeys | 'id'>

export type StateType = {
  members: MemberType[]
}

export const initialData: StateType = {
  members: [...[]],
}

/* type ReducerActionType = {
  index: number
  type: MemberTypeKeys
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
          member[action.type as MemberTextKeys] = action.value
        } else if (typeof action.value === 'number') {
          member[action.type as MemberSelectKeys] = action.value
        }
      }
      return member
    }),
  }
} */

//export function useMembers(): UseToastType {
export function useMembers() {
  /* const [membersState, dispatch] = useReducer(reducer, {
    ...initialData,
  }) */
  const [membersState, dispatch] = useState({ ...initialData })

  /**
   * update member's text value.
   * @param {number} index
   * @param {MemberTextKeys} key
   * @param {string} value
   * @return {void}
   */
  const updateMemberTextData = (
    index: number,
    key: MemberTextKeys,
    value: string
  ) => {
    dispatch({
      members: membersState.members.map((member, i) => {
        if (i === index) {
          member[key as MemberTextKeys] = value
        }
        return member
      }),
    })
  }

  /**
   * update member's number value.
   * @param {number} index
   * @param {MemberSelectKeys} key
   * @param {number} value
   * @return {void}
   */
  const updateMemberNumberData = (
    index: number,
    key: MemberSelectKeys,
    value: number
  ) => {
    dispatch({
      members: membersState.members.map((member, i) => {
        if (i === index) {
          member[key as MemberSelectKeys] = value
        }
        return member
      }),
    })
  }

  /**
   * set member's data.
   * @param {MemberType[]} members
   * @return {void}
   */
  const setMembers = (members: MemberType[]) => {
    dispatch({
      members: [...members],
    })
  }

  /**
   * get members data.
   * @param {BaseAddHeaderResponse} header
   * @return {void}
   */
  const getMembersRequest = async (
    options: AuthAppHeaderOptions
  ): Promise<ServerRequestType> => {
    // axios.defaults.withCredentials = true
    return await useRequest()
      .getRequest<ServerRequestType<any>>(config.endpoint.members.members, {
        headers: options.headers,
      })
      .then((response) => {
        // TODO remove comment out
        // setMembers(response.data.data)
        // TODO fix to above
        setMembers(response.data as unknown as MemberType[])
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
   * @param {MemberTextKeys} key
   * @param {string} value
   * @return {void}
   */
  /* const updateMemberTextData = useCallback(
    (index: number, key: MemberTextKeys, value: string) => {
      dispatch({ type: key, index, value })
    },
    [dispatch]
  ) */

  /**
   * update member's number value.
   * @param {number} index
   * @param {MemberSelectKeys} key
   * @param {number} value
   * @return {void}
   */
  /* const updateMemberNumberData = useCallback(
    (index: number, key: MemberSelectKeys, value: number) => {
      dispatch({ type: key, index, value })
    },
    [dispatch]
  ) */

  return {
    membersState,
    updateMemberTextData,
    updateMemberNumberData,
    getMembersRequest,
  } as const
}

export type UseToastType = ReturnType<typeof useMembers>
