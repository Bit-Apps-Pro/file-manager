import { ComponentStyleConfig } from 'antd/es/config-provider/context'
import { type OverrideToken } from 'antd/es/theme/interface'
import { type AliasToken } from 'antd/es/theme/internal'

import commonThemeToken from './common'

export const lightThemeToken: Partial<AliasToken> = {
  ...commonThemeToken,
  // colorPrimary: '#ff0374',
  colorPrimary: '#ff246d',
  colorSuccess: '#00ff7d',
  colorWarning: '#ffc041',
  colorBgContainer: '#fff',
  controlOutline: '#48484823',
  boxShadowSecondary:
    '0 0 0 1px rgba(0,0,0,0.05) ,0 6px 16px 0 rgba(0, 0, 0, 0.08), 0 3px 6px -4px rgba(0, 0, 0, 0.12),      0 9px 28px 8px rgba(0, 0, 0, 0.05)    ',
  boxShadow:
    '0 0 0 1px rgba(0,0,0,0.05), 0 6px 16px 0 rgba(0, 0, 0, 0.08), 0 3px 6px -4px rgba(0, 0, 0, 0.12),      0 9px 28px 8px rgba(0, 0, 0, 0.05)    '
}

export const lightThemeComponentToken: OverrideToken = {}
