import AntIconWrapper from './AntIconWrapper'
import type IconTypes from './IconTypes'

export default function SearchIcon({ size = '1em', stroke = 4, className }: IconTypes) {
  return (
    <AntIconWrapper>
      <svg
        data-testid="searchIcon"
        width={size}
        height={size}
        viewBox="0 0 24 24"
        strokeWidth={stroke}
        stroke="currentColor"
        fill="none"
        strokeLinecap="round"
        className={className}
      >
        <circle cx="11" cy="11" r="8" />
        <line x1="21" y1="21" x2="16.65" y2="16.65" />
      </svg>
    </AntIconWrapper>
  )
}
