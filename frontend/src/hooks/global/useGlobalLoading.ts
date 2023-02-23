// eslint-disable-next-line @typescript-eslint/no-unused-vars
import React, { useState, useReducer, useCallback } from 'react'

export type UseGlobalLoadingType = {
  isOpenLoading: boolean
  updateGlobalLoading: (value: boolean) => void
}

/**
 * reducer function.
 * @param {boolean} currentValue
 * @param {Record<'value', boolean> & Record<'type', 'update'>} action
 * @return {void}
 */
const reducer = (
  currentValue: boolean,
  action: Record<'value', boolean> & Record<'type', 'update'>
) => {
  if (action.type === 'update') {
    return action.value
  } else {
    return currentValue
  }
}

const initialData = false

export function useGlobalLoading(): UseGlobalLoadingType {
  // const [isOpenLoading, updateGlobalLoading] = React.useState<boolean>(false)
  // const [state, dispatch] = React.useReducer(() => {return {...initialData}}, initialData)
  const [isOpenLoading, dispatch] = React.useReducer(reducer, initialData)

  const updateGlobalLoading = React.useCallback(
    (nextValue: boolean) => {
      dispatch({ type: 'update', value: nextValue })
    },
    [dispatch]
  )

  return {
    isOpenLoading,
    updateGlobalLoading,
  } as const
}
