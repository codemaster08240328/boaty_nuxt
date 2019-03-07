'use strict'

class AdminAuth {
  async handle ({ request, auth, response }, next) {
    const user = await auth.getUser()
    if (user.is_admin === 1) {
      await next()
    } else {
      response.status(401).send('Not Authorized')
    }
  }
}

module.exports = AdminAuth
