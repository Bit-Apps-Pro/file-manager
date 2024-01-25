import { type ReactNode } from 'react'
import { useEffect, useRef } from 'react'
import FocusBounder from 'react-focus-bounder'

import useOnClickOutside from '@common/hooks/useOnClickOutside'

import cls from './ContextMenu.module.css'

interface ContextMenuType {
  children: ReactNode
  clientY: number
  clientX: number
  closeContextMenu: () => void
}

export default function ContextMenu({ children, clientY, clientX, closeContextMenu }: ContextMenuType) {
  const ref = useRef<HTMLDivElement>(null)
  useOnClickOutside(ref, closeContextMenu)

  const onPressEscCloseContextMenu = (e: KeyboardEvent) => {
    if (e.key === 'Escape') closeContextMenu()
  }

  useEffect(() => {
    document.addEventListener('keyup', onPressEscCloseContextMenu)
    return () => {
      document.removeEventListener('keyup', onPressEscCloseContextMenu)
    }
  }, [])

  return (
    <div
      onContextMenuCapture={e => {
        e.preventDefault()
        return false
      }}
      className={cls.contextMenu}
      style={{ top: `${clientY}px`, left: `${clientX}px` }}
      ref={ref}
    >
      <FocusBounder>{children}</FocusBounder>
    </div>
  )
}
