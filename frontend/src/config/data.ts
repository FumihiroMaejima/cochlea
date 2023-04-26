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
    admins: {
      admins: '/api/v1/admin/admins',
      csv: '/api/v1/admin/admins/csv',
      admin: '/api/v1/admin/admins/admin/:id',
      create: '/api/v1/admin/admins/admin',
      roles: '/api/v1/admin/roles/list',
    },
    banners: {
      banners: '/api/v1/admin/banners',
      csv: '/api/v1/admin/banners/csv',
      banner: '/api/v1/admin/banners/banner/:id',
      image: '/api/v1/admin/banners/banner/image/:uuid',
      create: '/api/v1/admin/banners/banner',
      delete: '/api/v1/admin/banners/banner',
      fileTemplate: '/api/v1/admin/banners/file/template',
    },
    coins: {
      coins: '/api/v1/admin/coins',
      csv: '/api/v1/admin/coins/csv',
      coin: '/api/v1/admin/coins/coin/:id',
      create: '/api/v1/admin/coins/coin',
      delete: '/api/v1/admin/coins/coin',
      fileTemplate: '/api/v1/admin/coins/file/template',
    },
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
    debugs: {
      status: '/api/v1/admin/debug/status',
      encrypt: '/api/v1/debug/email/encrypt',
      decrypt: '/api/v1/debug/email/decrypt',
      timestamp: '/api/v1/debug/datetimes/timestamp',
      datetime: '/api/v1/debug/datetimes/datetime',
      log: '/api/v1/debug/logs/dateLog',
    },
  },
}

export default appConfig
