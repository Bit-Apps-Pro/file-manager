import { type CSSProperties } from 'react'

import * as icons from 'lucide-react'
import { type LucideIcon as LucideIconType } from 'lucide-react'

import AntIconWrapper from './AntIconWrapper'

interface LucideIcnPropsTypes {
  name: keyof typeof icons
  color?: string
  size?: number | string
  strokeWidth?: number
  style?: CSSProperties
}
export default function LucideIcn({
  name,
  color,
  size = '1em',
  strokeWidth,
  style
}: LucideIcnPropsTypes) {
  const LucideIcon = icons[name] as LucideIconType
  return (
    <AntIconWrapper>
      <LucideIcon color={color} size={size} strokeWidth={strokeWidth} style={style} />
    </AntIconWrapper>
  )
}
