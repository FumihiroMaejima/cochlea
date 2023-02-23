import React, { ReactElement } from 'react'

// props.childrenでslotと同様のことが出来る
// 型定義無しでも利用出来るがHTMLElementとして型定義しておく
type Props = {
  children: ReactElement
}

export const PartsMessageBoard: React.VFC<Props> = (props) => {
  return <div className="parts-message-board">{props.children}</div>
}

export default PartsMessageBoard
