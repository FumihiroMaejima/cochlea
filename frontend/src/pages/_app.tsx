import '@/styles/globals.scss'
import '@/styles/scss/index.scss'
import type { AppProps } from 'next/app'

import { GlobalFooter } from '@/components/_global/GlobalFooter'
import { GlobalBackToPageTopButton } from '@/components/_global/GlobalBackToPageTopButton'
import { GlobalContextWrapper } from '@/components/_global/context/GlobalContextWrapper'
import { AuthAppProviderContainer } from '@/components/container/AuthAppProviderContainer'
import { GlobalLinerLoadingProviderContainer } from '@/components/container/GlobalLinerLoadingProviderContainer'
import { GlobalLoadingProviderContainer } from '@/components/container/GlobalLoadingProviderContainer'
import { ToastProviderContainer } from '@/components/container/ToastProviderContainer'
import { Layout } from '@/components/layout/Layout'

function MyApp({ Component, pageProps }: AppProps) {
  // return <Component {...pageProps} />

  return (
    <div className="app app-dark-mode">
      <GlobalLinerLoadingProviderContainer>
        <GlobalLoadingProviderContainer>
          <AuthAppProviderContainer>
            <ToastProviderContainer>
              <div>
                <GlobalContextWrapper />
                <GlobalBackToPageTopButton />
                <div className="app-content">
                  <Layout {...pageProps}>
                    <Component {...pageProps} />
                  </Layout>
                </div>
                <GlobalFooter />
              </div>
            </ToastProviderContainer>
          </AuthAppProviderContainer>
        </GlobalLoadingProviderContainer>
      </GlobalLinerLoadingProviderContainer>
    </div>
  )
}

export default MyApp
