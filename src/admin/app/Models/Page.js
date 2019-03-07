'use strict'

const Model = use('Model')

class Page extends Model {
  static boot () {
    super.boot()

    this.addTrait('@provider:Lucid/Slugify', {
      fields: {
        slug: 'title'
      },
      // strategy: 'dbIncrement',
      strategy: async (field, value, modelInstance) => {
        if (modelInstance.slug) {
          return `${modelInstance.slug}`
        } else {
          const primaryKey = modelInstance.constructor.primaryKey
          const query = modelInstance.constructor.queryWithOutScopes()
          /**
           * Below are the different queries to be executed based upon the
           * database client in use
           */
          query.whereRaw(`?? REGEXP ?`, [field, `^${value}(-[0-9]*)?$`])
          const [row] = await query.orderBy(primaryKey, 'desc').pluck(field).limit(1)
          if (!row) {
            return value
          }

          const lastNum = Number(row.replace(`${value}-`, ''))
          return !lastNum || isNaN(lastNum) ? `${value}-1` : `${value}-${lastNum + 1}`
        }
      },
      disableUpdates: true
    })
  }

  static get computed () {
    return ['link']
  }

  getLink () {
    let link = `/${this.type}/${this.slug}/`
    return link
  }

  user () {
    return this.belongsTo('App/Models/User')
  }

  country () {
    return this.belongsTo('App/Models/Country')
  }

  pageImage () {
    return this.hasMany('App/Models/PageImage')
  }

  pageChequer () {
    return this.hasMany('App/Models/PageChequer')
  }

  pageCategory () {
    return this.hasMany('App/Models/PageCategory')
  }

  pageTag () {
    return this.hasMany('App/Models/PageTag')
  }

  pageBoatBrand () {
    return this.hasMany('App/Models/PageBoatBrand')
  }

  pageBoatType () {
    return this.hasMany('App/Models/PageBoatType')
  }

  pageLink () {
    return this.hasMany('App/Models/PageLink')
  }
}

module.exports = Page
