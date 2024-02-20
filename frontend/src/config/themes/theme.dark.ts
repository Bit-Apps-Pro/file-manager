import { type OverrideToken } from 'antd/es/theme/interface'
import { type AliasToken } from 'antd/es/theme/internal'

import commonThemeToken from './common'

export const darkThemeToken: Partial<AliasToken> = {
  ...commonThemeToken,
  colorBgContainer: '#1c1a1e',
  colorBgBase: '#1c1a1e',
  // colorBgBase: '#161218',
  // colorBgBase: '#040304',
  // colorTextBase: '#fce3ff',
  // colorTextBase: '#fef1ff',
  // colorBgElevated: 'rgb(28, 21, 28)',
  // colorBgContainer: '#151015',
  // colorBgContainer: '#221E20',
  // colorBgContainer: '#231E27',
  // colorBgContainer: '#2A242F',
  // boxShadow:
  //   '0 0 0 1px rgb(52, 40, 52), 0 6px 16px 0 rgba(0, 0, 0, 0.08), 0 3px 6px -4px rgba(0, 0, 0, 0.12), 0 9px 28px 8px rgba(0, 0, 0, 0.05);',
  // boxShadowSecondary:
  //   '  0 0 0 1px rgb(52, 40, 52),    0 6px 16px 0 rgba(0, 0, 0, 0.08),      0 3px 6px -4px rgba(0, 0, 0, 0.12),      0 9px 28px 8px rgba(0, 0, 0, 0.05)    ',
  // controlOutline: '#ffaace33'
}

export const darkThemeComponentToken: OverrideToken = {
  Menu: {
    darkPopupBg: darkThemeToken.colorBgContainer
  }
}
