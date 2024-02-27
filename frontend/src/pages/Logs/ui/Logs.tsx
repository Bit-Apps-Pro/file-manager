import { useParams } from 'react-router-dom'

import config from '@config/config'
import { type LogType } from '@pages/Logs/data/useFetchLogs'
import useFetchLogs from '@pages/Logs/data/useFetchLogs'
import { Card, Col, Row, Space, Table } from 'antd'
import { type TableColumnsType } from 'antd'

const { USERS } = config
const columns: TableColumnsType<LogType> = [
  Table.SELECTION_COLUMN,
  { title: 'Id', dataIndex: 'id', key: 'id' },
  {
    title: 'User',
    dataIndex: 'user_id',
    key: 'user',
    render: userId => USERS[userId]?.display_name ?? ''
  },
  { title: 'Command', dataIndex: 'command', key: 'command' },
  {
    title: 'Details',
    dataIndex: 'details',
    key: 'details',
    render: logDetails => (
      <Space>
        <Row>
          <Col>Driver</Col>
          <Col>Files</Col>
        </Row>
        <hr />
        <Row>
          <Col>{logDetails?.driver}</Col>
          <Col title="Files">{logDetails?.files?.map(file => <ol>{file?.path}</ol>)}</Col>
        </Row>
      </Space>
    )
  },
  { title: 'Created', dataIndex: 'created_at', key: 'created_at' }
]

export default function Logs() {
  const { page } = useParams()
  const pageNo = Number(page) || 1
  const limit = 14

  const { isLoading, isLogsFetching, logs, total } = useFetchLogs({
    pageNo,
    limit
  })

  return (
    <Table columns={columns} rowSelection={{}} dataSource={logs} loading={isLoading || isLogsFetching} />
  )
}
