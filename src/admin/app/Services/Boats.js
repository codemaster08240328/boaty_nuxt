'use strict'
const _ = use('lodash')
const Moment = use('moment')
const Database = use('Database')
const Helpers = use('App/Services/Helpers')
const Url = use('App/Services/Url')

class Boats {
  async searchBase ({
    countryId = false,
    areaId = false,
    baseId = false,
    boatBrand = [],
    boatType = [],
    prices = [],
    lengths = [],
    cabins = [],
    years = [],
    toilets = [],
    date = '',
    urlPrefix = 'search',
    orderBy = false,
    page = 1,
    limit = 4,
    paginate = false
  }) {
    const boats = Database.from('v1_search_base')
    const [ thisWeek, nextWeek ] = Helpers.DatesFromNow(date)

    boats.where('v1_search_base.boatPeriodFrom', '>=', thisWeek)
    boats.where('v1_search_base.boatPeriodTo', '<=', nextWeek)

    if (boatBrand.length > 0) {
      boats.whereIn('v1_search_base.BrandID', boatBrand)
    }

    if (boatType.length > 0) {
      boats.whereIn('v1_search_base.TypeID', boatType)
    }

    if (prices.length > 0) {
      boats.where(function () {
        for (let group of prices) {
          this.orWhereBetween('v1_search_base.boatPriceWithDiscount', _.flattenDeep(group))
        }
      })
    }

    if (lengths.length > 0) {
      boats.where(function () {
        for (let group of lengths) {
          this.orWhereBetween('v1_search_base.OverallLength', _.flattenDeep(group))
        }
      })
    }

    if (years.length > 0) {
      boats.where(function () {
        for (let group of years) {
          this.orWhereBetween('v1_search_base.BuildYear', _.flattenDeep(group))
        }
      })
    }

    if (cabins.length > 0) {
      boats.whereIn('v1_search_base.Cabins', cabins)
    }

    if (toilets.length > 0) {
      boats.whereIn('v1_search_base.Toilets', toilets)
    }

    if (countryId) {
      boats.where('v1_search_base.country_id', countryId)
    }

    if (areaId) {
      boats.where('v1_search_base.area_id', areaId)
    }

    if (baseId) {
      boats.where('v1_search_base.base_id', baseId)
    }

    let result = {}

    if (paginate) {
      const paginateBase = _.merge({}, boats)
      let paginate = await paginateBase.countDistinct('ID AS total').first()
      result.paginate = {
        total: paginate.total,
        pages: Math.ceil(paginate.total / limit),
        currentPage: page
      }
    }

    switch (orderBy) {
      case 1:
        boats.orderBy('v1_search_base.boatPriceWithDiscount', 'asc')
        break
      case 2:
        boats.orderBy('v1_search_base.boatPriceWithDiscount', 'desc')
        break
      case 3:
        boats.orderBy('v1_search_base.BuildYear', 'desc')
        break
      case 4:
        boats.orderBy('v1_search_base.Cabins', 'asc')
        break
      case 5:
        boats.orderByRaw('v1_search_base.rating desc')
        break
      default:
        boats.orderByRaw('v1_search_base.rating desc')
        break
    }

    result.boats = await boats
      .select('v1_search_base.*')
      .groupBy('v1_search_base.ID')
      .forPage(page, limit)

    _.each(result.boats, (value, index) => {
      result.boats[index].boatUrl = '/boat' + Url.stringToUrl({ urlParts: [ value.BrandName, value.ModelName + '-' + value.ID ] })
      result.boats[index].countryUrl = urlPrefix + Url.stringToUrl({ urlParts: [ value.country_name ] })
      result.boats[index].areaUrl = urlPrefix + Url.stringToUrl({ urlParts: [ value.country_name, value.area_name ] })
      result.boats[index].baseUrl = urlPrefix + Url.stringToUrl({ urlParts: [ value.country_name, value.area_name, value.base_name ] })
    })

    return result
  }

  /*
    Sorts equipment into groups by their CategoryID e.g. navigation equipment, under deck etc
  */
  sortEquipment (equipmentObject) {
    // empty object to hold headers
    let equipment = {
      headers: { // things like under deck, navigation etc
      }
    }

    // get a unique list of the equipments by their categoryname
    let equipmentHeaders = _.uniqBy(equipmentObject, 'CategoryName')

    // add each unique header to equipment
    _.each(equipmentHeaders, (value) => {
      equipment.headers[value.CategoryName] = []
    })

    // add each equipment into their respective header
    _.each(equipmentObject, (value) => {
      equipment.headers[value.CategoryName].push(value)
    })

    return equipment
  }

  sortPeriods (periodsObject) {
    let periods = {
      months: {

      }
    }

    // add the month name to each period
    _.each(periodsObject, (value, index) => {
      periodsObject[index].month = Moment(value.From).format('MMMM YYYY')
      periodsObject[index].nice_date = Moment(value.From).format('YYYY-MM-DD')
    })

    // get the unique month names
    let periodMonths = _.uniqBy(periodsObject, 'month')

    // add each unique month to period header
    _.each(periodMonths, (value) => {
      periods.months[value.month] = []
    })

    // add each period into their respective month
    _.each(periodsObject, (value) => {
      periods.months[value.month].push(value)
    })

    return periods
  }
}

module.exports = new Boats()
