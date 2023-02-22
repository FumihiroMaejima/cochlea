import React, {
  FormEventHandler,
  ChangeEventHandler,
  FocusEventHandler,
  MouseEventHandler,
} from 'react'

type Props = {
  value?: string | number | readonly string[] | undefined
  label?: string
  className?: string
  isClose?: boolean
  onInput?: FormEventHandler<HTMLSelectElement>
  onChange?: ChangeEventHandler<HTMLSelectElement>
  onFocus?: FocusEventHandler<HTMLDivElement>
  onBlur?: FocusEventHandler<HTMLDivElement>
  onClick?: MouseEventHandler<HTMLDivElement>
  onClickClose?: MouseEventHandler<HTMLButtonElement>
}

export const PartsSimpleChip: React.VFC<Props> = ({
  label = 'label text',
  value = undefined,
  className = undefined,
  isClose = false,
  onClick = undefined,
  onClickClose = undefined,
}) => {
  return (
    <div
      className={`parts-simple-chip${className ? ' ' + className : ''}`}
      onClick={onClick}
    >
      {label}
      {isClose && (
        <button
          type="button"
          className="parts-simple-chip__close-chip"
          value={value}
          onClick={onClickClose}
        ></button>
      )}
    </div>
  )
}

export default PartsSimpleChip
