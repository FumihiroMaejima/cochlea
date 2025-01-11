import React, { JSX } from 'react'

type Props = {
  children?: JSX.Element
}

export const Grid: React.FC<Props> = ({ children = undefined }) => {
  return <div className="grid grid-container-auto">{children}</div>
}

export default Grid
