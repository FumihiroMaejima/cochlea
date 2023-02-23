import React, { useContext, useEffect } from 'react'
import type {
  NextPage,
  // GetServerSideProps,
  // GetServerSidePropsContext,
} from 'next'

import { useNavigationGuard } from '@/hooks/auth/useNavigationGuard'
import { PartsSimpleButton } from '@/components/parts/button/PartsSimpleButton'
import { PartsLabelHeading } from '@/components/parts/heading/PartsLabelHeading'
import { PartsSimpleHeading } from '@/components/parts/heading/PartsSimpleHeading'
import { AuthAppContext } from '@/components/container/AuthAppProviderContainer'

export const Home: NextPage = () => {
  const { navigationGuardHandler } = useNavigationGuard()
  const { getAuthId } = useContext(AuthAppContext)

  // mount後に実行する処理
  const onDidMount = (): void => {
    const asyncInitPageHandler = async () => {
      // 認証情報のチェック
      await navigationGuardHandler()

      if (getAuthId() !== null) {
        // TODO 認証情報取得後の処理
        // xxxxx
      }
    }
    asyncInitPageHandler()
  }
  useEffect(onDidMount, [])

  return (
    <div className="home page-container page-container__mx-auto">
      <PartsSimpleHeading text="サンプル ページ" color="dark-grey" />
      <div className="mx-2">
        <PartsLabelHeading text="サブヘッダー1" color="blue" />
        <div className="util-text__contents-area util-border-full-solid-2p__color--dark-grey util-border-radius__round--5p util-color__text--dark-grey">
          <p>test1</p>
          <p>test2</p>
          <p>test3</p>
        </div>

        <PartsLabelHeading text="サブヘッダー2" color="red" />
        <div className="util-text__contents-area util-border-full-solid-2p__color--dark-grey util-border-radius__round--5p util-color__text--dark-grey">
          <p>test1</p>
          <p>test2</p>
          <p>test3</p>
        </div>

        <PartsLabelHeading text="サブヘッダー3" color="green" />
        <div className="util-text__contents-area util-border-full-solid-2p__color--dark-grey util-border-radius__round--5p util-color__text--dark-grey">
          <p>test1</p>
          <p>test2</p>
          <p>test3</p>
        </div>

        <PartsLabelHeading text="サンプルボタン" color="dark-grey" />
        <div className="my-2">
          <div className="util-text__contents-area util-border-full-solid-2p__color--dark-grey util-border-radius__round--5p util-color__text--dark-grey">
            <PartsSimpleButton text="blue" color="blue" />
            <PartsSimpleButton text="red" color="red" />
            <PartsSimpleButton text="green" color="green" />
          </div>
        </div>
      </div>

      {/* <div className="">
        <Link to={`/about`}>Go To About</Link>
      </div>
      <div className="">
        <Link to={`/sample`}>Go To Sample</Link>
      </div> */}
    </div>
  )
}

export default Home
