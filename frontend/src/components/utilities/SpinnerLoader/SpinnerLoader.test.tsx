import { cleanup, render, screen } from '@testing-library/react'
import { afterEach, describe, expect, it } from 'vitest'

import SpinnerLoader from './SpinnerLoader'

describe('test SpinnerLoader component', () => {
  afterEach(cleanup)
  it('should render with text, and default classes but no others', () => {
    render(<SpinnerLoader size={20} />)
    const slElm = screen.getByTestId('spinnerLoader')
    expect(slElm).toBeTruthy()
  })
})
