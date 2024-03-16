import request from '@common/helpers/request'
import { useQuery } from '@tanstack/react-query'

interface SearchType {
  searchKeyValue?: {
    title: string
    tags: number[]
  }
  pageNo: number
  limit: number
}

export type LoggedFileDetailsType = {
  path: string
  hash: string
}

type LogDetailsType = {
  driver: string
  files: Array<LoggedFileDetailsType>
}

export type LogType = {
  id: number
  user_id: number
  command: string
  details: LogDetailsType
}

type FetchLogsType = {
  logs: Array<LogType>
  count: number
}

export default function useFetchLogs(searchData: SearchType) {
  const queryId = `logs-${searchData.pageNo}`

  const { data, isLoading, isFetching } = useQuery({
    queryKey: ['all_logs', queryId],
    queryFn: async () => request<FetchLogsType>({ action: 'logs/all', data: searchData })
  })
  return {
    isLoading,
    isLogsFetching: isFetching,
    logs: data?.data?.logs ?? [],
    total: data?.data?.count ?? 0
  }
}
