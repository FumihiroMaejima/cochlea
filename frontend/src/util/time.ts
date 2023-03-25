/**
 * get current time stamp
 * @return {number} timestampe
 */
export const getCurrentTimeStamp = (): number => {
  return new Date().getTime()
}

/**
 * get time stamp by datetime
 * @return {number} timestampe
 */
export const getTimeStamp = (data: string): number => {
  return new Date(data).getTime()
}
