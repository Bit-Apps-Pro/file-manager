/* eslint-disable react/jsx-props-no-spreading */
import { cleanup, fireEvent, render } from '@testing-library/react'
import { afterEach, describe, expect, it, vitest } from 'vitest'

import TagFilter from './TagFilter'

const tagsList: TagsListType = {
  tagsList: [
    { id: 1, label: 'Tag 1', pinned: true, active: true },
    { id: 2, label: 'Tag 2', pinned: true, active: false },
    { id: 3, label: 'Tag 3', pinned: false, active: false }
  ],
  onAdd: vitest.fn(),
  onEdit: vitest.fn(),
  onRemove: vitest.fn(),
  onPin: vitest.fn(),
  onUnpin: vitest.fn(),
  onActive: vitest.fn(),
  onInactive: vitest.fn(),
  className: 'mt-2 mb-4'
}

describe('test Tags component', () => {
  afterEach(cleanup)

  it('renders the tags list', () => {
    const { queryByLabelText } = render(<TagFilter {...tagsList} />)
    expect(queryByLabelText('pinned-tag-1')).toBeTruthy()
    expect(queryByLabelText('pinned-tag-2')).toBeTruthy()
    expect(queryByLabelText('pinned-tag-3')).toBeFalsy()
  })

  it('renders the tags wrapper className', () => {
    const { getByLabelText } = render(<TagFilter {...tagsList} />)
    expect(getByLabelText('tags-wrapper').className).toMatch(/mt-2 mb-4/i)
  })

  it('calls the onActive function when all tag clicked', () => {
    const { getByLabelText } = render(<TagFilter {...tagsList} />)
    const allTag = getByLabelText('All tags')
    fireEvent.click(allTag)
    expect(tagsList.onActive).toHaveBeenCalledWith(0)
  })

  it('calls the onInactive function with the correct tag ID when active tag is clicked', () => {
    const { getByLabelText } = render(<TagFilter {...tagsList} />)
    const tag1 = getByLabelText('pinned-tag-1')
    fireEvent.click(tag1)
    expect(tagsList.onInactive).toHaveBeenCalledWith(1)
  })

  it('calls the onActive function with the correct tag ID when tag is clicked', () => {
    const { getByLabelText } = render(<TagFilter {...tagsList} />)
    const tag2 = getByLabelText('pinned-tag-2')
    fireEvent.click(tag2)
    expect(tagsList.onActive).toHaveBeenCalledWith(2)
  })

  it('tags dropdown open when click on 3 dot button', () => {
    const { getByLabelText, queryByLabelText } = render(<TagFilter {...tagsList} />)
    const moreTags = getByLabelText('more-tags')
    fireEvent.click(moreTags)
    expect(queryByLabelText('tags list')).toBeTruthy()
  })

  it('calls the onInactive function with the correct tag ID when active tag is clicked inside dropdown', () => {
    const { getByLabelText } = render(<TagFilter {...tagsList} />)
    fireEvent.click(getByLabelText('more-tags'))

    const dropdownTag1 = getByLabelText('tag-1')
    fireEvent.click(dropdownTag1)
    expect(tagsList.onInactive).toHaveBeenCalledWith(1)
  })

  it('calls the onActive function with the correct tag ID when tag is clicked inside dropdown', () => {
    const { getByLabelText } = render(<TagFilter {...tagsList} />)
    fireEvent.click(getByLabelText('more-tags'))

    const dropdownTag2 = getByLabelText('tag-2')
    fireEvent.click(dropdownTag2)
    expect(tagsList.onActive).toHaveBeenCalledWith(2)
  })

  it('calls the onAdd function when add tag button is clicked', () => {
    const { getByLabelText } = render(<TagFilter {...tagsList} />)
    fireEvent.click(getByLabelText('more-tags'))

    const addButton = getByLabelText('add-tag')
    fireEvent.click(addButton)
    expect(tagsList.onAdd).toHaveBeenCalled()
  })

  it('calls the onEdit function with the correct tag ID when edit tag button is clicked', () => {
    const { getByLabelText } = render(<TagFilter {...tagsList} />)
    fireEvent.click(getByLabelText('more-tags'))

    const editButton = getByLabelText('edit-tag-2')
    fireEvent.click(editButton)
    expect(tagsList.onEdit).toHaveBeenCalledWith(2)
  })

  it('calls the onRemove function with the correct tag ID when delete tag button is clicked', () => {
    const { getByLabelText } = render(<TagFilter {...tagsList} />)
    fireEvent.click(getByLabelText('more-tags'))

    const deleteButton = getByLabelText('delete-tag-1')
    fireEvent.click(deleteButton)
    expect(tagsList.onRemove).toHaveBeenCalledWith(1)
  })

  it('calls the onPin function with the correct tag ID when pin tag button is clicked', () => {
    const { getByLabelText } = render(<TagFilter {...tagsList} />)
    fireEvent.click(getByLabelText('more-tags'))

    const pinButton = getByLabelText('pin-unpin-tag-3')
    fireEvent.click(pinButton)
    expect(tagsList.onPin).toHaveBeenCalledWith(3)
  })

  it('calls the onUnpin function with the correct tag ID when unpin tag button is clicked', () => {
    const { getByLabelText } = render(<TagFilter {...tagsList} />)
    fireEvent.click(getByLabelText('more-tags'))

    const unpinButton = getByLabelText('pin-unpin-tag-2')
    fireEvent.click(unpinButton)
    expect(tagsList.onUnpin).toHaveBeenCalledWith(2)
  })
})
