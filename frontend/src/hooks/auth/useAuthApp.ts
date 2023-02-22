/* eslint-disable @typescript-eslint/no-var-requires */
// eslint-disable-next-line @typescript-eslint/no-unused-vars
import React, { useState } from 'react'
// import axios, { AxiosResponse, AxiosError } from 'axios'
import Authentication from '@/plugins/auth/authentication'
import {
  AuthState,
  // RootState,
  HeaderDataState,
  AuthEndpoint,
  // IAppConfig,
  // eslint-disable-next-line @typescript-eslint/no-unused-vars
  BaseAddHeaderResponse,
  // ServerRequestType,
  AuthAppHeaderOptions,
} from '@/types'

// JSX側でエラーが発生
// const config: IAppConfig = require('@/config/data')
import { appConfig } from '@/config/data'

const config = { ...appConfig }

const endpoint: AuthEndpoint = config.authEndpoint
const appKey = 'application_token'
const headerPrefix = 'Bearer'

/* export type State = {
  router: unknown
  store: AuthState
  authentication: Authentication
} */

/* export type UseAuthAppStateType = {
  router: unknown
  store: AuthState
  authentication: Authentication
} */

export const initialHeaders = {
  Authorization: '',
  'X-Auth-ID': '',
  'X-Auth-Authority': '',
  'Content-Type': '',
}

export const initialHeaderOptions: AuthAppHeaderOptions = {
  headers: { ...initialHeaders },
  // eslint-disable-next-line @typescript-eslint/no-empty-function
  callback: () => {},
}

const initialAuthState: AuthState = {
  name: '',
  id: null,
  authority: [],
}

export type UseAuthAppType = {
  // state: AuthState
  getAuthId: () => AuthState['id']
  getAuthName: () => AuthState['name']
  getAuthAuthority: () => AuthState['authority']
  checkAuthority: (scope: string[]) => boolean
  getHeaderOptions: () => AuthAppHeaderOptions
  login: (email: string, password: string) => Promise<boolean>
  logout: () => Promise<boolean>
  checkAuthenticated: () => Promise<boolean>
}

