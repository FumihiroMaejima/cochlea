import React from 'react'

type ColorType = 'black' | 'dark-grey' | 'blue' | 'green' | 'red' | 'white'
type Props = {
  icon?: string
  color?: ColorType
  textColor?: ColorType
}

export const PartsSimpleFlatButton: React.VFC<Props> = ({
  icon = '',
  color = 'dark-grey',
  textColor = 'white',
}) => {
  return (
    <a
      className={`parts-simple-flat-button util-color__bg--${color} util-color__text--${textColor}`}
    >
      <i className={icon}></i>
    </a>
  )
}

export default PartsSimpleFlatButton
