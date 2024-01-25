import AntIconWrapper from './AntIconWrapper'
import type IconTypes from './IconTypes'

export default function CloseIcn({ size = '1em', stroke = 4, className }: IconTypes) {
  return (
    <AntIconWrapper>
      <svg
        className={className}
        width={size}
        height={size}
        viewBox="0 0 30 30"
        strokeLinecap="round"
        strokeLinejoin="round"
        strokeWidth={stroke}
        fill="none"
        stroke="currentColor"
      >
        <line x1="4" y1="3.88" x2="26" y2="26.12" />
        <line x1="26" y1="3.88" x2="4" y2="26.12" />
      </svg>
    </AntIconWrapper>
  )
}
