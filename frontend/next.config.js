// next.js/packages/next/shared/lib/constants.ts
// eslint-disable-next-line @typescript-eslint/no-var-requires
const { PHASE_DEVELOPMENT_SERVER } = require('next/constants')

const developServerPort = process.env.NEXT_PUBLIC_BACKEND_PORT || '50100'

/** @type {import('next').NextConfig} */
// Default Setting
/* const nextConfig = {
  reactStrictMode: true,
}

module.exports = nextConfig */

// Custom Setting
// eslint-disable-next-line @typescript-eslint/no-unused-vars
module.exports = (phase, { defaultConfig }) => {
  /**
   * @type {import('next').NextConfig}
   */
  const nextConfig = {
    /* config options here */
    reactStrictMode: true,
    poweredByHeader: false,
    basePath: '/admin',
    distDir: 'dist',
    // TODO Now global, cange only develop setting
    /* rewrites: () => {
      return [
        {
          source: '/api',
          destination: `http://localhost:${developServerPort}/api`,
          // destination: `http://localhost:${developServerPort}`,
          // destination: `http://localhost:50100/api`,
          // destination: 'http://localhost:50100',
          basePath: false,
          // basePath: undefined,
        },
      ]
    }, */
  }
  // return nextConfig

  // if Divide config in environmental.
  if (phase === PHASE_DEVELOPMENT_SERVER) {
    return {
      // TODO development only config options here
      ...nextConfig,
      basePath: '',
    }
  }

  return {
    // TODO config options for all phases except development here
    ...nextConfig,
  }
}
