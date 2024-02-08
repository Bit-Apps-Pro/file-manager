export type ThemeType = {
  key: string
  title: string
}

export type LanguageType = {
  code: string
  name: string
}

export interface SettingsType {
  show_url_path: string
  language: string
  size: FinderWindowSize
  default_view_type: 'icons' | 'list'
  display_ui_options: Array<'toolbar' | 'places' | 'tree' | 'path' | 'stat'>
  root_folder_path: string
  root_folder_url: string
  theme: string
  fm_root_folder_name: string
  show_hidden_files: boolean
  create_hidden_files_folders: boolean
  remember_last_dir: boolean
  clear_history_on_reload: boolean
  create_trash_files_folders: boolean
}

export type FinderWindowSize = {
  width: number
  height: number
  unit?: 'px'
}

export type DefaultOptionType = {
  path: string
  url: string
}

interface FetchSettingsType {
  settings: SettingsType
  languages: Array<LanguageType>
  themes: Array<ThemeType>
  defaults: DefaultOptionType
}
