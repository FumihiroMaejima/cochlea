/* eslint-disable @typescript-eslint/no-unused-vars */
import React, { createContext, ReactElement } from 'react'
import { useToast, StatusType, UseToastType } from '@/hooks/global/useToast'

type Props = {
  children: ReactElement
}

const defaultContextValue: UseToastType = {
  toastState: { message: '', status: 'success', isDisplay: false },
  updateToastState: (
    message: string,
    status: StatusType,
    isDisplay: boolean,
    // eslint-disable-next-line @typescript-eslint/no-empty-function
  ) => {},
}
export const ToastContext = createContext(defaultContextValue)

export const ToastProviderContainer: React.FC<Props> = (props) => {
  const { toastState, updateToastState } = useToast()
  return (
    <ToastContext.Provider value={{ toastState, updateToastState }}>
      {props.children}
    </ToastContext.Provider>
  )
}

export default ToastProviderContainer
