/* eslint-disable @typescript-eslint/no-unused-vars */
/* eslint-disable @typescript-eslint/no-empty-function */
import React, { createContext } from 'react'

import {
  useAuthApp,
  UseAuthAppType,
  initialHeaderOptions,
} from '@/hooks/auth/useAuthApp'

const defaultContextValue: UseAuthAppType = {
  getAuthId: () => {
    return null
  },
  getAuthName: () => {
    return ''
  },
  getAuthAuthority: () => {
    return []
  },
  checkAuthority: (scope: string[]) => {
    return false
  },
  getHeaderOptions: () => {
    return { ...initialHeaderOptions }
  },
  login: async (email: string, password: string) => {
    return false
  },
  logout: async () => {
    return false
  },
  checkAuthenticated: async () => {
    return false
  },
}

// export const NotificationContext = createContext(defaultContextValue)
export const AuthAppContext = createContext(defaultContextValue)

export const AuthAppProviderContainer: React.FC = (props) => {
  // const { state, updateState } = useAuthApp()
  const {
    getAuthId,
    getAuthName,
    getAuthAuthority,
    checkAuthority,
    getHeaderOptions,
    login,
    logout,
    checkAuthenticated,
  } = useAuthApp()

  return (
    <AuthAppContext.Provider
      value={{
        getAuthId,
        getAuthName,
        getAuthAuthority,
        checkAuthority,
        getHeaderOptions,
        login,
        logout,
        checkAuthenticated,
      }}
    >
      {props.children}
    </AuthAppContext.Provider>
  )
}

export default AuthAppProviderContainer
