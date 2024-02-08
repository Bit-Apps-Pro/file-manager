import request from '@common/helpers/request'
import { type FetchSettingsType } from '@pages/Settings/settingsTypes'
import { useQuery } from '@tanstack/react-query'

export default function useFetchSettings() {
  const { data, isLoading, isFetching } = useQuery({
    queryKey: ['fetch_settings'],
    queryFn: async () => request<FetchSettingsType>({ action: 'settings/get', method: 'GET' })
  })
  return {
    isLoading,
    isFetching,
    settings: data?.data.settings,
    languages: data?.data.languages,
    themes: data?.data.themes,
    defaults: data?.data.defaults
  }
}
