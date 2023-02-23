import React, { FormEventHandler, FocusEventHandler } from 'react'

type Props = {
  value: string
  className?: string
  id?: string
  onInput?: FormEventHandler<HTMLInputElement>
  onFocus?: FocusEventHandler<HTMLInputElement>
  onBlur?: FocusEventHandler<HTMLInputElement>
  type?: React.HTMLInputTypeAttribute
  placeholder?: string
  maxLength?: number
  required?: boolean
  disabled?: boolean
  readOnly?: boolean
}

export const PartsSimpleTextField: React.VFC<Props> = ({
  value = '',
  className = undefined,
  id = undefined,
  onInput = undefined,
  onFocus = undefined,
  onBlur = undefined,
  type = 'text',
  placeholder = undefined,
  maxLength = undefined,
  required = undefined,
  disabled = false,
  readOnly = false,
}) => {
  return (
    <input
      // className={`parts-simple-text-field`}
      className={`parts-simple-text-field ${className ? ' ' + className : ''}`}
      type={type}
      id={id}
      value={value}
      onInput={onInput}
      onFocus={onFocus}
      onBlur={onBlur}
      placeholder={placeholder}
      maxLength={maxLength}
      required={required}
      disabled={disabled}
      readOnly={readOnly}
    />
  )
}

export default PartsSimpleTextField
