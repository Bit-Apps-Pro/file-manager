import AntIconWrapper from './AntIconWrapper'
import type IconTypes from './IconTypes'

export default function SupportIcn({ size = '1em', stroke = 2, className }: IconTypes) {
  return (
    <AntIconWrapper>
      <svg
        className={className}
        width={size}
        height={size}
        viewBox="0 0 24 24"
        xmlns="http://www.w3.org/2000/svg"
      >
        <g
          fill="none"
          stroke="currentColor"
          strokeLinecap="round"
          strokeLinejoin="round"
          strokeWidth={stroke}
        >
          <circle cx="12" cy="12" r="9" />
          <path d="m18 6l-3.525 3.525M6 18l3.525-3.525M6 6l3.525 3.525M18 18l-3.525-3.525m-4.95 0c-1.348-1.348-1.348-3.601 0-4.95m0 4.95c1.348 1.348 3.601 1.348 4.95 0m0 0c1.348-1.348 1.348-3.601 0-4.95m0 0c-1.348-1.348-3.601-1.348-4.95 0" />
        </g>
      </svg>
    </AntIconWrapper>
  )
}
