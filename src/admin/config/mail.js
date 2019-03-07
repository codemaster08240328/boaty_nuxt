'use strict'

const Env = use('Env')

module.exports = {
  /*
  |--------------------------------------------------------------------------
  | Connection
  |--------------------------------------------------------------------------
  |
  | Connection to be used for sending emails. Each connection needs to
  | define a driver too.
  |
  */
  connection: Env.get('MAIL_CONNECTION', 'smtp'),

  /*
  |--------------------------------------------------------------------------
  | SMTP
  |--------------------------------------------------------------------------
  |
  | Here we define configuration for sending emails via SMTP.
  |
  */
  ses: {
    driver: 'smtp',
    apiVersion: '2010-12-01',
    accessKeyId: Env.get('S3_KEY'),
    secretAccessKey: Env.get('S3_SECRET'),
    region: 'eu-west-2',
    host: 'email-smtp.us-west-2.amazonaws.com',
    rateLimit: 10,
    auth: {
      user: Env.get('SES_USERNAME'),
      pass: Env.get('SES_PASSWORD')
    }
  },
  smtp: {
    driver: 'smtp',
    pool: true,
    port: 587,
    host: 'email-smtp.us-west-2.amazonaws.com',
    secure: false,
    auth: {
      user: Env.get('S3_KEY'),
      pass: Env.get('S3_SECRET')
    },
    maxConnections: 5,
    maxMessages: 100,
    rateLimit: 10
  },

  /*
  |--------------------------------------------------------------------------
  | SparkPost
  |--------------------------------------------------------------------------
  |
  | Here we define configuration for spark post. Extra options can be defined
  | inside the `extra` object.
  |
  | https://developer.sparkpost.com/api/transmissions.html#header-options-attributes
  |
  | extras: {
  |   campaign_id: 'sparkpost campaign id',
  |   options: { // sparkpost options }
  | }
  |
  */
  sparkpost: {
    driver: 'sparkpost',
    apiKey: Env.get('SPARKPOST_API_KEY'),
    extras: {}
  }
}
