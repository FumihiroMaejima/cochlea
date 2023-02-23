import React from 'react'

type ColorType = 'blue' | 'green' | 'red'
type Props = {
  text: string
  color: ColorType
}

export const PartsLabelHeader: React.VFC<Props> = (props) => {
  return (
    <h2 className={`parts-label-header parts-label-header__${props.color}`}>
      {props.text}
    </h2>
  )
}

export default PartsLabelHeader
