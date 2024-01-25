import AntIconWrapper from './AntIconWrapper'
import type IconTypes from './IconTypes'

export default function DotsVertical({ size = undefined, stroke = 2, className }: IconTypes) {
  return (
    <AntIconWrapper>
      <svg viewBox="0 0 24 24" width={size} height={size} className={className}>
        <g
          fill="none"
          stroke="currentColor"
          strokeWidth={stroke}
          strokeLinecap="round"
          strokeLinejoin="round"
        >
          <circle cx="12" cy="12" r="1" />
          <circle cx="12" cy="19" r="1" />
          <circle cx="12" cy="5" r="1" />
        </g>
      </svg>
    </AntIconWrapper>
  )
}
