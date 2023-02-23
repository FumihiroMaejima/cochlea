// TODO install @types/node error, but not resolved,
/* eslint-disable @typescript-eslint/no-var-requires */
const { performance } = require('perf_hooks')
// NOTICE
//Node.jsでPerformation API(performanceオブジェクト)を使用する為には pref_hookモジュールをインポートする必要がある。

/**
 * check method performance
 * @param {string} name
 * @param {never} callback
 * @return {number}
 */
/* eslint-disable-next-line */
export const checkPerformance = (name: string, callback: any): void => {
  const startTime = performance.now()
  callback
  const endTime = performance.now()

  console.log('performance %s: %d ms', name, `${endTime - startTime}`) // per ＊/ms
}

export type SelectBoxType = {
  text: string
  value: number
}

/**
 * sort numbers array order by asc
 * @param {number[]} nums
 * @return {number[]} result
 */
export const sortArrayNumbers = (nums: number[]): number[] => {
  let result: number[] = []
  result = nums.sort((current, next) => {
    if (current < next) return -1
    if (current > next) return 1
    return 0
  })

  return result
}

/**
 * sort numbers array order by desc
 * @param {number[]} nums
 * @return {number[]} result
 */
export const sortArrayNumbersDesc = (nums: number[]): number[] => {
  let result: number[] = []
  result = nums.sort((current, next) => {
    if (current > next) return -1
    if (current < next) return 1
    return 0
  })

  return result
}

/**
 * get selected item`s text.
 * @param {number[]} value
 * @param {SelectBoxType[]} items
 * @return {string[]}
 */
export const getMultiSelectLabel = (
  value: number[],
  items: SelectBoxType[]
): string[] => {
  return items
    .filter((item) => value.includes(item.value))
    .map((item) => item.text)
}

/**
 * make binary data object url.
 * [0xef, 0xbb, 0xbf]はbyte order mark(BOM)。Unicodeで符号化したテキストの先頭に付与される数バイトのデータ。
 * 8ビット符号なし整数値を表現可能なUint8Array()メソッドでバイナリデータとしてセットする事で文字コードをBOM付きのUTF-8と指定する。
 * @param {string} data
 * @param {string} mimeType - default 'text/csv'
 * @return {string}
 */
export const makeDataUrl = (data: BlobPart, mimeType = 'text/csv'): string => {
  if (!(mimeType === 'text/csv' || mimeType === 'application/csv')) {
    return (window.URL || window.webkitURL).createObjectURL(new Blob([data]))
  } else {
    const bom = new Uint8Array([0xef, 0xbb, 0xbf])
    // バイナリデータを表すBlobオブジェクトに設定したいデータとmimetypeを指定する
    const blob = new Blob([bom, data], { type: mimeType })
    return (window.URL || window.webkitURL).createObjectURL(blob)
  }
}

/**
 * download file.
 * @param {string} url
 * @param {string} name
 * @return {void}
 */
export const downloadFile = (url: string, name: string): void => {
  const link = document.createElement('a')
  link.download = name
  link.href = url
  document.body.appendChild(link)
  link.click()
  document.body.removeChild(link)
}

/**
 * get rounding random number value.(四捨五入)
 * @param {number} maxNumber
 * @return {number}
 */
export const getRoundingRandomInt = (maxNumber: number): number => {
  return Math.round(Math.random() * maxNumber)
}

/**
 * get floor random value.(切り捨て)
 * @param {number} maxNumber
 * @return {number}
 */
export const getFloorRandomInt = (maxNumber: number): number => {
  return Math.floor(Math.random() * maxNumber)
}

/**
 * ファイルの読み込みとデータをテキストとして取得
 * @param {File} file
 * @return {Promise<string | ArrayBuffer | null>}
 */
export const readFileDataAsText = async (
  file: File
): Promise<string | ArrayBuffer | null> => {
  return new Promise(
    (resolve: (param: string | ArrayBuffer | null) => void) => {
      const reader = new FileReader()
      // reader.onload = (e: ProgressEvent) => {
      reader.onload = () => {
        // 読み込んだ結果をresolve(解決)する
        resolve(reader.result)
      }
      // 読み込み
      reader.readAsText(file)
    }
  )
}

/**
 * ファイルの読み込みとデータをデータURLとして取得
 * @param {File} file
 * @return {Promise<string | ArrayBuffer | null>}
 */
export const readFileDataAsDataURL = async (
  file: File
): Promise<string | ArrayBuffer | null> => {
  return new Promise(
    (resolve: (param: string | ArrayBuffer | null) => void) => {
      const reader = new FileReader()
      // reader.onload = (e: ProgressEvent) => {
      reader.onload = () => {
        // 読み込んだ結果をresolve(解決)する
        resolve(reader.result)
      }
      // 読み込み
      reader.readAsDataURL(file)
    }
  )
}

/**
 * 日付の形式(yyyy/mm/dd)のチェック
 * @param {string} value
 * @return {boolean}
 */
export const checkDateFormat = (value: string): boolean => {
  return value.match(/^[0-9]{4}\/[0-9]{2}\/[0-9]{2}/u) !== null
}

/**
 * 日時の形式(yyyy/mm/dd hh:mm:ss)のチェック
 * @param {string} value
 * @return {boolean}
 */
export const checkDateTimeFormat = (value: string): boolean => {
  return (
    value.match(
      /^[0-9]{4}\/[0-9]{2}\/[0-9]{2}\s[0-9]{2}:[0-9]{2}:[0-9]{2}/u
    ) !== null
  )
}

/**
 * invalid type error class.
 * @param {Ref<boolean>} value
 * @return {void}
 */
export class InvalidStateErrorUtil extends Error {
  constructor(value: never, message?: string) {
    super(message)
  }
}
