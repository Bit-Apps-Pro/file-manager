import type IconTypes from './IconTypes'

export default function BitPiLogo({ size = '1em', className }: IconTypes) {
  return (
    <svg viewBox="0 0 104 104" className={className}>
      <g filter="url(#a)">
        <rect width="100" height="100" x="2" fill="#d9d9d9" rx="24.5" />
        <rect width="100" height="100" x="2" fill="url(#b)" rx="24.5" />
        <rect width="100" height="100" x="2" fill="url(#c)" rx="24.5" />
        <g filter="url(#d)">
          <path
            fill="#fff"
            fillRule="evenodd"
            d="m32 36 20-17q9-6 16 2 6 7-2 15l-2 2 4 4q6 7-1 15l-3 3 4 4q6 7-1 15-9 6-16-1-3-4-2-9-5 0-8-4l-3-9q-5-1-8-4-5-9 2-16m4 5q-4 4-1 7 4 3 7 1l20-17q4-4 1-7-4-4-7-1zm10 13q-3 4-1 7 4 3 7 1l11-9q3-3 1-7-4-4-7-1zm10 20q-3-4 1-7t6 0q3 5 0 7-3 3-7 0"
            clipRule="evenodd"
          />
        </g>
      </g>
      <defs>
        <linearGradient id="b" x1="52" x2="52" y1="0" y2="100" gradientUnits="userSpaceOnUse">
          <stop stopColor="#010b2c" />
          <stop offset="1" stopColor="#1c2233" />
        </linearGradient>
        <linearGradient id="c" x1="52" x2="52" y1="0" y2="100" gradientUnits="userSpaceOnUse">
          <stop stopColor="#ff6b28" />
          <stop offset="1" stopColor="#e74700" />
        </linearGradient>
        <filter
          id="a"
          width="103"
          height="104"
          x="1"
          y="0"
          colorInterpolationFilters="sRGB"
          filterUnits="userSpaceOnUse"
        >
          <feFlood floodOpacity="0" result="BackgroundImageFix" />
          <feColorMatrix
            in="SourceAlpha"
            result="hardAlpha"
            values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"
          />
          <feOffset dy="2" />
          <feGaussianBlur stdDeviation=".8" />
          <feComposite in2="hardAlpha" operator="out" />
          <feColorMatrix values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.25 0" />
          <feBlend in2="BackgroundImageFix" result="effect1_dropShadow_674_2" />
          <feBlend in="SourceGraphic" in2="effect1_dropShadow_674_2" result="shape" />
          <feColorMatrix
            in="SourceAlpha"
            result="hardAlpha"
            values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"
          />
          <feOffset dy="4" />
          <feGaussianBlur stdDeviation="3.5" />
          <feComposite in2="hardAlpha" k2="-1" k3="1" operator="arithmetic" />
          <feColorMatrix values="0 0 0 0 1 0 0 0 0 1 0 0 0 0 1 0 0 0 0.3 0" />
          <feBlend in2="shape" result="effect2_innerShadow_674_2" />
        </filter>
        <filter
          id="d"
          width="44.9"
          height="68"
          x="27"
          y="17"
          colorInterpolationFilters="sRGB"
          filterUnits="userSpaceOnUse"
        >
          <feFlood floodOpacity="0" result="BackgroundImageFix" />
          <feColorMatrix
            in="SourceAlpha"
            result="hardAlpha"
            values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"
          />
          <feOffset dy="2" />
          <feGaussianBlur stdDeviation=".5" />
          <feComposite in2="hardAlpha" operator="out" />
          <feColorMatrix values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.25 0" />
          <feBlend in2="BackgroundImageFix" result="effect1_dropShadow_674_2" />
          <feBlend in="SourceGraphic" in2="effect1_dropShadow_674_2" result="shape" />
        </filter>
      </defs>
    </svg>
  )
}
