import React, { useState, useEffect, useContext } from 'react'
// import { Link, useOutletContext } from 'react-router-dom'
import Link from 'next/link'
import { PartsLabelTable } from '@/components/parts/table/PartsLabelTable'
import { PartsSimpleBox } from '@/components/parts/box/PartsSimpleBox'
import { PartsTitleBox } from '@/components/parts/box/PartsTitleBox'
import { PartsSimpleChip } from '@/components/parts/chip/PartsSimpleChip'
import { PartsCircleButton } from '@/components/parts/button/PartsCircleButton'
import { PartsSimpleButton } from '@/components/parts/button/PartsSimpleButton'
import { PartsSimpleFlatButton } from '@/components/parts/button/PartsSimpleFlatButton'
import { PartsSimpleAutoComplete } from '@/components/parts/form/PartsSimpleAutoComplete'
import { PartsSimpleSelectBox } from '@/components/parts/form/PartsSimpleSelectBox'
import { PartsSimpleTextField } from '@/components/parts/form/PartsSimpleTextField'
import { PartsLabelHeading } from '@/components/parts/heading/PartsLabelHeading'
import { PartsSimpleHeading } from '@/components/parts/heading/PartsSimpleHeading'
import { PartsSimpleList } from '@/components/parts/list/PartsSimpleList'
import { PartsStickyNoteList } from '@/components/parts/list/PartsStickyNoteList'
import { PartsSimpleToast } from '@/components/parts/toast/PartsSimpleToast'
import {
  PartsSimpleTable,
  TableHeaderType,
  SimpleTableDataType,
} from '@/components/parts/table/PartsSimpleTable'

import { useNavigationGuard } from '@/hooks/auth/useNavigationGuard'
import { AuthAppContext } from '@/components/container/AuthAppProviderContainer'

import { TableContentsType } from '@/types'

const tableData: TableContentsType[] = [
  { label: '会社名', value: 'テスト株式会社' },
  { label: '創業', value: '1950年' },
  { label: '従業員数', value: '600人' },
  { label: '電話番号', value: '123-456-7890' },
]

const simpleTableHeaderData: TableHeaderType[] = [
  { label: 'label1' },
  { label: 'label2' },
  { label: 'label3' },
]
const simpleTableData: SimpleTableDataType[] = [
  { label1: 'v1', label2: 'v2', label3: 'v3' },
  { label1: 'v4', label2: 'v5', label3: 'v6' },
  { label1: 'v7', label2: 'v8', label3: 'v9' },
]

const selectBoxItems = [
  { text: 'test1', value: 1 },
  { text: 'test2', value: 2 },
  { text: 'test3', value: 3 },
  { text: 'test4', value: 4 },
  { text: 'test5', value: 5 },
  { text: 'test6', value: 6 },
  { text: 'test7', value: 7 },
  { text: 'test8', value: 8 },
  { text: 'test9', value: 9 },
  { text: 'test10', value: 10 },
  { text: 'test11', value: 11 },
  { text: 'test12', value: 12 },
  { text: 'test13', value: 13 },
  { text: 'test14', value: 14 },
  { text: 'test15', value: 15 },
]

