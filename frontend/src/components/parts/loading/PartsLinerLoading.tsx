import React from 'react'

/* type Props = {
  open: boolean
} */

export const PartsLinerLoading: React.VFC = () => {
  return (
    <div className="parts-liner-loading">
      {/* <div className="parts-liner-loading__bar"></div> */}
      <div className="parts-liner-loading__line"></div>
      <div className="parts-liner-loading__line mx-2"></div>
      <div className="parts-liner-loading__line"></div>
    </div>
  )
}

export default PartsLinerLoading
