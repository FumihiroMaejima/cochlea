import axios, { AxiosResponse, AxiosError } from 'axios'
import { BaseAddHeaderResponse, AuthEndpoint } from '@/types'

export default class Authentication {
  options: AuthEndpoint

  constructor(options: AuthEndpoint) {
    this.options = options
  }

  async getUser(header: BaseAddHeaderResponse) {
    axios.defaults.withCredentials = true
    return await axios
      .post(this.options.authSelf, {}, { headers: header })
      .then((response: AxiosResponse<any>) => {
        return { data: response.data, status: response.status }
      })
      .catch((error: AxiosError<any>) => {
        // for check console.error('axios error' + JSON.stringify(error.message, null, 2))
        return {
          data: error,
          status: error.response ? error.response.status : 401,
        }
      })
  }

  async loginRequest(data: { email: string; password: string }) {
    axios.defaults.withCredentials = true
    return await axios
      .post(this.options.authLogin, data, {
        timeout: 5000,
        timeoutErrorMessage:
          "login request is time out. it's not sever response ",
      })
      .then((response: AxiosResponse<any>) => {
        return { data: response.data, status: response.status }
      })
      .catch((error: AxiosError<any>) => {
        // console.error('axios error' + JSON.stringify(error.message, null, 2))
        return {
          data: error,
          status: error.response ? error.response.status : 500,
        }
      })
  }

  async logoutRequest(header: BaseAddHeaderResponse) {
    axios.defaults.withCredentials = true
    return await axios
      .post(this.options.authLogout, {}, { headers: header })
      .then((response: AxiosResponse<any>) => {
        return { data: response.data, status: response.status }
      })
      .catch((error: AxiosError<any>) => {
        // for check console.error('axios error' + JSON.stringify(error.message, null, 2))
        return {
          data: error,
          status: error.response ? error.response.status : 500,
        }
      })
  }
}
