import { NextApiRequest, NextApiResponse } from 'next'
import httpProxyMiddleware from 'next-http-proxy-middleware'
// import https from 'https'

const developServerPort = process.env.NEXT_PUBLIC_BACKEND_PORT || '50100'

// ファイルのアップロードなどでmultipart/form-dataを使用するときの設定
export const config = {
  api: {
    bodyParser: false,
  },
}

// eslint-disable-next-line @typescript-eslint/no-explicit-any
export default (
  req: NextApiRequest,
  res: NextApiResponse
): ReturnType<typeof httpProxyMiddleware> => {
  const proxy = httpProxyMiddleware(req, res, {
    // target: process.env.API_HOST,
    target: `http://localhost:${developServerPort}/api`,
    changeOrigin: true,
    headers: {
      'x-test-key': process.env.API_KEY || '',
    },
    pathRewrite: [
      // '^/api/proxy': '',
      // '^/api': '', // true
      {
        patternStr: '/api',
        replaceStr: '',
      },
    ],
    /* pathRewrite: {
      // '^/api/proxy': '',
      // '^/api': '', // true
      '^/api': '',
    }, */
    /* pathRewrite: [
      {patternStr: '/api'}
    ] */
    // agent: new https.Agent({
    //   rejectUnauthorized: false,
    // }),
  })

  return proxy
}

/* import { IncomingMessage, ServerResponse } from 'http'
import httpProxy from 'http-proxy'

const developServerPort = process.env.NEXT_PUBLIC_BACKEND_PORT || '50100'

const target = `http://localhost:${developServerPort}/api`
const proxy = httpProxy.createProxyServer({ target, changeOrigin: true })

export default async function handler(
  req: IncomingMessage,
  res: ServerResponse
) {
  // req.url = req.url!.replace(new RegExp('^/api/proxy'), '')
  req.url = req.url!.replace(new RegExp('^/api'), '')

  return new Promise((resolve, reject) => {
    try {
      proxy.web(req, res, { proxyTimeout: 5000 }, (e) => {
        reject(e)
      })
      resolve
    } catch (e) {
      reject(e)
    }
  })
} */
