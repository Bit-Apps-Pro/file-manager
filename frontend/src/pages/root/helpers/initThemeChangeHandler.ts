import { type RefObject } from 'react'

import request from '@common/helpers/request'

export default function initThemeChangeHandler(finderRef: RefObject<HTMLDivElement>) {
  finderRef.current?.addEventListener('change', (e: Event) => {
    const target = e.target as HTMLSelectElement

    if (target && target.nodeName !== 'SELECT') {
      return
    }

    if (
      target?.className?.indexOf('elfinder-tabstop') !== -1 &&
      target[0]?.className.indexOf('elfinder-theme-option') !== -1
    ) {
      request({ action: 'theme/update', data: { theme: target.value } }).then(response => {
        if (response.code === 'SUCCESS') {
          window.location.reload()
        }
      })
    }
  })
}