export const Sample: React.VFC = () => {
  /* const { navigationGuardHandler } =
    useOutletContext<GlobalNavigationGuardHandlerType>() */
  const [toastValue, setToastValue] = useState<boolean>(false)
  const [textValue, setTextValue] = useState('')
  const [selectValue, setSelectValue] = useState<undefined | number>(undefined)
  const [selectMultiValue, setSelectMultiValue] = useState<number[]>([])
  // const [multiSelectValue, setMultiSelectValue] = useState<number[]>([])
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

  // TSX内でジェネリクスを使う場合の書き方。
  // アロー関数は1つしか付けられない。
  /* const genFunc = function <T>(x: T): T {
    return x
  }

  const genFunc2 = <T, _>(x: T): T => {
    return x
  } */

  const setAuctoCompleteSelect = function <T = string>(x: T): void {
    if (typeof x === 'string') {
      setSelectValue(parseInt(x))
    } else {
      throw Error('invalid type')
    }
  }

  const setMultiAuctoCompleteSelect = function <T = string>(x: T): void {
    if (typeof x === 'string') {
      // setSelectMultiValue([...selectMultiValue, parseInt(x)])
      // 重複を削除しつつ配列の値を更新する。
      setSelectMultiValue(
        [...selectMultiValue, parseInt(x)].filter(
          (v, i, self) => self.indexOf(v) === i
        )
      )
    } else {
      throw Error('invalid type')
    }
  }

  const removeMultiAuctoCompleteSelect = function <T = string>(x: T): void {
    if (typeof x === 'string') {
      setSelectMultiValue([
        ...selectMultiValue.filter((v) => v !== parseInt(x)),
      ])
    } else {
      throw Error('invalid type')
    }
  }

  return (
    <div className="sample page-container page-container__mx-auto">
      <PartsSimpleToast
        value={toastValue}
        data={{ message: 'test message', status: 'success' }}
        onAnimationEnd={() => {
          setToastValue(false)
        }}
      />
      <PartsSimpleHeading
        text="simple heading"
        color="dark-grey"
        isDashed={false}
        isDouble={false}
      />

      <div className="mxy-2">
        <p className="util-color__text--black">test util basic color black</p>
        <p className="util-color__text--dark-grey">
          test util basic color dark-grey
        </p>
        <p className="util-color__text--red">test util basic color red</p>
        <p className="util-color__text--blue">test util basic color blue</p>
        <p className="util-color__text--green">test util basic color green</p>
        <p className="util-color__text--white">test util basic color white</p>
      </div>

      <div className="mxy-2">
        <button
          onClick={() => {
            setToastValue(true)
          }}
        >
          open toast
        </button>
      </div>

      <div className="mxy-2">
        <PartsSimpleBox text="simple box" isDashed={false} />
      </div>

      <div className="mxy-2">
        <PartsSimpleBox text="simple box" textColor="red" isDashed={false} />
      </div>

      <div className="mxy-2">
        <PartsTitleBox text="title box" isDashed={false} />
      </div>

      <div className="mxy-2">
        <PartsTitleBox
          text="title box"
          color="green"
          textColor="blue"
          isDashed={true}
        />
      </div>

      <div className="mxy-2">
        <PartsSimpleFlatButton />
        <PartsSimpleFlatButton color="blue" />
        <PartsSimpleFlatButton color="green" />
        <PartsSimpleFlatButton color="red" />
      </div>

      <div className="mxy-2">
        <PartsSimpleButton text="button text" />
        <PartsSimpleButton text="black" color="black" />
        <PartsSimpleButton text="blue" color="blue" />
        <PartsSimpleButton text="red" color="red" />
        <PartsSimpleButton text="green" color="green" />
        <PartsSimpleButton text="white" color="white" />
      </div>

      <div className="mxy-2">
        <PartsCircleButton text="text" />
        <PartsCircleButton text="black" color="black" />
        <PartsCircleButton text="blue" color="blue" />
        <PartsCircleButton text="red" color="red" />
        <PartsCircleButton text="green" color="green" />
        <PartsCircleButton text="white" color="white" />
      </div>

      <div className="mxy-2">
        {/* <PartsSimpleTextField value={textValue} onInput={(e) => console.log('input: ' + e.currentTarget.value)} /> */}
        <PartsSimpleTextField
          value={textValue}
          onInput={(e) => setTextValue(e.currentTarget.value)}
        />

        <div className="mxy-2">
          <PartsSimpleTextField
            value={textValue}
            onInput={(e) => setTextValue(e.currentTarget.value)}
            placeholder="test placeholder"
          />
        </div>

        <div className="mxy-2">
          <PartsSimpleTextField
            value={textValue}
            onInput={(e) => setTextValue(e.currentTarget.value)}
            placeholder="max length 10 strings"
            maxLength={10}
          />
        </div>

        <div className="mxy-2">
          <PartsSimpleTextField
            value={textValue}
            onInput={(e) => setTextValue(e.currentTarget.value)}
            placeholder="test required"
            required={true}
          />
        </div>

        <div className="mxy-2">
          <PartsSimpleTextField
            value={textValue}
            onInput={(e) => setTextValue(e.currentTarget.value)}
            placeholder="test readOnly"
            readOnly={true}
          />
        </div>

        <div className="mxy-2">
          <PartsSimpleTextField
            value={textValue}
            onInput={(e) => setTextValue(e.currentTarget.value)}
            placeholder="test disabled"
            disabled={true}
          />
        </div>
      </div>

      <div className="mxy-2">
        <div className="mxy-2">
          <PartsSimpleSelectBox
            value={selectValue}
            onChange={(e) => setSelectValue(parseInt(e.target.value))}
            items={selectBoxItems}
            placeholder="test select box"
            disabled={false}
          />
        </div>
        <div className="mxy-2"></div>
      </div>

      <div className="mxy-2">
        <div className="mxy-2">
          <PartsSimpleAutoComplete
            value={selectValue}
            // onChange={(e) => setSelectValue(parseInt(e.target.value))}
            onClickOtion={setAuctoCompleteSelect}
            items={selectBoxItems}
            onClickClose={() => {
              setSelectValue(undefined)
            }}
            placeholder="test auto complete box"
          />
        </div>
      </div>

      <div className="mxy-2">
        <div className="mxy-2">
          <PartsSimpleAutoComplete
            value={selectMultiValue.map((i) => String(i))}
            onClickOtion={setMultiAuctoCompleteSelect}
            items={selectBoxItems}
            onClickClose={(e) => {
              removeMultiAuctoCompleteSelect(e.currentTarget.value)
            }}
            placeholder="test multi select auto complete box"
          />
        </div>
      </div>

      <div className="mxy-2">
        <PartsSimpleChip label="Hello World" />
        <PartsSimpleChip label="Hello World Close Test" isClose={true} />
      </div>

      <div className="mxy-2">
        <PartsLabelHeading text="text" />
      </div>
      <PartsLabelHeading text="grey" />
      <PartsLabelHeading text="blue" color="blue" />
      <PartsLabelHeading text="red" color="red" />
      <PartsLabelHeading text="green" color="green" />
      <PartsLabelHeading text="white" color="white" />

      <div className="mxy-2">
        <PartsSimpleList items={[1, 2, 3, 4, 5]} />
        <PartsSimpleList items={[1, 2, 3, 4, 5]} color="blue" />
        <PartsSimpleList items={[1, 2, 3, 4, 5]} color="red" />
        <PartsSimpleList items={[1, 2, 3, 4, 5]} color="green" />
      </div>

      <div className="mxy-2">
        <PartsStickyNoteList items={[1, 2, 3, 4, 5]} />
      </div>

      <div className="mxy-2">
        <PartsLabelTable items={tableData} />
      </div>

      <div className="mxy-2">
        <PartsSimpleTable
          headers={simpleTableHeaderData}
          items={simpleTableData}
        />
      </div>

      <div className="my-4">
        <div className="">
          <Link href={`/about`}>Go To About</Link>
        </div>
        <div className="">
          <Link href={`/sample`}>Go To Sample</Link>
        </div>
        <div className="">
          <Link href={`/test1`}>Go To Test1</Link>
        </div>
        <div className="">
          <Link href={`/404`}>Go To 404</Link>
        </div>
      </div>
    </div>
  )
}

export default Sample
