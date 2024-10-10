import { useState } from 'react'

import {
  type LogQueryType,
  type LogType,
  type LoggedFileDetailsType
} from '@pages/Logs/data/useFetchLogs'
import useFetchLogs from '@pages/Logs/data/useFetchLogs'
import { Col, Row, Space, Table } from 'antd'
import { type TableColumnsType } from 'antd'

const columns: TableColumnsType<LogType> = [
  { title: 'Id', dataIndex: 'id', key: 'id' },
  {
    title: 'User',
    dataIndex: 'user',
    key: 'user',
    render: user => user?.display_name ?? ''
  },
  { title: 'Command', dataIndex: 'command', key: 'command' },
  {
    title: 'Details',
    dataIndex: 'details',
    key: 'details',
    render: (details, _record, index) => (
      <Space>
        <Row>
          <Col>{details?.driver && `Driver: ${details?.driver}`}</Col>
        </Row>
        <hr />
        <Row>
          <Col>
            {details?.files && (
              <>
                <span>Files:</span>
                <li>
                  {details?.files?.map((file: LoggedFileDetailsType) => (
                    <ol key={`${index}-${file?.path}`}>{file?.path}</ol>
                  ))}
                </li>
              </>
            )}
          </Col>
        </Row>
      </Space>
    )
  },
  { title: 'Created', dataIndex: 'created_at', key: 'created_at' }
]

export default function Logs() {
  const [pagination, setPagination] = useState<LogQueryType>({
    pageNo: 1,
    limit: 20
  } as LogQueryType)
  const { isLoading, isLogsFetching, logs, total } = useFetchLogs(pagination)

  const onChange = (page: number, pageSize: number) => {
    setPagination({ pageNo: page, limit: pageSize })
  }

  return (
    <Table
      columns={columns}
      rowSelection={{}}
      dataSource={logs}
      loading={isLoading || isLogsFetching}
      pagination={{
        current: pagination.pageNo,
        total,
        pageSize: pagination.limit,
        onChange,
        position: ['bottomRight', 'topLeft']
      }}
    />
  )
}
