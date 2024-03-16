import AntIconWrapper from './AntIconWrapper'

type RouterIcnProps = {
  size?: number
  className?: string
}

export default function RouterIcn({ size = 42, className }: RouterIcnProps) {
  return (
    <AntIconWrapper>
      <svg
        width={size}
        height={size}
        viewBox="0 0 47 40"
        fill="none"
        stroke="currentColor"
        strokeLinecap="round"
        className={className}
      >
        <path
          d="M3 20H5.34623C9.13708 20 12.7459 21.6258 15.2576 24.4651V24.4651C19.4961 29.2565 25.586 32 31.983 32H39"
          strokeWidth="5"
        />
        <path
          d="M3 19H5.61958C9.25455 19 12.7334 17.5226 15.2576 14.907V14.907C19.5172 10.4931 25.3877 8 31.5218 8H39"
          strokeWidth="5"
        />
        <path d="M38.0311 26.0737L43.837 31.5604L38.3573 37.3358" strokeWidth="4" />
        <path d="M38.0311 2.14453L43.837 7.63119L38.3573 13.4066" strokeWidth="4" />
      </svg>
    </AntIconWrapper>
  )
}
