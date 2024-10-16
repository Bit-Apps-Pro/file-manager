jQuery(document).ready(function () {
  var $ = jQuery
  const finder = jQuery('#file-manager').elfinder({
    url: fm.ajaxURL,
    themes: fm.options.themes,
    theme: fm.options.theme,
    cssAutoLoad: fm.options.cssAutoLoad,
    contextmenu: fm.options.contextmenu,
    customData: {
      action: fm.action,
      nonce: fm.nonce
    },
    lang: fm.options.lang,
    requestType: fm.options.requestType,
    width: fm.options.width,
    height: fm.options.height,
    commandsOptions: fm.options.commandsOptions,
    commands: fm.options.commands,
    disabled: fm.options.disabled,
    rememberLastDir: fm.options.rememberLastDir,
    reloadClearHistory: fm.options.reloadClearHistory,
    defaultView: fm.options.defaultView,
    ui: fm.options.ui,
    sortOrder: fm.options.sortOrder,
    sortStickFolders: fm.options.sortStickFolders,
    dragUploadAllow: fm.options.dragUploadAllow,
    fileModeStyle: fm.options.fileModeStyle,
    resizable: fm.options.resizable,
    handlers: {
      dblclick() {
        const disabled = fm?.options?.disabled || []
        if (
          disabled?.includes('dblclick') ||
          disabled?.includes('download') ||
          disabled?.includes('get')
        ) {
          return false
        }
      }
    }

  })
})
