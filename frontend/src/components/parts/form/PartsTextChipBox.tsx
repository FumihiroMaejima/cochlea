import React, {
  FormEventHandler,
  FocusEventHandler,
  MouseEventHandler,
} from 'react'
import { PartsSimpleChip } from '@/components/parts/chip/PartsSimpleChip'

type Props = {
  value: string
  className?: string
  items?: (Record<'text', string> & Record<'value', number>)[]
  onInput?: FormEventHandler<HTMLInputElement>
  onFocus?: FocusEventHandler<HTMLInputElement>
  onBlur?: FocusEventHandler<HTMLInputElement>
  onClickClose?: MouseEventHandler<HTMLButtonElement>
  placeholder?: string
  maxLength?: number
  required?: boolean
  disabled?: boolean
  readOnly?: boolean
}

export const PartsTextChipBox: React.VFC<Props> = ({
  value = '',
  className = undefined,
  items = [],
  onInput = undefined,
  onFocus = undefined,
  onBlur = undefined,
  onClickClose = undefined,
  placeholder = undefined,
  maxLength = undefined,
  required = undefined,
  disabled = false,
  readOnly = false,
}) => {
  return (
    <div className={`parts-text-chip-box${className ? ' ' + className : ''}`}>
      {items.length > 0 && (
        <div className={`parts-text-chip-box__selected-area`}>
          {items.map((item, i) => (
            <PartsSimpleChip
              className="parts-simple-chip__small"
              label={item.text}
              value={item.value}
              isClose={true}
              onClickClose={onClickClose}
              key={i}
            />
          ))}
        </div>
      )}
      <input
        // className={`parts-simple-text-field`}
        type="text"
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
    </div>
  )
}

export default PartsTextChipBox
