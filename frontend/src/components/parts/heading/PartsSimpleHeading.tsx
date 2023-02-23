import React from 'react'

type ColorType = 'black' | 'dark-grey' | 'blue' | 'green' | 'red' | 'white'
type Props = {
  text: string
  color: ColorType
  isDashed?: boolean
  isDouble?: boolean
  isUpAndDown?: boolean
}

export const PartsSimpleHeading: React.VFC<Props> = ({
  text = '',
  color = 'dark-grey',
  isDashed = false,
  isDouble = false,
  isUpAndDown = false,
}) => {
  return (
    <h1
      className={`parts-simple-heading util-border-bottom-solid-2p__color--${color} ${
        isDashed ? `util-border-bottom-dash__color--${color}` : ''
      } ${isDouble ? 'parts-simple-heading__double' : ''}
      ${isUpAndDown ? 'parts-simple-heading__up-down' : ''}
      `}
    >
      {text}
    </h1>
  )
}

export default PartsSimpleHeading
