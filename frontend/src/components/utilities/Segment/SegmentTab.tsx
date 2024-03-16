/* eslint-disable react/no-unused-prop-types */
import { type ReactNode } from 'react'

import cls from './Segment.module.css'

export interface SegmentTabPropType {
  children: ReactNode | ReactNode[]
  style?: React.CSSProperties | undefined
  value: string | number
  tip?: string | ReactNode
  disabled?: boolean
}

export default function SegmentTab({ children, style = undefined }: SegmentTabPropType) {
  return (
    <div style={style} className={cls.controlItemText}>
      {children}
    </div>
  )
}
