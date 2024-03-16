import AntIconWrapper from './AntIconWrapper'
import type IconTypes from './IconTypes'

export default function Wire({ size = '1em', stroke = 2, className }: IconTypes) {
  return (
    <AntIconWrapper>
      <svg
        height={size}
        width={size}
        viewBox="0 0 25 33.1"
        fill="none"
        stroke="currentColor"
        strokeWidth={stroke}
        strokeMiterlimit="10"
        className={className}
      >
        <path d="M20.5 7.7v13.8a4 4 0 0 1-4 4 4 4 0 0 1-4-4v-9.8a4 4 0 0 0-8 0v13.8" />
        <circle cx="20.5" cy="4.5" r="3.5" />
        <circle cx="4.5" cy="28.6" r="3.5" />
      </svg>
    </AntIconWrapper>
  )
}
