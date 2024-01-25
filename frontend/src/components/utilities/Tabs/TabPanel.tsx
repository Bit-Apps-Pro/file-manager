import { type ComponentPropsWithoutRef, type ReactElement, type ReactNode } from 'react'

interface TabPanelType extends ComponentPropsWithoutRef<'div'> {
  children: ReactNode | ReactElement
  value?: string
  panelRef?: undefined
}

// eslint-disable-next-line @typescript-eslint/no-unused-vars
function TabPanel({ children, panelRef = undefined, ...restProps }: TabPanelType) {
  return (
    // eslint-disable-next-line react/jsx-props-no-spreading
    <div ref={panelRef} {...restProps}>
      {children}
    </div>
  )
}
export default TabPanel
