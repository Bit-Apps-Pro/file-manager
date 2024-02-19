import { useEffect } from 'react'

import { __ } from '@common/helpers/i18nwrap'
import useFetchSettings from '@pages/Settings/data/useFetchSettings'
import useUpdateSettings from '@pages/Settings/data/useUpdateSettings'
import { Card, Form, Input, Select, Space, Switch } from 'antd'
import { useForm } from 'antd/es/form/Form'

export default function Settings() {
  const { settings, themes, languages, defaults } = useFetchSettings()
  const { updateSettings, isSettingsUpdating } = useUpdateSettings()
  const [form] = useForm()

  useEffect(() => {
    form.setFieldsValue(settings)
  }, [settings, form])

  const handleValueChanges = changedValues => {
    const changedField = changedValues ? Object.keys(changedValues)[0] : null
    console.log('first', Object.keys(changedValues), { changedValues, changedField })
    if (changedField) {
      const fieldData = {
        name: changedField,
        validating: true
      }
      console.log('changedField', form.isFieldValidating(changedField))
      if (!form.isFieldValidating(changedField)) {
        form.setFields([fieldData])
        console.log('Not validating')
        updateSettings(changedValues).then(response => console.log(response))
      }
    }
  }

  return (
    <Form form={form} initialValues={defaults} colon={false} onValuesChange={handleValueChanges}>
      <Space direction="vertical" size="middle" style={{ display: 'flex' }} className="p-6">
        <Card title={__('URL and Path')}>
          <Form.Item label="Show Url" name="show_url_path" hasFeedback>
            <Switch />
          </Form.Item>
          <Form.Item
            label="Root Path"
            name="root_folder_path"
            tooltip={`${__('Root folder path must be correct. Default: ')}${defaults?.root_folder_path}`}
            hasFeedback
          >
            <Input />
          </Form.Item>
          <Form.Item
            label="Root URL"
            name="root_folder_url"
            tooltip={`${__('Root folder URL must be correct. Default: ')}${defaults?.root_folder_url}`}
          >
            <Input />
          </Form.Item>
          <Form.Item label={__('Root Folder Name')} name="root_folder_name">
            <Input />
          </Form.Item>
        </Card>
        <Card title={__('File Manager Settings')}>
          <Form.Item label={__('Select Language')}>
            <Select value={settings?.language}>
              {languages?.map(language => (
                <Select.Option value={language.code} key={language.code}>
                  {language.name}
                </Select.Option>
              ))}
            </Select>
          </Form.Item>
          <Form.Item label={__('Select Theme')} name="theme">
            <Select>
              {themes?.map(fmTheme => (
                <Select.Option value={fmTheme.key} key={fmTheme.key}>
                  {fmTheme.title}
                </Select.Option>
              ))}
            </Select>
          </Form.Item>
          <Card title={__('Size')}>
            <Form.Item
              label="Width"
              name={['size', 'width']}
              tooltip="File Manager window width (in px)"
            >
              <Input />
            </Form.Item>
            <Form.Item
              label="Height"
              name={['size', 'height']}
              tooltip="File Manager window height (in px)"
            >
              <Input />
            </Form.Item>
          </Card>
          <Form.Item label={__('Show Hidden Files')} name="show_hidden_files">
            <Switch />
          </Form.Item>
          <Form.Item
            label={__('Allow Create/Upload Hidden Files/Folders')}
            name="create_hidden_files_folders"
          >
            <Switch />
          </Form.Item>
          <Form.Item label={__('Allow Trash')} name="create_trash_files_folders">
            <Switch />
          </Form.Item>
          <Form.Item label={__('Display UI options')} name="display_ui_options">
            <Select mode="multiple">
              <Select.Option value="toolbar">Toolbar</Select.Option>
              <Select.Option value="places">Places</Select.Option>
              <Select.Option value="tree">Tree</Select.Option>
              <Select.Option value="path">Path</Select.Option>
              <Select.Option value="stat">Stat</Select.Option>
            </Select>
          </Form.Item>
          <Form.Item
            label={__('Remember Last Directory')}
            name="remember_last_dir"
            tooltip={__('Remember last opened dir to open it after reload.')}
          >
            <Switch />
          </Form.Item>
          <Form.Item
            label={__('Clear History On Reload')}
            name="clear_history_on_reload"
            tooltip={__("Clear history's(elFinder) on reload(not browser)")}
          >
            <Switch />
          </Form.Item>
          <Form.Item label={__('Default View Type')} name="default_view_type">
            <Select>
              <Select.Option value="icons">Icons</Select.Option>
              <Select.Option value="list">List</Select.Option>
            </Select>
          </Form.Item>
        </Card>
      </Space>
    </Form>
  )
}
