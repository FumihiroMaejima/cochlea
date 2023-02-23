import React, { MouseEventHandler, useEffect, useState } from 'react'

type Props = {
  showPosition?: number
}

export const GlobalBackToPageTopButton: React.VFC<Props> = ({
  showPosition = 100, //ボタンを表示させ始める位置
}) => {
  const [isButtonActive, setIsButtonActive] = useState(false)

  const returnTop: MouseEventHandler<HTMLButtonElement> = (): void => {
    window.scrollTo({
      top: 0,
      behavior: 'smooth',
    })
  }

  const scrollWindow = () => {
    if (showPosition <= window.scrollY) {
      setIsButtonActive(true)
    } else {
      setIsButtonActive(false)
    }
  }

  // mount後に実行する処理
  const onDidMount = (): (() => void) => {
    window.addEventListener('scroll', scrollWindow)
    return () => {
      window.removeEventListener('scroll', scrollWindow)
    }
  }
  useEffect(onDidMount, [])

  return (
    <button
      className={
        isButtonActive
          ? 'global-back-to-page-top-button'
          : 'global-back-to-page-top-button__none'
      }
      onClick={returnTop}
    >
      ↑
    </button>
  )
}

export default GlobalBackToPageTopButton
