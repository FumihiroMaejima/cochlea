import React from 'react'

type Props = {
  children?: JSX.Element
}

export const Grid: React.VFC<Props> = ({ children = undefined }) => {
  return <div className="grid grid-container-auto">{children}</div>
}

export default Grid
