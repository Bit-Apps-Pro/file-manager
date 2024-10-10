import request from '@common/helpers/request'
import { type PermissionConfig } from '@pages/Permissions/PermissionsSettingsTypes'
import { useMutation } from '@tanstack/react-query'

type UpdatePermissionPayload = { id: number } & PermissionConfig
export default function useAddUserPermission() {
  const { mutateAsync, isLoading } = useMutation(async (permission: UpdatePermissionPayload) =>
    request({
      action: 'permissions/user/add',
      data: permission
    })
  )

  return {
    addPermission: (permission: UpdatePermissionPayload) => mutateAsync(permission),
    addingUserPermission: isLoading
  }
}
