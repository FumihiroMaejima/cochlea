import React, { useState, useEffect, useContext } from 'react'
import { PartsSimpleButton } from '@/components/parts/button/PartsSimpleButton'
import { PartsSimpleTextField } from '@/components/parts/form/PartsSimpleTextField'
import { PartsLabelHeading } from '@/components/parts/heading/PartsLabelHeading'
import { PartsSimpleHeading } from '@/components/parts/heading/PartsSimpleHeading'
import {
  PartsSimpleTable,
  TableHeaderType,
  SimpleTableDataType,
} from '@/components/parts/table/PartsSimpleTable'

import { useDebugs, DebugType } from '@/hooks/modules/debugs/useDebugs'
import { GlobalLoadingContext } from '@/components/container/GlobalLoadingProviderContainer'
import { AuthAppContext } from '@/components/container/AuthAppProviderContainer'
import { useNavigationGuard } from '@/hooks/auth/useNavigationGuard'

export const Debug: React.VFC = () => {
  const { navigationGuardHandler } = useNavigationGuard()
  const { debugsState, getDebugStatusRequest, updateLocalFakerTime } =
    useDebugs()
  const { updateGlobalLoading } = useContext(GlobalLoadingContext)
  const { getAuthId, getHeaderOptions } = useContext(AuthAppContext)

  // mount後に実行する処理
  const onDidMount = (): void => {
    const asyncInitPageHandler = async () => {
      // 認証情報のチェック
      await navigationGuardHandler()

      if (getAuthId() !== null) {
        updateGlobalLoading(true)
        await getDebugStatusRequest(getHeaderOptions()).then((res) => {
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
      <PartsSimpleHeading text="デバッグ情報一覧 ページ" color="dark-grey" />
      <div className="mx-2">
        <PartsLabelHeading text="デバッグ情報" color="dark-grey" />
        <div className="my-2">
          <div className="util-text__contents-area util-border-full-solid-2p__color--dark-grey util-border-radius__round--5p util-color__text--dark-grey">
            <PartsSimpleButton text="blue" color="blue" />
            <PartsSimpleButton text="red" color="red" />
            <PartsSimpleButton text="green" color="green" />
            <p>
              <b>userId</b> : {debugsState.status.userId}
            </p>
            <p>
              <b>sessionId</b> : {debugsState.status.sessionId}
            </p>
            <p>
              <b>email</b> : {debugsState.status.email}
            </p>
            <p>
              <b>name</b> : {debugsState.status.name}
            </p>
            <p>
              <b>fakerTimeStamp</b> :{' '}
              {debugsState.status.fakerTimeStamp ?? '時間偽装設定無し'}
            </p>
            <p>
              <b>host</b> : {debugsState.status.host}
            </p>
            <p>
              <b>clinetIp</b> : {debugsState.status.clinetIp}
            </p>
            <p>
              <b>userAgent</b> : {debugsState.status.userAgent}
            </p>
          </div>
          <div className="my-4 d-flex flex-align-center">
            <label className="width-2 text-left" htmlFor="time">
              時間偽装時刻
            </label>
            <PartsSimpleTextField
              id="time"
              className="width-8"
              type="datetime-local"
              value={debugsState.fakerTime}
              onInput={(e) => updateLocalFakerTime(e.currentTarget.value)}
              placeholder="time"
            />
          </div>
        </div>
      </div>
    </div>
  )
}

export default Debug
