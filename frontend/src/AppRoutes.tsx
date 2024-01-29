import { useEffect } from 'react'
import { Route, Routes, useNavigate } from 'react-router-dom'

import { StyleProvider } from '@ant-design/cssinjs'
import { $appConfig } from '@common/globalStates'
import $navigate from '@common/globalStates/$navigate'
import { removeUnwantedCSS, setAppBgFromAdminBg } from '@common/helpers/globalHelpers'
import { darkThemeConfig, lightThemeConfig } from '@config/theme'
import loadable from '@loadable/component'
import Layout from '@pages/Layout'
import Root from '@pages/root/Root'
import { ConfigProvider, theme } from 'antd'
import { useAtom, useAtomValue } from 'jotai'

const Support = loadable(() => import('@pages/Support'), { fallback: <div>Loading...</div> })
const Error404 = loadable(() => import('@pages/Error404'), { fallback: <div>Loading...</div> })

const { defaultAlgorithm, darkAlgorithm } = theme

export default function AppRoutes() {
  const [navigateUrl, setNavigateUrl] = useAtom($navigate)
  const navigate = useNavigate()
  const { isDarkTheme } = useAtomValue($appConfig)
  const themeTokens = isDarkTheme ? darkThemeConfig : lightThemeConfig
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

  return (
    <ConfigProvider
      theme={{
        algorithm: themeAlgorithm,
        token: themeTokens
      }}
    >
      <StyleProvider hashPriority="high">
        <Routes>
          <Route path="/" element={<Layout />}>
            <Route index element={<Root />} />
            <Route path='/elf_l1_Lw/' element={<Root />} />
            <Route path="/support" element={<Support />} /> 
            <Route path="*" element={<Error404 />} />
          </Route>
        </Routes>
      </StyleProvider>
    </ConfigProvider>
  )
}
