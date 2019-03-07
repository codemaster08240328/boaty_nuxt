'use strict'

class UserLogin {
  get rules () {
    return {
      first_name: 'string|required',
      last_name: 'string|required',
      email: 'email|required',
      picture: 'string',
      login_source: 'string|required',
      token: 'string|required'
    }
  }
}

module.exports = UserLogin
