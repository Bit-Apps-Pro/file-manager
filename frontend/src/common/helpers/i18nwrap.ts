import { getServerVariable } from '@config/config'

// eslint-disable-next-line no-underscore-dangle
const __ = (text: string) => {
  const translations = getServerVariable('translations', {})
  if (text in translations) {
    return translations[text]
  }
  return text
}

const sprintf = (text: string, ...vars: any) => {
  const matches: RegExpMatchArray | null = text.match(/%[s d u c o x X bg G e E f F]/g)
  if (!matches) {
    return text
  }
  let str = text
  vars.map((val: string, idx: number) => {
    str = str.replace(matches[idx], val)
  })
  return str
}

export { __, sprintf }
