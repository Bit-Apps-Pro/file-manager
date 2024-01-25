// eslint-disable-next-line import/extensions, import/no-unresolved, import/no-absolute-path
import RefreshRuntime from '/@react-refresh'

RefreshRuntime.injectIntoGlobalHook(window)
window.$RefreshReg$ = () => {}
window.$RefreshSig$ = () => type => type
window.__vite_plugin_react_preamble_installed__ = true // eslint-disable-line no-underscore-dangle
