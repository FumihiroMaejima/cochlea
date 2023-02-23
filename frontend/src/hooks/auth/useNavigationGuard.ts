import { useContext } from 'react'
import { useRouter } from 'next/router'

// global context
import { AuthAppContext } from '@/components/container/AuthAppProviderContainer'
import { GlobalLinerLoadingContext } from '@/components/container/GlobalLinerLoadingProviderContainer'

import { routes } from '@/AppRouterConfig'

export type GlobalNavigationGuardHandlerType = {
  navigationGuardHandler: () => Promise<void>
}

export function useNavigationGuard(): GlobalNavigationGuardHandlerType {
  const { updateGlobalLinerLoading } = useContext(GlobalLinerLoadingContext)
  const { checkAuthenticated, getAuthAuthority } = useContext(AuthAppContext)
  // const locationState = useLocation()
  const routerState = useRouter()

  /**
   * redirect login page.
   * @return {void}
   */
  const redirectLoginPage = () => {
    // navigate('/login', { replace: true })
    // データの初期化も兼ねてグローバルなLocationクラスを利用する
    location.assign(
      process.env.NODE_ENV === 'production' ? '/admin/login' : '/login'
    )
  }

  /**
   * global navigation guard handler.
   * @return {Promise<void>}
   */
  const navigationGuardHandler: GlobalNavigationGuardHandlerType['navigationGuardHandler'] =
    async (): Promise<void> => {
      const currentRoute = routes.find(
        // (route) => route.path === locationState.pathname
        (route) => route.path === routerState.pathname
      )

      // 認証が必要なページ
      if (currentRoute && currentRoute.requiredAuth) {
        updateGlobalLinerLoading(true)
        // 認証情報のチェック処理
        const result = await checkAuthenticated()
        // updateGlobalLinerLoading(false)
        if (!result) {
          redirectLoginPage()
        } else {
          if (currentRoute.permissions) {
            // 権限情報のチェック
            if (
              !getAuthAuthority().some((role) =>
                // eslint-disable-next-line @typescript-eslint/no-non-null-assertion
                currentRoute.permissions!.includes(role)
              )
            ) {
              // 認可されていないユーザーの場合
              redirectLoginPage()
            } else {
              // 認証・認可を満たすユーザー
              updateGlobalLinerLoading(false)
            }
          } else {
            // 認証付きページかつ認可情報が設定されていない場合
            redirectLoginPage()
          }
        }
      }
    }

  return {
    navigationGuardHandler,
  }
}
