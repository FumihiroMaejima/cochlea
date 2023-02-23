// eslint-disable-next-line @typescript-eslint/no-unused-vars
import React, { useState, useReducer, useCallback } from 'react'

/* export type UseToastStateType = {
  isOpen: boolean
} */

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

export type UseToastType = {
  toastState: State
  updateToastState: (
    message: string,
    status: StatusType,
    isDisplay: boolean
  ) => void
}

/**
 * reducer function.
 * @param {State} currentValue
 * @param {State & Record<'type', 'update'>} action
 * @return {void}
 */
const reducer = (
  currentValue: State,
  action: State & Record<'type', 'update'>
) => {
  if (action.type === 'update') {
    return {
      ...currentValue,
      message: action.message,
      status: action.status,
      isDisplay: action.isDisplay,
    }
  } else {
    return currentValue
  }
}

export function useToast(): UseToastType {
  // const [state, dispatch] = React.useState<State>({ ...initialData })
  // const [state, dispatch] = React.useReducer(() => {return {...initialData}}, initialData)
  const [toastState, dispatch] = React.useReducer(reducer, {
    ...initialData,
  })

  /**
   * update state by parameter value.
   * @param {string} message
   * @param {StatusType} status
   * @param {boolean} isDisplay
   * @return {void}
   */
  const updateToastState = React.useCallback(
    (message: string, status: StatusType, isDisplay: boolean) => {
      dispatch({ type: 'update', message, status, isDisplay })
    },
    [dispatch]
  )

  /**
   * update state by parameter value.
   * @param {string} message
   * @param {StatusType} status
   * @param {boolean} isDisplay
   * @return {void}
   */
  /* const updateToastState = (
    message: string,
    status: StatusType,
    isDisplay: boolean
  ): void => {
    dispatch({ ...state, message, status, isDisplay })
  } */

  return {
    toastState,
    updateToastState,
  } as const
}
