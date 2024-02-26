import React, { useState } from 'react'

import { __ } from '@common/helpers/i18nwrap'
import useFetchPermissionsSettings from '@pages/Permissions/data/useFetchPermissionsSettings'
import { Button, Card, Checkbox, Form, Input, Radio, Select, Space, Switch, Table } from 'antd'
import { useForm } from 'antd/es/form/Form'

function Permissions() {
  const { isLoading, permissions, commands, fileTypes, roles, users } = useFetchPermissionsSettings()
  const [form] = useForm()
  // Define state for form values
  const [formValues, setFormValues] = useState({
    do_not_use_for_admin: false,
    fileType: [],
    file_size: '',
    root_folder: '',
    root_folder_url: '',
    folder_options: '',
    by_role: [],
    guest: {},
    by_user: []
  })
  const permissionSettings = {
    allUsers: () => [],
    allRoles: () => [],
    getGuestPermissions: () => []
  }

  console.log('permissions', permissions)
  // Handle form submission
  const handleSubmit = values => {
    // Handle form submission logic
    console.log('Form values:', values)
  }

  return (
    <Form
      form={form}
      onFinish={handleSubmit}
      disabled={isLoading}
      initialValues={permissions}
      colon={false}
    >
      <Space direction="vertical" size="middle" style={{ display: 'flex' }} className="px-2">
        <Space style={{ display: 'flex', justifyContent: 'right', paddingBlock: '8px' }}>
          <Form.Item style={{ marginBottom: 0 }}>
            <Button type="primary" htmlType="submit" loading={false}>
              Update
            </Button>
          </Form.Item>
        </Space>
        <Card>
          <Form.Item
            name="do_not_use_for_admin"
            label={__('Disable this permission inside WordPress dashboard')}
            tooltip={__(
              'If enabled, the root folder for the file manager will be determined by this permission setting.'
            )}
          >
            <Switch />
          </Form.Item>

          <h3>Allowed MIME types and size</h3>
          <Form.Item name="fileType">
            <Select mode="multiple">
              {fileTypes?.map(fileType => (
                <Checkbox key={fileType} value={fileType}>
                  {fileType}
                </Checkbox>
              ))}
            </Select>
          </Form.Item>

          <Form.Item name="file_size">
            <Input type="number" placeholder="Maximum File Size" addonAfter="MB" />
          </Form.Item>

          <Form.Item name="root_folder">
            <Input placeholder="Root Folder Path" />
          </Form.Item>

          <Form.Item name="root_folder_url">
            <Input placeholder="Root Folder URL" />
          </Form.Item>
        </Card>

        <Card>
          <h3>Folder Options</h3>
          <Form.Item name="folder_options">
            <Radio.Group>
              <Radio value="common">Enable a common folder for everyone</Radio>
              <Radio value="user">Enable separate folders for each user</Radio>
              <Radio value="role">Enable folders for each user role</Radio>
            </Radio.Group>
          </Form.Item>

          <h3>Roles Permission</h3>
          <Table dataSource={permissionSettings.allRoles()}>{/* Define columns for the table */}</Table>

          <h3>User Permission</h3>
          <Table dataSource={permissionSettings.allUsers()}>{/* Define columns for the table */}</Table>
          <h3>Guest User Settings</h3>
          <Table dataSource={[permissionSettings.getGuestPermissions()]}>
            {/* Define columns for the table */}
          </Table>
        </Card>
        <Form.Item>
          <Space style={{ display: 'flex', justifyContent: 'center' }}>
            <Form.Item>
              <Button type="primary" htmlType="submit" loading={false}>
                Update
              </Button>
            </Form.Item>
          </Space>
        </Form.Item>
      </Space>
    </Form>
  )
}

export default Permissions
