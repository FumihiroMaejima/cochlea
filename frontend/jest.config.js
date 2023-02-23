module.exports = {
  preset: 'ts-jest',
  roots: ['<rootDir>/'],
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
  // testURL: 'http://localhost/',
  testEnvironmentOptions: {
    url: 'http://localhost/',
  },
  collectCoverage: false, // no check coverage
  globals: {
    'ts-jest': {
      tsconfig: './tsconfig.json',
    },
  },
}
