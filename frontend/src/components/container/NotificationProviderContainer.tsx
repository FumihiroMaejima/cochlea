/* eslint-disable @typescript-eslint/no-unused-vars */
import React, { createContext, ReactElement } from 'react'
import {
  useNotificationCore,
  StatusType,
  UseNotificationCoreType,
} from '@/hooks/global/useNotification'

type Props = {
  children: ReactElement
}

const defaultContextValue: UseNotificationCoreType = {
  state: { message: '', status: 'success', isDisplay: false },
  // eslint-disable-next-line @typescript-eslint/no-empty-function
  updateState: (message: string, status: StatusType, isDisplay: boolean) => {},
}
export const NotificationContext = createContext(defaultContextValue)

export const NotificationProviderContainer: React.FC<Props> = (props) => {
  const { state, updateState } = useNotificationCore()
  return (
    <NotificationContext.Provider value={{ state, updateState }}>
      {props.children}
    </NotificationContext.Provider>
  )
}

export default NotificationProviderContainer
