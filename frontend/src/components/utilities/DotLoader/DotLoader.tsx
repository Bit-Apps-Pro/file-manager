import ut from '@resource/utilsCssInJs'

import css from './DotLoader.module.scss'

export default function DotLoader() {
  return (
    <div className={css.loadingDots}>
      <span className={`${css.dot}`} css={ut({ bg: 'colorText' })} />
      <span className={`${css.dot}`} css={ut({ bg: 'colorText' })} />
      <span className={`${css.dot}`} css={ut({ bg: 'colorText' })} />
    </div>
  )
}
