import React from 'react'

type ColorType = 'black' | 'dark-grey' | 'blue' | 'green' | 'red' | 'white'
type Props = {
  text: string
  title?: string
  color?: ColorType
  textColor?: ColorType
  isDashed?: boolean
  isDouble?: boolean
}

export const PartsTitleBox: React.VFC<Props> = ({
  text = '',
  title = 'title',
  color = 'dark-grey',
  textColor = 'dark-grey',
  isDashed = false,
  isDouble = false,
}) => {
  return (
    <div
      className={`parts-title-box util-border-full-solid-2p__color--${color} util-border-radius__round--5p util-color__text--${textColor}
       ${isDashed ? `util-border-full-dash__color--${color}` : ''} ${
        isDouble ? 'parts-title-box__double' : ''
      }
      `}
    >
      <span
        className={`parts-title-box__title parts-title-box__title--over parts-title-box__title-color--${color}`}
      >
        {title}
      </span>
      <p>{text}</p>
    </div>
  )
}

export default PartsTitleBox
