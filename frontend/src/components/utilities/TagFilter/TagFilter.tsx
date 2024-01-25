import { type MouseEvent, useMemo, useState } from 'react'

import LucideIcn from '@icons/LucideIcn'
import PinIcon from '@icons/PinIcon'
import PinSolidIcon from '@icons/PinSolidIcon'
import { Button, Dropdown, type MenuProps, Space, theme } from 'antd'

import css from './TagFilter.module.css'
import TagListMenu from './TagListMenu'

export default function TagFilter({
  tagsList,
  onAdd,
  onEdit,
  onRemove,
  onPin,
  onUnpin,
  onActive,
  onInactive,
  className
}: TagsListType) {
  const { token } = theme.useToken()
  const [isTagsOpen, setIsTagsOpen] = useState(false)
  const isAllActive = useMemo(() => tagsList?.every(tag => !tag.active), [tagsList])

  const selectedTags = () => {
    if (isAllActive) return []
    return tagsList?.filter(tag => tag.active).map(tag => tag.id.toString())
  }

  const handleOnAdd = () => {
    onAdd?.()
    setIsTagsOpen(false)
  }

  const handleOnEdit = (tagId: number) => (e: MouseEvent) => {
    e.stopPropagation()
    onEdit?.(tagId)
    setIsTagsOpen(false)
  }

  const handleOnRemove = (tagId: number) => (e: MouseEvent) => {
    e.stopPropagation()
    onRemove?.(tagId)
    setIsTagsOpen(false)
  }

  const handleOnPin = (tagId: number, isPinned: boolean) => (e: MouseEvent) => {
    e.stopPropagation()
    if (!isPinned) {
      onPin?.(tagId)
    } else {
      onUnpin?.(tagId)
    }
  }

  const dropdownTags: MenuProps['items'] = tagsList?.map((tag: TagList) => {
    const textColor = tag.active ? `${token.colorPrimary} !important` : token.colorText

    return {
      key: tag.id,
      label: (
        <div className={css.tagItem} css={{ borderRadius: token.borderRadius, paddingBlock: 2 }}>
          <span className={css.tagItemTitle}>
            {onPin && (
              <Button
                type="text"
                size="small"
                className={`${css.tagItemActionBtn} ${tag.pinned && css.pinTag}`}
                onClick={handleOnPin(tag.id, tag.pinned)}
                icon={Number(tag.pinned) ? <PinSolidIcon size={12} /> : <PinIcon size={12} />}
                css={{ color: textColor }}
                aria-label={`pin-unpin-tag-${tag.id}`}
              />
            )}
            <span css={{ marginLeft: 4 }} aria-label={`tag-${tag.id}`}>
              {tag.label}
            </span>
          </span>
          <div className={`${css.tagItemAction}`}>
            {onEdit && (
              <Button
                type="text"
                size="small"
                className={css.tagItemActionBtn}
                onClick={handleOnEdit(tag.id)}
                icon={<LucideIcn name="Edit3" size={13} />}
                css={{ color: textColor }}
                aria-label={`edit-tag-${tag.id}`}
              />
            )}
            {onRemove && (
              <Button
                type="text"
                size="small"
                className={css.tagItemActionBtn}
                onClick={handleOnRemove(tag.id)}
                icon={<LucideIcn name="Trash2" size={13} />}
                css={{ color: textColor }}
                aria-label={`delete-tag-${tag.id}`}
              />
            )}
          </div>
        </div>
      )
    }
  })

  return (
    <Space size={4} className={className} aria-label="tags-wrapper">
      <Button
        shape="round"
        type={`${isAllActive ? 'primary' : 'default'}`}
        onClick={() => (isAllActive ? onInactive(0) : onActive(0))}
        aria-label="All tags"
      >
        All
      </Button>

      {tagsList?.map(
        (tag: TagList) =>
          tag.pinned && (
            <Button
              key={tag.id}
              shape="round"
              type={`${tag.active ? 'primary' : 'default'}`}
              onClick={() => (tag.active ? onInactive(tag.id) : onActive(tag.id))}
              aria-label={`pinned-tag-${tag.id}`}
            >
              {tag.label}
            </Button>
          )
      )}

      <Dropdown
        arrow
        trigger={['click']}
        placement="bottom"
        menu={{
          selectable: true,
          multiple: true,
          items: dropdownTags,
          selectedKeys: selectedTags(),
          onSelect: item => onActive(Number(item.key)),
          onDeselect: item => onInactive(Number(item.key)),
          className: css.tagList
        }}
        onOpenChange={setIsTagsOpen}
        open={isTagsOpen}
        dropdownRender={TagListMenu({ handleOnAdd, token, isAddable: !!onAdd })}
      >
        <Button shape="circle" icon={<LucideIcn name="MoreVertical" />} aria-label="more-tags" />
      </Dropdown>
    </Space>
  )
}