export function useAuthApp(): UseAuthAppType {
  const [state, dispatch] = React.useState<AuthState>({ ...initialAuthState })
  // const router = undefined
  const authentication = new Authentication(endpoint)

  /**
   * update state by parameter value.
   * @param {number | null} id
   * @param {string | null} name
   * @param {string[]} authority
   * @return {void}
   */
  const updateState = (
    id: number | null,
    name: string | null,
    authority: string[]
  ): void => {
    dispatch({ ...state, id, name, authority })
  }

  /**
   * return authenticated id
   * @return {number} id
   */
  const getAuthId = (): AuthState['id'] => {
    return state.id
  }

  /**
   * return authenticated name
   * @return {string} name
   */
  const getAuthName = (): AuthState['name'] => {
    return state.name
  }

  /**
   * return authenticated authority
   * @return {Object} authority
   */
  const getAuthAuthority = (): AuthState['authority'] => {
    return state.authority
  }

  /**
   * get authApp header options.
   * @return {AuthAppHeaderOptions} { headers, callback }
   */
  const getHeaderOptions = (): AuthAppHeaderOptions => {
    const token: string = getCookie(appKey)
    // tokenが無い場合はデータを初期化する
    if (token === '') {
      resetAction()
    }
    restoreToken()

    return {
      headers: addHeaders({
        id: getAuthId(),
        authority: String(getAuthAuthority()),
        token: token,
      }),
      callback: () => restoreToken(token, true),
    }
  }

  /**
   * get specific cookie.
   * @param {string} key
   * @return {string} cookie
   */
  const getCookie = (key = '') => {
    const targetCookie = document.cookie
      .split(',')
      .find((value) => value.startsWith(`${key}=`))
    // RegExpオブジェクトで置き換え
    return targetCookie === undefined
      ? ''
      : targetCookie.replace(new RegExp(`${key}=`, 'g'), '')
  }

  /**
   * set store data.
   * @param {number | null} id
   * @param {string | null} name
   * @param {string[]} authority
   * @return {void}
   */
  const setStoreStatus = (
    id: number | null,
    name: string | null,
    authority: string[] = []
  ) => {
    // state.store = { id, name, authority }
    updateState(id, name, authority)
  }

  /**
   * set cookie.
   * @param {string} key
   * @param {string} value
   * @return {void}
   */
  const setCookie = (key: string, value: string, minutes = 10) => {
    document.cookie = `${key}=${value};max-age=${60 * minutes}`
  }

  /**
   * remove cookies.
   * @param {string} key
   * @return {void}
   */
  const removeCookie = (key: string) => {
    document.cookie = `${key}=;max-age=0`
  }

  /**
   * make request header.
   * @param {HeaderDataState} data
   * @return {BaseAddHeaderResponse}
   */
  const addHeaders = (data: HeaderDataState): BaseAddHeaderResponse => {
    return {
      Authorization: `${headerPrefix} ${data.token ? data.token : ''}`,
      'X-Auth-ID': data.id ? String(data.id) : '',
      'X-Auth-Authority': data.authority ? data.authority : '',
      'Content-Type': 'application/json;charset=UTF-8',
    }
  }

  /**
   * run check authenticated request.
   * @param {number | null} id
   * @param {string | null} token
   * @return {Object}
   */
  const authInstance = async (headers: BaseAddHeaderResponse) => {
    const response = await authentication.getUser(headers)
    if (response.status !== 200) {
      return {
        id: null,
        name: null,
        authority: {},
      }
    } else {
      return {
        id: response.data.id,
        name: response.data.name,
        authority: response.data.authority,
      }
    }
  }

  /**
   * reset relation data.
   * @param {boolean} resetCookie (default: false)
   * @return {void}
   */
  const resetAction = (resetCookie = false) => {
    if (resetCookie) {
      removeCookie(appKey)
    }

    refreshAuthData()
  }

  /**
   * refresh auth data.
   * @return {void}
   */
  const refreshAuthData = () => {
    // state.store.dispatch('auth/getAuthData', { id: null, name: null, authority: [] })
    // state.store = { ...initialAuthState }
    const tmp = { ...initialAuthState }
    updateState(tmp.id, tmp.name, tmp.authority)
    // ('auth/getAuthData', { id: null, name: null, authority: [] })
  }

  /**
   * check own authority
   * @param {string[]} scope
   * @return {boolean}
   */
  const checkAuthority = (scope: string[]): boolean => {
    return getAuthAuthority().some((role) => scope.includes(role))
  }

  /**
   * restore or remove cookie.
   * @param {boolean} isRestore
   * @return {void}
   */
  const restoreToken = (token = '', isRestore = false) => {
    isRestore ? setCookie(appKey, token) : removeCookie(appKey)
  }

  /**
   * login action.
   * @param {Object} data
   * @return {boolean}
   */
  const login = async (email: string, password: string): Promise<boolean> => {
    const response = await authentication.loginRequest({
      email: email,
      password: password,
    })
    if (response.status !== 200) {
      return false
    } else {
      // 認証情報の設定
      // this.store.dispatch('auth/getAuthData', { id: response.data.user.id, name: response.data.user.name, authority: response.data.user.authority })
      setStoreStatus(
        response.data.user.id,
        response.data.user.name,
        response.data.user.authority
      )
      setCookie(appKey, response.data.access_token)

      // homeへ遷移
      // this.router.push('/')
      return true
    }
  }

  /**
   * logout action.
   * @return {Object}
   */
  const logout = async (): Promise<boolean> => {
    const response = await authentication.logoutRequest(
      getHeaderOptions().headers
    )
    const result = response.status === 200

    // データの初期化
    resetAction(true)
    // login画面へ遷移
    // this.router.push('/login')
    return result
  }

  /**
   * check authenticated data.
   * @return {boolean}
   */
  const checkAuthenticated = async (): Promise<boolean> => {
    const { headers, callback } = getHeaderOptions()

    // tokenが無い場合はデータを初期化する
    if (headers.Authorization.trim().length <= headerPrefix.length) {
      resetAction()
      return false
    }

    const isAuth = await authInstance(headers).then((response) => {
      // 認証情報が無い場合
      if (!response.id) {
        resetAction(true)
        return false
      }

      // 取得した認証情報の設定
      // this.store.dispatch('auth/getAuthData', { id: response.id, name: response.name, authority: response.authority })
      setStoreStatus(response.id, response.name, response.authority)
      callback()
      return true
    })
    return isAuth
  }

  return {
    // state,
    getAuthId,
    getAuthName,
    getAuthAuthority,
    checkAuthority,
    getHeaderOptions,
    login,
    logout,
    checkAuthenticated,
  }
  /* return {
    state,
    getAuthId,
    getAuthName,
    getAuthAuthority,
    checkAuthority,
    getHeaderOptions,
    login,
    logout,
    checkAuthenticated,
  } as const */
}
