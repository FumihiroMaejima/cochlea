/* eslint-disable @typescript-eslint/no-empty-interface */

export type ToastType = {
  add(args: ToastData): void
}

export type ToastData = {
  severity?: string
  summary?: string
  detail?: string
  life?: number
  closable?: boolean
  group?: string
}

export type SelectBoxType = {
  text: string
  value: number
}

export type ServerHeadersType = Record<string, string>

export type ServerRequestType<T = any> = {
  data: string | T | ServerErrorResponseType | unknown
  status: number
}

export type ServerRequestResponseType<T = any> = {
  data: T | ServerErrorResponseType | unknown
  status: number
  headers: ServerHeadersType
}

export type ServerErrorResponseType = {
  status: number
  errors: string[]
  message: string
}
