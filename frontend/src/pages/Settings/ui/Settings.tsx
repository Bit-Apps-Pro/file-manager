import { __ } from '@common/helpers/i18nwrap'
import useFetchSettings from '@pages/Settings/data/useFetchSettings'
import SpinnerLoader from '@utilities/SpinnerLoader'
import { Form, Input, Select, Switch, Typography } from 'antd'

const { Title, Text } = Typography

export default function Settings() {
  const { isLoading, isFetching, settings, themes, languages, defaults } = useFetchSettings()

  if (isLoading || isFetching) {
    return (
      <div className="p-6">
        <SpinnerLoader size={20} />
      </div>
    )
  }

  return (
    <div className="p-6">
      <Form style={{ maxWidth: 600 }}>
        <Title level={5}>{__('URL and Path')}</Title>
        <Form.Item label="Show Url" valuePropName="show_url_path">
          <Switch />
        </Form.Item>
        <Form.Item label="Root Path" valuePropName="root_folder_path">
          <Input />
          <Title level={5}>
            {__('Default Path:')} {defaults?.path}
          </Title>
        </Form.Item>
        <Form.Item label="Root URL" valuePropName="root_folder_url">
          <Input />
          <Title level={5}>
            {__('Default URL: ')}
            <Text type="secondary">{defaults?.url}</Text>
          </Title>
        </Form.Item>
        <Title level={5}>
          {__("Root folder path and URL must be correct, otherwise it won't work.")}
        </Title>
        <Form.Item label={__('Select Language')}>
          <Select>
            {languages?.map(language => (
              <Select.Option value={language.code} key={language.code}>
                {language.name}
              </Select.Option>
            ))}
          </Select>
        </Form.Item>
        <Form.Item label={__('Select Theme')}>
          <Select>
            {themes?.map(fmTheme => (
              <Select.Option value={fmTheme.key} key={fmTheme.key}>
                {fmTheme.title}
              </Select.Option>
            ))}
          </Select>
        </Form.Item>
        <Title level={5}>{__('Size')}</Title>
        <Form.Item label="Width" valuePropName="width">
          <Input />
        </Form.Item>
        <Form.Item label="Height" valuePropName="height">
          <Input />
        </Form.Item>
        <Form.Item label={__('Show Hidden Files')} valuePropName="show_hidden_files">
          <Switch />
        </Form.Item>
        <Form.Item
          label={__('Allow Create/Upload Hidden Files/Folders')}
          valuePropName="fm-create-hidden-files-folders"
        >
          <Switch />
        </Form.Item>
        <Form.Item label={__('Allow Trash')} valuePropName="fm-create-trash-files-folders">
          <Switch />
        </Form.Item>
        <Form.Item label={__('Root Folder Name')} valuePropName="fm_root_folder_name">
          <Input />
        </Form.Item>
        <Form.Item label={__('Default View Type')}>
          <Select mode="multiple">
            <Select.Option value="icons">Icons</Select.Option>
            <Select.Option value="list">List</Select.Option>
          </Select>
        </Form.Item>
        <Form.Item label={__('Remember Last Directory')} valuePropName="fm-remember-last-dir">
          <Switch />
          {__('Remeber last opened dir to open it after reload.')}
        </Form.Item>
        <Form.Item label={__('Clear History On Reload')} valuePropName="fm-clear-history-on-reload">
          <Switch />
          {__('Clear historys(elFinder) on reload(not browser)')}
        </Form.Item>
        <Form.Item label={__('Default View Type')}>
          <Select mode="multiple">
            <Select.Option value="toolbar">Toolbar</Select.Option>
            <Select.Option value="places">Places</Select.Option>
            <Select.Option value="tree">Tree</Select.Option>
            <Select.Option value="path">Path</Select.Option>
            <Select.Option value="stat">Stat</Select.Option>
          </Select>
        </Form.Item>
      </Form>
    </div>
  )
}
