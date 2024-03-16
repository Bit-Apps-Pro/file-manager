import request from '@common/helpers/request'
import { useQuery } from '@tanstack/react-query'

type LangType = {
  code: string
  name: string
}
export default function useFetchLang() {
  const { data, isLoading, isFetching } = useQuery({
    queryKey: ['fetch_languages'],
    queryFn: async () => request<Array<LangType>>({ action: 'language/get', method: 'GET' }),
    staleTime: 4320000
  })
  return {
    isLoading,
    isFetching,
    languages: data?.data
  }
}
