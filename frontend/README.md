# Next.js application

my Next.js application.

---

# 構成

| 名前 | バージョン |
| :--- | :---: |
| npm | 9.3.1 |
| node | 18.14.1 |
| yarn | 1.22.17 |
| react | 17.0.2 |
| TypeScript | 4.9.5 |

---

This is a [Next.js](https://nextjs.org/) project bootstrapped with [`create-next-app`](https://github.com/vercel/next.js/tree/canary/packages/create-next-app).

## Getting Started

First, run the development server:

```bash
npm run dev
# or
yarn dev
```

Open [http://localhost:3000](http://localhost:3000) with your browser to see the result.

You can start editing the page by modifying `pages/index.tsx`. The page auto-updates as you edit the file.

[API routes](https://nextjs.org/docs/api-routes/introduction) can be accessed on [http://localhost:3000/api/hello](http://localhost:3000/api/hello). This endpoint can be edited in `pages/api/hello.ts`.

The `pages/api` directory is mapped to `/api/*`. Files in this directory are treated as [API routes](https://nextjs.org/docs/api-routes/introduction) instead of React pages.

## Learn More

To learn more about Next.js, take a look at the following resources:

- [Next.js Documentation](https://nextjs.org/docs) - learn about Next.js features and API.
- [Learn Next.js](https://nextjs.org/learn) - an interactive Next.js tutorial.

You can check out [the Next.js GitHub repository](https://github.com/vercel/next.js/) - your feedback and contributions are welcome!

## Deploy on Vercel

The easiest way to deploy your Next.js app is to use the [Vercel Platform](https://vercel.com/new?utm_medium=default-template&filter=next.js&utm_source=create-next-app&utm_campaign=create-next-app-readme) from the creators of Next.js.

Check out our [Next.js deployment documentation](https://nextjs.org/docs/deployment) for more details.


---

## プロジェクト作成

```Shell-session
$ yarn create next-app --typescript

yarn create v1.22.17
[1/4] 🔍  Resolving packages...
[2/4] 🚚  Fetching packages...
[3/4] 🔗  Linking dependencies...
[4/4] 🔨  Building fresh packages...

success Installed "create-next-app@12.0.10" with binaries:
      - create-next-app
✔ What is your project named? … project-name
Creating a new Next.js app in /path/current/project-name.

Using yarn.

Installing dependencies:
- react
- react-dom
- next

yarn add v1.22.17
info No lockfile found.
✨  Done in 276.47s.

Installing devDependencies:
- eslint
- eslint-config-next
- typescript
- @types/react
- @types/node

yarn add v1.22.17
[1/4] 🔍  Resolving packages...
info There appears to be trouble with your network connection. Retrying...
[2/4] 🚚  Fetching packages...
[3/4] 🔗  Linking dependencies...
[4/4] 🔨  Building fresh packages...
success Saved lockfile.
success Saved 161 new dependencies.
info Direct dependencies

✨  Done in 75.27s.

Success! Created next at /path/current/project-name
Inside that directory, you can run several commands:

  yarn dev
    Starts the development server.

  yarn build
    Builds the app for production.

  yarn start
    Runs the built app in production mode.

We suggest that you begin by typing:

  cd next
  yarn dev

✨  Done in 419.68s.

```

---

# devサーバーの実行

```Shell-session
$ yarn dev
```

`http://localhost:3000`でトップページにアクセス出来る。

`http://localhost:3000/api/hello`でサンプルにAPIにアクセス出来る。


---

# srcディレクトリの作成

```Shell-session
$ mkdir src
$ mv pages src/
$ mv styles src/

$ mkdir src && mv pages src/ && mv styles src/
```

---

# eslintの設定の修正

### .eslintrc.jsonを使う場合

下記のパッケージのみインストールすれば良い。

`.eslintrc.json`に既存のものと同様の設定を追記されば良い。

`.prettierrc`も作成しておくこと。

```shell
$ yarn add --dev @next/eslint-plugin-next
$ yarn add --dev eslint-config-prettier
```

設定例

```json
{
  // "extends": "next",
  "extends": ["next/core-web-vitals", "prettier"]
}

```

### .eslintrc.jsonを使わない場合
既存の.eslintrc.jsonを削除し、react側で使用している.eslintrcを利用する。

```Shell-session
$ mv .eslintrc.json .eslintrc.json.old
$ touch .eslintrc.js
```

eslint関係のパッケージの追加

```Shell-session
$ yarn add --dev @typescript-eslint/eslint-plugin
$ yarn add --dev @typescript-eslint/parser
// 一括
$ yarn add --dev @typescript-eslint/eslint-plugin @typescript-eslint/parser
```

next用のパッケージのインストール

```Shell-session
$ yarn add --dev @next/eslint-plugin-next
```
`.eslintrc.js`の`extends`に下記を追記する。


```javascript
  extends: [
    ...
    'plugin:@next/next/recommended',
  ],

```

---

# prettierのインストール

```Shell-session
$ yarn add --dev prettier
// eslint連携用のパッケージ
$ yarn add --dev eslint-config-prettier eslint-plugin-prettier
```

### .prettierrcの作成と編集

```Json
{
  "semi": false,
  "arrowParens": "always",
  "singleQuote": true
}
```

---

# stylelint

```Shell-session
$ yarn add --dev stylelint
```

`package.json`の`scripts`に設定を追記

```json
  "scripts": {
    ...
    "lint:css": "stylelint src/**/*.scss"
  },
```

### .stylelintrcの作成と編集

```Json
{
  "rules": {
    "color-hex-length": "short",
    "color-no-invalid-hex": true,
    "custom-property-no-outside-root": true,
    "indentation": 2,
    "length-zero-no-unit": true,
    "media-feature-name-no-vendor-prefix": true,
    "number-leading-zero": "never",
    "selector-root-no-composition": true,
    "string-quotes": "single"
  }
}
```

---


## tsconfig.jsonの設定(エイリアス等)

デフォルトのtsconfig.jsonの設定は下記

```Json
{
  "compilerOptions": {
    "target": "esnext",
    "lib": ["dom", "dom.iterable", "esnext"],
    "allowJs": true,
    "skipLibCheck": true,
    "strict": true,
    "forceConsistentCasingInFileNames": true,
    "noEmit": true,
    "esModuleInterop": true,
    "module": "esnext",
    "moduleResolution": "node",
    "resolveJsonModule": true,
    "isolatedModules": true,
    "jsx": "preserve",
    "incremental": true,
    "types": [
      // "@types/jest",
      // "jest",
      "node"
    ],
    "baseUrl": ".",
    "paths": {
      "@/*": [
        "src/*"
      ]
    }
  },
  "include": [
    "next-env.d.ts",
    "src/**/*.ts",
    "src/**/*.tsx",
    "tests/unit/**/*.ts",
    "tests/unit/**/*.tsx"
  ],
  "exclude": ["node_modules"]
}
```

---

### axios

```Shell-session
$ yarn add axios
```

---

# @types/nodeのインストール

Next.jsの場合はプロジェクト作成時にデフォルトでインストールされる。

```Shell-session
$ yarn add --dev @types/node
```

---

# sass


```Shell-session
$ yarn add --dev sass
```

下記のcssファイルの拡張子を`scss`に変更する

```Shell-session
- /src/App.css
- /src/index.css
```

元々のcssファイルをインポートしていた箇所も修正する

```Shell-session
- /src/App.tsx
- /src/main.tsx
```

---

# jest


```Shell-session
$ yarn add --dev jest ts-jest @types/jest @testing-library/react
```

`package.json`の設定

```Json
  "scripts": {
    ...
    "test:unit": "jest --config jest.config.js",
    "test:e2e": "cypress open --browser chrome"
  },
```

## `jest.config.js`の設定

```JavaScript
module.exports = {
  preset: 'ts-jest',
  moduleNameMapper: {
    '^@/(.*)$': '<rootDir>/src/$1',
    '^~/(.*)$': '<rootDir>/src/$1',
  },
  transform: {
    '^.+\\.ts$': 'ts-jest',
    '^.+\\.tsx$': 'ts-jest',
  },
  testMatch: ['<rootDir>/tests/unit/**/*.spec.ts'],
  moduleFileExtensions: ['ts', 'js', 'tsx', 'jsx', 'json'],
  testURL: 'http://localhost/',
  collectCoverage: false, // no check coverage
}

```

---

# Cypress

専用の`tsconfig.json`を用意すること。

```Shell-session
$ yarn add --dev cypress @types/jest
```

### package.jsonの設定

`package.json`の`scripts`の設定を追加

```Json
  "scripts": {
    ...
    "test:e2e": "cypress open --browser chrome"
  },
```


### cypress.jsonの設定

```Json
{
  "baseUrl": "http://localhost:3000",
  "pluginsFile": "tests/e2e/plugins/index.js"
}
```

### e2eディレクトリの設定

vue-cliの設定を踏襲して、`tests/e2e`ディレクトリで実行させる。

`cypress`ディレクトリ = `e2e`ディレクトリ

`tests/e2e/plugins/index.js`の設定を修正(ファイルの出力先の設定)

```JavaScript
module.exports = (on, config) => {
  // `on` is used to hook into various events Cypress emits
  // `config` is the resolved Cypress config

  return Object.assign({}, config, {
    fixturesFolder: 'tests/e2e/fixtures',
    integrationFolder: 'tests/e2e/integrations',
    screenshotsFolder: 'tests/e2e/screenshots',
    videosFolder: 'tests/e2e/videos',
    supportFile: 'tests/e2e/support/index.js'
  })
}
```

---

# husky,lint-staged

huskyが設定されていなければ追加する

```Shell-session
$ yarn add --dev husky lint-staged
```

package.jsonの`srcripts`に下記のエイリアスを追記する。(モノレポ用の設定)

Nextのプロジェクト名は`next`とする。

`frontend/.huskyディレクトリ`を作成する為に`yarn prepare`と`yarn create-precommit`をそれぞれ実行する。

```Shell-session
$ yarn prepare
$ yarn create-precommit
```

```json
  "scripts": {
    ...
    "prepare": "cd .. && husky install next/.husky",
    "create-precommit": "cd .. && husky add next/.husky/pre-commit \"cd next && yarn lint-staged\"",
    "lint-staged": "lint-staged"
  },
```

---

---

# proxy設定

`next-http-proxy-middleware`のインストール

```Shell-session
$ yarn add --dev next-http-proxy-middleware
```

`@/pages/api/`ディレクトリに`[...all].ts`を追加


```TypeScript
import { NextApiRequest, NextApiResponse } from 'next'
import httpProxyMiddleware from 'next-http-proxy-middleware'

// eslint-disable-next-line @typescript-eslint/no-explicit-any
export default (
  req: NextApiRequest,
  res: NextApiResponse
): ReturnType<typeof httpProxyMiddleware> => {
  const proxy = httpProxyMiddleware(req, res, {
    // target: process.env.API_HOST,
    target: `http://localhost/api`,
    changeOrigin: true,
    headers: {
      'x-test-key': 'test header data',
    },
    pathRewrite: {
      '^/api': '',
    },
  })

  return proxy
}
```


`next.config.js`にて`basePath`が設定されていると、basepathを含めてリクエストを送らないとプロキシ設定が有効化出来ない。

公式ドキュメントには`rewrites`などの設定で特定のパスの時にbasePathを無効化する設定があるが、2022年2月現在は有効化されない。

[参考](https://nextjs.org/docs/api-reference/next.config.js/rewrites)


対応策として`prosess.env.NODE_ENV`が`development`の時は別途basePathを設定してreturnする必要がある。

公式ドキュメントによると`PHASE_DEVELOPMENT_SERVER`で判定して早期returnさせている。


```TypeScript
const { PHASE_DEVELOPMENT_SERVER } = require('next/constants')

// custom setting
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
    distDir: 'build',
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
```



---

### Debug設定

`launch.json`に下記の設定を追加

```json
{
    // IntelliSense を使用して利用可能な属性を学べます。
    // 既存の属性の説明をホバーして表示します。
    // 詳細情報は次を確認してください: https://go.microsoft.com/fwlink/?linkid=830387
    "version": "0.2.0",
    "configurations": [
        {
            "name": "Next.js Server Side Debug",
            "type": "node",
            "request": "attach",
            "skipFiles": ["<node_internals>/**"],
            "port": 9229
        },
        {
            "name": "Next.js Clinet Side Debug",
            "type": "chrome",
            "request": "launch",
            "url": "http://localhost:3001",
            "webRoot": "${workspaceFolder}/frontend",
            "sourceMaps": true, // sourceMapを有効にするか無効にするか選択。SourceMap：コンパイル前後の対応関係を記録したファイル。
            "sourceMapPathOverrides": {
              "webpack://_N_E/*": "${webRoot}/*"
            }
        }
    ]
}

```

`package.json`の`scripts`に記載されている`next dev`のスクリプトに`NODE_OPTIONS='--inspect'`を追加。

```json
  "scripts": {
    "dev": "next dev -p 3001",
    "debug": "NODE_OPTIONS='--inspect' next dev -p 3001",
  },

```

```shell
yarn debug
```

---

# licenses

```shell

### 一覧出力
yarn licenses list

### LINCENSESの出力
yarn licenses generate-disclaimer
```

---


# auditの実行

```shell

### 一覧出力
yarn audit

```

---

