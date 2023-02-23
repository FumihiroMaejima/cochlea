import React, { ChangeEventHandler } from 'react'

export type TableHeaderType = Record<'label', string>

export type SimpleTableDataType = {
  [key: string]: undefined | null | string | number | boolean
}

type Props = {
  headers: TableHeaderType[]
  items: SimpleTableDataType[]
  editable?: boolean
  editableKeys?: string[]
  // onInput?: FormEventHandler<HTMLInputElement>
  // eslint-disable-next-line @typescript-eslint/no-unused-vars
  onInput?: <T = string>(i: number, k: string, v: T) => void
  onChange?: ChangeEventHandler<HTMLInputElement>
  // eslint-disable-next-line @typescript-eslint/no-unused-vars
  onClickUpdate?: (i: number) => void
  onClickDelete?: (i: number) => void
  maxLength?: number
  required?: boolean
  disabled?: boolean
  readOnly?: boolean
}

export const PartsSimpleEditTable: React.VFC<Props> = ({
  headers = [],
  items = [],
  editable = false,
  editableKeys = [],
  onInput = undefined,
  onChange = undefined,
  onClickUpdate = undefined,
  onClickDelete = undefined,
  maxLength = undefined,
  required = undefined,
  disabled = false,
  readOnly = false,
}) => {
  return (
    <table className="parts-simple-edit-table parts-simple-edit-table__table-element">
      <thead>
        <tr>
          {headers.map((header, i) => (
            <th key={i}>{header.label}</th>
          ))}
          {editable && <th>更新</th>}
        </tr>
      </thead>
      <tbody>
        {items.map((item, j) => (
          <tr key={j}>
            {Object.keys(item).map((key) => (
              <td key={key}>
                {editable && editableKeys.includes(key) ? (
                  <input
                    className="parts-simple-edit-table__text-field"
                    type="text"
                    value={item[key] as string}
                    // onInput={onInput}
                    onInput={(e) => {
                      if (onInput !== undefined) {
                        onInput(j, key, e.currentTarget.value)
                      }
                    }}
                    onChange={onChange}
                    maxLength={maxLength}
                    required={required}
                    disabled={disabled}
                    readOnly={readOnly}
                  />
                ) : (
                  item[key]
                )}
              </td>
            ))}
            {editable && (
              <td>
                <button
                  className="parts-simple-button parts-simple-button__color--green util-color__text--white"
                  onClick={() => {
                    if (onClickUpdate !== undefined) {
                      onClickUpdate(j)
                    }
                  }}
                >
                  update
                </button>
                <button
                  className="parts-simple-button parts-simple-button__color--red util-color__text--white"
                  onClick={() => {
                    if (onClickDelete !== undefined) {
                      onClickDelete(j)
                    }
                  }}
                >
                  delete
                </button>
              </td>
            )}
          </tr>
        ))}
      </tbody>
    </table>
  )
}

export default PartsSimpleEditTable
