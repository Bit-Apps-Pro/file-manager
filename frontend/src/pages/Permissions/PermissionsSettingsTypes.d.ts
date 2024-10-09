export interface PermissionConfig {
  commands: Array<string>
  path: string
}

export interface PermissionsSettingsType {
  do_not_use_for_admin: boolean
  file_type: 'text' | 'image' | 'application' | 'video' | 'audio'
  file_size: number
  folder_options: 'common' | 'role' | 'user'
  by_role: Array<string, PermissionConfig>
  by_user: Array<string, PermissionConfig>
}
export interface UserPermissionType extends PermissionConfig {
  user_id: number
}

export type User = {
  ID: number
  user_login: string
  display_name: string
}

export type DefaultOptionType = Partial<PermissionsSettingsType>

interface FetchPermissionsSettingsType {
  permissions: PermissionsSettingsType
  roles: Array<string>
  users: Array<User>
  commands: Array<string>
  fileTypes: Array<string>
  wpRoot: string
}

interface FetchUsersType {
  users: Array<User>
  total: number
  pages: number
  current: number
}
