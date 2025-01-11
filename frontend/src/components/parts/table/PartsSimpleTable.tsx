import React from 'react'

export type TableHeaderType = Record<'label', string>

export type SimpleTableDataType = {
  [key: string]: undefined | null | string | number | boolean
}

type Props = {
  headers: TableHeaderType[]
  items: SimpleTableDataType[]
}

// 画像を表示するkey
const imageKeys = ['image', 'pc_image', 'sp_image']

export const PartsSimpleTable: React.FC<Props> = (props) => {
  return (
    <table className="parts-simple-table parts-simple-table__table-element">
      <thead>
        <tr>
          {props.headers.map((header, i) => (
            <th key={i}>{header.label}</th>
          ))}
        </tr>
      </thead>
      <tbody>
        {props.items.map((item, j) => (
          <tr key={j}>
            {Object.keys(item).map((key) => (
              <td key={key}>
                {imageKeys.find((k) => k === key) ? (
                  <img src={item[key] as string} alt={`sample image${j}`}></img>
                ) : (
                  item[key]
                )}
              </td>
            ))}
          </tr>
        ))}
      </tbody>
    </table>
  )
}

export default PartsSimpleTable
