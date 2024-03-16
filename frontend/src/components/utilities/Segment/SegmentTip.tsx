import { type ReactElement, type ReactNode } from 'react'

import Tippy from '@tippyjs/react'
import { type Placement } from 'tippy.js'
import 'tippy.js/animations/shift-away.css'
import 'tippy.js/dist/tippy.css'

interface SegmentTipType {
  children: ReactElement<ReactNode>
  content: ReactNode | string
  placement: Placement
}
export default function SegmentTip({ children, content, placement }: SegmentTipType) {
  if (content) {
    return (
      <Tippy
        placement={placement}
        content={content}
        interactive
        animation="shift-away"
        inertia
        arrow={false}
      >
        {children}
      </Tippy>
    )
  }
  return children
}
