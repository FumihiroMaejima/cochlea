import React, { ChangeEventHandler } from 'react'
import { PartsSimpleFileInput } from '@/components/parts/form/PartsSimpleFileInput'

export type TableHeaderType = Record<'label', string>

export type SimpleTableDataType = {
  [key: string]: undefined | null | string | number | boolean
}

type Props = {
  headers: TableHeaderType[]
  items: SimpleTableDataType[]
  fileObjects?: Record<number, File | undefined> | undefined
  editable?: boolean
  editableKeys?: string[]
  // onInput?: FormEventHandler<HTMLInputElement>
  // eslint-disable-next-line @typescript-eslint/no-unused-vars
  onInput?: <T = string>(i: number, k: string, v: T) => void
  onChange?: ChangeEventHandler<HTMLInputElement>
  // eslint-disable-next-line @typescript-eslint/no-unused-vars
  onClickUpdate?: (i: number) => void
  onClickUpdateImgae?: (i: number) => void
  onClickDelete?: (i: number) => void
  onResetFile?: (i: number) => void
  updateImageHandler?: (i: number, v: File) => void
  maxLength?: number
  required?: boolean
  disabled?: boolean
  readOnly?: boolean
}

// 画像を表示するkey
const imageKeys = ['image', 'pc_image', 'sp_image']

export const PartsSimpleEditTable: React.FC<Props> = ({
  headers = [],
  items = [],
  fileObjects = undefined,
  editable = false,
  editableKeys = [],
  onInput = undefined,
  onChange = undefined,
  onClickUpdate = undefined,
  onClickUpdateImgae = undefined,
  onClickDelete = undefined,
  onResetFile = undefined,
  updateImageHandler = undefined,
  maxLength = undefined,
  required = undefined,
  disabled = false,
  readOnly = false,
}) => {
  /**
   * create preview image
   * @param {number} index
   * @param {File} file
   * @return {void}
   */
  const updateImage = (index: number, file: File): void => {
    if (updateImageHandler) {
      updateImageHandler(index, file)
    }
  }

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
            {Object.keys(item).map((key, i) => (
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
                ) : imageKeys.find((k) => k === key) ? (
                  // <img src={item[key] as string} alt={`sample image${j}`}></img>
                  <PartsSimpleFileInput
                    value={
                      !fileObjects
                        ? undefined
                        : fileObjects[item['id'] as unknown as number]
                    }
                    onUpdateFile={(e) => updateImage(j, e)}
                    onResetFile={() => {
                      if (onResetFile !== undefined) {
                        onResetFile(j)
                      }
                    }}
                    isOpenPreview={true}
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
                {onClickUpdateImgae !== undefined && (
                  <button
                    className="parts-simple-button parts-simple-button__color--black util-color__text--white my-1"
                    onClick={() => {
                      onClickUpdateImgae(j)
                    }}
                  >
                    update image
                  </button>
                )}
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
