type TagList = {
  id: number
  label: string
  pinned: boolean
  active: boolean
  filter?: JSON
}

type TagsListType = {
  tagsList: TagList[]
  onAdd?: () => void
  onEdit?: (tagId: number) => void
  onRemove?: (tagId: number) => void
  onPin?: (tagId: number) => void
  onUnpin?: (tagId: number) => void
  onActive: (tagId: number) => void
  onInactive: (tagId: number) => void
  onFilter?: (tagId: number) => void
  className?: string
}
