import SearchIcon from '@icons/SearchIcon'
import { cleanup, render, screen } from '@testing-library/react'
import { afterEach, describe, expect, it } from 'vitest'

import IconBtn from './IconBtn'

/**
 * @vitest-environment jsdom
 */

describe('test IconBtn component', () => {
  afterEach(cleanup)
  it('should have text', () => {
    render(<IconBtn>button</IconBtn>)
    const btnElm = screen.getByRole('button')
    expect(btnElm.textContent).toBe('button')
    expect(btnElm.textContent).toBe('button')
    expect(btnElm.className).toMatch(/solid/i)
    expect(btnElm.className).toMatch(/default/i)
    expect(btnElm.className).toMatch(/md/i)
    expect(btnElm.getAttribute('type')).toBe('button')
  })
  it('should be render with icon', () => {
    render(
      <IconBtn>
        <SearchIcon size={20} stroke={2} />
      </IconBtn>
    )
    const btnElm = screen.getByTestId('searchIcon')
    expect(btnElm).toBeTruthy()
  })
  it('should be render a submit type IconBtn ', () => {
    render(
      <IconBtn type="submit">
        <SearchIcon size={20} stroke={2} />
      </IconBtn>
    )
    const btnElm = screen.getByRole('button')
    expect(btnElm.getAttribute('type')).toBe('submit')
  })
  it('should be render a reset type IconBtn with round', () => {
    render(
      <IconBtn round type="reset">
        <SearchIcon size={20} stroke={2} />
      </IconBtn>
    )
    const btnElm = screen.getByRole('button')
    expect(btnElm.getAttribute('type')).toBe('reset')
    expect(btnElm.className).toMatch(/round/i)
  })
})
