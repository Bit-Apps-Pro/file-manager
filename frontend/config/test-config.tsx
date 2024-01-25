/* eslint-disable import/no-extraneous-dependencies */
import type React from 'react'

import { QueryClient, QueryClientProvider } from '@tanstack/react-query'
import { render } from '@testing-library/react'

const queryClient = new QueryClient()

// eslint-disable-next-line @typescript-eslint/no-explicit-any
const customRender = (ui: React.ReactNode, options: any) =>
  render(<QueryClientProvider client={queryClient}>{ui}</QueryClientProvider>, { ...options })

export * from '@testing-library/react'

export { customRender as render }
