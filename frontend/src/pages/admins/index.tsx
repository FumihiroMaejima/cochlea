import React, { useEffect, useContext } from 'react'
import { PartsSimpleButton } from '@/components/parts/button/PartsSimpleButton'
import { PartsLabelHeading } from '@/components/parts/heading/PartsLabelHeading'
import { PartsSimpleHeading } from '@/components/parts/heading/PartsSimpleHeading'
import {
  PartsSimpleTable,
  TableHeaderType,
  SimpleTableDataType,
} from '@/components/parts/table/PartsSimpleTable'

import { useAdmins, AdminType } from '@/hooks/modules/admins/useAdmins'
import { GlobalLoadingContext } from '@/components/container/GlobalLoadingProviderContainer'
import { AuthAppContext } from '@/components/container/AuthAppProviderContainer'
import { useNavigationGuard } from '@/hooks/auth/useNavigationGuard'

/* const simpleTableHeaderData: TableHeaderType[] = [
  { label: 'label1' },
  { label: 'label2' },
  { label: 'label3' },
] */
const simpleTableHeaderData: TableHeaderType[] = [
  { label: 'id' },
  { label: 'name' },
  { label: 'email' },
  { label: 'roleId' },
]
const simpleTableData: SimpleTableDataType[] = [
  { label1: 'v1', label2: 'v2', label3: 'v3' },
  { label1: 'v4', label2: 'v5', label3: 'v6' },
  { label1: 'v7', label2: 'v8', label3: 'v9' },
]

export const Admins: React.VFC = () => {
  const { navigationGuardHandler } = useNavigationGuard()
  const { adminsState, getAdminsRequest } = useAdmins()
  const { updateGlobalLoading } = useContext(GlobalLoadingContext)
  const { getAuthId, getHeaderOptions } = useContext(AuthAppContext)

  // mount後に実行する処理
  const onDidMount = (): void => {
    const asyncInitPageHandler = async () => {
      // 認証情報のチェック
      await navigationGuardHandler()

      if (getAuthId() !== null) {
        updateGlobalLoading(true)
        await getAdminsRequest(getHeaderOptions()).then((res) => {
          // console.log('response: ' + JSON.stringify(res, null, 2))
          updateGlobalLoading(false)
        })
      }
    }
    asyncInitPageHandler()
  }
  useEffect(onDidMount, [])

  return (
    <div className="admins page-container page-container__mx-auto">
      <PartsSimpleHeading text="管理者一覧 ページ" color="dark-grey" />
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

        <PartsLabelHeading text="管理者一覧" color="dark-grey" />
        <div className="mxy-2">
          <PartsSimpleTable
            headers={simpleTableHeaderData}
            items={adminsState.admins}
          />
        </div>
      </div>
    </div>
  )
}

export default Admins
