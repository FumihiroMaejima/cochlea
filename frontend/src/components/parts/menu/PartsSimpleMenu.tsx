import React, {
  // FormEventHandler,
  // ChangeEventHandler,
  FocusEventHandler,
  TouchEventHandler,
  MouseEventHandler,
} from 'react'

type Props = {
  value: string | number | readonly string[] | undefined
  className?: string
  // onInput?: FormEventHandler<HTMLSelectElement>
  // onChange?: ChangeEventHandler<HTMLSelectElement>
  onFocus?: FocusEventHandler<HTMLDivElement>
  onBlur?: FocusEventHandler<HTMLDivElement>
  onTouchMove?: TouchEventHandler<HTMLDivElement>
  onClickOtion?: MouseEventHandler<HTMLOptionElement>
  onClickCapture?: MouseEventHandler<HTMLDivElement>
  onBlurCapture?: FocusEventHandler<HTMLDivElement>
  items?: Record<string, string | number | string[] | undefined>[]
  itemText?: string
  itemValue?: string
  selectedItems?: (string | number)[]
  // multiple?: boolean
  // required?: boolean
  // disabled?: boolean
}

export const PartsSimpleMenu: React.VFC<Props> = ({
  value = undefined,
  className = undefined,
  // onInput = undefined,
  // onChange = undefined,
  onClickCapture = undefined,
  onBlurCapture = undefined,
  onFocus = undefined,
  onBlur = undefined,
  onTouchMove = undefined,
  onClickOtion = undefined,
  items = [],
  itemText = 'text',
  itemValue = 'value',
  selectedItems = [],
  // multiple = false,
  // required = undefined,
  // disabled = false,
}) => {
  /**
   * get selected items for chip by selected value.
   * @param {string | number | readonly string[] | undefined} v
   * @return {boolean}
   */
  const checkIsSelected = (
    v: string | number | readonly string[] | undefined
  ): boolean => {
    if (!v || Array.isArray(v)) {
      return false
    } else if (typeof v === 'string' || typeof v === 'number') {
      return selectedItems.includes(v)
    } else {
      return false
    }
  }

  return (
    <div
      className={`parts-simple-menu__wrapper${
        className ? ' ' + className : ''
      }`}
    >
      <div
        // className={className !== '' ? className : `parts-simple-menu`}
        // className={`parts-simple-menu${className ? ' ' + className : ''}`}
        className={`parts-simple-menu`}
        onFocus={onFocus}
        onClickCapture={onClickCapture}
        onBlurCapture={onBlurCapture}
        onBlur={onBlur}
        onTouchMove={onTouchMove}
        defaultValue={value}
      >
        {items.length === 0 && 'no items!'}
        {items.map((item, i) => (
          /* <option key={i} value={item[itemValue]} onClick={onClickOtion}>
            {item[itemText]}
          </option> */
          <option
            className={`parts-simple-menu__item${
              checkIsSelected(item[itemValue])
                ? ' parts-simple-menu__item--selected'
                : ''
            }`}
            key={i}
            value={item[itemValue]}
            onClick={onClickOtion}
          >
            {item[itemText]}
          </option>
          /* <div
            className={`parts-simple-menu__item`}
            key={i}
            value={item[itemValue]}
            role="option"
            onClick={onClickItem}
          >
            {item[itemText]}
          </div> */
        ))}
      </div>
    </div>
  )
}

export default PartsSimpleMenu
