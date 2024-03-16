import { Button } from 'antd'

import ConfirmationModal, { type ConfirmationModalTypes } from './ConfirmationModal'

export default function ConfirmationModalStory({
  open,
  layoutId,
  closeModal,
  confirmDelete
}: ConfirmationModalTypes) {
  return (
    <>
      <Button onClick={() => closeModal()}>Confirm Modal</Button>
      <ConfirmationModal
        layoutId={layoutId}
        open={open}
        closeModal={() => closeModal()}
        confirmDelete={() => confirmDelete()}
      />
    </>
  )
}
