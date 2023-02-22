import React from 'react'
import { TableContentsType } from '@/types'

type Props = {
  items: TableContentsType[]
}

export const PartsLabelTable: React.VFC<Props> = (props) => {
  return (
    <table className="parts-label-table parts-label-table__table-element">
      <tbody>
        {props.items.map((item, i) => (
          <tr key={i}>
            <th>{item.label}</th>
            <td>{item.value}</td>
          </tr>
        ))}
      </tbody>
    </table>
  )
}

export default PartsLabelTable
