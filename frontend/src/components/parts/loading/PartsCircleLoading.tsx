import React from 'react'

/* type Props = {
  open: boolean
} */

export const PartsCircleLoading: React.VFC = () => {
  return (
    <div className="parts-circle-loading">
      <div className="parts-circle-loading__content">
        <div className="parts-circle-loading__spinner"></div>
      </div>
    </div>
  )
}

export default PartsCircleLoading
