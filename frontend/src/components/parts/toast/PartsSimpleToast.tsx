import React from 'react'

type Props = {
  value?: boolean
  className?: string
  data?: Record<'message', string> &
    Record<'status', 'normal' | 'success' | 'warning' | 'error'>
  onAnimationEnd?: React.AnimationEventHandler<HTMLDivElement> | undefined
}

export const PartsSimpleToast: React.VFC<Props> = ({
  value = false,
  className = undefined,
  data = undefined,
  onAnimationEnd = undefined,
}) => {
  return (
    <div
      className={`parts-simple-toast${className ? ' ' + className : ''}${
        value ? ' parts-simple-toast__display' : ''
      }
        ${value && data ? ' parts-simple-toast__status--' + data.status : ''}`}
      onAnimationEnd={onAnimationEnd}
    >
      {data ? data.message : ''}
    </div>
  )
}

export default PartsSimpleToast
