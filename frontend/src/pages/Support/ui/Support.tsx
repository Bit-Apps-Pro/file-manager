import { useState } from 'react'

import { __ } from '@common/helpers/i18nwrap'
import config from '@config/config'
import LucideIcn from '@icons/LucideIcn'
import { Avatar, Card, Col, Flex, Row, Skeleton, Space, Typography, theme } from 'antd'

const { Meta } = Card

const supportInfo = {
  supportEmail: 'support@bitapps.pro',
  supportLink: 'https://bitapps.pro/contact',
  bitAppsLogo: 'https://bitapps.pro/wp-content/uploads/2023/03/bit-apps.svg',
  pluginsList: [
    {
      name: 'Bit Flows',
      icon: 'https://ps.w.org/bit-pi/assets/icon-256x256.gif?rev=3325692',
      description: 'Communicate with your customers using different messaging apps.',
      doc: 'https://bit-flows.com/users-guide/',
      url: 'https://wordpress.org/plugin/bit-pi'
    },
    {
      name: 'Bit Form',
      icon: 'https://ps.w.org/bit-form/assets/icon-128x128.gif?rev=2947008',
      description: 'A drag and drop form builder that allows you to create complex form in a minute.',
      doc: 'https://bitapps.pro/docs/bit-form',
      url: 'https://wordpress.org/plugin/bit-form'
    },
    {
      name: 'Bit Integrations',
      icon: 'https://ps.w.org/bit-integrations/assets/icon-128x128.gif?rev=2974059',
      description:
        'Best Automation Plugin for WordPress. Automate 200+ (highest in WordPress) Individual Platforms.',
      doc: 'https://bitapps.pro/docs/bit-integrations',
      url: 'https://wordpress.org/plugin/bit-integrations'
    },
    {
      name: 'Bit Assist',
      icon: 'https://ps.w.org/bit-assist/assets/icon-128x128.gif?rev=3008729',
      description: 'Communicate with your customers using different messaging apps.',
      doc: 'https://bitapps.pro/docs/bit-assist',
      url: 'https://wordpress.org/plugin/bit-assist'
    },
    {
      name: 'Bit Social',
      icon: 'https://ps.w.org/bit-social/assets/icon-128x128.gif?rev=3176768',
      description:
        'The easiest WordPress plugin for automatic social media posting which allows you to automatically share your WordPress posts on social media platforms..',
      doc: 'https://bitapps.pro/docs/bit-social',
      url: 'https://wordpress.org/plugin/bit-social'
    }
  ]
}

const { Title, Paragraph, Link, Text } = Typography

export default function Support() {
  const [loading] = useState(false)

  const { token } = theme.useToken()

  return (
    <div className="p-6">
      <Row>
        <Col md={13} sm={24}>
          <div className="mb-5">
            <Title level={5}>Support</Title>
            <Paragraph style={{ color: token.colorTextSecondary }}>
              {__(
                'In Bit Apps, we provide all kind product support for any types of customer, it dose not matter FREE or PRO user.'
              )}
              {__('We actively provide support through Email and Live Chat.')}
            </Paragraph>

            <Space direction="vertical">
              <Text>
                <Flex gap={10}>
                  <LucideIcn name="Mail" size={18} />
                  <Link
                    href={`mailto:${supportInfo.supportEmail}`}
                    strong
                    underline
                    style={{ color: token.colorText }}
                  >
                    {supportInfo.supportEmail}
                  </Link>
                </Flex>
              </Text>

              <Text>
                <Flex gap={10}>
                  <LucideIcn name="MessageCircle" size={18} />
                  <Link href={supportInfo.supportLink} strong>
                    {__('Chat here')}
                    <LucideIcn name="MoveUpRight" size={12} style={{ transform: 'translateY(-4px)' }} />
                  </Link>
                </Flex>
              </Text>
            </Space>
          </div>
        </Col>

        <Col md={{ span: 9, offset: 2 }} sm={{ span: 24 }}>
          <div className="mb-5">
            <Title level={5}>More Plugins by Bit Apps</Title>

            {supportInfo.pluginsList
              .filter(item => item.name !== config.PRODUCT_NAME)
              .map((plugin, index) => (
                <Card
                  key={`${index * 2}`}
                  style={{ marginTop: 16, borderColor: token.colorBorder }}
                  bodyStyle={{ padding: '16px 20px', color: 'red !important' }}
                >
                  <Skeleton loading={loading} avatar active>
                    <Meta
                      avatar={
                        <Link
                          target="_blank"
                          href={plugin.url}
                          css={{ '&:focus': { boxShadow: 'none' } }}
                        >
                          <Avatar style={{ height: 70, width: 70 }} shape="square" src={plugin.icon} />
                        </Link>
                      }
                      title={
                        <Link
                          target="_blank"
                          href={plugin.url}
                          style={{ color: token.colorTextSecondary, fontSize: '1rem' }}
                          css={{
                            '&:focus': { boxShadow: 'none' },
                            '&:hover': { textDecoration: 'underline !important' }
                          }}
                        >
                          {plugin.name}{' '}
                          <LucideIcn
                            name="MoveUpRight"
                            size={12}
                            style={{ transform: 'translateY(-4px)' }}
                          />
                        </Link>
                      }
                      description={
                        <Text style={{ color: token.colorTextSecondary }}>{plugin.description}</Text>
                      }
                    />
                  </Skeleton>
                </Card>
              ))}
          </div>
        </Col>
      </Row>
    </div>
  )
}
