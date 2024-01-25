import config from '@config/config'


export default function Root() {
  return (
    <div className="p-6">
     Hi From, {config.PRODUCT_NAME}
    </div>
  )
}
