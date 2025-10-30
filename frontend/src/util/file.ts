/**
 * get file object by file url (include server)
 * @param {string} url
 * @param {string} fileName
 * @return Promise<File>
 */
export const getFileObjectByUrl = async (
  url: string,
  fileName = 'image.png',
): Promise<File> => {
  return await fetch(url)
    .then((response: Response) => response.blob())
    .then((blob: Blob) => new File([blob], fileName))
    .then((file: File) => {
      // Fileオブジェクト
      return file
    })
}

/**
 * get file object by file url (include server)
 * @param {File} file file object
 * @param {string} key form key name
 * @return FormData
 */
export const createFileRequestFormData = (
  file: File,
  key = 'file',
): FormData => {
  const data = new FormData()
  data.append(key, file)
  return data
}

/**
 * 画像の縦横サイズの取得(データURL版)
 * @param {string} dataUrl base64などのDataURL形式の画像データ
 * @return {Promise<number[]>}
 */
export const getImageWidthAndHeightByDataUrl = async (dataUrl: string): Promise<number[]> => {
  const image = new Image()
  return new Promise(
    (resolve: (param: number[]) => void, reject: (reason: unknown) => void) => {
      image.onload = () => {
        const width = image.naturalWidth
        const height = image.naturalHeight
        // 読み込んだ結果をresolve(解決)する
        resolve([width, height])
      }

      image.onerror = (error) => {
        // throw new Error('get width, height error: ' + (typeof error === 'string') ? error : '')
        reject(error)
      }

      // 読み込み
      image.src = dataUrl
    },
  )
}

/**
 * base64エンコードデータのサイズ取得
 * @param {string} base64Data base64エンコードデータ
 * @return {Promise<number[]>}
 */
export const getBase64FileSize = (base64Data: string): number => {
  // データURLの「,」以降を取得(「data:image/png;base64,」などのprefix部分を除去)
  const cleaned = base64Data.split(',').pop() ?? ''
  // パディングの数を取得(末尾の「=」の数)
  const padding = (cleaned.match(/=+$/)?.[0].length ?? 0)
  // サイズ計算(元データのバイト数を計算。base64は元データの3バイト4文字にエンコードするため、その比率で計算)
  return Math.floor((cleaned.length * 3) / 4) - padding
}
