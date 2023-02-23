import { AxiosError } from 'axios'
import { AuthEndpoint } from '@/types/config'

export interface IAuthentication {
  [key: string]: AuthEndpoint
  options: AuthEndpoint
}

export type GetUserFunctionResponse = {
  [key: string]: any | AxiosError | number
  data: any | AxiosError
  string: number
}
