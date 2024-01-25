import { cleanup, fireEvent, render, screen } from '@testing-library/react'
import { afterEach, describe, expect, it, vi } from 'vitest'

import Segment from './Segment'
import SegmentTab from './SegmentTab'

describe('test Segment component', () => {
  afterEach(cleanup)
  it('should be render with children', () => {
    const mockOnChange = vi.fn()
    const { container } = render(
      <Segment onChange={mockOnChange} value="hello">
        <SegmentTab tip="hello world man" value="hello">
          <div>hello</div>
        </SegmentTab>
        <SegmentTab tip="hello world man" value="right">
          <div>Right</div>
        </SegmentTab>
      </Segment>
    )
    const segmentChildren = screen.getAllByRole('button')
    expect(segmentChildren).length(2)
    const segmentFirstElement = container.firstElementChild
    expect(segmentFirstElement?.className).toMatch(/md/i)
    expect(segmentChildren.length).toBe(2)
    fireEvent.click(segmentChildren[0])
    fireEvent.click(segmentChildren[1])
    expect(mockOnChange).toHaveBeenCalledTimes(2)
  })
  it('should be render with rounded, underline variant and sm size', () => {
    const mockOnChange = vi.fn()
    const { container } = render(
      <Segment rounded variant="underline" onChange={mockOnChange} size="sm" value="hello">
        <SegmentTab tip="hello world man" value="hello">
          <div>hello</div>
        </SegmentTab>
        <SegmentTab tip="i am right" value="right">
          <div>right</div>
        </SegmentTab>
      </Segment>
    )
    const hello = screen.getByText('hello')
    expect(hello.innerHTML).toBe('hello')
    const rightTab = screen.getByText('right')
    expect(rightTab.innerHTML).toBe('right')
    fireEvent.click(rightTab)
    const segmentFirstElement = container.firstElementChild
    expect(segmentFirstElement?.className).toMatch(/rounded/i)
    expect(segmentFirstElement?.className).toMatch(/sm/i)
  })
})
