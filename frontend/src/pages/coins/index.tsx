import React, { useEffect, useContext } from 'react'
import { PartsSimpleButton } from '@/components/parts/button/PartsSimpleButton'
import { PartsLabelHeading } from '@/components/parts/heading/PartsLabelHeading'
import { PartsSimpleHeading } from '@/components/parts/heading/PartsSimpleHeading'
import {
  PartsSimpleTable,
  TableHeaderType,
} from '@/components/parts/table/PartsSimpleTable'

import { useCoins, CoinType } from '@/hooks/modules/coins/useCoins'
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
  { label: 'detail' },
  { label: 'price' },
  { label: 'cost' },
  { label: 'start_at' },
  { label: 'end_at' },
  { label: 'image' },
  { label: 'created_at' },
  { label: 'updated_at' },
  { label: 'deleted_at' },
]

export const Coins: React.VFC = () => {
  const { navigationGuardHandler } = useNavigationGuard()
  const { coinsState, getCoinsRequest } = useCoins()
  const { updateGlobalLoading } = useContext(GlobalLoadingContext)
  const { getAuthId, getHeaderOptions } = useContext(AuthAppContext)

  // mount後に実行する処理
  const onDidMount = (): void => {
    const asyncInitPageHandler = async () => {
      // 認証情報のチェック
      await navigationGuardHandler()

      if (getAuthId() !== null) {
        updateGlobalLoading(true)
        /* await getAdminsRequest(getHeaderOptions()).then((res) => {
          // console.log('response: ' + JSON.stringify(res, null, 2))
          updateGlobalLoading(false)
        }) */
        await getCoinsRequest(getHeaderOptions()).then((res) => {
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
      <PartsSimpleHeading text="コイン一覧 ページ" color="dark-grey" />
      <div className="mx-2">
        <PartsLabelHeading text="コイン一覧" color="dark-grey" />
        <div className="mxy-2 util-color__text--dark-grey over-flow-auto">
          <PartsSimpleTable
            headers={simpleTableHeaderData}
            items={coinsState.coins}
          />
        </div>
      </div>
    </div>
  )
}

export default Coins
