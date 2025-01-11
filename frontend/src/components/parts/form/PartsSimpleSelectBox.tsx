import React, { FormEventHandler, ChangeEventHandler } from 'react'

type Props = {
  value: string | number | readonly string[] | undefined
  onInput?: FormEventHandler<HTMLSelectElement>
  onChange?: ChangeEventHandler<HTMLSelectElement>
  items?: Record<string, string | number | string[] | undefined>[]
  itemText?: string
  itemValue?: string
  multiple?: boolean
  required?: boolean
  disabled?: boolean
}

export const PartsSimpleSelectBox: React.FC<Props> = ({
  value = undefined,
  onInput = undefined,
  onChange = undefined,
  items = [],
  itemText = 'text',
  itemValue = 'value',
  multiple = false,
  required = undefined,
  disabled = false,
}) => {
  return (
    <select
      className="parts-simple-select-box"
      value={value}
      onInput={onInput}
      onChange={onChange}
      multiple={multiple}
      required={required}
      disabled={disabled}
    >
      {items.map((item, i) => (
        <option key={i} value={item[itemValue]}>
          {item[itemText]}
        </option>
      ))}
    </select>
  )
}

export default PartsSimpleSelectBox
