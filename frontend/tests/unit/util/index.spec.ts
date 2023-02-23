import {
  checkPerformance,
  sortArrayNumbers,
  sortArrayNumbersDesc,
  getMultiSelectLabel,
  InvalidStateErrorUtil,
} from '@/util/index'

const testData = [{ param: [5, 3, 9, 1, 10], expect: [1, 3, 5, 9, 10] }]

describe('sortArrayNumbers', () => {
  it('sortArrayNumbers test', () => {
    testData.forEach((v) => {
      expect(sortArrayNumbers(v.param)).toEqual(v.expect)
    })
  })
})

const testDataDesc = [{ param: [5, 3, 9, 1, 10], expect: [10, 9, 5, 3, 1] }]

describe('sortArrayNumbersDesc', () => {
  it('sortArrayNumbersDesc test', () => {
    testDataDesc.forEach((v) => {
      expect(sortArrayNumbersDesc(v.param)).toEqual(v.expect)
    })
  })
})

describe('sortArrayNumbers check performance', () => {
  it('sortArrayNumbers performance check', () => {
    const data = [5, 3, 9, 1, 10]

    expect(checkPerformance('sortArrayNumbers', sortArrayNumbers(data))).toBe(
      undefined
    )
  })
})

const testSelectBoxData = [
  { text: 'test1', value: 1 },
  { text: 'test2', value: 2 },
  { text: 'test3', value: 3 },
]

describe('Utility File Check', () => {
  it('check getMultiSelectLabel', () => {
    const testParam = [testSelectBoxData[0].value, testSelectBoxData[1].value]
    const expectValue = [testSelectBoxData[0].text, testSelectBoxData[1].text]

    expect(getMultiSelectLabel(testParam, testSelectBoxData)).toStrictEqual(
      expectValue
    )
  })

  it('check InvalidStateErrorUtil class', () => {
    const testData = 'invalid string Data.'
    const message = `catch invalid value ${testData}`
    const instance = new InvalidStateErrorUtil(testData as never, message)

    expect(instance.message).toStrictEqual(message)
    expect(instance.name).toStrictEqual('Error')
  })
})
