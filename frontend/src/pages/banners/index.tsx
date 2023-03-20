import React, { useEffect, useContext, MouseEventHandler } from 'react'
import { PartsSimpleButton } from '@/components/parts/button/PartsSimpleButton'
import { PartsLabelHeading } from '@/components/parts/heading/PartsLabelHeading'
import { PartsSimpleHeading } from '@/components/parts/heading/PartsSimpleHeading'
import {
  PartsSimpleTable,
  TableHeaderType,
} from '@/components/parts/table/PartsSimpleTable'
import { PartsSimpleEditTable } from '@/components/parts/table/PartsSimpleEditTable'

import { useBanners, BannerType } from '@/hooks/modules/banners/useBanners'
import { GlobalLoadingContext } from '@/components/container/GlobalLoadingProviderContainer'
import { AuthAppContext } from '@/components/container/AuthAppProviderContainer'
import { useNavigationGuard } from '@/hooks/auth/useNavigationGuard'

const simpleTableHeaderData: TableHeaderType[] = [
  { label: 'id' },
  { label: 'uuid' },
  { label: 'name' },
  { label: 'detail' },
  { label: 'location' },
  { label: 'pc_height' },
  { label: 'pc_width' },
  { label: 'sp_height' },
  { label: 'sp_width' },
  { label: 'start_at' },
  { label: 'end_at' },
  { label: 'url' },
  { label: 'image' },
  { label: 'created_at' },
  { label: 'updated_at' },
]

export const Banners: React.VFC = () => {
  const { navigationGuardHandler } = useNavigationGuard()
  const {
    bannersState,
    getBannersRequest,
    getBannersCsvFileRequest,
    getBannerTemplateRequest,
    updateBannerTextData,
    updateBannerFileObject,
    updateBannerRequest,
    deleteBannerRequest,
  } = useBanners()
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
          updateGlobalLoading(false)
        }) */
        await getBannersRequest(getHeaderOptions()).then((res) => {
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
   * @param {Extract<keyof BannerType, 'name' | 'detail' | 'url'>} key
   * @param {string} value
   * @return {void}
   */
  const editRecordHandler = (
    index: number,
    key: Extract<keyof BannerType, 'name' | 'detail' | 'url'>,
    value: string
  ): void => {
    // TODO remove
    // 追加になる。
    // setTodo([...todos, { ...todos[index], ...{ [key]: value } }])
    // setTodo([...todos, { ...todos[index], [key]: value }])
    updateBannerTextData(index, key, value)
  }

  /**
   * update request handler
   * @param {number} index
   * @return {Promise<void>}
   */
  const updateRecordRequestHandler = async (index: number): Promise<void> => {
    const banner = bannersState.banners[index]
    updateGlobalLoading(true)
    await updateBannerRequest(banner, getHeaderOptions()).then((res) => {
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
    await getBannersCsvFileRequest(getHeaderOptions()).then((res) => {
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
    await getBannerTemplateRequest(getHeaderOptions()).then((res) => {
      updateGlobalLoading(false)
    })
  }

  /**
   * delete request handler
   * @param {number} index
   * @return {Promise<void>}
   */
  const deleteRecordRequestHandler = async (index: number): Promise<void> => {
    const banner = bannersState.banners[index]
    updateGlobalLoading(true)
    await deleteBannerRequest([banner.id], getHeaderOptions()).then((res) => {
      updateGlobalLoading(false)
    })
  }

  /**
   * update file each object for preview
   * @param {number} index
   * @param {File} file
   * @return {Promise<void>}
   */
  const updateImageHandler = async (
    index: number,
    file: File
  ): Promise<void> => {
    updateBannerFileObject(index, file)
  }

  return (
    <div className="admins page-container page-container__mx-auto">
      <PartsSimpleHeading text="バナー一覧 ページ" color="dark-grey" />
      <div className="mx-2">
        <PartsLabelHeading text="バナー一覧" color="dark-grey" />
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
            items={bannersState.banners}
          />
        </div>
      </div>

      <div className="mx-2">
        <PartsLabelHeading text="バナー一覧 編集可能" color="dark-grey" />
        <div className="mxy-2 util-color__text--dark-grey over-flow-auto">
          <PartsSimpleEditTable
            headers={simpleTableHeaderData}
            items={bannersState.banners}
            fileObjects={bannersState.images}
            editable={true}
            editableKeys={['name', 'detail', 'url']}
            onInput={(index, key, value) => {
              /* console.log('form edit1 index:', index)
              console.log('form edit2 key:', key)
              console.log('form edit3 value:', value) */
              editRecordHandler(
                index,
                key as Extract<keyof BannerType, 'name' | 'detail' | 'url'>,
                value as unknown as string
              )
            }}
            onClickUpdate={updateRecordRequestHandler}
            onClickDelete={deleteRecordRequestHandler}
            updateImageHandler={updateImageHandler}
          />
        </div>
      </div>
    </div>
  )
}

export default Banners
