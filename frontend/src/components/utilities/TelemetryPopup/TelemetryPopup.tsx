/* eslint-disable import/no-relative-parent-imports */

/* eslint-disable no-nested-ternary */
import type React from 'react'
import { useState } from 'react'

import request from '@common/helpers/request'
import config from '@config/config'
import earlyBirdOffer from '@resource/img/earlyBirdOffer.webp'
import { Button, Flex, Modal, Popconfirm, Steps, Typography } from 'antd'

import changeLogs from '../../../changeLog'
import cls from './TelemetryPopup.module.css'

type TelemetryPopupProps = {
  isTelemetryModalOpen: boolean
  setIsTelemetryModalOpen: (value: boolean) => void
}

function TelemetryPopup({ isTelemetryModalOpen, setIsTelemetryModalOpen }: TelemetryPopupProps) {
  const [current, setCurrent] = useState(0)
  const { TELEMETRY } = config
  const [isDataNoticeShow, setIsDataNoticeShow] = useState(false)
  const [isPopConfirmOpen, setIsPopConfirmOpen] = useState(false)

  const handleTelemetryAccess = () => {
    request({ action: 'telemetry_permission_handle', data: { isChecked: true } })
    setIsTelemetryModalOpen(false)
  }

  const handleTryPlugin = () => {
    setCurrent(previous => previous + 1)
    const defaultAccptedPlugin: Record<string, boolean> = {}
    Object.values<{ title: string; slug: string }>(TELEMETRY.tryPlugin).map(plugin => {
      defaultAccptedPlugin[plugin.slug] = true
    })
    request({ action: 'telemetry/tryplugin', data: { tryPlugin: defaultAccptedPlugin } })
  }
  const handleTelemetryModalSkip = () => {
    setIsPopConfirmOpen(true)
    const modalContent = document.getElementsByClassName('ant-modal-content')
    if (modalContent.length) {
      const content = modalContent[0] as HTMLElement
      content.style.filter = 'blur(2px)'
    }
  }

  const handleTelemetryPopConfirmSkip = () => {
    setIsTelemetryModalOpen(false)
    request({ action: 'telemetry_permission_handle', data: { isChecked: false } })
  }

  const steps = [
    {
      title: '',
      id: '',
      'data-modaltitle': 'Bit Social Release',
      content: (
        <div className={cls.bitSocialReleaseBanner}>
          <a
            href="https://bit-social.com/?utm_source=bit-fm&utm_medium=inside-plugin&utm_campaign=early-bird-offer"
            target="_blank"
            rel="noreferrer"
          >
            <img src={earlyBirdOffer} alt="Bit Social Release Promotional Banner" width="100%" />
          </a>
          <div className={cls.footerBtn}>
            <a
              href="https://bit-social.com/?utm_source=bit-fm&utm_medium=inside-plugin&utm_campaign=early-bird-offer"
              target="_blank"
              rel="noreferrer"
            >
              Get My Discount!
            </a>
          </div>
        </div>
      )
    },
    {
      title: '',
      'data-modaltitle': 'Bit File Manager 2024 Updates',
      content: (
        <>
          <span className={cls.improvementsTitle}>New Improvements</span>
          <div className={cls.improvements}>
            <ul>
              {changeLogs.improvements.map(item => (
                <li key={item}>{item}</li>
              ))}
            </ul>
          </div>
          <span className={cls.fixedTitle}>Fixed</span>
          <div className={cls.fixed}>
            <ul>
              {changeLogs.fixed.map(item => (
                <li key={item}>{item}</li>
              ))}
            </ul>
          </div>
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
                  Server details (PHP, MySQL, server, WordPress versions), plugin usage
                  (active/inactive), site name and URL, your name and email. No sensitive data is
                  tracked.{' '}
                  <a href="https://bitapps.pro/terms-of-service/" target="_blank" rel="noreferrer">
                    {' '}
                    Terms & Conditions.
                  </a>
                </span>
              </>
            )}
          </div>
        </>
      )
    }
  ]

  if (TELEMETRY?.tryPlugin && Object.keys(TELEMETRY.tryPlugin).length) {
    steps.splice(steps.length - 1, 0, {
      title: '',
      id: 'tryplugin',
      'data-modaltitle': 'Try Bit Apps Plugins',
      content: <TryPlugins />
    })
  }

  const next = () => {
    setCurrent(current + 1)
  }
  const footerBtnStyle: React.CSSProperties = {
    display: 'flex',
    justifyContent: 'space-between',
    flexFlow: current !== 1 ? 'row-reverse' : 'initial',
    marginTop: '30px'
  }

  return (
    <Modal
      title={
        <div style={{ textAlign: 'center', fontSize: '20px', marginBottom: '20px' }}>
          {steps[current]['data-modaltitle']}
        </div>
      }
      open={isTelemetryModalOpen}
      closable={false}
      width="450px"
      centered
      className="telemetry-popup"
      footer={null}
    >
      <>
        <Steps current={current} items={steps} />
        <div className={cls.popupContent}>{steps[current].content}</div>
        <div style={footerBtnStyle}>
          {current < steps.length - 1 && (
            <Button type={current === 1 ? 'link' : 'primary'} onClick={() => next()}>
              {current === 1 ? 'Skip' : 'Next'}
            </Button>
          )}

          {steps[current].id == 'tryplugin' && (
            <Button type="primary" onClick={handleTryPlugin}>
              Accept & Install
            </Button>
          )}

          {current === steps.length - 1 && (
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
              okText="Don't Skip"
              cancelText="I won't accept"
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
    </Modal>
  )
}

export default TelemetryPopup

function TryPlugins() {
  const { TELEMETRY } = config
  return (
    <Flex vertical>
      {Object.values<{ title: string; slug: string; tutorial: string }>(TELEMETRY.tryPlugin).map(
        plugin => (
          <>
            <iframe
              width="auto"
              height="315"
              src={plugin.tutorial}
              title={plugin.title}
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
            />
            <Typography.Text style={{ color: 'black' }}>{plugin.title}</Typography.Text>
          </>
        )
      )}
    </Flex>
  )
}
