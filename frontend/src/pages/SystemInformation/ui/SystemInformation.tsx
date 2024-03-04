import { useState } from 'react'

import config from '@config/config'
import { Card, Space, theme } from 'antd'

export default function SystemInformation() {
  return (
    <div className="p-6">
      <Card title="System Information">
        <Space direction="vertical">
          <Space>
            <span>Current Media Directory:</span>
            <span> {config.SYS_INFO?.currentMediaDir} </span>
          </Space>
          <Space>
            <span>PHP version:</span>
            <span> {config.SYS_INFO?.phpVersion} </span>
          </Space>
          <Space>
            <span>PHP ini file:</span>
            <span> {config.SYS_INFO?.iniPath} </span>
          </Space>
          <Space>
            <span>Maximum file upload size:</span>
            <span> {config.SYS_INFO?.uploadMaxFilesize} </span>
          </Space>
          <Space>
            <span>Post maximum file upload size:</span>
            <span> {config.SYS_INFO?.postMaxSize} </span>
          </Space>
          <Space>
            <span>Memory Limit:</span>
            <span> {config.SYS_INFO?.memoryLimit} </span>
          </Space>
          <Space>
            <span>Timeout:</span>
            <span> {config.SYS_INFO?.maxExecutionTime} </span>
          </Space>
          <Space>
            <span>Browser and OS:</span>
            <span> {config.SYS_INFO?.ua} </span>
          </Space>
          <Space>
            <span>DISALLOW_FILE_EDIT:</span>
            <span> {config.SYS_INFO?.fileEditNotAllowed ? 'true' : 'false'} </span>
          </Space>
        </Space>
      </Card>
    </div>
  )
}
