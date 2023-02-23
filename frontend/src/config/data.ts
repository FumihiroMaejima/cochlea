/* module.exports = {
  headerName: 'Game',
  headerContents: ['about', 'start game', 'contact'],
  noticeData: [
    { title: '「event」ページを更新しました。', date: '2020/09/24 10:00' },
    { title: '「event」ページを作成しました。', date: '2020/09/23 10:00' },
    { title: 'ポータルサイトを作成しました。', date: '2020/09/22 10:00' },
  ],
  authEndpoint: {
    authLogin: '/api/v1/admin/auth/login',
    authLogout: '/api/v1/admin/auth/logout',
    authSelf: '/api/v1/admin/auth/self',
  },
} */

import { IAppConfig } from '@/types'

export const appConfig: IAppConfig = {
  headerName: 'Game',
  headerContents: ['about', 'start game', 'contact'],
  noticeData: [
    { title: '「event」ページを更新しました。', date: '2020/09/24 10:00' },
    { title: '「event」ページを作成しました。', date: '2020/09/23 10:00' },
    { title: 'ポータルサイトを作成しました。', date: '2020/09/22 10:00' },
  ],
  authEndpoint: {
    authLogin: '/api/v1/admin/auth/login',
    authLogout: '/api/v1/admin/auth/logout',
    authSelf: '/api/v1/admin/auth/self',
  },
  endpoint: {
    members: {
      members: '/api/v1/admin/members',
      csv: '/api/v1/admin/members/csv',
      member: '/api/v1/admin/members/member/:id',
      create: '/api/v1/admin/members/member',
      roles: '/api/v1/admin/roles/list',
    },
    roles: {
      roles: '/api/v1/admin/roles',
      csv: '/api/v1/admin/roles/csv',
      role: '/api/v1/admin/roles/role/:id',
      create: '/api/v1/admin/roles/role',
      delete: '/api/v1/admin/roles/role',
      permissions: '/api/v1/admin/permissions/list',
    },
  },
}

export default appConfig
