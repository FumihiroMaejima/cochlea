import React from 'react'
// import '@/assets/scss/index.scss'

export const GlobalFooter: React.VFC = () => {
  // const currentYear = process.env.REACT_APP_CURRENT_YEAR || 2022
  // for vite
  const currentYear = process.env.NEXT_PUBLIC_CURRENT_YEAR || ''

  return (
    <footer className="global-footer">
      <address className="">
        Copyright(C){currentYear} Domain Name,Allright Reserved.
      </address>
    </footer>
  )
}

export default GlobalFooter
