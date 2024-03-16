import CloseIcn from '@icons/CloseIcn'
import { Button, Row, Typography } from 'antd'

interface PopoverHeaderType {
  title: string
  onClose: () => void
  infoLink?: string
}

export default function PopoverHeader({ title, infoLink, onClose }: PopoverHeaderType) {
  return (
    <Row justify="space-between">
      <Typography.Title level={5}>{title}</Typography.Title>
      <div>
        {infoLink && <Button onClick={onClose} size="small" type="link" href={infoLink} icon="?" />}
        <Button onClick={onClose} size="small" type="text" icon={<CloseIcn size={10} />} />
      </div>
    </Row>
  )
}
