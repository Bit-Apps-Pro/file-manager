// eslint-disable-next-line import/no-extraneous-dependencies
import { Link } from 'react-router-dom'

import { __ } from '@common/helpers/i18nwrap'
import greeting from '@resource/img/home.svg'

export default function Welcome() {
  return (
    <div className="btcd-greeting">
      <img src={greeting} alt="" />
      <h2>{__('Welcome to Bit Integrations', 'bit-integrations')}</h2>
      <div className="sub">{__('Thank you for installing Bit Integrations.', 'bit-integrations')}</div>
      <Link to="/flow/new" className="btn round btcd-btn-lg dp-blue">
        {__('Create Integration', 'bit-integrations')}
      </Link>
    </div>
  )
}
