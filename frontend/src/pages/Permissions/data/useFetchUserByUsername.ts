import request from '@common/helpers/request'
import { type FetchUsersType } from '@pages/Permissions/PermissionsSettingsTypes'
import { useQuery } from '@tanstack/react-query'

export default function useFetchUserByUsername(userName: string, page: number) {
  const { data, isLoading, isFetching } = useQuery({
    refetchOnWindowFocus: false,
    queryKey: ['user/get', userName],
    queryFn: async () =>
      request<FetchUsersType>({ action: 'user/get', method: 'GET', data: { userName, page } }),
    keepPreviousData: true,
    enabled: !!userName
  })

  return {
    isLoading,
    isFetching,
    users: data?.data.users || [],
    totalPages: data?.data.totalPages || 0
  }
}
