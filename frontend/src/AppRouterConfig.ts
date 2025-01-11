import { JSX } from 'react'
export type AppRouteType = {
  title: string
  shortTitle: string
  path: string
  element?: JSX.Element
  requiredAuth?: boolean
  permissions?: string[]
}

const adminRoutes: AppRouteType[] = [
  {
    title: 'ホーム | 管理システム',
    shortTitle: 'ホーム',
    path: '/',
    // element: <Home />,
    requiredAuth: true,
    permissions: ['master', 'administrator'],
  },
  {
    title: '管理者 | 管理システム',
    shortTitle: '管理者',
    path: '/admins',
    // element: <Members />,
    requiredAuth: true,
    permissions: ['master', 'administrator'],
  },
  {
    title: 'コイン | 管理システム',
    shortTitle: 'コイン',
    path: '/coins',
    // element: <Members />,
    requiredAuth: true,
    permissions: ['master', 'administrator'],
  },
  {
    title: 'バナー | 管理システム',
    shortTitle: 'バナー',
    path: '/banners',
    // element: <Members />,
    requiredAuth: true,
    permissions: ['master', 'administrator'],
  },
  {
    title: 'デバッグページ | 管理システム',
    shortTitle: 'デバッグ',
    path: '/debug',
    // element: <Sample />,
    requiredAuth: true,
    permissions: ['master', 'administrator'],
  },
  {
    title: 'サンプル | 管理システム',
    shortTitle: 'サンプル',
    path: '/sample',
    // element: <Sample />,
    requiredAuth: true,
    permissions: ['master', 'administrator'],
  },
  {
    title: 'サンプル画像 | 管理システム',
    shortTitle: 'サンプル画像',
    path: '/sample/picsum',
    // element: <Sample />,
    requiredAuth: true,
    permissions: ['master', 'administrator'],
  },
  {
    title: 'サンプルテスト | 管理システム',
    shortTitle: 'サンプルテスト',
    path: '/sample/test1',
    // element: <Sample />,
    requiredAuth: true,
    permissions: ['master', 'administrator'],
  },
]

const normalRoutes: AppRouteType[] = [
  {
    title: 'ログイン | 管理システム',
    shortTitle: 'ログイン',
    path: '/login',
    // element: <Login />,
    requiredAuth: false,
  },
]

// 開発時専用ページ
// const devlopOnlyRoutes: AppRouteType[] = []

export const routes: AppRouteType[] = adminRoutes.concat(normalRoutes)
