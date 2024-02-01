import React from 'react';
import { Divider, Layout, Menu, MenuProps, Typography, theme } from 'antd';


import cls from './TopNavigation.module.css'
import LogoIcn from '@icons/LogoIcn';
import LogoText from '@icons/LogoText';
import { $appConfig } from '@common/globalStates';
import { useAtomValue } from 'jotai';

const { Header } = Layout;

const items: MenuProps['items'] = [
  {
    key: 'bit-form',
    label: (
      <a href='https://bitapps.pro/bit-form' target='_blank'>Bit Form</a>
    )
  },{
    key: 'bit-assist',
    label: (
      <a href='https://bitapps.pro/bit-assist' target='_blank'>Bit Assist</a>
    )
  },{
    key: 'bit-social',
    label: (
      <a href='https://bitapps.pro/bit-social' target='_blank'>Bit Social</a>
    )
  },{
    key: 'bit-integration',
    label: (
      <a href='https://bitapps.pro/bit-integration' target='_blank'>Bit Integration</a>
    )
  },{
    key: 'bit-smtp',
    label: (
      <a href='https://bitapps.pro/bit-smtp' target='_blank'>Bit SMTP</a>
    )
  },
]

const TopNavigation: React.FC = () => {
  const {
    token: { colorBgContainer },
  } = theme.useToken();

  const { isDarkTheme } = useAtomValue($appConfig)
  console.log('isDarkTheme', isDarkTheme)

  return (
      <Header style={{ display: 'flex', alignItems: 'center', background: colorBgContainer, flexWrap: 'wrap' }}>
        <div className={cls.logo}>
          <LogoIcn size={30} />
          <LogoText h={35}/>
        </div>
          <Divider orientation='left' type='vertical'/>
          <Typography.Text>Share Your Product Experience!</Typography.Text>
          <Menu
            theme={isDarkTheme ?  'dark' : 'light'}
            mode="horizontal"
            items={items}
            style={{ flex: 1, minWidth: 0, flexWrap: 'wrap' }}
          />
      </Header>
  );
};

export default TopNavigation;