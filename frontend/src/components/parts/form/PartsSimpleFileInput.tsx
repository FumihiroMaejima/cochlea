// TODO create Event Handler
import React, {
  FormEventHandler,
  FocusEventHandler,
  useRef,
  useState,
} from 'react'
import {
  checkFileSize,
  checkFileType,
  checkFileLength,
} from '@/util/validation'
import { HTMLElementEvent } from '@/types/'

type Props = {
  value: undefined | File
  className?: string
  formLabel: string
  accept: string
  enablePreview: boolean
  fileSize: number
  fileLength: number

  id?: string
  onInput?: FormEventHandler<HTMLInputElement>
  onFocus?: FocusEventHandler<HTMLInputElement>
  onBlur?: FocusEventHandler<HTMLInputElement>
  type?: React.HTMLInputTypeAttribute
  placeholder?: string
  maxLength?: number
  required?: boolean
  disabled?: boolean
  readOnly?: boolean
}

// acceptはWindowsの時は注意が必要
// for windows csv : application/octet-stream(Excel無し),application/vnd.ms-excel(Excel有り)

export const PartsSimpleFileInput: React.VFC<Props> = ({
  value = undefined,
  className = undefined,
  formLabel = 'ファイルの選択',
  accept = 'image/png,image/jpeg,image/gif',
  enablePreview = false,
  fileSize = 1000000, // byte size
  fileLength = 1,

  id = undefined,
  onInput = undefined,
  onFocus = undefined,
  onBlur = undefined,
  type = 'text',
  placeholder = undefined,
  maxLength = undefined,
  required = undefined,
  disabled = false,
  readOnly = false,
}) => {
  // const [imageData, setImageData] = useState<string | ArrayBuffer | null>('')
  const [imageData, setImageData] = useState<string | undefined>('')
  const [errorText, setTextValue] = useState('')
  const [isError, setIsError] = useState<boolean>(false)
  const [isDraged, setIdDraged] = useState<boolean>(false)
  // const refElement = useRef<HTMLDivElement>(null) // reference to container
  const refElement = useRef<HTMLInputElement>(null) // reference to container

  /* const imageData = ref<string | ArrayBuffer | null>('')
  const fileRef = ref<HTMLInputElement>()
  const errorText = ref<string>('')
  const isError = ref<boolean>(false)
  const isDraged = ref<boolean>(false)

  // computed
  const inputValue = computed((): undefined | File => props.value)
  const imageDataValue = computed(
    (): string | ArrayBuffer | null => imageData.value
  )
  const errorTextValue = computed((): string => errorText.value)
  const isInputError = computed((): boolean => isError.value)
  const isDragedState = computed((): boolean => isDraged.value) */

  // methods
  /**
   * chcek file validatiaon
   * @param {FileList} files
   * @return {void}
   */
  const checkFileValidation = (files: FileList) => {
    if (!checkFileLength(files.length, fileLength)) {
      setIsError(true)
      setTextValue('invalid file length')
      return
    }

    Object.keys(files).forEach((key) => {
      const file = files[parseInt(key)]
      if (!checkFileSize(file.size, fileSize)) {
        setIsError(true)
        setTextValue('invalid file size')
      } else if (!checkFileType(file.type, accept)) {
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
  const createImage = (file: File) => {
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
    event: HTMLElementEvent<HTMLInputElement> | FormEvent<HTMLFormElement>
  ) => {
    const data = event.target.files ? event.target.files : undefined

    if (data) {
      checkFileValidation(data)

      if (!isError) {
        // TODO emit
        // context.emit('update:value', data[0])

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
  const resetFile = (event: Event) => {
    // context.emit('reset-file', event)
    // TODO emit
    setIsError(false)
    setTextValue('')
  }

  const changeFileDrag = (event: DragEvent) => {
    if (event.dataTransfer?.files) {
      const files = event.dataTransfer?.files
      checkFileValidation(files)
      // const data = event.target.files ? event.target.files![0] : undefined

      if (!isError) {
        // TODO emit
        // context.emit('update:value', files[0])

        if (enablePreview) {
          createImage(files[0])
        }
      }
    }
  }

  /**
   * draged status
   * @param {boolean} value
   * @return {void}
   */
  const changeDragedState = (value = false) => {
    setIdDraged(value)
  }

  /**
   * drop file handler
   * @param {DragEvent} event
   * @return {void}
   */
  const dropFile = (event: DragEvent) => {
    changeFileDrag(event as DragEvent)
    changeDragedState()
  }

  return (
    <div
      className={`parts-simple-file-input ${className ? ' ' + className : ''}${
        isDraged ? ' ' + 'app-simple-file-input__drag_on' : ''
      }`}
      onDragOver={() => {
        changeDragedState(true)
      }}
      onDrag={(e) => {
        // dropFile(e)
        dropFile()
      }}
      onDragLeave={() => {
        changeDragedState(false)
      }}
      onDragEnd={() => {
        changeDragedState(false)
      }}
      /* @dragover.prevent={changeDragedState(true)}
      @drop.prevent="dropFile"
      @dragleave.prevent="changeDragedState(false)"
      @dragend.prevent="changeDragedState(false)" */
    >
      <div
        className={`app-simple-file-input__drop-area ${
          isDraged ? ' app-simple-file-input__drag_on' : ''
        }`}
        onDragOver={() => {
          changeDragedState(true)
        }}
        onDrop={(e) => {
          // dropFile(e)
          dropFile
        }}
        onDragLeave={() => {
          changeDragedState(false)
        }}
        onDragEnd={() => {
          changeDragedState(false)
        }}
        /* @dragover.prevent="changeDragedState(true)"
        @drop.prevent="dropFile"
        @dragleave.prevent="changeDragedState(false)"
        @dragend.prevent="changeDragedState(false)" */
      >
        {value && enablePreview && (
          <div className="app-simple-file-input__selected-image-file">
            <img
              src={imageData}
              width="150"
              // async
              alt=""
              loading="lazy"
            />
            <span
              className="app-simple-file-input__reset-file-icon"
              onClick={(e) => {
                // resetFile(e)
                resetFile
              }}
            >
              ×
            </span>
          </div>
        )}
        {value && !enablePreview && (
          <div className="app-simple-file-input__selected-file">
            <span className="app-simple-file-input__file-name">
              <span>{value.name}</span>
              <span
                className="app-simple-file-input__reset-file-icon"
                onClick={(e) => {
                  // resetFile(e)
                  resetFile
                }}
              >
                ×
              </span>
            </span>
          </div>
        )}
        {!value && (
          <label>
            <span>{formLabel}</span>
            <input
              className={`parts-simple-file-input ${
                className ? ' ' + className : ''
              }`}
              ref={refElement}
              type="file"
              accept={accept}
              onInput={(e) => inputEventHandler(e)}
              // onInput={inputEventHandler}
              /* onInput={onInput}
              onFocus={onFocus}
              onBlur={onBlur}
              placeholder={placeholder}
              maxLength={maxLength}
              required={required}
              disabled={disabled}
              readOnly={readOnly} */
            />
          </label>
        )}
      </div>
      {isError && (
        <p className="app-simple-file-input__error-text">{errorText}</p>
      )}
    </div>
  )
}

export default PartsSimpleFileInput
