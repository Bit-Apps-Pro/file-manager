import AntIconWrapper from './AntIconWrapper'
import type IconTypes from './IconTypes'

export default function Plus({ size = '1em', stroke = 4, className }: IconTypes) {
  return (
    <AntIconWrapper>
      <svg
        width={size}
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        strokeWidth={stroke}
        strokeLinecap="round"
        strokeLinejoin="round"
        className={`${'svgIcn'} ${className}`}
      >
        <line x1="12" y1="5" x2="12" y2="19" />
        <line x1="5" y1="12" x2="19" y2="12" />
      </svg>
    </AntIconWrapper>
  )
}
