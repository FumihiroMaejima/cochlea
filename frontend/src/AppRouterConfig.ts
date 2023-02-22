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
    title: 'メンバー | 管理システム',
    shortTitle: 'メンバー',
    path: '/members',
    // element: <Members />,
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
