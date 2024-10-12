import { useState } from 'react'

import { __ } from '@common/helpers/i18nwrap'
import { type User, type UserPermissionType } from '@pages/Permissions/PermissionsSettingsTypes'
import useFetchPermissionsSettings from '@pages/Permissions/data/useFetchPermissionsSettings'
import useFetchUserByUsername from '@pages/Permissions/data/useFetchUserByUsername'
import useUpdateUserPermission from '@pages/Permissions/data/useUpdateUserPermission'
import { Button, Card, Form, Input, Modal, Select, Space, Spin, notification } from 'antd'

function AddUserPermissionModal({
  isModalOpen,
  setIsModalOpen,
  commands
}: {
  isModalOpen: boolean
  setIsModalOpen: React.Dispatch<React.SetStateAction<boolean>>
  commands: Array<string>
}) {
  const { refetch } = useFetchPermissionsSettings()
  const { isUserPermissionUpdating, updateUserPermission } = useUpdateUserPermission()
  const [searchQuery, setSearchQuery] = useState('')
  const { users, fetchNextPage, isFetching, isFetchingNextPage } = useFetchUserByUsername(searchQuery)
  const { useForm } = Form
  const [form] = useForm()

  const [selectedUser, setSelectedUser] = useState<User>({} as User)

  const handleSearch = (value: string) => {
    setSearchQuery(value)
  }

  const handleScroll = () => {
    if (isFetching && isFetchingNextPage) {
      return
    }
    fetchNextPage()
  }

  const handleChange = (_: number, option: any) => {
    if (option?.user) {
      setSelectedUser(option?.user)
      form.resetFields()
      form.setFieldValue('id', option?.user?.id)
    }
  }
  const handleSubmit = (changedValues: UserPermissionType) => {
    updateUserPermission(changedValues).then(response => {
      if (response.code === 'SUCCESS') {
        notification.success({ message: response?.message ?? __('User permission deleted') })
        refetch()
        const updatedFields = form.getFieldsError().map(field => {
          if (field.errors) {
            field.errors = []
          }
          return field
        })
        form.setFields(updatedFields)
      } else {
        const fieldErrors: { name: string[]; errors: string[] }[] = []
        Object.keys(response.data).forEach(field => {
          fieldErrors.push({
            name: field.split('.'),
            errors: response.data[field] as string[]
          })
          notification.error({ message: response?.message ?? __('Failed to save permission') })
        })
        form.setFields(fieldErrors)
      }
    })
  }

  return (
    <Modal
      open={isModalOpen}
      onClose={() => setIsModalOpen(false)}
      onCancel={() => setIsModalOpen(false)}
      centered
      footer={false}
      title="Set Permission for selected user"
    >
      <Space direction="vertical" style={{ display: 'flex' }} className="px-2">
        <Select
          showSearch
          style={{ width: '100%' }}
          placeholder="Search User"
          onSearch={handleSearch}
          onChange={handleChange}
          loading={isFetching}
          notFoundContent={isFetching ? <Spin size="small" /> : 'No users found'}
          filterOption={false} // Disable default filtering, we'll handle it with API
          options={users.map(user => ({ value: user.ID, user, label: user.display_name }))}
          allowClear
          onPopupScroll={handleScroll}
        />
        {selectedUser?.display_name && (
          <Form
            form={form}
            onFinish={handleSubmit}
            disabled={isUserPermissionUpdating}
            colon={false}
            scrollToFirstError
          >
            <Space direction="vertical" size="middle" style={{ display: 'flex' }} className="px-2">
              <Card title={selectedUser.display_name} key={`permissions-for-${selectedUser.ID}`}>
                <Form.Item name="id" initialValue={selectedUser.ID} hidden>
                  <Input />
                </Form.Item>
                <Form.Item name="path" label={__('Path')}>
                  <Input placeholder="Root Folder Path" />
                </Form.Item>
                <Form.Item name="commands" label={__('Enabled Commands')}>
                  <Select mode="multiple">
                    {commands?.map(command => (
                      <Select.Option key={command} value={command}>
                        {command}
                      </Select.Option>
                    ))}
                  </Select>
                </Form.Item>
                <Form.Item style={{ marginBottom: 0 }}>
                  <Button type="primary" htmlType="submit" loading={isUserPermissionUpdating}>
                    Save
                  </Button>
                </Form.Item>
              </Card>
            </Space>
          </Form>
        )}
      </Space>
    </Modal>
  )
}

export default AddUserPermissionModal
