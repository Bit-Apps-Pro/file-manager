import request from '@common/helpers/request'
import { type FetchPermissionsSettingsType } from '@pages/Permissions/PermissionsSettingsTypes'
import { useQuery } from '@tanstack/react-query'

export default function useFetchPermissionsSettings() {
  const { data, isLoading, isFetching } = useQuery({
    queryKey: ['fetch_permissions_settings'],
    queryFn: async () =>
      request<FetchPermissionsSettingsType>({ action: 'permissions/get', method: 'GET' })
  })
  return {
    isLoading,
    isFetching,
    permissions: data?.data.permissions,
    roles: data?.data.roles,
    users: data?.data.users,
    commands: data?.data.commands,
    fileTypes: data?.data.fileTypes
  }
}
