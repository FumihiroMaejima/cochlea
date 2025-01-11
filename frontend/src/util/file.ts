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
