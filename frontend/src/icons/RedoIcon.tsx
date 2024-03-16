import AntIconWrapper from './AntIconWrapper'
import type IconTypes from './IconTypes'

export default function RedoIcon({ size = '1em', stroke = 4, className }: IconTypes) {
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
        className={`${'svgIcn'} ${className}`}
      >
        <path strokeMiterlimit="10" d="M16.87 18.31h-8c-2.76 0-5-2.24-5-5s2.24-5 5-5h11" />
        <path d="M17.57 10.81l2.56-2.56-2.56-2.56" />
      </svg>
    </AntIconWrapper>
  )
}
