import React, { useContext } from 'react'
import { PartsSimpleToast } from '@/components/parts/toast/PartsSimpleToast'
import { PartsCircleLoading } from '@/components/parts/loading/PartsCircleLoading'
// import { PartsLinerLoading } from '@/components/parts/loading/PartsLinerLoading'
import { PartsLinerLoadingWrapper } from '@/components/parts/loading/PartsLinerLoadingWrapper'
// global context
// import { AuthAppContext } from '@/components/container/AuthAppProviderContainer'
import { ToastContext } from '@/components/container/ToastProviderContainer'
import { GlobalLinerLoadingContext } from '@/components/container/GlobalLinerLoadingProviderContainer'
import { GlobalLoadingContext } from '@/components/container/GlobalLoadingProviderContainer'

export const GlobalContextWrapper: React.VFC = () => {
  const { isOpenLinerLoading } = useContext(GlobalLinerLoadingContext)
  const { isOpenLoading } = useContext(GlobalLoadingContext)
  const { toastState, updateToastState } = useContext(ToastContext)
  // const { login } = useContext(AuthAppContext)
  console.log(
    'global wrapper component.: ' + JSON.stringify(updateToastState, null, 2)
  )

  return (
    <div className="global-context-wrapper">
      {/* {isOpenLinerLoading && <PartsLinerLoading />} */}
      {isOpenLinerLoading && <PartsLinerLoadingWrapper />}
      {!isOpenLinerLoading && isOpenLoading && <PartsCircleLoading />}
      <PartsSimpleToast
        value={toastState.isDisplay}
        data={{ message: toastState.message, status: toastState.status }}
        onAnimationEnd={() => {
          updateToastState('close', 'normal', false)
        }}
      />
    </div>
  )
}

export default GlobalContextWrapper
