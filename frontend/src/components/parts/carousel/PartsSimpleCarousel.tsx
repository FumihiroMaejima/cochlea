import React from 'react'

type Props = {
  isOpen?: boolean
}

export const PartsSimpleCarousel: React.VFC<Props> = () => {
  return (
    <section className="parts-simple-carousel" aria-label="Gallery">
      <ol className="parts-simple-carousel__viewport">
        <li
          id="carousel__slide1"
          tabIndex={0}
          className="parts-simple-carousel__slide"
        >
          <div className="parts-simple-carousel__snapper">
            <a href="#carousel__slide3" className="parts-simple-carousel__prev">
              Go to last slide
            </a>
            <a href="#carousel__slide2" className="parts-simple-carousel__next">
              Go to next slide
            </a>
          </div>
        </li>
        <li
          id="carousel__slide2"
          tabIndex={0}
          className="parts-simple-carousel__slide"
        >
          <div className="parts-simple-carousel__snapper"></div>
          <a href="#carousel__slide1" className="parts-simple-carousel__prev">
            Go to previous slide
          </a>
          <a href="#carousel__slide3" className="parts-simple-carousel__next">
            Go to next slide
          </a>
        </li>
        <li
          id="carousel__slide3"
          tabIndex={0}
          className="parts-simple-carousel__slide"
        >
          <div className="parts-simple-carousel__snapper"></div>
          <a href="#carousel__slide2" className="parts-simple-carousel__prev">
            Go to previous slide
          </a>
          <a href="#carousel__slide1" className="parts-simple-carousel__next">
            Go to first slide
          </a>
        </li>
      </ol>
      <aside className="parts-simple-carousel__navigation">
        <ol className="parts-simple-carousel__navigation-list">
          <li className="parts-simple-carousel__navigation-item">
            <a
              href="#carousel__slide1"
              className="parts-simple-carousel__navigation-button"
            >
              Go to slide 1
            </a>
          </li>
          <li className="parts-simple-carousel__navigation-item">
            <a
              href="#carousel__slide2"
              className="parts-simple-carousel__navigation-button"
            >
              Go to slide 2
            </a>
          </li>
          <li className="parts-simple-carousel__navigation-item">
            <a
              href="#carousel__slide3"
              className="parts-simple-carousel__navigation-button"
            >
              Go to slide 3
            </a>
          </li>
        </ol>
      </aside>
    </section>
  )
}

export default PartsSimpleCarousel
