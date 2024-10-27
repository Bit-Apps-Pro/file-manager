export interface BreadcrumbItemType {
  key?: React.Key
  /**
   * Different with `path`. Directly set the link of this item.
   */
  href?: string
  /**
   * Different with `href`. It will concat all prev `path` to the current one.
   */
  path?: string
  title?: React.ReactNode
  breadcrumbName?: string
  menu?: BreadcrumbItemProps['menu']
  /** @deprecated Please use `menu` instead */
  overlay?: React.ReactNode
  className?: string
  dropdownProps?: DropdownProps
  onClick?: React.MouseEventHandler<HTMLAnchorElement | HTMLSpanElement>
  /** @deprecated Please use `menu` instead */
  children?: Omit<BreadcrumbItemType, 'children'>[]
}
export interface BreadcrumbSeparatorType {
  type: 'separator'
  separator?: React.ReactNode
}
