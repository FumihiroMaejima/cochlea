// eslint-disable-next-line @typescript-eslint/no-unused-vars
import React, { useState, useReducer, useCallback } from 'react'

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

export type useGlobalLinerLoadingType = {
  isOpenLinerLoading: boolean
  updateGlobalLinerLoading: (value: boolean) => void
}

export function useGlobalLinerLoading(): useGlobalLinerLoadingType {
  // const [isOpenLinerLoading, updateGlobalLinerLoading] =
  //   React.useState<boolean>(false)
  const [isOpenLinerLoading, dispatch] = React.useReducer(reducer, initialData)

  const updateGlobalLinerLoading = React.useCallback(
    (nextValue: boolean) => {
      dispatch({ type: 'update', value: nextValue })
    },
    [dispatch]
  )

  return {
    isOpenLinerLoading,
    updateGlobalLinerLoading,
  } as const
}
