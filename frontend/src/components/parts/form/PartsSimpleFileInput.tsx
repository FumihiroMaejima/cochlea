// TODO create Event Handler
import React, {
  FormEventHandler,
  FocusEventHandler,
  useRef,
  useState,
  DragEvent,
  MouseEvent,
  MouseEventHandler,
} from 'react'
import {
  checkFileSize,
  checkFileType,
  checkFileLength,
} from '@/util/validation'
import { HTMLElementEvent } from '@/types/'

type Props = {
  value: undefined | File
  onUpdateFile: (v: File) => void
  onResetFile: () => void
  className?: string
  formLabel?: string
  accept?: string
  enablePreview?: boolean
  fileSize?: number
  fileLength?: number
  required?: boolean
  disabled?: boolean
  readOnly?: boolean
}

// acceptはWindowsの時は注意が必要
// for windows csv : application/octet-stream(Excel無し),application/vnd.ms-excel(Excel有り)

export const PartsSimpleFileInput: React.VFC<Props> = ({
  value = undefined,
  onUpdateFile = (v) => console.log(JSON.stringify(v)),
  onResetFile = () => console.log(''),
  className = undefined,
  formLabel = 'ファイルの選択',
  accept = 'image/png,image/jpeg,image/gif',
  enablePreview = false,
  fileSize = 1000000, // byte size
  fileLength = 1,
  required = undefined,
  disabled = undefined,
  readOnly = undefined,
}) => {
  const [imageData, setImageData] = useState<string | undefined>('')
  const [errorText, setTextValue] = useState('')
  const [isError, setIsError] = useState<boolean>(false)
  const [isDraged, setIsDraged] = useState<boolean>(false)
  const refElement = useRef<HTMLInputElement>(null) // reference to container

  // methods
  /**
   * chcek file validatiaon
   * @param {FileList} files
   * @return {void}
   */
  const checkFileValidation = (files: FileList): void => {
    if (!checkFileLength(files.length, fileLength)) {
      setIsError(true)
      setTextValue('invalid file length')
      return
    }

    // 下記の形で配列にも出来る
    // const fileList = Array.from(files)
    Object.keys(files).forEach((key: string) => {
      let accepts: undefined | string[]
      if (accept.includes(',')) {
        accepts = accept.split(',')
      }
      const file = files[parseInt(key)]
      if (!checkFileSize(file.size, fileSize)) {
        setIsError(true)
        setTextValue('invalid file size')
      } else if (!checkFileType(file.type, accepts ?? accept)) {
        setIsError(true)
        setTextValue('invalid file type')
      } else {
        setIsError(false)
        setTextValue('')
      }
    })
  }

  /**
   * create preview image
   * @param {File} file
   * @return {void}
   */
  const createImage = (file: File): void => {
    const reader = new FileReader()
    // reader.onload = (e: ProgressEvent) => {
    reader.onload = () => {
      // const target = e.target as FileReader
      // setImageData(e.target.result)
      // setImageData(reader.result)
      setImageData(reader.result?.toString)
    }
    reader.readAsDataURL(file)
  }

  /**
   * input event handler
   * @param {Event} event
   * @return {void}
   */
  const inputEventHandler = (
    event: HTMLElementEvent<HTMLInputElement>
  ): void => {
    const data = event.target.files ? event.target.files : undefined

    if (data) {
      checkFileValidation(data)

      if (!isError) {
        // update emit
        onUpdateFile(data[0])

        if (enablePreview) {
          createImage(data[0])
        }
      }
    }
  }

  /**
   * reset input file
   * @param {Event} event
   * @return {void}
   */
  const resetFile: MouseEventHandler<HTMLSpanElement> = (
    // eslint-disable-next-line @typescript-eslint/no-unused-vars
    _: MouseEvent<HTMLSpanElement>
  ): void => {
    // reset emit
    onResetFile()
    setIsError(false)
    setTextValue('')
  }

  /**
   * change file data by drag event
   * @param {DragEvent} event
   * @return {void}
   */
  const changeFileByDragEvent = (event: DragEvent): void => {
    if (event.dataTransfer?.files) {
      const files = event.dataTransfer?.files
      checkFileValidation(files)
      // const data = event.target.files ? event.target.files![0] : undefined

      if (!isError) {
        // update emit
        onUpdateFile(files[0])

        if (enablePreview) {
          createImage(files[0])
        }
      }
    }
  }

  /**
   * change draged status
   * @param {DragEvent} dragEvent
   * @param {boolean} value
   * @return {void}
   */
  const changeDragedState = (dragEvent: DragEvent, value = false): void => {
    // prevent設定
    const event = dragEvent as unknown as Event
    event.stopPropagation()
    event.preventDefault()
    setIsDraged(value)
  }

  /**
   * drop file handler
   * @param {DragEvent} event
   * @return {void}
   */
  const dropFile = (event: DragEvent): void => {
    changeFileByDragEvent(event as DragEvent)
    changeDragedState(event)
  }

  return (
    <div
      className={`parts-simple-file-input ${className ? ' ' + className : ''}${
        isDraged ? ' ' + 'parts-simple-file-input__drag_on' : ''
      }`}
      onDragOver={(e) => {
        changeDragedState(e, true)
      }}
      onDrop={dropFile}
      onDragLeave={(e) => {
        changeDragedState(e, false)
      }}
      onDragEnd={(e) => {
        changeDragedState(e, false)
      }}
    >
      <div
        className={`parts-simple-file-input__drop-area ${
          isDraged ? ' parts-simple-file-input__drag_on' : ''
        }`}
        onDragOver={(e) => {
          changeDragedState(e, true)
        }}
        onDrop={dropFile}
        onDragLeave={(e) => {
          changeDragedState(e, false)
        }}
        onDragEnd={(e) => {
          changeDragedState(e, false)
        }}
      >
        {value && enablePreview && (
          <div className="parts-simple-file-input__selected-image-file">
            <img
              src={imageData}
              width="150"
              // async
              alt=""
              loading="lazy"
            />
            <span
              className="parts-simple-file-input__reset-file-icon"
              onClick={resetFile}
            >
              ×
            </span>
          </div>
        )}
        {value && !enablePreview && (
          <div className="parts-simple-file-input__selected-file">
            <span className="parts-simple-file-input__file-name">
              <span>{value.name}</span>
              <span
                className="parts-simple-file-input__reset-file-icon"
                onClick={resetFile}
              >
                ×
              </span>
            </span>
          </div>
        )}
        {value === undefined && (
          <label>
            <span className="parts-simple-file-input__form-label">
              {formLabel}
            </span>
            <input
              className={`parts-simple-file-input ${
                className ? ' ' + className : ''
              }`}
              ref={refElement}
              type="file"
              accept={accept}
              onInput={(e) =>
                inputEventHandler(
                  e as unknown as HTMLElementEvent<HTMLInputElement>
                )
              }
              required={required}
              disabled={disabled}
              readOnly={readOnly}
            />
          </label>
        )}
      </div>
      {isError && (
        <p className="parts-simple-file-input__error-text">{errorText}</p>
      )}
    </div>
  )
}

export default PartsSimpleFileInput
