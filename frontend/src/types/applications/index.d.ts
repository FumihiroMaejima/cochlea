/* eslint-disable @typescript-eslint/no-empty-interface */
import { AxiosResponse, AxiosError } from 'axios'

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

export type ServerRequestType<T = any> = {
  data:
    | string
    | T
    | AxiosResponse<T>
    | ServerErrorResponseType
    | AxiosError<ServerErrorResponseType>
  status: number
}

export type ServerRequestResponseType<T = any> = {
  data: T | AxiosError<ServerErrorResponseType>
  status: number
}

export type ServerErrorResponseType = {
  status: number
  errors: string[]
  message: string
}
