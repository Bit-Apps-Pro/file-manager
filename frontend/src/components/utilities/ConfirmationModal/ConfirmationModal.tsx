import FocusBounder from 'react-focus-bounder'

import CloseIcn from '@icons/CloseIcn'
import { Button, Space } from 'antd'
import { AnimatePresence, motion } from 'framer-motion'

import cls from './ConfirmationModal.module.css'

export interface ConfirmationModalTypes {
  layoutId: string
  open: boolean
  closeModal: () => void
  confirmDelete: () => void
}

export default function ConfirmationModal({
  open,
  layoutId,
  closeModal,
  confirmDelete
}: ConfirmationModalTypes) {
  return (
    <AnimatePresence>
      {open && (
        <div className={cls.modalWrapper}>
          <motion.div className={cls.modalDialog} layoutId={layoutId}>
            <FocusBounder firstElementIndex={1}>
              <button
                type="button"
                className={cls.clossBtn}
                onClick={closeModal}
                aria-label="close button"
              >
                <CloseIcn size={15} stroke={5} />
              </button>
              <div>
                <h2 className={cls.modalTitle}>Are you Confirm to Delete?</h2>
                <p className={cls.modalSubtitle}>If you delete you can&apos;t recover it</p>
              </div>
              <Space>
                <Button onClick={() => closeModal()}>Cancel</Button>
                <Button type="primary" onClick={() => confirmDelete()}>
                  Confirm
                </Button>
              </Space>
            </FocusBounder>
          </motion.div>
        </div>
      )}
    </AnimatePresence>
  )
}
