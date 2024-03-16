import AntIconWrapper from './AntIconWrapper'
import type IconTypes from './IconTypes'

export default function CheckCircle({ size = '1em', stroke = 4, className }: IconTypes) {
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
        in="round"
      >
        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
        <polyline points="22 4 12 14.01 9 11.01" />
      </svg>
    </AntIconWrapper>
  )
}
