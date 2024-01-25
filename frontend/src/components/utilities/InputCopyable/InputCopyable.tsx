/* eslint-disable react/jsx-props-no-spreading */
import { useId } from 'react'

import useCopyToClipboard from '@common/hooks/useCopyToClipboard'
import LucideIcn from '@icons/LucideIcn'
import { Button, Input, type InputProps, Space, Tooltip, theme } from 'antd'

interface InputType extends InputProps {
  label?: string
}

const { Compact } = Space
const { useToken } = theme

export default function InputCopyable({ label, value, ...props }: InputType) {
  const { copy, copied } = useCopyToClipboard()
  const { token } = useToken()
  const id = useId()

  return (
    <>
      {label && (
        <label htmlFor={id} className="mb-1 d-ib" css={{ color: token.colorText }}>
          {label}
        </label>
      )}
      <Compact className="w-100">
        <Input readOnly id={id} value={value} {...props} />
        <Tooltip title={copied ? 'Copied' : 'Copy'}>
          <Button
            onClick={() => copy(String(value))}
            icon={<LucideIcn name={copied ? 'Check' : 'Copy'} />}
          />
        </Tooltip>
      </Compact>
    </>
  )
}
