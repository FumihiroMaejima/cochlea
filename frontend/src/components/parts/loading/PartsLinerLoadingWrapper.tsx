import React from 'react'
import { PartsLinerLoading } from '@/components/parts/loading/PartsLinerLoading'

/* type Props = {
  open: boolean
} */

export const PartsLinerLoadingWrapper: React.VFC = () => {
  return (
    <div className="parts-liner-loading-wrapper">
      <div className="parts-liner-loading-wrapper__content">
        <PartsLinerLoading />
      </div>
    </div>
  )
}

export default PartsLinerLoadingWrapper
