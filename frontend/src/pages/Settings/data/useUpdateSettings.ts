import request from '@common/helpers/request'
import { type SettingsType } from '@pages/Settings/settingsTypes'
import { useMutation } from '@tanstack/react-query'

export default function useUpdateSettings() {
  const { mutateAsync, isLoading } = useMutation(async (settingsToUpdate: SettingsType) =>
    request({ action: 'settings/update', data: settingsToUpdate })
  )

  return {
    updateSettings: (updatedSettings: SettingsType) => mutateAsync(updatedSettings),
    isSettingsUpdating: isLoading
  }
}
