import type IconTypes from './IconTypes'

export default function LogoIcn({ size = '1em', stroke = 6, className }: IconTypes) {
  return (
    <svg height={size} width={size} viewBox="0 0 139 139" className={className}>
      <path d="M69.5 0C23.2 0 0 23.2 0 69.5S23.2 139 69.5 139 139 116 139 69.5 115.8 0 69.5 0" />
      <circle
        cx="96.7"
        cy="31.7"
        r="8.8"
        fill="none"
        stroke="#fff"
        strokeWidth={stroke}
        strokeMiterlimit={10}
      />
      <path
        fill="none"
        stroke="#fff"
        strokeMiterlimit="10"
        strokeWidth={stroke && stroke + 3}
        d="M91.3 27.4A52 52 0 0 0 76.8 24a32.5 32.5 0 0 0-33.4 14.5c-4 7-4.7 14.3 6.8 32.6 5 7.7 12.5 13.6 16.5 22 3.1 6.5 4.5 17.9-2.5 22.5-2.5 1.7-5.7 1.6-8.6.8-5.7-1.7-11-7.4-11.5-14.5-.7-10.5 8.2-37.3 45.3-37.4"
      />
      <circle
        cx="95.7"
        cy="65.9"
        r="8.8"
        fill="none"
        stroke="#fff"
        strokeWidth={stroke}
        strokeMiterlimit={10}
      />
    </svg>
  )
}
