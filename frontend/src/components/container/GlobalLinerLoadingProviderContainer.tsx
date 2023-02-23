/* eslint-disable @typescript-eslint/no-unused-vars */
import React, { createContext, ReactElement } from 'react'
import {
  useGlobalLinerLoading,
  useGlobalLinerLoadingType,
} from '@/hooks/global/useGlobalLinerLoading'

type Props = {
  children: ReactElement
}

const defaultContextValue: useGlobalLinerLoadingType = {
  isOpenLinerLoading: false,
  // eslint-disable-next-line @typescript-eslint/no-empty-function
  updateGlobalLinerLoading: (value: boolean) => {},
}

export const GlobalLinerLoadingContext = createContext(defaultContextValue)

export const GlobalLinerLoadingProviderContainer: React.FC<Props> = (props) => {
  const { isOpenLinerLoading, updateGlobalLinerLoading } =
    useGlobalLinerLoading()
  return (
    <GlobalLinerLoadingContext.Provider
      value={{ isOpenLinerLoading, updateGlobalLinerLoading }}
    >
      {props.children}
    </GlobalLinerLoadingContext.Provider>
  )
}

export default GlobalLinerLoadingProviderContainer
