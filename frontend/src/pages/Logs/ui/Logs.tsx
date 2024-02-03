import type React from 'react'

import { Table } from 'antd'
import { type TableColumnsType } from 'antd'

interface DataType {
  key: React.Key
  user: string
  command: string
  details: number
  created: string
  description: string
}

const columns: TableColumnsType<DataType> = [
  Table.SELECTION_COLUMN,
  { title: 'User', dataIndex: 'user', key: 'user' },
  Table.EXPAND_COLUMN,
  { title: 'Command', dataIndex: 'command', key: 'command' },
  { title: 'Details', dataIndex: 'details', key: 'details' },
  { title: 'Created', dataIndex: 'created', key: 'created' }
]

const data: DataType[] = [
  {
    key: 1,
    user: 'John Brown',
    command: 'John Brown',
    details: 32,
    created: 'New York No. 1 Lake Park',
    description: 'My name is John Brown, I am 32 years old, living in New York No. 1 Lake Park.'
  }
]

export default function Logs() {
  return (
    <Table
      columns={columns}
      rowSelection={{}}
      expandable={{
        expandedRowRender: record => <p style={{ margin: 0 }}>{record.description}</p>
      }}
      dataSource={data}
    />
  )
}
