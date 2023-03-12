import React, { MouseEventHandler, useState } from 'react'
import Link from 'next/link'
import { PartsSimpleButton } from '@/components/parts/button/PartsSimpleButton'
import { PartsLabelHeading } from '@/components/parts/heading/PartsLabelHeading'
import { PartsSimpleHeading } from '@/components/parts/heading/PartsSimpleHeading'
// import { PartsCircleLoading } from '@/components/parts/PartsCircleLoading'
import { PartsSimpleCarousel } from '@/components/parts/carousel/PartsSimpleCarousel'
import { PartsSimpleModal } from '@/components/parts/modal/PartsSimpleModal'

export const Test1: React.VFC = () => {
  const baseImageHost = 'https://picsum.photos/'

  const [isOpenModal, updateOpenModalFlad] = useState(false)

  /**
   * update modal open flag.
   * @return {void}
   */
  const changeOpenModalValueHandler = (): void => {
    updateOpenModalFlad(!isOpenModal)
  }

  /**
   * modal open button click event handler
   * @param {React.MouseEvent<HTMLButtonElement>} event
   * @return {MouseEventHandler<HTMLButtonElement>}
   */
  const openModalHandler: MouseEventHandler<HTMLButtonElement> = (
    // eslint-disable-next-line @typescript-eslint/no-unused-vars
    event: React.MouseEvent<HTMLButtonElement>
  ): void => {
    changeOpenModalValueHandler()
  }

  return (
    <div className="test1 page-container page-container__mx-auto">
      {/* <PartsCircleLoading /> */}
      <PartsSimpleModal
        isOpen={isOpenModal}
        hideOnClickOutSide={true}
        closeModalHandler={changeOpenModalValueHandler}
      />
      <PartsSimpleHeading text="サンプル 画像 ページ" color="dark-grey" />
      <div className="mx-2">
        <PartsLabelHeading text="サブヘッダー1" color="blue" />
        <div className="util-text__contents-area util-border-full-solid-2p__color--dark-grey util-border-radius__round--5p util-color__text--dark-grey">
          <p>test1</p>
          <p>test2</p>
          <p>test3</p>
        </div>

        <div className="util-text__contents-area util-border-full-solid-2p__color--dark-grey util-border-radius__round--5p util-color__text--dark-grey">
          <PartsSimpleButton
            text="modal"
            color="blue"
            onClick={openModalHandler}
          />
        </div>

        <PartsLabelHeading text="サンプル画像 200px * 200px" color="green" />
        <div className="util-text__contents-area util-border-full-solid-2p__color--dark-grey util-border-radius__round--5p util-color__text--dark-grey">
          {/* <img src={`${baseImageHost}id/290/200/200`} alt="sample image"></img> */}
          {/* <img src={`${baseImageHost}id/292/200/200`} alt="sample image"></img> */}
          {/* <img src={`${baseImageHost}id/295/200/200`} alt="sample image"></img> */}
          {/* <img src={`${baseImageHost}id/299/200/200`} alt="sample image"></img>
          <img src={`${baseImageHost}id/300/200/200`} alt="sample image"></img> */}
          <img src={`${baseImageHost}id/301/200/200`} alt="sample image"></img>
          <img src={`${baseImageHost}id/307/200/200`} alt="sample image"></img>
        </div>

        <div>
          <PartsSimpleCarousel />
        </div>
      </div>

      <div className="my-4">
        <div className="">
          <Link href={'/'}>Go To Home</Link>
        </div>
      </div>
    </div>
  )
}

export default Test1
