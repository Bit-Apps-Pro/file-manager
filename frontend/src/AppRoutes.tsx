import { useEffect } from 'react'
import { Route, Routes, useNavigate } from 'react-router-dom'

import { StyleProvider } from '@ant-design/cssinjs'
import { $appConfig } from '@common/globalStates'
import $navigate from '@common/globalStates/$navigate'
import { removeUnwantedCSS, setAppBgFromAdminBg } from '@common/helpers/globalHelpers'
import { darkThemeComponentToken, darkThemeToken } from '@config/themes/theme.dark'
import { lightThemeComponentToken, lightThemeToken } from '@config/themes/theme.light'
import loadable from '@loadable/component'
import Layout from '@pages/Layout'
import Root from '@pages/root/Root'
import { ConfigProvider, Flex, Spin, notification, theme } from 'antd'
import { useAtom, useAtomValue } from 'jotai'

const Support = loadable(() => import('@pages/Support'), { fallback: <Loader /> })
const Logs = loadable(() => import('@pages/Logs'), { fallback: <Loader /> })
const Settings = loadable(() => import('@pages/Settings'), { fallback: <Loader /> })
const Permissions = loadable(() => import('@pages/Permissions'), { fallback: <Loader /> })
const SystemInformation = loadable(() => import('@pages/SystemInformation'), { fallback: <Loader /> })

const { defaultAlgorithm, darkAlgorithm } = theme

export default function AppRoutes() {
  const [navigateUrl, setNavigateUrl] = useAtom($navigate)
  const navigate = useNavigate()
  const { isDarkTheme } = useAtomValue($appConfig)
  const themeTokens = isDarkTheme ? darkThemeToken : lightThemeToken
  const componentTokens = isDarkTheme ? darkThemeComponentToken : lightThemeComponentToken
  const themeAlgorithm = isDarkTheme ? darkAlgorithm : defaultAlgorithm

  useEffect(() => {
    removeUnwantedCSS()
    setAppBgFromAdminBg()
  }, [])

  useEffect(() => {
    if (navigateUrl && navigateUrl !== '') {
      navigate(navigateUrl, { replace: true })
      setNavigateUrl('')
    }
  }, [navigateUrl])

  const [, contextHolder] = notification.useNotification()
  notification.config({ placement: 'bottomRight', maxCount: 3 })

  return (
    <ConfigProvider
      theme={{
        algorithm: themeAlgorithm,
        token: themeTokens,
        components: componentTokens
      }}
    >
      <StyleProvider hashPriority="high">
        {contextHolder}
        <Routes>
          <Route path="/" element={<Layout />}>
            <Route path="/support" element={<Support />} />
            <Route path="/logs" element={<Logs />} />
            <Route path="/settings" element={<Settings />} />
            <Route path="/permissions" element={<Permissions />} />
            <Route path="/system-info" element={<SystemInformation />} />
            <Route path="*" caseSensitive element={<Root />} />
          </Route>
        </Routes>
      </StyleProvider>
    </ConfigProvider>
  )
}

function Loader() {
  return (
    <Flex
      align="center"
      justify="center"
      style={{ minWidth: 'calc(100vw - 180px)', minHeight: '100vh' }}
    >
      <Spin size="large" />
    </Flex>
  )
}
