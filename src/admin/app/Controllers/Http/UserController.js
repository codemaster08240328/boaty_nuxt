'use strict'

const User = use('App/Models/User')

class UserController {
  async index () {
    return User.all()
  }

  async show ({ params }) {
    return User.find(params.id)
  }

  async storeSocial ({ auth, params, request }) {
    const userDetails = request.only([
      'first_name',
      'last_name',
      'email',
      'picture',
      'login_source',
      'token'
    ])

    const whereClause = {
      email: userDetails.email
    }

    const user = await User.findOrCreate(whereClause, userDetails)
    return auth.generate(user)
  }

  async update ({ params, request }) {
    return User
      .query()
      .where({ id: params.id })
      .update(request.post())
  }

  async login ({ request, auth }) {
    const { email, password } = request.all()
    const token = await auth.attempt(email, password)
    return token
  }

  async authTest ({ request, auth }) {
    await auth.check()
    return auth.getUser()
  }

  async destroy ({ params }) {
    return User
      .query()
      .where({ id: params.id })
      .delete()
  }

  async facebook ({ ally }) {
    await ally.driver('facebook').redirect()
  }

  async facebookCallback ({ ally, auth, response }) {
    try {
      const fbUser = await ally.driver('facebook').getUser()

      // user details to be saved
      const userDetails = {
        email: fbUser.getEmail(),
        token: fbUser.getAccessToken(),
        login_source: 'facebook'
      }

      // search for existing user
      const whereClause = {
        email: fbUser.getEmail()
      }

      const user = await User.findOrCreate(whereClause, userDetails)

      response.auth = await auth.generate(user)
      response.redirect('/boat/test')
    } catch (error) {
      return 'Unable to authenticate. Try again later'
    }
  }
}

module.exports = UserController
