import AntIconWrapper from './AntIconWrapper'
import type IconTypes from './IconTypes'

export default function MoonIcn({ size = '1em', stroke = 2, className }: IconTypes) {
  return (
    <AntIconWrapper>
      <svg width={size} height={size} viewBox="0 0 24 24" fill="none" className={className}>
        <path
          d="M2.03 12.42c.36 5.15 4.73 9.34 9.96 9.57 3.69.16 6.99-1.56 8.97-4.27.82-1.11.38-1.85-.99-1.6-.67.12-1.36.17-2.08.14C13 16.06 9 11.97 8.98 7.14c-.01-1.3.26-2.53.75-3.65.54-1.24-.11-1.83-1.36-1.3C4.41 3.86 1.7 7.85 2.03 12.42Z"
          stroke="currentColor"
          strokeWidth={stroke}
          strokeLinecap="round"
          strokeLinejoin="round"
        />
      </svg>
    </AntIconWrapper>
  )
}
