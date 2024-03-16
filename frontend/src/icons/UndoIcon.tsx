import AntIconWrapper from './AntIconWrapper'
import type IconTypes from './IconTypes'

export default function UndoIcon({ size = '1em', stroke = 4, className }: IconTypes) {
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
        <path strokeMiterlimit="10" d="M7.13 18.31h8c2.76 0 5-2.24 5-5s-2.24-5-5-5h-11" />
        <path d="M6.43 10.81L3.87 8.25l2.56-2.56" />
      </svg>
    </AntIconWrapper>
  )
}
