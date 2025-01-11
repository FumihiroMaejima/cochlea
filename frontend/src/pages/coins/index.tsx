import React, { useEffect, useContext, MouseEventHandler } from 'react'
import { PartsSimpleButton } from '@/components/parts/button/PartsSimpleButton'
import { PartsLabelHeading } from '@/components/parts/heading/PartsLabelHeading'
import { PartsSimpleHeading } from '@/components/parts/heading/PartsSimpleHeading'
import {
  PartsSimpleTable,
  TableHeaderType,
} from '@/components/parts/table/PartsSimpleTable'
import { PartsSimpleEditTable } from '@/components/parts/table/PartsSimpleEditTable'

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

export const Coins: React.FC = () => {
  const { navigationGuardHandler } = useNavigationGuard()
  const {
    coinsState,
    getCoinsRequest,
    getCoinsCsvFileRequest,
    getCoinTemplateRequest,
    updateCoinTextData,
    updateCoinRequest,
    deleteCoinRequest,
  } = useCoins()
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

  /**
   * edit data handler
   * @param {number} index
   * @param {Extract<keyof CoinType, 'name' | 'detail'>} key
   * @param {string} value
   * @return {void}
   */
  const editRecordHandler = (
    index: number,
    key: Extract<keyof CoinType, 'name' | 'detail'>,
    value: string,
  ): void => {
    // TODO remove
    // 追加になる。
    // setTodo([...todos, { ...todos[index], ...{ [key]: value } }])
    // setTodo([...todos, { ...todos[index], [key]: value }])
    updateCoinTextData(index, key, value)
  }

  /**
   * update request handler
   * @param {number} index
   * @return {Promise<void>}
   */
  const updateRecordRequestHandler = async (index: number): Promise<void> => {
    const coin = coinsState.coins[index]
    updateGlobalLoading(true)
    await updateCoinRequest(coin, getHeaderOptions()).then((res) => {
      updateGlobalLoading(false)
    })
  }

  /**
   * download csv file request handler
   * @return {Promise<void>}
   */
  // eslint-disable-next-line @typescript-eslint/no-unused-vars, prettier/prettier
  const onClickDownLoadCsvButtonHandler = async (): Promise<void> => {
    updateGlobalLoading(true)
    await getCoinsCsvFileRequest(getHeaderOptions()).then((res) => {
      updateGlobalLoading(false)
    })
  }

  /**
   * download templte file request handler
   * @return {Promise<void>}
   */
  // eslint-disable-next-line @typescript-eslint/no-unused-vars, prettier/prettier
  const onClickDownLoadTemplateButtonHandler = async (): Promise<void> => {
    updateGlobalLoading(true)
    await getCoinTemplateRequest(getHeaderOptions()).then((res) => {
      updateGlobalLoading(false)
    })
  }

  /**
   * delete request handler
   * @param {number} index
   * @return {Promise<void>}
   */
  const deleteRecordRequestHandler = async (index: number): Promise<void> => {
    const coin = coinsState.coins[index]
    updateGlobalLoading(true)
    await deleteCoinRequest([coin.id], getHeaderOptions()).then((res) => {
      updateGlobalLoading(false)
    })
  }

  return (
    <div className="admins page-container page-container__mx-auto">
      <PartsSimpleHeading text="コイン一覧 ページ" color="dark-grey" />
      <div className="mx-2">
        <PartsLabelHeading text="コイン一覧" color="dark-grey" />
        <div className="mxy-2 util-color__text--dark-grey">
          <PartsSimpleButton
            text="downlaod csv"
            color="black"
            onClick={onClickDownLoadCsvButtonHandler}
          />

          <PartsSimpleButton
            text="downlaod template"
            color="green"
            onClick={onClickDownLoadTemplateButtonHandler}
          />
        </div>
        <div className="mxy-2 util-color__text--dark-grey over-flow-auto">
          <PartsSimpleTable
            headers={simpleTableHeaderData}
            items={coinsState.coins}
          />
        </div>
      </div>

      <div className="mx-2">
        <PartsLabelHeading text="コイン一覧 編集可能" color="dark-grey" />
        <div className="mxy-2 util-color__text--dark-grey over-flow-auto">
          <PartsSimpleEditTable
            headers={simpleTableHeaderData}
            items={coinsState.coins}
            editable={true}
            editableKeys={['name', 'detail']}
            onInput={(index, key, value) => {
              /* console.log('form edit1 index:', index)
              console.log('form edit2 key:', key)
              console.log('form edit3 value:', value) */
              editRecordHandler(
                index,
                key as Extract<keyof CoinType, 'name' | 'detail'>,
                value as unknown as string,
              )
            }}
            onClickUpdate={updateRecordRequestHandler}
            onClickDelete={deleteRecordRequestHandler}
          />
        </div>
      </div>
    </div>
  )
}

export default Coins
