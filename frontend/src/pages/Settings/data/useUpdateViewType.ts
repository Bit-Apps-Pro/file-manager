import request from '@common/helpers/request'
import { useMutation } from '@tanstack/react-query'

export default function useUpdateViewType() {
  const { mutateAsync, isLoading } = useMutation(async (viewType: string) =>
    request<string & Record<string, unknown>>({
      action: 'settings/toggle-view',
      data: { viewType }
    })
  )

  return {
    toggleViewType: (viewType: string) => mutateAsync(viewType),
    isSettingsUpdating: isLoading
  }
}
