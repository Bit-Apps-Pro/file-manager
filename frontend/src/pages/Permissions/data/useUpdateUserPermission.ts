import request from '@common/helpers/request'
import { type UserPermissionType } from '@pages/Permissions/PermissionsSettingsTypes'
import { useMutation } from '@tanstack/react-query'

export default function useUpdateUserPermission() {
  const { mutateAsync, isLoading } = useMutation(async (updatedPermission: UserPermissionType) =>
    request<UserPermissionType & Record<string, unknown>>({
      action: 'permissions/user/add',
      data: updatedPermission
    })
  )

  return {
    updateUserPermission: (updatedPermission: UserPermissionType) => mutateAsync(updatedPermission),
    isUserPermissionUpdating: isLoading
  }
}
