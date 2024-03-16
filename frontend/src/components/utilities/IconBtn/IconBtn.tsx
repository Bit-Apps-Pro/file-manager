import { type ComponentPropsWithRef } from 'react'
import { forwardRef } from 'react'

import cls from './IconBtn.module.css'

export interface IconBtnPropsType extends ComponentPropsWithRef<'button'> {
  variant?: 'solid' | 'outline' | 'ghost'
  color?: 'default' | 'primary'
  type?: 'button' | 'submit' | 'reset'
  size?: 'xs' | 'sm' | 'md' | 'lg'
  round?: boolean
}

const IconBtn = forwardRef<HTMLButtonElement, IconBtnPropsType>(
  (
    {
      variant = 'solid',
      color = 'default',
      type = 'button',
      className,
      size = 'md',
      round = false,
      ...props
    }: IconBtnPropsType,
    ref
  ) => (
    <button
      ref={ref}
      className={`${className} ${cls.btn} ${cls[color]} ${cls[variant]} ${cls[size]} 
      ${round && cls.round}`}
      type={type} // eslint-disable-line react/button-has-type
      {...props} // eslint-disable-line react/jsx-props-no-spreading
    />
  )
)

export default IconBtn
