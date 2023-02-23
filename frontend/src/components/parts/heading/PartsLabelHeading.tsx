import React from 'react'

type ColorType = 'black' | 'dark-grey' | 'blue' | 'green' | 'red' | 'white'
type Props = {
  text: string
  color?: ColorType
  isDashed?: boolean
  isDouble?: boolean
  isUpAndDown?: boolean
}

export const PartsLabelHeading: React.VFC<Props> = ({
  text = '',
  color = 'dark-grey',
  isDashed = false,
  isDouble = false,
}) => {
  return (
    <h2
      className={`parts-label-heading util-border-left-solid-5p__color--${color} ${
        isDashed ? `util-border-bottom-dash__color--${color}` : ''
      } ${isDouble ? 'parts-label-heading__double' : ''}
      `}
    >
      {text}
    </h2>
  )
}

export default PartsLabelHeading
