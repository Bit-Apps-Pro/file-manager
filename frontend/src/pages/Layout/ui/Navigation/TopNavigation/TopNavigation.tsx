import { useState } from 'react'

import { $appConfig } from '@common/globalStates'
import $finder from '@common/globalStates/$finder'
import { __ } from '@common/helpers/i18nwrap'
import config from '@config/config'
import AntIconWrapper from '@icons/AntIconWrapper'
import LogoIcn from '@icons/LogoIcn'
import LogoText from '@icons/LogoText'
import earlyBirdOffer from '@resource/img/earlyBirdOffer.png'
import { Button, Layout, Modal, Select, Space, Typography, notification, theme } from 'antd'
import { useAtomValue } from 'jotai'

import cls from './TopNavigation.module.css'
import useFetchLang from './data/useFetchLang'
import useUpdateLang from './data/useUpdateLang'
import useUpdateTheme from './data/useUpdateTheme'
import { type ProductDetail } from './static/MenuItems'
import { items } from './static/MenuItems'

const { Header } = Layout

export default function TopNavigation() {
  const [isModalOpen, setIsModalOpen] = useState(false)

  const showModal = () => {
    setIsModalOpen(true)
  }

  const handleOk = () => {
    setIsModalOpen(false)
  }

  const handleCancel = () => {
    setIsModalOpen(false)
  }
  const {
    token: { colorBgContainer }
  } = theme.useToken()

  const { isDarkTheme } = useAtomValue($appConfig)
  const finder = useAtomValue($finder)
  const { languages } = useFetchLang()
  const { updateLanguage } = useUpdateLang()
  const { updateTheme } = useUpdateTheme()

  const handleThemeChange = (updatedTheme: string) => {
    updateTheme(updatedTheme).then(response => {
      if (response.code === 'SUCCESS') {
        if (finder && typeof finder.changeTheme === 'function') {
          finder?.changeTheme(updatedTheme).storage('theme', updatedTheme)
        }

        if (
          !(finder && typeof finder.changeTheme === 'function') ||
          ['bootstrap', 'default'].includes(config.THEME) ||
          ['bootstrap', 'default'].includes(updatedTheme)
        ) {
          window.location.reload()
        }
      } else if (response?.message) {
        notification.error({ message: response.message })
      } else {
        response?.data?.theme?.map(error => notification.error({ message: error }))
      }
    })
  }

  const handleLanguageChange = (value: string) => {
    updateLanguage(value).then(response => {
      if (response.code === 'SUCCESS') {
        finder?.storage('lang', value)
        // eslint-disable-next-line @typescript-eslint/ban-ts-comment
        // @ts-expect-error
        jQuery(`#${finder.id}`).elfinder('reload')
      } else {
        notification.error({ message: response?.message ?? __('Failed to update language') })
      }
    })
  }

  return (
    <>
      <Header
        style={{
          display: 'flex',
          alignItems: 'center',
          justifyContent: 'space-between',
          background: isDarkTheme ? colorBgContainer : '#F1F5FF',
          flexWrap: 'wrap',
          width: '100%',
          height: 'auto',
          zIndex: 1,
          paddingInline: '10px'
        }}
      >
        <div className={cls.logo}>
          <LogoIcn size={30} />
          <LogoText h={35} dark={isDarkTheme} />
        </div>
        <Space style={{ paddingInline: '40px', fontSize: '12px' }}>
          <Typography.Text>{__('Share Your Product Experience')}!</Typography.Text>
          <Button
            style={{ fontSize: 14, borderRadius: 14 }}
            className={cls.reviewUs}
            ghost
            href="https://wordpress.org/support/plugin/file-manager/reviews/#new-post"
            target="_blank"
          >
            {__('Review us')}
            <AntIconWrapper>
              <span
                className="dashicons dashicons-star-filled"
                style={{ display: 'inline', fontSize: '14px' }}
              />
            </AntIconWrapper>
          </Button>
        </Space>

        <Space style={{ paddingInline: '10px', fontSize: '12px' }}>
          <div className={cls.bitSocialMenu}>
            <button type="button" onClick={() => showModal()} className={cls.btn}>
              {__('New Product Released')}
              <span className={cls.star} />
              <span className={cls.star} />
              <span className={cls.star} />
              <span className={cls.star} />
            </button>
          </div>
        </Space>

        <Space id="product-list">
          {items?.map((item: ProductDetail) => (
            <Button type="text" title={item.title} key={item.key}>
              {item.label}
            </Button>
          ))}
        </Space>
        <Space id="fm-theme-lang">
          {__('Theme')}:
          <Select
            defaultValue={config.THEME}
            style={{ maxWidth: '140px' }}
            variant="borderless"
            onChange={handleThemeChange}
          >
            {config.THEMES.map(finderTheme => (
              <Select.Option key={finderTheme.key}>{finderTheme.title}</Select.Option>
            ))}
          </Select>
          <Select
            defaultValue={config.LANG}
            style={{ maxWidth: 'max-content', minWidth: '115px' }}
            variant="borderless"
            onChange={handleLanguageChange}
          >
            {languages?.map(lang => <Select.Option key={lang.code}>{lang.name}</Select.Option>)}
          </Select>
        </Space>
      </Header>

      <Modal
        open={isModalOpen}
        onOk={handleOk}
        onCancel={handleCancel}
        footer={null}
        centered
        className="bit-social-release-modal"
      >
        <a
          href="https://bit-flows.com/?utm_source=bit-fm&utm_medium=inside-plugin&utm_campaign=bit_flows_early_bird"
          target="_blank"
          rel="noreferrer"
        >
          <img src={earlyBirdOffer} alt="Bit Social Release Promotional Banner" width="100%" />
        </a>
        <div className={cls.footerBtn}>
          <a
            href="https://bit-flows.com/?utm_source=bit-fm&utm_medium=inside-plugin&utm_campaign=bit_flows_early_bird"
            target="_blank"
            rel="noreferrer"
          >
            Grab the Deal Now
          </a>
        </div>
      </Modal>
    </>
  )
}
