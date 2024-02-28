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
import { ConfigProvider, notification, theme } from 'antd'
import { useAtom, useAtomValue } from 'jotai'

const Support = loadable(() => import('@pages/Support'), { fallback: <div>Loading...</div> })
const Error404 = loadable(() => import('@pages/Error404'), { fallback: <div>Loading...</div> })
const Logs = loadable(() => import('@pages/Logs'), { fallback: <div>Loading...</div> })
const Settings = loadable(() => import('@pages/Settings'), { fallback: <div>Loading...</div> })
const Permissions = loadable(() => import('@pages/Permissions'), { fallback: <div>Loading...</div> })
const SystemInformation = loadable(() => import('@pages/SystemInformation'), {
  fallback: <div>Loading...</div>
})

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
            <Route index element={<Root />} />
            <Route path="*" caseSensitive element={<Root />} />
            <Route path="/support" element={<Support />} />
            <Route path="/logs" element={<Logs />} />
            <Route path="/settings" element={<Settings />} />
            <Route path="/permissions" element={<Permissions />} />
            <Route path="/system-info" element={<SystemInformation />} />
          </Route>
        </Routes>
      </StyleProvider>
    </ConfigProvider>
  )
}
