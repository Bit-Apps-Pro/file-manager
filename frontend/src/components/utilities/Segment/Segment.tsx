import { useEffect, useId, useState } from 'react'

import { clsx } from '@common/helpers/globalHelpers'
import { motion } from 'framer-motion'
import { type Placement } from 'tippy.js'

import cls from './Segment.module.css'
import SegmentTip from './SegmentTip'

const spring = {
  type: 'spring',
  stiffness: 450,
  damping: 30
}

export interface SegmentType {
  rounded?: boolean
  variant?: 'contained' | 'underline'
  color?: 'default' | 'primary'
  children: JSX.Element[]
  size?: 'sm' | 'md' | 'lg'
  onChange?: (value: string) => void
  style?: React.CSSProperties
  value?: string | number
  tipPlacement?: Placement
  className?: string
}

export default function Segment({
  rounded = false,
  variant = 'contained',
  color = 'default',
  size = 'md',
  onChange = undefined,
  style = undefined,
  value = undefined,
  className = undefined,
  tipPlacement = 'top',
  children
}: SegmentType) {
  const [currentValue, setCurrentValue] = useState<string>(value || children[0].props.value)
  const id = useId()
  const handleOnClick = (disabled: boolean, tabValue: string) => () =>
    !disabled && setCurrentValue(tabValue)

  useEffect(() => {
    onChange?.(currentValue)
  }, [currentValue])
  return (
    <div
      style={style}
      className={clsx([className, rounded && cls.rounded, cls[size], cls.controlContainer])}
    >
      {children?.map((item, i) => {
        const { tip, value: tabValue, disabled } = item?.props || {}
        return (
          <SegmentTip key={`${id}-seg-${i * 9}`} content={tip} placement={tipPlacement}>
            <div
              role="button"
              className={clsx([
                currentValue === tabValue && variant === 'contained' && cls[`${color}Active`],
                cls.controlItem
              ])}
              onKeyUp={handleOnClick(disabled, tabValue)}
              onClick={handleOnClick(disabled, tabValue)}
              tabIndex={i}
            >
              {currentValue === tabValue && (
                <motion.div
                  layout
                  transition={spring}
                  layoutId={id}
                  className={clsx([
                    rounded && cls.rounded,
                    cls[color],
                    (variant === 'contained' && cls.controlItemBgSolid) ||
                      (variant === 'underline' && cls.controlItemBgUnderline)
                  ])}
                />
              )}
              {item}
            </div>
          </SegmentTip>
        )
      })}
    </div>
  )
}
