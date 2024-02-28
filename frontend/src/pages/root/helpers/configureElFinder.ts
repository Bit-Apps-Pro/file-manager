import config from '@config/config'

export default function configureElFinder() {
  const { AJAX_URL, NONCE } = config
  const finder = jQuery('#file-manager')
    .elfinder({
      url: AJAX_URL,
      customData: {
        action: fm.action,
        nonce: NONCE
      },
      themes: fm.options.themes,
      theme: fm.options.theme,
      cssAutoLoad: fm.options.cssAutoLoad,
      contextmenu: fm.options.contextmenu,
      lang: fm.options.lang,
      requestType: fm.options.requestType,
      width: fm.options.width,
      height: fm.options.height,
      commandsOptions: fm.options.commandsOptions,
      rememberLastDir: fm.options.rememberLastDir,
      reloadClearHistory: fm.options.reloadClearHistory,
      defaultView: fm.options.defaultView,
      ui: fm.options.ui,
      sortOrder: fm.options.sortOrder,
      sortStickFolders: fm.options.sortStickFolders,
      dragUploadAllow: fm.options.dragUploadAllow,
      fileModeStyle: fm.options.fileModeStyle,
      resizable: fm.options.resizable
    })
    .elfinder('instance')

  return finder
}
