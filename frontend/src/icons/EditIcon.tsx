import AntIconWrapper from './AntIconWrapper'
import type IconTypes from './IconTypes'

export default function EditIcon({ size = '1em', stroke = 2, className }: IconTypes) {
  return (
    <AntIconWrapper>
      <svg
        width={size}
        height={size}
        viewBox="0 0 24 24"
        fill="none"
        strokeMiterlimit="10"
        strokeLinecap="round"
        strokeLinejoin="round"
        strokeWidth={stroke}
        stroke="currentColor"
        className={className}
      >
        <path d="m13.26 3.6-8.21 8.69c-.31.33-.61.98-.67 1.43l-.37 3.24c-.13 1.17.71 1.97 1.87 1.77l3.22-.55c.45-.08 1.08-.41 1.39-.75l8.21-8.69c1.42-1.5 2.06-3.21-.15-5.3-2.2-2.07-3.87-1.34-5.29.16Z" />
        <path d="M11.89 5.05a6.126 6.126 0 0 0 5.45 5.15M3 22h18" />
      </svg>
    </AntIconWrapper>
  )
}
