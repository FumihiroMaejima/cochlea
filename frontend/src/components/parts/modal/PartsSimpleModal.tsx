import React from 'react'

type Props = {
  isOpen: boolean
  hideOnClickOutSide?: boolean
  closeModalHandler: () => void
}

export const PartsSimpleModal: React.VFC<Props> = ({
  isOpen = false,
  hideOnClickOutSide = false,
  closeModalHandler = () => {
    // eslint-disable-next-line no-console
    console.log('default click')
  },
}) => {
  return (
    <div
      className={`parts-simple-modal ${
        isOpen ? 'parts-simple-modal--active' : ''
      }`}
      onClick={hideOnClickOutSide ? closeModalHandler : undefined}
    >
      <div className="parts-simple-modal__dialog">
        <div
          className={`parts-simple-modal__content ${
            isOpen ? 'parts-simple-modal__content--active' : ''
          }`}
        >
          <div className="parts-simple-modal__content-head">
            <h1>modal header</h1>
          </div>
          <button
            className="parts-simple-modal__close"
            onClick={closeModalHandler}
          >
            <svg className="" viewBox="0 0 24 24">
              <path d="M19 6.41l-1.41-1.41-5.59 5.59-5.59-5.59-1.41 1.41 5.59 5.59-5.59 5.59 1.41 1.41 5.59-5.59 5.59 5.59 1.41-1.41-5.59-5.59z" />
              <path d="M0 0h24v24h-24z" fill="none" />
            </svg>
          </button>
          <div className="parts-simple-modal__content-body">
            <p>test 1234 567890 abcdefg modal content!</p>
            <p>test 1234 567890 abcdefg modal content!</p>
            <p>test 1234 567890 abcdefg modal content!</p>
            <p>test 1234 567890 abcdefg modal content!</p>
            <p>test 1234 567890 abcdefg modal content!</p>
          </div>
        </div>
      </div>
    </div>
  )
}

export default PartsSimpleModal
