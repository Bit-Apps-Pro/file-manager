import { type ReactElement } from 'react'
import { Children } from 'react'

interface PopoverTriggerPropsType {
  children: JSX.Element
}

export default function PopoverTrigger({ children }: PopoverTriggerPropsType): ReactElement {
  const singleChildren = Children.only(children)
  return singleChildren
}
