// import 'tippy.js/dist/tippy.css'
import { type TippyProps } from '@tippyjs/react'
import Tippy from '@tippyjs/react'
import { roundArrow } from 'tippy.js'
import 'tippy.js/animations/shift-away.css'

// import 'tippy.js/dist/svg-arrow.css'
import './static/TipLightTheme.css'

interface TipPropsTypes {
  isArrow?: boolean
  target?: TippyProps['singleton']
  children: JSX.Element[]
}

export default function Tip({ isArrow = true, target = undefined, children }: TipPropsTypes) {
  return (
    <Tippy
      content={children[1]}
      placement="bottom"
      allowHTML
      arrow={isArrow && roundArrow}
      animation="shift-away"
      inertia
      singleton={target}
      // theme="light"
      className="bf-tooltip"
      interactive
    >
      {children[0]}
    </Tippy>
  )
}
