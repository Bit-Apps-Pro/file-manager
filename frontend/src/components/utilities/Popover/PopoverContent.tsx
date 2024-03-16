import { type CSSProperties, type ReactNode } from 'react'

interface PopoverContentPropsType {
  children: ReactNode | ReactNode[]
  className?: string | undefined
  style?: CSSProperties | undefined
}

export default function PopoverContent({ children, className, style }: PopoverContentPropsType) {
  return (
    <div className={`${className} p-1`} style={style}>
      {children}
    </div>
  )
}
