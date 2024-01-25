/* eslint-disable react/jsx-props-no-spreading */

/* eslint-disable import/no-extraneous-dependencies */
import { type MouseEvent } from 'react'
import FocusBounder from 'react-focus-bounder'

import { $paneContextMenu } from '@common/globalStates'
import css from '@features/FlowBuilder/internals/NodeEdgeContextMenu/NodeEdgeContextMenu.module.css'
import DeleteIcon from '@icons/DeleteIcon'
import { type Meta, type StoryFn } from '@storybook/react'
import DropDown from '@utilities/DropDown'
import { Button, Space } from 'antd'
import { useAtom } from 'jotai'
import { hideAll } from 'tippy.js'

import ContextMenu from './ContextMenu'

export default {
  title: 'Component/ContextMenu',
  component: ContextMenu
} as Meta<typeof ContextMenu>

// eslint-disable-next-line react/function-component-definition
const Template: StoryFn<typeof ContextMenu> = () => {
  const [contextMenuOpen, setContextMenuOpen] = useAtom($paneContextMenu)

  const onRightClickHandle = (e: MouseEvent) => {
    e.preventDefault()
    setContextMenuOpen({
      isOpen: true,
      clientX: e.pageX,
      clientY: e.pageY
    })
  }

  const closeContextMenu = () =>
    setContextMenuOpen({
      isOpen: false,
      clientX: 0,
      clientY: 0
    })

  return (
    <>
      {contextMenuOpen.isOpen && (
        <ContextMenu
          clientY={contextMenuOpen.clientY}
          clientX={contextMenuOpen.clientX}
          closeContextMenu={closeContextMenu}
        >
          <div className={css.nodeEdgeContextMenu}>
            <DropDown btnClassName={`${css.contextMenuItem} ${css.contextMenuDeleteBtn}`}>
              <>
                <DeleteIcon size={15} />
                Delete
              </>
              <FocusBounder>
                <div className={css.deleteConfirmationCard}>
                  <span className={css.deleteConfirmationHeaderIcon}>
                    <DeleteIcon size={18} stroke={1.5} />
                  </span>
                  <h2 className={css.deleteConfirmationTitle}>Are you sure delete this item?</h2>
                  <Space>
                    <Button onClick={() => hideAll()}>Cancel</Button>
                    <Button color="primary" onClick={() => {}}>
                      Delete
                    </Button>
                  </Space>
                </div>
              </FocusBounder>
            </DropDown>
          </div>
        </ContextMenu>
      )}
      <div
        style={{ height: 200, width: 400, backgroundColor: '#e5e5e5' }}
        className="flx jc-cen ai-cen"
        onContextMenu={onRightClickHandle}
      >
        Right Mouse Click Here
      </div>
    </>
  )
}

export const Canvas = Template.bind({})
