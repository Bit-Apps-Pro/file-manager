import type React from 'react'

import { $appConfig } from '@common/globalStates'
import LogoIcn from '@icons/LogoIcn'
import LogoText from '@icons/LogoText'
import { type MenuProps } from 'antd'
import { Divider, Layout, Menu, Typography, theme } from 'antd'
import { useAtomValue } from 'jotai'

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
      style={{ display: 'flex', alignItems: 'center', background: colorBgContainer, flexWrap: 'wrap' }}
    >
      <div className={cls.logo}>
        <LogoIcn size={30} />
        <LogoText h={35} />
      </div>
      <Divider orientation="left" type="vertical" />
      <Typography.Text>Share Your Product Experience!</Typography.Text>
      <Menu
        theme={isDarkTheme ? 'dark' : 'light'}
        mode="horizontal"
        items={items}
        style={{ flex: 1, minWidth: 0, flexWrap: 'wrap', backgroundColor: colorBgContainer }}
      />
    </Header>
  )
}
