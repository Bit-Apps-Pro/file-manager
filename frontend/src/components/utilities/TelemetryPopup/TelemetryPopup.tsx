/* eslint-disable no-nested-ternary */
import type React from 'react'
import { useState } from 'react'

import { Modal as AntModal, Button, Checkbox, Steps, message } from 'antd'

import cls from './TelemetryPopup.module.css'

type TelemetryPopupProps = {
  handleSubmit: (param: number) => void
  isPopupOpen: boolean
  handlePopupSkip: (e: React.MouseEvent<HTMLElement>) => void
}

const steps = [
  {
    content: (
      <div className={cls.modalContent}>
        <span className={cls.improvementsTitle}>New Improvements</span>
        <div className={cls.improvements}>
          <span>1.SMTP debug enable/disable option added</span>
          <span>2.UI modified and minor issue fixed</span>
        </div>
        <h3 style={{ marginTop: '20px' }}>Make Bit File Manager Better</h3>
        <p>
          Accept and continue to share non-sensitive diagnostic data to help us improve your experience,
          <a href="https://bitapps.pro/terms-of-service/"> Terms & Conditions.</a>
        </p>
        <Checkbox>Install Bit Form to create multi step form</Checkbox>
      </div>
    )
  },
  {
    content: 'Second-content'
  },
  {
    content: 'Last-content'
  }
]

function TelemetryPopup({ isPopupOpen, handleSubmit, handlePopupSkip }: TelemetryPopupProps) {
  const [current, setCurrent] = useState(0)

  const next = () => {
    setCurrent(current + 1)
  }

  const prev = () => {
    setCurrent(current - 1)
  }

  const footerBtnStyle: React.CSSProperties = {
    display: 'flex',
    justifyContent: 'space-between',
    flexFlow: current !== 2 ? 'row-reverse' : 'initial',
    marginTop: '30px'
  }

  return (
    <AntModal
      title={
        <div style={{ textAlign: 'center', fontSize: '20px', marginBottom: '20px' }}>
          {current === 0 ? 'Bit Social Release' : current === 1 ? 'Bit File Manager Updates' : 'Final'}
        </div>
      }
      open={isPopupOpen}
      closable={false}
      width="400px"
      centered
      className="telemetry-popup"
      footer={null}
    >
      <>
        <Steps current={current} items={steps} />
        <div className={cls.popupContent}>{steps[current].content}</div>
        <div style={footerBtnStyle}>
          {current < steps.length - 1 && (
            <Button type="primary" onClick={() => next()}>
              Next
            </Button>
          )}
          {current > 0 && (
            <Button className={cls.skipBtn} onClick={() => prev()}>
              Skip
            </Button>
          )}
          {current === steps.length - 1 && (
            <Button type="primary" onClick={() => message.success('Processing complete!')}>
              Accept & Complete
            </Button>
          )}
        </div>
      </>
    </AntModal>
  )
}

export default TelemetryPopup
