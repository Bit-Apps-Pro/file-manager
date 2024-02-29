import { $appConfig } from '@common/globalStates'
import AntIconWrapper from '@icons/AntIconWrapper'
import LogoIcn from '@icons/LogoIcn'
import LogoText from '@icons/LogoText'
import { Button, type MenuProps, Select, Space } from 'antd'
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

  return (
    <Header
      style={{
        display: 'flex',
        alignItems: 'center',
        background: colorBgContainer,
        flexWrap: 'wrap',
        // position: 'fixed',
        width: '100%',
        height: 'auto',
        zIndex: 1,
        paddingInline: '10px'
      }}
    >
      <div className={cls.logo}>
        <LogoIcn size={30} />
        <LogoText h={35} />
      </div>
      <Divider orientation="left" type="vertical" />
      <Space style={{ paddingInline: '40px', fontSize: '12px' }}>
        <Typography.Text>Share Your Product Experience!</Typography.Text>
        <Button
          style={{ fontSize: 14, borderRadius: 14 }}
          className={cls.reviewUs}
          ghost
          href="https://wordpress.org/support/plugin/file-manager/reviews/#new-post"
          target="_blank"
        >
          Review us
          <AntIconWrapper>
            <span
              className="dashicons dashicons-star-filled"
              style={{ display: 'inline', fontSize: '14px' }}
            />
          </AntIconWrapper>
        </Button>
      </Space>
      <Menu
        theme={isDarkTheme ? 'dark' : 'light'}
        mode="horizontal"
        items={items}
        style={{
          flex: 1,
          flexWrap: 'wrap',
          backgroundColor: colorBgContainer
        }}
      />
      <Divider
        orientation="right"
        type="vertical"
        style={{ borderInlineStart: '2px solid rgba(5, 5, 5, 0.20)', marginTop: '4px' }}
      />
      <Space>
        Theme:
        <Select defaultValue={fm?.options?.theme} style={{ width: 'max-content' }} variant="borderless">
          {Object.keys(fm?.options?.themes).map(theme => (
            <Select.Option key={theme}>{theme.toUpperCase()}</Select.Option>
          ))}
        </Select>
        <Select defaultValue={fm?.options?.theme} style={{ width: 'max-content' }} variant="borderless">
          {Object.keys(fm?.options?.themes).map(theme => (
            <Select.Option key={theme}>{theme.toUpperCase()}</Select.Option>
          ))}
        </Select>
      </Space>
    </Header>
  )
}
