import request from '@common/helpers/request'
import { type string } from '@pages/Settings/settingsTypes'
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
