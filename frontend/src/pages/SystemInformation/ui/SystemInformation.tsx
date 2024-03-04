import { useState } from 'react'

import { Card, Space, theme } from 'antd'

export default function SystemInformation() {
  return (
    <div className="p-6">
      <Space direction="vertical">
        <Space direction="vertical">
          <Space direction="vertical">
            <Card title="System Information">
              <Space direction="vertical">
                <Space>
                  <span>Current Media Directory:</span>
                  <span>wp_upload_dir.path</span>
                </Space>
                <Space>
                  <span>PHP version:</span>
                  <span>PHP_VERSION</span>
                </Space>
                <Space>
                  <span>PHP ini file:</span>
                  <span />
                </Space>
                <Space>
                  <span>Maximum file upload size:</span>
                  <span>upload_max_filesize</span>
                </Space>
                <Space>
                  <span>Post maximum file upload size:</span>
                  <span>post_max_size</span>
                </Space>
                <Space>
                  <span>Memory Limit:</span>
                  <span>memory_limit</span>
                </Space>
                <Space>
                  <span>Timeout:</span>
                  <span>max_execution_time</span>
                </Space>
                <Space>
                  <span>Browser and OS:</span>
                  <span>{navigator.userAgent}</span>
                </Space>
                <Space>
                  <span>DISALLOW_FILE_EDIT:</span>
                  <span>{'DISALLOW_FILE_EDIT' ? 'TRUE' : 'FALSE'}</span>
                </Space>
              </Space>
            </Card>
          </Space>
        </Space>
      </Space>
    </div>
  )
}
