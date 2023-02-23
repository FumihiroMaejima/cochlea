import React, {
  useState,
  useRef,
  useEffect,
  // FormEventHandler,
  // ChangeEventHandler,
  MouseEventHandler,
} from 'react'
import { PartsTextChipBox } from '@/components/parts/form/PartsTextChipBox'
import { PartsSimpleMenu } from '@/components/parts/menu/PartsSimpleMenu'

type Props = {
  value: string | number | readonly string[] | undefined
  // onInput?: FormEventHandler<HTMLSelectElement>
  // onChange?: ChangeEventHandler<HTMLSelectElement>
  // onClickOtion?: MouseEventHandler<HTMLOptionElement>
  onClickOtion?: <T = string>(v: T) => void
  // setter?: <T = string>(v: T) => void
  items?: Record<string, string | number | string[] | undefined>[]
  itemText?: string
  itemValue?: string
  // multiple?: boolean
  onClickClose?: MouseEventHandler<HTMLButtonElement>
  placeholder?: string
  required?: boolean
  disabled?: boolean
}

export const PartsSimpleAutoComplete: React.VFC<Props> = ({
  value = undefined,
  // onInput = undefined,
  // onChange = undefined,
  onClickOtion = undefined,
  // setter = undefined,
  items = [],
  itemText = 'text',
  itemValue = 'value',
  // multiple = false,
  onClickClose = undefined,
  placeholder = undefined,
  required = undefined,
  disabled = false,
}) => {
  const [searchValue, setSearchValue] = useState<string>('')
  const [isFocus, setFocusValue] = useState<boolean>(false)

  const refElement = useRef<HTMLDivElement>(null) // reference to container

  // componentの外側をクリックした時のハンドリング
  useEffect(() => {
    const closeMenuByFocusEventHandler = (e: MouseEvent | FocusEvent) => {
      if (refElement?.current?.contains(e.target as Node)) {
        // click inside thie component
        // console.log('in')
        return
      } else {
        // click outside thie component
        // console.log('out')

        // フォーカスを閉じる
        setFocusValue(false)
        setSearchValue('')
      }
    }
    // document.addEventListener('mousedown', closeMenuByFocusEventHandler)
    // ;() => document.removeEventListener('mousedown', closeMenuByFocusEventHandler)
    document.addEventListener('mousedown', (e: MouseEvent) => {
      closeMenuByFocusEventHandler(e)
      document.removeEventListener('mousedown', closeMenuByFocusEventHandler)
    })

    document.addEventListener('focusout', (e: FocusEvent) => {
      closeMenuByFocusEventHandler(e)
      document.removeEventListener('focusout', closeMenuByFocusEventHandler)
    })
  }, [])

  /**
   * get selected items for chip by selected value.
   * @param {string | number | readonly string[] | undefined} v
   * @return {(Record<'text', string> & Record<'value', number>)[]}
   */
  const getSelectedChipItems = (
    v: string | number | readonly string[] | undefined
  ): (Record<'text', string> & Record<'value', number>)[] => {
    if (v) {
      if (typeof v === 'string') {
        const target = items.find((item) => item[itemValue] === v)
        return [
          {
            text: target ? String(target[itemText]) : '',
            value: parseInt(v),
          },
        ]
      } else if (Array.isArray(v)) {
        return items
          .filter((item) => v.includes(String(item[itemValue])))
          .map((item) => {
            return {
              text: String(item[itemText]),
              value: parseInt(item[itemValue] as string),
            }
          })
      } else {
        return items
          .filter((item) => item[itemValue] === v)
          .map((item) => {
            return {
              text: String(item[itemText]),
              value: parseInt(item[itemValue] as string),
            }
          })
      }
    } else {
      return []
    }
  }

  return (
    <div className="parts-simple-auto-complete" ref={refElement}>
      <PartsTextChipBox
        value={searchValue}
        items={getSelectedChipItems(value)}
        onInput={(e) => setSearchValue(e.currentTarget.value)}
        onFocus={() => {
          setFocusValue(true)

          // 隣接する要素の取得
          // const menu = e.currentTarget.nextElementSibling
          // if (menu) {
          //   menu.addEventListener(
          //     'click',
          //     () => {
          //       setFocusValue(true)
          //     },
          //     { once: true }
          //   )
          // }
        }}
        onClickClose={onClickClose}
        placeholder={placeholder}
        required={required}
        disabled={disabled}
      />
      <PartsSimpleMenu
        className={isFocus ? '' : 'parts-simple-menu__none'}
        value={value}
        selectedItems={getSelectedChipItems(value).map((v) => v.value)}
        // onChange={onChange}
        onClickOtion={(e) => {
          if (onClickOtion !== undefined) {
            onClickOtion(e.currentTarget.value)
          }
          // setFocusValue(false)
          // setSearchValue('')
        }}
        // items={items}
        items={
          searchValue === ''
            ? items
            : items.filter(
                (
                  item: Record<string, string | number | string[] | undefined>
                ) => {
                  const keyText = item[itemText]
                  return typeof keyText === 'string'
                    ? keyText.match(searchValue)
                    : false
                }
              )
        }
        // multiple={false}
        // placeholder={placeholder}
        // disabled={false}
      />
    </div>
  )
}

export default PartsSimpleAutoComplete
