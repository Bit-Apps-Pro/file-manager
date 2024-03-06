import config, { getOptionVariable } from '@config/config'
import { type FinderInstance } from 'elfinder'

export default function configureElFinder(): FinderInstance {
  const { AJAX_URL, NONCE, LANG, THEME, ViewType, ACTION } = config
  // @ts-ignore
  const finder = jQuery('#file-manager').elfinder({
    url: AJAX_URL,
    customData: {
      action: ACTION,
      nonce: NONCE
    },
    theme: THEME,
    lang: LANG,
    cssAutoLoad: getOptionVariable('cssAutoLoad'),
    contextmenu: getOptionVariable('contextmenu'),
    requestType: getOptionVariable('requestType'),
    themes: getOptionVariable('themes', []),
    width: getOptionVariable('width'),
    height: getOptionVariable('height'),
    commandsOptions: getOptionVariable('commandsOptions'),
    disabled: getOptionVariable('disabled'),
    rememberLastDir: getOptionVariable('rememberLastDir'),
    reloadClearHistory: getOptionVariable('reloadClearHistory'),
    defaultView: getOptionVariable('defaultView'),
    ui: getOptionVariable('ui'),
    uiOptions: {
      toolbar: [
        ['back', 'forward'],
        ['reload'],
        ['home', 'up'],
        ['mkfile'],
        ['open', 'download', 'getfile'],
        ['info', 'sort'],
        ['quicklook'],
        ['copy', 'cut', 'paste'],
        ['rm'],
        ['duplicate', 'rename', 'edit', 'resize'],
        ['extract', 'archive'],
        ['fullscreen'],
        ['search']
      ]
    },
    sortOrder: getOptionVariable('sortOrder'),
    sortStickFolders: getOptionVariable('sortStickFolders'),
    dragUploadAllow: getOptionVariable('dragUploadAllow'),
    fileModeStyle: getOptionVariable('fileModeStyle'),
    resizable: getOptionVariable('resizable'),
    handlers: {
      dblclick() {
        const disabled: Array<string> = getOptionVariable('disabled')
        if (
          disabled?.includes('dblclick') ||
          disabled?.includes('download') ||
          disabled?.includes('get')
        ) {
          return false
        }
      }
    }
  })[0].elfinder

  finder.storage('lang', LANG)
  finder.storage('theme', THEME)
  finder.storage('view', ViewType)
  return finder
}
