import request from "@common/helpers/request";
import { RefObject } from "react";

export default function initThemeChangeHandler(finderRef: RefObject<HTMLDivElement>) {

  finderRef.current.addEventListener('change', function (e: Event) {
    const target = e.target as HTMLSelectElement

    if (target && target.nodeName !== 'SELECT') {
      return
    }

    const currentTarget = e.currentTarget
    console.log('target.', target[0].className,
      target
      && target.nodeName === 'SELECT'
      && target?.className?.indexOf('elfinder-tabstop') !== -1,
      // currentTarget[0].className
    )
    if (target?.className?.indexOf('elfinder-tabstop') !== -1
      && target[0]?.className.indexOf('elfinder-theme-option') !== -1
    ) {
      request('theme', { theme: target.value })
        .then(response => {
          console.log('response', response)
          if (response.code === 'SUCCESS') {
            location.reload()
          }
        })
    }
  });
}
