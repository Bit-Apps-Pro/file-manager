import { type BreadcrumbItemType, type BreadcrumbSeparatorType } from 'antd/es/breadcrumb/Breadcrumb'
import { type FinderInstance } from 'elfinder'
import { atom } from 'jotai'

const $finder = atom<FinderInstance>({} as FinderInstance)
export const $finderCurrentPath = atom<Partial<BreadcrumbItemType & BreadcrumbSeparatorType>[]>([])
export const $finderViewType = atom('icons')

export default $finder
