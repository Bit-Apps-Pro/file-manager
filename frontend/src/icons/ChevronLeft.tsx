import AntIconWrapper from './AntIconWrapper'
import type IconTypes from './IconTypes'

export default function ChevronLeft({ size = '1em', stroke = 4, className }: IconTypes) {
  return (
    <AntIconWrapper>
      <svg
        className={className}
        width={size}
        height={size}
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        strokeWidth={stroke}
        strokeLinecap="round"
        strokeLinejoin="round"
      >
        <polyline points="15 18 9 12 15 6" />
      </svg>
    </AntIconWrapper>
  )
}
