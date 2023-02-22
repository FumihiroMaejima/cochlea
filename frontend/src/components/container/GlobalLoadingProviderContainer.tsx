/* eslint-disable @typescript-eslint/no-unused-vars */
import React, { createContext } from 'react'
import {
  useGlobalLoading,
  UseGlobalLoadingType,
} from '@/hooks/global/useGlobalLoading'

const defaultContextValue: UseGlobalLoadingType = {
  isOpenLoading: false,
  // eslint-disable-next-line @typescript-eslint/no-empty-function
  updateGlobalLoading: (value: boolean) => {},
}

export const GlobalLoadingContext = createContext(defaultContextValue)

export const GlobalLoadingProviderContainer: React.FC = (props) => {
  const { isOpenLoading, updateGlobalLoading } = useGlobalLoading()
  return (
    <GlobalLoadingContext.Provider
      value={{ isOpenLoading, updateGlobalLoading }}
    >
      {props.children}
    </GlobalLoadingContext.Provider>
  )
}

export default GlobalLoadingProviderContainer
