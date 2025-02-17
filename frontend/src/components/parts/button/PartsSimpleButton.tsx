import React, { MouseEventHandler } from 'react'

type ColorType = 'black' | 'dark-grey' | 'blue' | 'green' | 'red' | 'white'
type Props = {
  className?: string
  text?: string
  color?: ColorType
  textColor?: ColorType
  onClick?: MouseEventHandler<HTMLButtonElement>
  disabled?: boolean
}

export const PartsSimpleButton: React.FC<Props> = ({
  className = undefined,
  text = 'text',
  color = 'dark-grey',
  textColor = 'white',
  // eslint-disable-next-line @typescript-eslint/no-unused-vars
  onClick = (e: React.MouseEvent<HTMLButtonElement>) => {
    // eslint-disable-next-line no-console
    console.log('default click')
  },
  disabled = false,
}) => {
  return (
    <button
      // className={`parts-simple-button util-color__bg--${color} util-color__text--${textColor}`}
      className={`parts-simple-button parts-simple-button__color--${color} util-color__text--${textColor}${
        disabled ? ' parts-simple-button__disabled' : ''
      }${className ? ' ' + className : ''}`}
      onClick={onClick}
      disabled={disabled}
    >
      {text}
    </button>
  )
}

export default PartsSimpleButton
