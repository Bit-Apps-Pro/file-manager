import { useEffect, useRef } from 'react'

import $finder, { $finderCurrentPath } from '@common/globalStates/$finder'
import { Breadcrumb, Space } from 'antd'
import { useAtom, useSetAtom } from 'jotai'

import configureElFinder from './helpers/configureElFinder'
import initThemeChangeHandler from './helpers/initThemeChangeHandler'

export default function Root() {
  const finderRef = useRef<HTMLDivElement>(null)
  const setFinder = useSetAtom($finder)
  const [currentPath, setFinderCurrentPath] = useAtom($finderCurrentPath)

  const generateFullPath = finder => {
    const parents = finder.parents(finder.cwd().hash)
    const breadcrumbItems = parents.map(hash => {
      const fileObj = finder.file(hash)
      const fileName = fileObj.i18n ?? fileObj.name
      return { title: fileName }
    })
    console.log('finder.cwd()', finder.cwd(), { breadcrumbItems })
    setFinderCurrentPath(breadcrumbItems)
  }

  useEffect(() => {
    const finder = configureElFinder()
    setFinder(finder)
    initThemeChangeHandler(finderRef)
    finder.bind('open searchend parents', () => {
      generateFullPath(finder)
    })
    return () => {
      finder?.destroy()
      setFinder(null)
    }
  }, [])

  return (
    <>
      <Space>
        <Breadcrumb items={currentPath} />
      </Space>
      <div id="file-manager" ref={finderRef} />
    </>
  )
}
