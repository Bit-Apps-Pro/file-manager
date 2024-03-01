import { useEffect, useRef } from 'react'

import $finder from '@common/globalStates/$finder'
import { Space } from 'antd'
import { useAtom, useSetAtom } from 'jotai'

import configureElFinder from './helpers/configureElFinder'
import initThemeChangeHandler from './helpers/initThemeChangeHandler'

export default function Root() {
  const finderRef = useRef<HTMLDivElement>(null)
  const [el, setFinder] = useAtom($finder)
  useEffect(() => {
    const finder = configureElFinder()
    setFinder(finder)
    initThemeChangeHandler(finderRef)
    return () => {
      setFinder(null)
      // if (finder) {
      //   finder.destroy()
      // }
    }
  }, [])

  return <div id="file-manager" ref={finderRef} />
}
