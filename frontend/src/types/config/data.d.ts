export type IAppConfig = {
  headerName: string
  headerContents: string[]
  noticeData: NoticeData[]
  // aboutMessage: AboutMessageType
  authEndpoint: AuthEndpoint
  endpoint: EndpointType
}

export type NoticeData = {
  [key: string]: string
  title: string
  date: string
}

export type AboutMessageType = {
  main: string
  author: string
  contact: string
}

export type AuthEndpoint = {
  [key: string]: string
  authLogin: string
  authLogout: string
  authSelf: string
}

export type EndpointType = {
  // authinfo: AuthInfoServiceEndipont
  admins: AdminsServiceEndipont
  members: MembersServiceEndipont
  roles: RolesServiceEndipont
  // game: GameTotalEndipont
}

export type AdminsServiceEndipont = {
  admins: string
  csv: string
  admin: string
  create: string
  roles: string
}

export type MembersServiceEndipont = {
  members: string
  csv: string
  member: string
  create: string
  roles: string
}

export type RolesServiceEndipont = {
  roles: string
  csv: string
  role: string
  create: string
  delete: string
  permissions: string
}