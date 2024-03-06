import { Outlet } from 'react-router-dom'

import { $appConfig } from '@common/globalStates'
import { Global, ThemeProvider } from '@emotion/react'
import globalCssInJs from '@resource/globalCssInJs'
import { Layout as AntLayout, theme } from 'antd'
import { useAtomValue } from 'jotai'

import cls from './Layout.module.css'
import TopNavigation from './Navigation/TopNavigation'

const { useToken } = theme

export default function Layout() {
  const { isDarkTheme } = useAtomValue($appConfig)
  const antConfig = useToken()

  return (
    <ThemeProvider theme={antConfig}>
      <AntLayout
        color-scheme={isDarkTheme ? 'dark' : 'light'}
        style={{
          backgroundColor: antConfig.token.colorBgContainer,
          borderRadius: antConfig.token.borderRadius,
          border: `1px solid ${antConfig.token.controlOutline}`
        }}
        className={`${cls.layoutWrp} ${isDarkTheme ? 'dark' : 'light'}`}
      >
        <TopNavigation />
        <AntLayout hasSider>
          <Global styles={globalCssInJs(antConfig)} />
          {/* <Sidebar /> */}
          <div className="w-100 o-auto">
            <Outlet />
          </div>
        </AntLayout>
      </AntLayout>
    </ThemeProvider>
  )
}
