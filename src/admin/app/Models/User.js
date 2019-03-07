'use strict'

const Model = use('Model')

class User extends Model {
  static boot () {
    super.boot()
    this.addHook('beforeCreate', 'User.hashPassword')
  }

  static get hidden () {
    return ['password', 'is_admin']
  }

  static get computed () {
    return ['fullName']
  }

  getFullName () {
    let fullName = this.first_name
    if (this.last_name !== null) fullName += ` ${this.last_name}`
    return fullName
  }

  tokens () {
    return this.hasMany('App/Models/Token')
  }

  pages () {
    return this.hasMany('App/Models/Page')
  }
}

module.exports = User
