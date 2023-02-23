import React, { useState, useContext, useRef, useEffect } from 'react'
// import { useNavigate, Link } from 'react-router-dom'
// import { useRouter } from 'next/router'
import Link from 'next/link'
import { AppRouteType } from '@/AppRouterConfig'
import { AuthAppContext } from '@/components/container/AuthAppProviderContainer'
import { ToastContext } from '@/components/container/ToastProviderContainer'
import { GlobalLoadingContext } from '@/components/container/GlobalLoadingProviderContainer'

type Props = {
  routes?: AppRouteType[]
}

export const AuthGlobalHeader: React.VFC<Props> = ({ routes = [] }) => {
  // ナビゲーションメニューの開閉フラグ
  const [isOpen, updateOpenStatus] = useState(false)
  const { logout } = useContext(AuthAppContext)
  const { updateToastState } = useContext(ToastContext)
  const { updateGlobalLoading } = useContext(GlobalLoadingContext)
  // const navigate = useNavigate()
  const refElement = useRef<HTMLHeadElement>(null)
  const refNavigationElement = useRef<HTMLDivElement>(null)

  // mount後に実行する処理
  const onDidMount = (): void => {
    let timer = 0

    const closeMenuByFocusEventHandler = (e: MouseEvent | FocusEvent) => {
      if (refElement?.current?.contains(e.target as Node)) {
        // click inside thie component
        // console.log('in')
        return
      } else {
        // click outside thie component
        updateOpenStatus(false)
      }
    }

    // eslint-disable-next-line @typescript-eslint/no-unused-vars
    const resizeEventHandler = (e: UIEvent) => {
      // resizeイベントが発火した場合は開いているナビゲーションメニューを閉じる。
      if (
        refNavigationElement?.current?.className ===
        'global-header__navigation-menu-block'
      ) {
        updateOpenStatus(false)
      }
    }

    document.addEventListener('mousedown', (e: MouseEvent) => {
      closeMenuByFocusEventHandler(e)
      document.removeEventListener('mousedown', closeMenuByFocusEventHandler)
    })

    document.addEventListener('focusout', (e: FocusEvent) => {
      closeMenuByFocusEventHandler(e)
      document.removeEventListener('focusout', closeMenuByFocusEventHandler)
    })

    window.addEventListener('resize', (e: UIEvent) => {
      // resizeイベントが複数回キャッチされるケースを考慮
      // 最後resizeイベント時のみsetTimeoutが成功してコールバックを実行する
      if (timer > 0) window.clearTimeout(timer)
      timer = window.setTimeout(() => {
        resizeEventHandler(e)
        window.removeEventListener('resize', resizeEventHandler)
      }, 200)

      // resizeEventHandler(e)
      // window.removeEventListener('resize', resizeEventHandler)
    })
  }
  useEffect(onDidMount, [])

  /**
   * exectute sign out handiling.
   * @return {Promise<void>}
   */
  const signOutHandler = async () => {
    updateGlobalLoading(true)
    const result = await logout()
    updateGlobalLoading(false)

    if (result) {
      // updateToastState('Logout Success.', 'success', true)
      // navigate('/login')

      // リダイレクトするとトーストは出せない。
      // Router外の為baseNameも指定
      // location.assign('/admin/login')
      location.assign(
        process.env.NODE_ENV === 'production' ? '/admin/login' : '/login'
      )
    } else {
      updateToastState('Logout Filed', 'error', true)
    }
  }

  return (
    <header className="global-header" ref={refElement}>
      <nav className="global-header__navigation">
        <div className="global-header__title-wrapper">
          <span className="global-header__title">Header Name</span>
        </div>
        <div className="global-header__blok-button-area">
          <button
            className="global-header__blok-button"
            onClick={() => updateOpenStatus(!isOpen)}
          >
            <svg
              className="global-header__blok-button-image"
              viewBox={isOpen === true ? '0 0 30 30' : '0 0 20 20'}
            >
              <title>Menu</title>
              {isOpen === true && (
                <path d="M24 20.188l-8.315-8.209 8.2-8.282-3.697-3.697-8.212 8.318-8.31-8.203-3.666 3.666 8.321 8.24-8.206 8.313 3.666 3.666 8.237-8.318 8.285 8.203z" />
              )}
              {isOpen === false && (
                <path d="M0 3h20v2H0V3zm0 6h20v2H0V9zm0 6h20v2H0v-2z" />
              )}
            </svg>
          </button>
        </div>
        <div
          className={
            isOpen === true
              ? 'global-header__navigation-menu-block'
              : 'global-header__navigation-menu-hidden'
          }
          ref={refNavigationElement}
        >
          <div className="global-header__navigation-item">
            {routes.map((route, i) => (
              <Link
                // className="global-header__navigation-item-button"
                key={i}
                href={route.path}
              >
                {/* {route.shortTitle} */}
                <a
                  className="global-header__navigation-item-button"
                  href={route.path}
                >
                  {route.shortTitle}
                </a>
              </Link>
            ))}
            {/* <a
              className="global-header__navigation-item-button"
              href="#responsive-header1"
            >
              test link1
            </a> */}
            {isOpen && (
              <div
                className="global-header__navigation-item-button"
                onClick={signOutHandler}
              >
                <span>Sign Out</span>
              </div>
            )}
          </div>
        </div>
        {isOpen === false && (
          <div className="global-header__blok-button-left">
            <button
              className={`parts-simple-button parts-simple-button__color--dark-grey util-color__text--white`}
              onClick={signOutHandler}
            >
              Sign Out
            </button>
          </div>
        )}
      </nav>
    </header>
  )
}

export default AuthGlobalHeader
