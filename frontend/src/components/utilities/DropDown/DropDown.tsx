import { type ReactNode } from 'react'
import { useState } from 'react'

import Tippy from '@tippyjs/react'
import { roundArrow } from 'tippy.js'
// import 'tippy.js/dist/tippy.css'
import 'tippy.js/animations/shift-away.css'
import 'tippy.js/dist/svg-arrow.css'

import css from './DropDown.module.css'

// import './static/TippyLightTheme.css'

interface DropdownPropsTypes {
  children: ReactNode[]
  btnClassName?: string
}

export default function DropDown({
  children,
  btnClassName = css.dropDownBtn
}: DropdownPropsTypes): JSX.Element {
  const [visibleDropDown, setVisibleDropDown] = useState(false)

  return (
    <Tippy
      content={children[1]}
      visible={visibleDropDown}
      placement="bottom"
      allowHTML
      arrow={roundArrow}
      animation="shift-away"
      inertia
      interactive
      theme="light"
      // className="dropDownTippy"
      appendTo="parent"
      onClickOutside={() => setVisibleDropDown(false)}
      css={({ token }) => ({
        backgroundColor: token.colorBgElevated,
        borderRadius: token.borderRadius + 1,
        boxShadow: token.boxShadowSecondary,
        '& .tippy-svg-arrow': {
          fill: token.colorBgContainer,
          stroke: token.controlOutline
        }
      })}
    >
      <button onClick={() => setVisibleDropDown(prv => !prv)} type="button" className={btnClassName}>
        {children[0]}
      </button>
    </Tippy>
  )
}
