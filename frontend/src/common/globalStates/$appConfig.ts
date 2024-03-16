import { getColorPreference } from '@common/helpers/globalHelpers'
import config from '@config/config'
import { atomWithStorage } from 'jotai/utils'

const $appConfig = atomWithStorage('bitapps-fm-config', {
  isDarkTheme: config.THEME.includes('material') && getColorPreference(),
  isSidebarCollapsed: false,
  isWpMenuCollapsed: false,
  preferNodeDetailsInDrawer: false
})

export default $appConfig
