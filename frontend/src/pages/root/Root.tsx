import config from '@config/config'
import { useEffect, useRef } from 'react'
import configureElFinder from './helpers/configureElFinder'
import initThemeChangeHandler from './helpers/initThemeChangeHandler'


export default function Root() {
  const finderRef = useRef<HTMLDivElement>(null)
  useEffect(() => {
    const finder = configureElFinder()
    initThemeChangeHandler(finderRef)
    return () => {
      if (finder[0]?.elfinder) {
        finder[0].elfinder.destroy()
      }
    }
  }, [])

  return (
     <div id='file-manager' ref={finderRef}/>
  )
}
