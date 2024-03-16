import { type AliasToken } from 'antd/es/theme/internal'

const fontFamily =
  "'Outfit',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,'Helvetica Neue',Arial,'Noto Sans',sans-serif,'Apple Color Emoji','Segoe UI Emoji','Segoe UI Symbol','Noto Color Emoji'"

const commonThemeToken: Partial<AliasToken> = {
  fontFamily,
  borderRadius: 10,
  borderRadiusSM: 8,
  borderRadiusXS: 4,
  colorPrimary: '#2bbdff',
  colorSuccess: '#00ff7d',
  colorWarning: '#ffc041'
}

export default commonThemeToken
