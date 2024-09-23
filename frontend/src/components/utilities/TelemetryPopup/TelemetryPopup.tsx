/* eslint-disable no-nested-ternary */
import type React from 'react'
import { useState } from 'react'

import request from '@common/helpers/request'
import bitSocialBanner from '@resource/img/bit-social-release.png'
import { Modal as AntModal, Button, Popconfirm, Steps } from 'antd'

import cls from './TelemetryPopup.module.css'

type TelemetryPopupProps = {
  isTelemetryModalOpen: boolean
  setIsTelemetryModalOpen: (value: boolean) => void
}

function TelemetryPopup({ isTelemetryModalOpen, setIsTelemetryModalOpen }: TelemetryPopupProps) {
  const [current, setCurrent] = useState(0)
  const [isDataNoticeShow, setIsDataNoticeShow] = useState(false)
  const [isPopConfirmOpen, setIsPopConfirmOpen] = useState(false)
  // const handleStepToggle = () => {
  //   setIsBitFormInstallChecked(prev => !prev)
  // }

  const handleTelemetryAccess = () => {
    request({ action: 'telemetry_permission_handle', data: { isChecked: true } })
    setIsTelemetryModalOpen(false)
  }

  const handleTelemetryModalSkip = () => {
    setIsPopConfirmOpen(true)
    const modalContent = document.getElementsByClassName('ant-modal-content')
    if (modalContent.length) {
      modalContent[0].style.filter = 'blur(2px)'
    }
  }

  const handleTelemetryPopConfirmSkip = () => {
    setIsTelemetryModalOpen(false)
    request({ action: 'telemetry_permission_handle', data: { isChecked: false } })
  }

  const steps = [
    {
      content: (
        <div className={cls.bitSocialReleaseBanner}>
          <a href="https://bit-social.com/" target="_blank" rel="noreferrer">
            <img src={bitSocialBanner} alt="Bit Social Release Promotional Banner" width="100%" />
          </a>
        </div>
      )
    },
    {
      content: (
        <>
          <span className={cls.improvementsTitle}>New Improvements</span>
          <div className={cls.improvements}>
            <ul>
              <li>1. UI updated.</li>
              <li>2. Multiple drive/folder by user/role.</li>
              <li>3. ElFinder updated.</li>
            </ul>
          </div>
          <span className={cls.fixedTitle}>Fixed</span>
          <div className={cls.fixed}>
            <ul>
              <li>
                1. Addressed a potential RCE vulnerability related to a race condition during PHP syntax
                checks.
              </li>
              <li>
                2. Restricted file types in shortcodes. Users must grant permission to allow PHP files.
              </li>
              <li>3. PDF preview due to wrong file path.</li>
            </ul>
          </div>
          {/* <Checkbox onChange={handleStepToggle}>Install Bit Form to create multi step form</Checkbox> */}
        </>
      )
    },
    {
      content: (
        <div className={cls.telemetryContent}>
          <h3>
            <b>Build a better Bit File Manager</b>
          </h3>
          <span>
            Accept and complete to share non-sensitive diagnostic data to help us improve your
            experience.
          </span>
          <button type="button" onClick={() => setIsDataNoticeShow(true)}>
            What we collect?
          </button>
          {isDataNoticeShow && (
            <>
              <br />
              <span>
                Server details (PHP, MySQL, server, WordPress versions), plugin usage (active/inactive),
                site name and URL, your name and email. No sensitive data is tracked.{' '}
                <a href="https://bitapps.pro/terms-of-service/" target="_blank" rel="noreferrer">
                  {' '}
                  Terms & Conditions.
                </a>
              </span>
            </>
          )}
        </div>
      )
    }
  ]

  const next = () => {
    setCurrent(current + 1)
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
          {current === 0 ? 'Bit Social Release' : current === 1 ? 'Bit File Manager 2024 Updates' : ''}
        </div>
      }
      open={isTelemetryModalOpen}
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
          {current === 2 && (
            <Popconfirm
              title="Help Us Improve Your Experience"
              description={
                <>
                  It has helped us make informed decisions to improve our most popular features, resolve
                  issues more quickly, and enhance the overall user experience.
                  <br /> We guarantee no personal data is stored, and there’s absolutely no spam - WE
                  PROMISE!
                </>
              }
              open={isPopConfirmOpen}
              onConfirm={() => handleTelemetryAccess()}
              onCancel={() => handleTelemetryPopConfirmSkip()}
              okText="Yes"
              cancelText="No Thanks"
              placement="topLeft"
              overlayClassName="telemetry-popconfirm"
            >
              <Button className={cls.skipBtn} onClick={handleTelemetryModalSkip}>
                Skip
              </Button>
            </Popconfirm>
          )}
          {current === steps.length - 1 && (
            <Button type="primary" onClick={() => handleTelemetryAccess()}>
              Accept & Complete
            </Button>
          )}
        </div>
      </>
    </AntModal>
  )
}

export default TelemetryPopup
