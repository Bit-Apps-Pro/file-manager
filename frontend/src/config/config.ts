// eslint-disable-next-line camelcase, @typescript-eslint/ban-ts-comment
// @ts-ignore
const serverVariables = typeof fm === 'undefined' ? {} : fm // eslint-disable-line camelcase,
const options = serverVariables?.options ?? {}
export function getServerVariable(key: string, fallback?: unknown) {
  if (!(key in serverVariables) || !serverVariables[key]) {
    console.error('🚥🚥🚥 Missing server variable: ', key) // eslint-disable-line no-console
    return fallback
  }
  return serverVariables[key]
}

export function getOptionVariable(key: string, fallback?: unknown) {
  if (!(key in options) || !options[key]) {
    console.error('🚥🚥🚥 Missing option: ', key) // eslint-disable-line no-console
    return fallback
  }
  return options[key]
}

function getThemes() {
  const themes = getOptionVariable('themes', {})

  const formattedTheme: Array<{
    key: string
    title: string
  }> = []

  Object.keys(themes).map(key => {
    const title = (key.charAt(0).toUpperCase() + key.slice(1)).split('-').join(' ')
    formattedTheme.push({
      title,
      key
    })
  })
  formattedTheme.push({ key: 'default', title: 'Default' })
  return formattedTheme
}

const config = {
  IS_DEV: true,
  PRODUCT_NAME: 'Bit File Manager',
  PLUGIN_SLUG: getServerVariable('pluginSlug'),
  AJAX_URL: getServerVariable('ajaxURL', 'http://.local/wp-admin/admin-ajax.php'),
  ROOT_URL: getServerVariable('rootURL', 'http://.local'),
  NONCE: getServerVariable('nonce', ''),
  ACTION: getServerVariable('action', ''),
  ROUTE_PREFIX: getServerVariable('routePrefix', 'bit_fm_'),
  USERS: getServerVariable('users', []),
  BANNER: getServerVariable('adBanner', null),
  SYS_INFO: getServerVariable('sys_info', null),
  THEMES: getThemes(),
  THEME: getOptionVariable('theme', 'default'),
  LANG: getOptionVariable('lang', 'en'),
  ViewType: getOptionVariable('defaultView', 'icons')
}

export default config
