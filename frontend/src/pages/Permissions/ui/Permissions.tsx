import { useEffect } from 'react'

import { __ } from '@common/helpers/i18nwrap'
import { type PermissionsSettingsType } from '@pages/Permissions/PermissionsSettingsTypes'
import useFetchPermissionsSettings from '@pages/Permissions/data/useFetchPermissionsSettings'
import useUpdatePermissionsSettings from '@pages/Permissions/data/useUpdatePermissionsSettings'
import { Button, Card, Form, Input, Radio, Select, Space, Switch, Typography, notification } from 'antd'
import { useForm } from 'antd/es/form/Form'
import { type FieldData } from 'rc-field-form/es/interface'

function Permissions() {
  const { isLoading, permissions, commands, fileTypes, roles, users, wpRoot } =
    useFetchPermissionsSettings()
  const { updatePermission, isPermissionUpdating } = useUpdatePermissionsSettings()
  const [form] = useForm()
  useEffect(() => {
    form.setFieldsValue(permissions)
  }, [permissions, form])

  const handleSubmit = (changedValues: PermissionsSettingsType) => {
    updatePermission(changedValues).then(response => {
      if (response.code === 'SUCCESS') {
        notification.success({ message: response.message })
        const updatedFields = form.getFieldsError().map(field => {
          if (field.errors) {
            field.errors = []
          }
          return field
        })
        form.setFields(updatedFields)
      } else {
        const fieldErrors: FieldData[] = []
        Object.keys(response.data).forEach(field => {
          fieldErrors.push({
            name: field.split('.'),
            errors: response.data[field] as string[]
          })
          notification.error({ message: response?.message ?? __('Failed to update permission') })
        })
        form.setFields(fieldErrors)
      }
    })
  }

  return (
    <>
      <Card title="File Manager Shortcode" style={{ marginInline: '0.625rem' }}>
        <Typography.Text copyable={{ text: '[file-manager]' }}>[file-manager]</Typography.Text>
      </Card>
      <Form
        form={form}
        onFinish={handleSubmit}
        disabled={isLoading || isPermissionUpdating}
        initialValues={permissions}
        colon={false}
        scrollToFirstError
      >
        <Space direction="vertical" size="middle" style={{ display: 'flex' }} className="px-2">
          <Space style={{ display: 'flex', justifyContent: 'right', paddingBlock: '8px' }}>
            <Form.Item style={{ marginBottom: 0 }}>
              <Button type="primary" htmlType="submit" loading={isPermissionUpdating}>
                Update
              </Button>
            </Form.Item>
          </Space>
          <Card>
            <Form.Item
              name="do_not_use_for_admin"
              label={__('Disable this permission inside WordPress dashboard')}
              tooltip={__(
                'If disabled, the root folder for the file manager will be determined by this permission setting.'
              )}
            >
              <Switch />
            </Form.Item>

            <h3>Allowed MIME types and size</h3>
            <Form.Item name="fileType">
              <Select mode="multiple">
                {fileTypes?.map(fileType => (
                  <Select.Option key={fileType} value={fileType}>
                    {fileType}
                  </Select.Option>
                ))}
              </Select>
            </Form.Item>

            <Form.Item name="file_size">
              <Input type="number" placeholder="Maximum File Size" addonAfter="MB" />
            </Form.Item>
          </Card>

          <Card title={__('Public folder options')}>
            <Form.Item name="root_folder">
              <Input placeholder="Root Folder Path" />
            </Form.Item>

            <Form.Item name="root_folder_url">
              <Input placeholder="Root Folder URL" />
            </Form.Item>
            <Form.Item name="folder_options" label={__('Folder Options')}>
              <Radio.Group size="large">
                <Radio value="common">Enable a common folder for everyone</Radio>
                <Radio value="user">Enable separate folders for each user</Radio>
                <Radio value="role">Enable folders for each user role</Radio>
              </Radio.Group>
            </Form.Item>
          </Card>

          <Card title={__('Permissions by Roles')}>
            <Space size={20} wrap>
              {roles?.map(role => (
                <Card title={role.toUpperCase()} key={`permissions-for-${role}`}>
                  <Form.Item
                    name={['by_role', role, 'path']}
                    label={__('Path')}
                    rules={[
                      {
                        // eslint-disable-next-line no-useless-escape
                        pattern: new RegExp(`^${wpRoot}?(?:\/[^\/]+)*\/?$`),
                        message: __('Folder Path Must be within WordPress root directory')
                      }
                    ]}
                  >
                    <Input placeholder="Root Folder Path" />
                  </Form.Item>
                  <Form.Item name={['by_role', role, 'commands']} label={__('Enabled Commands')}>
                    <Select mode="multiple">
                      {commands?.map(command => (
                        <Select.Option key={command} value={command}>
                          {command}
                        </Select.Option>
                      ))}
                    </Select>
                  </Form.Item>
                </Card>
              ))}
            </Space>
          </Card>

          <Card title={__('Permissions by User')}>
            <Space size={20} wrap>
              {users?.map(user => (
                <Card title={user.display_name} key={`permissions-for-${user.ID}`}>
                  <Form.Item
                    name={['by_user', user.ID, 'path']}
                    label={__('Path')}
                    rules={[
                      {
                        // eslint-disable-next-line no-useless-escape
                        pattern: new RegExp(`^${wpRoot}?(?:\/[^\/]+)*\/?$`),
                        message: __('Folder Path Must be within WordPress root directory')
                      }
                    ]}
                  >
                    <Input placeholder="Root Folder Path" />
                  </Form.Item>
                  <Form.Item name={['by_user', user.ID, 'commands']} label={__('Enabled Commands')}>
                    <Select mode="multiple">
                      {commands?.map(command => (
                        <Select.Option key={command} value={command}>
                          {command}
                        </Select.Option>
                      ))}
                    </Select>
                  </Form.Item>
                </Card>
              ))}
            </Space>
          </Card>
          <Card title={__('Guest User Settings')}>
            <Form.Item
              name={['guest', 'path']}
              label={__('Path')}
              rules={[
                {
                  // eslint-disable-next-line no-useless-escape
                  pattern: new RegExp(`^${wpRoot}?(?:\/[^\/]+)*\/?$`),
                  message: __('Folder Path Must be within WordPress root directory')
                }
              ]}
            >
              <Input placeholder="Root Folder Path" />
            </Form.Item>
            <Form.Item name={['guest', 'can_download']} label={__('can download?')}>
              <Switch />
            </Form.Item>
          </Card>
          <Form.Item>
            <Space style={{ display: 'flex', justifyContent: 'center' }}>
              <Form.Item>
                <Button type="primary" htmlType="submit" loading={isPermissionUpdating}>
                  Update
                </Button>
              </Form.Item>
            </Space>
          </Form.Item>
        </Space>
      </Form>
    </>
  )
}

export default Permissions
