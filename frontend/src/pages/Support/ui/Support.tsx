import { useState } from 'react'

import config from '@config/config'
import LogoIcn from '@icons/LogoIcn'
import LogoText from '@icons/LogoText'
import LucideIcn from '@icons/LucideIcn'
import { Avatar, Card, Checkbox, Col, Flex, Row, Skeleton, Space, Typography, theme } from 'antd'

const { Meta } = Card

const supportInfo = {
  supportEmail: 'support@bitapps.pro',
  supportLink: 'https://bitapps.pro/contact',
  bitAppsLogo: 'https://bitapps.pro/wp-content/uploads/2023/03/bit-apps.svg',
  pluginsList: [
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
      icon: 'https://s.w.org/plugins/geopattern-icon/bit-social.svg',
      description:
        'The easiest WordPress plugin for automatic social media posting which allows you to automatically share your WordPress posts on social media platforms..',
      doc: 'https://bitapps.pro/docs/bit-social',
      url: 'https://wordpress.org/plugin/bit-social'
    },
    {
      name: 'Bit Flow',
      icon: '',
      description: 'Communicate with your customers using different messaging apps.',
      doc: 'https://bitapps.pro/docs/bit-flow',
      url: 'https://wordpress.org/plugin/bit-flow'
    }
  ]
}

const { Title, Paragraph, Link, Text } = Typography

export default function Support() {
  const [loading] = useState(false)

  const { token } = theme.useToken()

  return (
    <div className="p-6">
      <div className="mb-5">
        <Space size="middle">
          <LogoIcn size={56} />
          <LogoText h={50} />
        </Space>
      </div>

      <Row>
        <Col md={13} sm={24}>
          <div className="mb-5">
            <Paragraph style={{ color: token.colorTextSecondary }}>
              The first web browser with a graphical user interface, Mosaic, was released in 1993.
              Accessible to non-technical people, it played a prominent role in the rapid growth of the
              nascent World Wide Web.[11] The lead developers of Mosaic then founded the Netscape
              corporation, which released a more polished browser, Netscape Navigator, in 1994. This
              quickly became the most-used.
            </Paragraph>
          </div>

          <div className="mb-5">
            <Title level={5}>Docs</Title>
            <Paragraph style={{ color: token.colorTextSecondary }}>
              Explore our extensive documentation. From beginners to developers - everyone will get an
              answer{' '}
              <Link
                href={supportInfo.pluginsList.find(item => item.name === config.PRODUCT_NAME)?.doc}
                strong
                underline
              >
                here <LucideIcn name="MoveUpRight" size={12} style={{ transform: 'translateY(-4px)' }} />
              </Link>
            </Paragraph>
          </div>

          <div className="mb-5">
            <Title level={5}>Support</Title>
            <Paragraph style={{ color: token.colorTextSecondary }}>
              In Bit Apps, we provide all kind product support for any types of customer, it dose not
              matter FREE or PRO user. We actively provide support through Email and Live Chat.
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
                    Chat here{' '}
                    <LucideIcn name="MoveUpRight" size={12} style={{ transform: 'translateY(-4px)' }} />
                  </Link>
                </Flex>
              </Text>
            </Space>
          </div>

          <div className="mb-5">
            <Title level={5}>Improvement</Title>
            <Checkbox style={{ color: token.colorTextSecondary }}>
              Allow to collect javascript errors to improve application.
            </Checkbox>
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
