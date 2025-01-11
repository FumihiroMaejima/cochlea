import React from 'react'
import { PartsLinerLoading } from '@/components/parts/loading/PartsLinerLoading'

type Props = {
  isDarkMode?: boolean
}

export const PartsLinerLoadingWrapper: React.FC<Props> = ({
  isDarkMode = true,
}) => {
  return (
    <div
      className={`parts-liner-loading-wrapper${
        isDarkMode ? ' app-dark-mode' : ''
      }`}
    >
      <div className="parts-liner-loading-wrapper__content">
        <PartsLinerLoading />
      </div>
    </div>
  )
}

export default PartsLinerLoadingWrapper
