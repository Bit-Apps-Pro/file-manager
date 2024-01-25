import { type ReactElement } from 'react'

import Tippy from '@tippyjs/react'
import { roundArrow } from 'tippy.js'
import 'tippy.js/animations/shift-away.css'
import 'tippy.js/dist/svg-arrow.css'

// import 'tippy.js/dist/tippy.css'
// import './static/TippyLightTheme.css'

interface PopoverPropsTypes {
  children: [ReactElement, ReactElement]
  isOpen: boolean
  // setIsOpen?: Dispatch<SetStateAction<boolean>>
  placement?: 'right' | 'left' | 'bottom' | 'top'
  onClickOutside?: () => void
}

export default function Popover({
  children,
  isOpen,
  placement = 'right',
  onClickOutside
}: PopoverPropsTypes): JSX.Element {
  return (
    <Tippy
      visible={isOpen}
      content={children[1]}
      placement={placement}
      allowHTML
      arrow={roundArrow}
      animation="shift-away"
      inertia
      interactive
      theme="light"
      css={({ token }) => ({
        backgroundColor: token.colorBgElevated,
        borderRadius: token.borderRadius + 1,
        boxShadow: token.boxShadowSecondary,
        '& .tippy-svg-arrow': {
          fill: token.colorBgContainer,
          stroke: token.controlOutline
        }
      })}
      appendTo="parent"
      onClickOutside={onClickOutside}
    >
      <div className="d-ib">{children[0]}</div>
    </Tippy>
  )
}
