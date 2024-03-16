import AntIconWrapper from './AntIconWrapper'
import type IconTypes from './IconTypes'

export default function Dots({ size = '1em', stroke = 4, className }: IconTypes) {
  return (
    <AntIconWrapper>
      <svg viewBox="0 0 24 24" className={className} width={size} height={size} strokeWidth={stroke}>
        <g fill="none" stroke="currentColor" strokeLinecap="round" strokeLinejoin="round">
          <circle cx="5" cy="12" r="1" />
          <circle cx="12" cy="12" r="1" />
          <circle cx="19" cy="12" r="1" />
        </g>
      </svg>
    </AntIconWrapper>
  )
}
