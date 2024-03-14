import { useEffect, useRef, useState } from 'react'

import $finder, { $finderCurrentPath, $finderViewType } from '@common/globalStates/$finder'
import config from '@config/config'
import LucideIcn from '@icons/LucideIcn'
import useUpdateViewType from '@pages/Settings/data/useUpdateViewType'
import { Breadcrumb, Button, Flex, Image, Space, Spin } from 'antd'
import { type FinderInstance } from 'elfinder'
import { useAtom } from 'jotai'

import configureElFinder from './helpers/configureElFinder'
import initThemeChangeHandler from './helpers/initThemeChangeHandler'

export default function Root() {
  const finderRef = useRef<HTMLDivElement>(null)
  const [isOpening, setIsOpening] = useState(false)
  const [elfinder, setFinder] = useAtom($finder)
  const [currentPath, setFinderCurrentPath] = useAtom($finderCurrentPath)
  const [viewType, setFinderViewType] = useAtom($finderViewType)

  const { toggleViewType } = useUpdateViewType()

  const generateFullPath = (finder: FinderInstance) => {
    const parents = finder.parents(finder.cwd().hash)
    const breadcrumbItems = parents.map((hash: string) => {
      const fileObj = finder.file(hash)
      const fileName = fileObj.i18n ?? fileObj.name
      return { title: fileName }
    })
    setFinderCurrentPath(breadcrumbItems)
  }

  const changeViewState = (finder: FinderInstance) => {
    setFinderViewType(finder?.viewType)
  }

  const handleToggleViewType = () => {
    elfinder?.exec('view', [], { _userAction: true, _currentType: 'toolbar' })
    toggleViewType(viewType === 'list' ? 'icons' : 'list')
  }

  const openSettings = () => {
    elfinder?.exec('preference', [], { _userAction: true, _currentType: 'toolbar' })
  }

  useEffect(() => {
    if (finderRef) {
      const finder = configureElFinder(finderRef)
      setFinder(finder)
      initThemeChangeHandler(finderRef)

      finder.bind('open searchend parents', () => {
        generateFullPath(finder)
      })

      finder.bind('open', () => {
        setIsOpening(true)
      })

      finder.bind('opendone', () => {
        setIsOpening(false)
      })

      finder.bind('viewchange', () => {
        changeViewState(finder)
      })
    }
    return () => {
      /* if (finder) {
        finder?.destroy()
      } */
      setFinder({} as FinderInstance)
    }
  }, [])
  console.log('isOpening', isOpening)
  return (
    <>
      <div
        style={{
          position: 'relative',
          top: '50%',
          left: '50%',
          zIndex: 1,
          display: isOpening ? 'flex' : 'none',
          width: 'max-content'
        }}
      >
        <Spin size="large" />
      </div>
      <Flex style={{ paddingBottom: 15 }}>
        <Flex
          style={{
            flexDirection: 'column',
            width: `${config.BANNER !== null ? '50%' : '100%'}`,
            justifyContent: 'center',
            paddingInline: 10
          }}
        >
          <Breadcrumb items={currentPath} />
          <Flex style={{ justifyContent: 'space-between' }}>
            <Flex style={{ gap: 15 }}>
              <Button
                type="primary"
                icon={<LucideIcn name="UploadIcon" />}
                onClick={() =>
                  elfinder?.exec('upload', [], { _userAction: true, _currentType: 'toolbar' })
                }
              >
                Upload
              </Button>
              <Button
                ghost
                icon={<LucideIcn name="FolderPlusIcon" />}
                onClick={() =>
                  elfinder?.exec('mkdir', [], { _userAction: true, _currentType: 'toolbar' })
                }
              >
                Create Folder
              </Button>
            </Flex>
            <Flex style={{ gap: 15 }}>
              <Button icon={<LucideIcn name="Settings" />} onClick={openSettings} />
              <Button
                icon={<LucideIcn name="LayoutGridIcon" />}
                onClick={handleToggleViewType}
                disabled={viewType === 'icons'}
              />
              <Button
                icon={<LucideIcn name="LayoutList" />}
                onClick={handleToggleViewType}
                disabled={viewType === 'list'}
              />
            </Flex>
          </Flex>
        </Flex>
        {config.BANNER !== null && (
          <Space style={{ width: '50%' }}>
            <Image alt="adBanner" src={config.BANNER.img} preview={false} />
          </Space>
        )}
      </Flex>
      <div id="file-manager" ref={finderRef} style={{ height: '100%' }} />
    </>
  )
}
