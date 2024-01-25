/* eslint-disable react/jsx-props-no-spreading */

/* eslint-disable import/no-extraneous-dependencies */
import cls from '@features/FlowItem/FlowItem.module.css'
import DeleteIcon from '@icons/DeleteIcon'
import DotsVertical from '@icons/DotsVertical'
import { type Meta, type StoryFn } from '@storybook/react'

import DropDown from './DropDown'

export default {
  title: 'Component/DropDown',
  component: DropDown
} as Meta<typeof DropDown>

// eslint-disable-next-line react/function-component-definition
const Template: StoryFn<typeof DropDown> = () => (
  <DropDown>
    <DotsVertical size={18} className={cls.DotVertical} />
    <div className={cls.DropDownMenu}>
      <button type="button" className={cls.DropDownMenuItem}>
        <DeleteIcon size={16} className={cls.deleteIcon} />
        <span className={cls.DropDownMenuItemText}>Delete</span>
      </button>
    </div>
  </DropDown>
)

export const Canvas = Template.bind({})
