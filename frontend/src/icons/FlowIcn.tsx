import AntIconWrapper from './AntIconWrapper'
import type IconTypes from './IconTypes'

export default function FlowIcn({ size = '1em', stroke = 2, className }: IconTypes) {
  return (
    <AntIconWrapper>
      <svg width={size} height={size} viewBox="0 0 27 25" fill="none" className={className}>
        <circle cx="4" cy="4" r="3" stroke="currentColor" strokeWidth={stroke} />
        <circle cx="4" cy="20.4211" r="3" stroke="currentColor" strokeWidth={stroke} />
        <path
          d="M7.1579 4.76318V4.76318C9.10133 4.76318 10.9367 5.65741 12.1344 7.18787L13.6538 9.12928C15.3839 11.3399 18.0349 12.6316 20.8421 12.6316V12.6316"
          stroke="currentColor"
          strokeWidth="3"
        />
        <path
          d="M7.1579 20.5V20.5C9.14029 20.5 11.0148 19.5972 12.2507 18.0472L13.905 15.9726C15.5885 13.8613 18.1418 12.6316 20.8421 12.6316V12.6316"
          stroke="currentColor"
          strokeWidth="3"
        />
        <path
          d="M25.5 11.7656C26.1667 12.1505 26.1667 13.1128 25.5 13.4977L22.3421 15.3209C21.6754 15.7058 20.8421 15.2246 20.8421 14.4548L20.8421 10.8084C20.8421 10.0386 21.6754 9.55749 22.3421 9.94239L25.5 11.7656Z"
          fill="currentColor"
        />
      </svg>
    </AntIconWrapper>
  )
}
