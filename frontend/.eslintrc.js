module.exports = {
  root: true,
  env: {
    node: true,
  },
  parser: '@typescript-eslint/parser',
  extends: [
    'plugin:@next/next/recommended',
    'eslint:recommended',
    'prettier',
    'plugin:@typescript-eslint/recommended',
    'plugin:prettier/recommended',
  ],
  plugins: ['prettier', '@typescript-eslint'],
  parserOptions: {
    sourceType: 'module',
    ecmaVersion: 2021,
  },
  rules: {
    '@typescript-eslint/no-explicit-any': 0,
    '@typescript-eslint/interface-name-prefix': 0,
    'no-unused-components': 'off',
    'no-console': process.env.NODE_ENV === 'production' ? 'warn' : 'off',
    'no-debugger': process.env.NODE_ENV === 'production' ? 'warn' : 'off',
  },
  overrides: [
    {
      files: [
        '**/__tests__/*.{j,t}s?(x)',
        '**/tests/e2e/**/*.spec.{j,t}s?(x)',
        '**/tests/unit/**/*.spec.{j,t}s?(x)',
      ],
      env: {
        jest: true,
      },
    },
  ],
}
