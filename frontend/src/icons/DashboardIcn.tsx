import AntIconWrapper from './AntIconWrapper'
import type IconTypes from './IconTypes'

export default function DashboardIcn({ size = undefined, stroke = 2, className }: IconTypes) {
  return (
    <AntIconWrapper>
      <svg
        width={size}
        height={size}
        viewBox="0 0 21 21"
        fill="none"
        stroke="currentColor"
        strokeWidth={stroke}
        className={className}
      >
        <rect x="1" y="1" width="7.24" height="7.24" rx="2" />
        <rect x="12.76" y="1" width="7.24" height="19" rx="2" />
        <rect x="1" y="12.76" width="7.24" height="7.24" rx="2" />
      </svg>
    </AntIconWrapper>
  )
}
