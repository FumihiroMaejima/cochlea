import React from 'react'

type ColorType = 'black' | 'dark-grey' | 'blue' | 'green' | 'red' | 'white'
type Props = {
  text: string
  color?: ColorType
  textColor?: ColorType
  isDashed?: boolean
  isDouble?: boolean
}

export const PartsSimpleBox: React.VFC<Props> = ({
  text = '',
  color = 'dark-grey',
  textColor = 'dark-grey',
  isDashed = false,
  isDouble = false,
}) => {
  return (
    <div
      className={`parts-simple-box util-border-full-solid-2p__color--${color} util-border-radius__round--5p util-color__text--${textColor}
       ${isDashed ? `util-border-full-dash__color--${color}` : ''} ${
        isDouble ? 'parts-simple-box__double' : ''
      }
      `}
    >
      <p>{text}</p>
    </div>
  )
}

export default PartsSimpleBox
