import { useRef, useState } from 'react'

import CopyIcn from '@icons/CopyIcn'
import Tip from '@utilities/Tip'

import css from './InputGroup.module.css'

export default function InputGroup() {
  const [copySuccess, setCopySuccess] = useState<boolean>(false)
  const refInput = useRef<HTMLInputElement>(null)

  const copyUrl = () => {
    navigator.clipboard.writeText(refInput?.current?.value || '')
    setCopySuccess(true)
  }

  return (
    <div className={css.InputGroup}>
      <select className={css.selectInput}>
        <option value="post">POST</option>
        <option value="get">GET</option>
        <option value="put">PUT</option>
      </select>
      <input ref={refInput} type="text" value="www.bitapps.pro" className={css.textInput} readOnly />
      <Tip isArrow={false}>
        <button
          type="button"
          className={`${copySuccess && css.copySuccessBtn} ${css.InputGroupBtn}`}
          onClick={copyUrl}
          aria-label="copy button"
        >
          <CopyIcn size={22} stroke={0} />
        </button>
        <span>Copy Url</span>
      </Tip>
    </div>
  )
}
