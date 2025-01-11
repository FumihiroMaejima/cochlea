import React, { ReactNode } from 'react'

type ColorType = 'black' | 'dark-grey' | 'blue' | 'green' | 'red' | 'white'
type ItemType = number | string | Record<'key', number | string>
type Props = {
  items: ReactNode[]
  // color?: ColorType
  textColor?: ColorType
}

export const PartsStickyNoteList: React.FC<Props> = ({
  items = [],
  // color = 'dark-grey',
  textColor = 'dark-grey',
}) => {
  return (
    <ul
      // className={`parts-simple-button util-color__bg--${color} util-color__text--${textColor}`}
      className={`parts-sticky-note-list util-border-radius__round--5p util-color__text--${textColor}`}
    >
      {items.map((item: ReactNode, i) => (
        <li key={i}>{item}</li>
      ))}
    </ul>
  )
}

export default PartsStickyNoteList
