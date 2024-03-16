/* eslint-disable react/jsx-props-no-spreading */

/* eslint-disable import/no-extraneous-dependencies */
import { type Meta, type StoryFn } from '@storybook/react'

import InputGroup from './InputGroup'

// eslint-disable-next-line react/function-component-definition
const Template: StoryFn<typeof InputGroup> = () => <InputGroup />

export const Primary = Template.bind({})

export default {
  title: 'Component/InputGroup',
  component: InputGroup
} as Meta<typeof InputGroup>
