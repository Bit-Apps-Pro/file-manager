import type React from 'react'

import { StarFilled, StarOutlined } from '@ant-design/icons'
import { $appConfig } from '@common/globalStates'
import LogoIcn from '@icons/LogoIcn'
import LogoText from '@icons/LogoText'
import { Button, type MenuProps, Select } from 'antd'
import { Divider, Layout, Menu, Typography, theme } from 'antd'
import { useAtomValue } from 'jotai'
import { StarIcon } from 'lucide-react'

import cls from './TopNavigation.module.css'

const { Header } = Layout

const items: MenuProps['items'] = [
  {
    key: 'bit-form',
    label: (
      <a href="https://bitapps.pro/bit-form" target="_blank" rel="noreferrer">
        Bit Form
      </a>
    )
  },
  {
    key: 'bit-assist',
    label: (
      <a href="https://bitapps.pro/bit-assist" target="_blank" rel="noreferrer">
        Bit Assist
      </a>
    )
  },
  {
    key: 'bit-social',
    label: (
      <a href="https://bitapps.pro/bit-social" target="_blank" rel="noreferrer">
        Bit Social
      </a>
    )
  },
  {
    key: 'bit-integration',
    label: (
      <a href="https://bitapps.pro/bit-integration" target="_blank" rel="noreferrer">
        Bit Integration
      </a>
    )
  },
  {
    key: 'bit-smtp',
    label: (
      <a href="https://bitapps.pro/bit-smtp" target="_blank" rel="noreferrer">
        Bit SMTP
      </a>
    )
  }
]

export default function TopNavigation() {
  const {
    token: { colorBgContainer }
  } = theme.useToken()

  const { isDarkTheme } = useAtomValue($appConfig)
  console.log('isDarkTheme', isDarkTheme)

  return (
    <Header
      style={{
        display: 'flex',
        alignItems: 'center',
        background: colorBgContainer,
        flexWrap: 'wrap',
        position: 'fixed',
        width: '95%',
        zIndex: 1
      }}
    >
      <div className={cls.logo}>
        <LogoIcn size={30} />
        <LogoText h={35} />
      </div>
      <Divider orientation="left" type="vertical" />
      <Typography.Text>Share Your Product Experience!</Typography.Text>
      <Button
        style={{ marginInline: 8 }}
        className={cls.reviewUs}
        shape="round"
        ghost
        href="https://wordpress.org/support/plugin/file-manager/reviews/#new-post"
        target="_blank"
      >
        Review us
        <StarFilled style={{ marginLeft: 8 }} />
      </Button>
      <Menu
        theme={isDarkTheme ? 'dark' : 'light'}
        mode="horizontal"
        items={items}
        style={{
          flex: 1,
          minWidth: 0,
          maxWidth: 'max-content',
          flexWrap: 'wrap',
          backgroundColor: colorBgContainer
        }}
      />
      <Divider orientation="right" type="vertical" />
      <Select defaultValue={fm?.options?.theme} style={{ width: 'max-content' }}>
        {Object.keys(fm?.options?.themes).map(theme => (
          <Select.Option key={theme}>{theme.toUpperCase()}</Select.Option>
        ))}
      </Select>
    </Header>
  )
}
