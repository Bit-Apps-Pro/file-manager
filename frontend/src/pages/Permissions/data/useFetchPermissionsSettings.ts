import request from '@common/helpers/request'
import { type FetchPermissionsSettingsType } from '@pages/Permissions/PermissionsSettingsTypes'
import { useQuery } from '@tanstack/react-query'

export default function useFetchPermissionsSettings() {
  const { data, isLoading, isFetching, refetch } = useQuery({
    refetchOnWindowFocus: false,
    staleTime: 120000,
    queryKey: ['fetch_permissions_settings'],
    queryFn: async () =>
      request<FetchPermissionsSettingsType>({ action: 'permissions/get', method: 'GET' })
  })

  return {
    isLoading,
    isFetching,
    refetch,
    permissions: data?.data.permissions,
    roles: data?.data.roles,
    users: data?.data.users,
    commands: data?.data.commands,
    fileTypes: data?.data.fileTypes,
    wpRoot: data?.data.wpRoot
  }
}
