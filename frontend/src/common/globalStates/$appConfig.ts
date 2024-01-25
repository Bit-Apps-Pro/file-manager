import { getColorPreference } from '@common/helpers/globalHelpers'
import config from '@config/config'
import { atomWithStorage } from 'jotai/utils'

const $appConfig = atomWithStorage('bit-flow-config', {
  isDarkTheme: getColorPreference(),
  isSidebarCollapsed: false,
  isWpMenuCollapsed: false,
  preferNodeDetailsInDrawer: false,
})

export default $appConfig
