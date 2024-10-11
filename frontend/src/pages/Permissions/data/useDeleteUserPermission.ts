import request from '@common/helpers/request'
import { useMutation } from '@tanstack/react-query'

export default function useDeleteUserPermission() {
  const { mutateAsync, isLoading, variables } = useMutation(async (id: number) =>
    request({
      action: 'permissions/user/delete',
      data: { id }
    })
  )

  return {
    deletePermission: (id: number) => mutateAsync(id),
    isUserPermissionDeleting: isLoading,
    delInProgressId: variables
  }
}
