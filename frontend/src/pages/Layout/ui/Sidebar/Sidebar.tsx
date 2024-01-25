import { useEffect } from 'react'

import { $appConfig } from '@common/globalStates'
import { select } from '@common/helpers/globalHelpers'
import DashboardIcn from '@icons/DashboardIcn'
import LogoIcn from '@icons/LogoIcn'
import LogoText from '@icons/LogoText'
import LucideIcn from '@icons/LucideIcn'
import SupportIcn from '@icons/SupportIcn'
import Fade from '@utilities/Fade'
import If from '@utilities/If'
import { Button, Layout, Tooltip } from 'antd'
import { motion } from 'framer-motion'
import { useAtom } from 'jotai'

import cls from './Sidebar.module.css'
import SidebarNavItem from './SidebarNavItem'

const { Sider } = Layout

const navItems = [
  { label: 'Dashboard', path: '/', icon: <DashboardIcn size={17} /> },
  { label: 'Flows', path: '/flows', icon: <LucideIcn name="Workflow" size={19} /> },
  { label: 'Connections', path: '/connections', icon: <LucideIcn name="Link" size={18} /> },
  { label: 'Webhooks', path: '/webhooks', icon: <LucideIcn name="Globe" size={18} /> }
]

const collapseBtnStyle = () => ({
  position: 'absolute !important' as any, // eslint-disable-line @typescript-eslint/no-explicit-any
  top: '32px',
  right: '-12px'
})

export default function Sidebar() {
  const [{ isSidebarCollapsed, isDarkTheme, isWpMenuCollapsed }, setAppConfig] = useAtom($appConfig)

  const toggleTheme = () =>
    setAppConfig(prv => ({
      ...prv,
      isDarkTheme: !prv.isDarkTheme
    }))

  const toggleMenu = () =>
    setAppConfig(prv => ({
      ...prv,
      isSidebarCollapsed: !prv.isSidebarCollapsed,
      isWpMenuCollapsed: !prv.isWpMenuCollapsed
    }))

  useEffect(() => {
    const body = select('.wp-admin')
    if (body) {
      if (isWpMenuCollapsed) {
        body.classList.add('folded')
      } else {
        body.classList.remove('folded')
      }
    }
  }, [isWpMenuCollapsed])

  return (
    <Sider
      theme="light"
      collapsed={isSidebarCollapsed}
      collapsedWidth={50}
      width={150}
      css={({ token }) => ({
        borderRight: `1px solid ${token.controlOutline}`
      })}
      className={`${cls.sidebar}`}
    >
      <div className={cls.sidebarLogo}>
        <LogoIcn size={30} />
        <Fade is={!isSidebarCollapsed}>
          <LogoText h={40} w={72} />
        </Fade>
      </div>

      <Button
        css={collapseBtnStyle}
        size="small"
        shape="circle"
        onClick={toggleMenu}
        title={isSidebarCollapsed ? 'Expand' : 'Collapse'}
        icon={<LucideIcn name={isSidebarCollapsed ? 'ChevronRight' : 'ChevronLeft'} />}
      />
      <nav
        className={cls.navList}
        css={{
          width: isSidebarCollapsed ? 40 : 130
        }}
      >
        <motion.div>
          {navItems.map(link => (
            <SidebarNavItem key={link.label} props={link} />
          ))}
        </motion.div>

        <div>
          <SidebarNavItem
            key="Support"
            props={{ label: 'Support', path: '/support', icon: <SupportIcn size={18} /> }}
          />
          <Tooltip title={`Switch to ${isDarkTheme ? 'light' : 'dark'} mode`} placement="right">
            <Button
              className="mb-1"
              onClick={toggleTheme}
              block
              type="text"
              title={isDarkTheme ? 'Light' : 'Dark'}
              icon={
                isDarkTheme ? <LucideIcn name="Moon" size={18} /> : <LucideIcn name="Sun" size={18} />
              }
            >
              <If conditions={!isSidebarCollapsed}>{isDarkTheme ? 'Light' : 'Dark'}</If>
            </Button>
          </Tooltip>
        </div>
      </nav>
    </Sider>
  )
}
