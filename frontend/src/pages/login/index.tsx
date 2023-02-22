import React, { useState, useContext } from 'react'
// import { useNavigate } from 'react-router-dom'
import { useRouter } from 'next/router'
import { PartsSimpleButton } from '@/components/parts/button/PartsSimpleButton'
import { PartsSimpleTextField } from '@/components/parts/form/PartsSimpleTextField'
import { PartsSimpleHeading } from '@/components/parts/heading/PartsSimpleHeading'
// import { PartsSimpleToast } from '@/components/parts/toast/PartsSimpleToast'
// import { PartsCircleLoading } from '@/components/parts/PartsCircleLoading'

import { AuthAppContext } from '@/components/container/AuthAppProviderContainer'
import { ToastContext } from '@/components/container/ToastProviderContainer'
import { GlobalLoadingContext } from '@/components/container/GlobalLoadingProviderContainer'

const defaulName =
  process.env.NODE_ENV === 'production'
    ? ''
    : process.env.NEXT_PUBLIC_ADMIN_EMAIL || ''

const defaulPassword =
  process.env.NODE_ENV === 'production'
    ? ''
    : process.env.NEXT_PUBLIC_ADMIN_PASSWORD || ''

export const Login: React.VFC = () => {
  // const navigate = useNavigate()
  const router = useRouter()
  const [emailValue, setEmailValue] = useState(defaulName)
  const [passwordValue, setPasswordValue] = useState(defaulPassword)

  const { updateGlobalLoading } = useContext(GlobalLoadingContext)
  const { updateToastState } = useContext(ToastContext)
  const { login } = useContext(AuthAppContext)
  console.log('child: ' + JSON.stringify(updateToastState, null, 2))

  return (
    <div className="login page-container page-container__mx-auto">
      <PartsSimpleHeading text="Login Page" color="dark-grey" />

      <div className="mxy-4">
        <div className="util-border-full-solid-2p__color--dark-grey util-border-radius__round--5p util-color__text--dark-grey">
          <div className="mxy-4 d-flex flex-align-center">
            <label className="width-2 text-left" htmlFor="email">
              email
            </label>
            <PartsSimpleTextField
              id="email"
              className="width-8"
              value={emailValue}
              onInput={(e) => setEmailValue(e.currentTarget.value)}
              placeholder="email"
            />
            {/* <small class="p-error">{{ emailError }}</small> */}
          </div>

          <div className="mxy-4 d-flex flex-align-center">
            <label className="width-2 text-left" htmlFor="password">
              password
            </label>
            <PartsSimpleTextField
              id="password"
              className="width-8"
              value={passwordValue}
              onInput={(e) => setPasswordValue(e.currentTarget.value)}
              placeholder="password"
              type="password"
            />
            {/* <small class="p-error">{{ passwordError }}</small> */}
          </div>

          <div className="mxy-4">
            <div className="d-flex flex-justify-right">
              <PartsSimpleButton
                text="login"
                color="green"
                disabled={emailValue.length === 0 || passwordValue.length === 0}
                onClick={async () => {
                  updateGlobalLoading(true)
                  const result = await login(emailValue, passwordValue)

                  updateToastState(
                    result ? 'login success' : 'login failed',
                    result ? 'success' : 'error',
                    true
                  )
                  updateGlobalLoading(false)

                  if (result) {
                    router.push('/')
                    // location.assign('/admin')
                  }
                }}
              />
            </div>
          </div>
        </div>
      </div>
    </div>
  )
}

export default Login
