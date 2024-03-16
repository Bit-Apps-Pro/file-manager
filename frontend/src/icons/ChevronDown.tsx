import AntIconWrapper from './AntIconWrapper'
import type IconTypes from './IconTypes'

export default function ChevronDown({ size = '1em', stroke = 2, className }: IconTypes) {
  return (
    <AntIconWrapper>
      <svg
        width={size}
        height={size}
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        strokeWidth={stroke}
        strokeLinecap="round"
        strokeLinejoin="round"
        className={className}
      >
        <polyline points="6 9 12 15 18 9" />
      </svg>
    </AntIconWrapper>
  )
}
