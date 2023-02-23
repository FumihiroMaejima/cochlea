// eslint-disable-next-line @typescript-eslint/no-unused-vars
import React, { useState } from 'react'

export type UseToastStateType = {
  isOpen: boolean
}

export type StatusType = 'normal' | 'success' | 'warning' | 'error'

export type State = {
  message: string
  status: StatusType
  isDisplay: boolean
}

/* const initialStateFactory = (initialState?: Partial<State>): State => ({
  message: '',
  status: 'success',
  isDisplay: false,
  ...initialState,
}) */

export const initialData: State = {
  message: 'message',
  status: 'success',
  isDisplay: false,
}

export type UseNotificationCoreType = {
  state: State
  updateState: (message: string, status: StatusType, isDisplay: boolean) => void
}

export function useNotificationCore(): UseNotificationCoreType {
  const [state, dispatch] = React.useState<State>({ ...initialData })
  // const [state, dispatch] = React.useReducer(() => {return {...initialData}}, initialData)

  /**
   * update state by parameter value.
   * @param {string} message
   * @param {StatusType} status
   * @param {boolean} isDisplay
   * @return {void}
   */
  const updateState = (
    message: string,
    status: StatusType,
    isDisplay: boolean
  ): void => {
    dispatch({ ...state, message, status, isDisplay })
  }

  return {
    state,
    updateState,
  } as const
}
