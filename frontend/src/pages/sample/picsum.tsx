import React from 'react'
import Link from 'next/link'
import { PartsLabelHeading } from '@/components/parts/heading/PartsLabelHeading'
import { PartsSimpleHeading } from '@/components/parts/heading/PartsSimpleHeading'

export const Picsum: React.VFC = () => {
  const baseImageHost = 'https://picsum.photos/'

  return (
    <div className="page-container page-container__mx-auto">
      <PartsSimpleHeading text="サンプル 画像 ページ" color="dark-grey" />
      <div className="mx-2">
        <PartsLabelHeading text="サブヘッダー1" color="blue" />
        <div className="util-text__contents-area util-border-full-solid-2p__color--dark-grey util-border-radius__round--5p util-color__text--dark-grey">
          <p>test1</p>
          <p>test2</p>
          <p>test3</p>
        </div>

        <PartsLabelHeading text="サンプル画像 200px * 200px" color="green" />
        <div className="util-text__contents-area util-border-full-solid-2p__color--dark-grey util-border-radius__round--5p util-color__text--dark-grey">
          <img src={`${baseImageHost}id/237/200/200`} alt="sample image"></img>
          <img src={`${baseImageHost}id/263/200/200`} alt="sample image"></img>
          <img src={`${baseImageHost}id/250/200/200`} alt="sample image"></img>
          <img src={`${baseImageHost}id/270/200/200`} alt="sample image"></img>
          <img src={`${baseImageHost}id/282/200/200`} alt="sample image"></img>
          <img src={`${baseImageHost}id/281/200/200`} alt="sample image"></img>
        </div>

        <PartsLabelHeading
          text="グレースケール画像 200px * 200px"
          color="green"
        />
        <div className="util-text__contents-area util-border-full-solid-2p__color--dark-grey util-border-radius__round--5p util-color__text--dark-grey">
          <img
            src={`${baseImageHost}id/237/200/200?grayscale`}
            alt="sample image"
          ></img>
        </div>

        <PartsLabelHeading text="ぼやけた画像 200px * 200px" color="green" />
        <div className="util-text__contents-area util-border-full-solid-2p__color--dark-grey util-border-radius__round--5p util-color__text--dark-grey">
          <img
            src={`${baseImageHost}id/237/200/200?blur`}
            alt="sample image"
          ></img>
        </div>

        <PartsLabelHeading text="ランダム画像 200px * 200px" color="green" />
        <div className="util-text__contents-area util-border-full-solid-2p__color--dark-grey util-border-radius__round--5p util-color__text--dark-grey">
          <img src={`${baseImageHost}200/200?random`} alt="sample image"></img>
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

export default Picsum
