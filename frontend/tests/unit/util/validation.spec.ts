import {
  checkTextLength,
  checkTextMaxLength,
  checkTextMinLength,
} from '@/util/validation'

describe('Validation File Check', () => {
  it('check checkTextLength', () => {
    expect(checkTextLength('test text')).toBe(true)
    expect(checkTextLength('')).toBe(false)
    expect(checkTextLength(' ')).toBe(false)
  })

  it('check checkTextMaxLength', () => {
    expect(checkTextMaxLength('test text')).toBe(true)
    expect(checkTextMaxLength('test text', 10)).toBe(true)
    expect(checkTextMaxLength('test text', 9)).toBe(true)
    expect(checkTextMaxLength('test text', 8)).toBe(false)
  })

  it('check checkTextMinLength', () => {
    expect(checkTextMinLength('test text')).toBe(true)
    expect(checkTextMinLength('test text', 8)).toBe(true)
    expect(checkTextMinLength('test text', 9)).toBe(true)
    expect(checkTextMinLength('test text', 10)).toBe(false)
  })
})
