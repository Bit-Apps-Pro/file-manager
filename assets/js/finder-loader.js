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
    resizable: fm.options.resizable
  })

  jQuery('#file-manager').on('change', 'select.elfinder-tabstop', function (e) {
    if (
      e.currentTarget[0] &&
      e.currentTarget[0].className &&
      e.currentTarget[0].className.indexOf('elfinder-theme-option') !== -1
    ) {
      jQuery
        .ajax(ajaxurl, {
          method: 'POST',
          data: {
            action: 'bit_fm_theme',
            nonce: fm.nonce,
            theme: e.currentTarget.value
          }
        })
        .done(() => location.reload())
    }
  })
})
