import { getServerVariable } from "@config/config"

const __ = (text: string, domain = 'file') => {
  const translations = getServerVariable('translations', {})
  if (text in translations) {
    return translations[text]
  }
  return text
}

const sprintf = (text: string, ...vars: any) => {
    const matches: any = text.match(/%[s d u c o x X bg G e E f F]/g)
    let str = text
    vars.map((val: any, idx: number) => {
      str = str.replace(matches[idx], val)
    })
    return str
  
}

export { __, sprintf }
