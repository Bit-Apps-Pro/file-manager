// eslint-disable-next-line camelcase, @typescript-eslint/ban-ts-comment
// @ts-ignore
const serverVariables = typeof fm === 'undefined' ? {} : fm // eslint-disable-line camelcase,

export function getServerVariable(key: string, fallback?: unknown) {
  if (!(key in serverVariables) || !serverVariables[key]) {
    console.error('🚥🚥🚥 Missing server variable: ', key) // eslint-disable-line no-console
    return fallback
  }
  return serverVariables[key]
}

const config = {
  IS_DEV: true,
  PRODUCT_NAME: 'Bit File Manager',
  PLUGIN_SLUG: getServerVariable('pluginSlug'),
  AJAX_URL: getServerVariable('ajaxURL', 'http://.local/wp-admin/admin-ajax.php'),
  API_URL: getServerVariable('apiURL', {
    base: 'http://bitflow.test/wp-json/bit-flow/v1',
    separator: '?'
  }),
  ROOT_URL: getServerVariable('rootURL', 'http://.local'),
  NONCE: getServerVariable('nonce', ''),
  ROUTE_PREFIX: getServerVariable('routePrefix', 'bit_fm_')
}

export default config
