import { type Interpolation, type Theme } from '@emotion/react'

const globalCssInJs = ({ token }: Theme) =>
  ({
    '#bit-apps-root *': {
      scrollbarWidth: 'thin',
      scrollbarColor: 'red'
    },
    '.ant-popover-inner-content': {
      maxHeight: 'calc(100vh - 80px)',
      overflowY: 'auto'
    },
    ':is(.ant-popover, .ant-popover, .ant-modal, #bit-apps-root) *::-webkit-scrollbar': {
      width: '7px',
      margin: '10px',
      transition: 'width .2s ease !important'
    },
    ':is(.ant-popover, .ant-modal, #bit-apps-root) *:hover::-webkit-scrollbar': {
      width: '10px'
    },
    ':is(.ant-popover, .ant-modal, #bit-apps-root) *::-webkit-scrollbar-thumb': {
      width: '15px',
      borderRadius: '10px',
      backgroundColor: `${token.colorBgTextHover} !important`
    },
    ':is(.ant-popover, .ant-modal, #bit-apps-root) *::-webkit-scrollbar-track': {
      borderRadius: '10px',
      backgroundColor: `${token.colorBgContainer} !important`
    },
    ':is(.ant-popover, .ant-modal, #bit-apps-root) *:hover::-webkit-scrollbar-track': {
      // backgroundColor: `${token.c} !important`
      // backgroundColor: `red !important`
    },

    '.ant-select-dropdown, .ant-tooltip': {
      zIndex: '999999 !important'
    },
    '.ant-input-affix-wrapper-focused:has(.ant-input), .ant-select-focused .ant-select-selector': {
      boxShadow: `none !important`,
      transition: 'box-shadow 0s, outline .2s cubic-bezier(0.18, 0.89, 0.32, 1.28) !important',
      outline: `2px solid ${token.colorPrimaryTextActive} !important`,
      borderColor: `${token.colorPrimaryTextActive}!important`
    },
    '.ant-input-borderless': {
      border: `1px solid transparent !important`
    },
    '.ant-input:not(:has(~ .ant-input-suffix))': {
      transition: 'box-shadow 0s, outline .2s cubic-bezier(0.18, 0.89, 0.32, 1.28) !important',
      '&:hover': {
        borderColor: `${token.colorPrimary}!important`
      },
      '&:focus': {
        boxShadow: `none !important`,
        outline: `2px solid ${token.colorPrimaryTextActive} !important`,
        borderColor: `${token.colorPrimaryTextActive}!important`
      }
    },
    '.flow-draggable-item': {
      borderRadius: token.borderRadius,
      backgroundColor: token.colorBgContainer,
      border: `1px solid ${token.colorBorder}`
    },
    '.flow-draggable-item-dragging': {
      cursor: 'grabbing !important',
      zIndex: '99999 !important',
      boxShadow: token.boxShadowSecondary
    }
  }) as Interpolation<Theme>
export default globalCssInJs
