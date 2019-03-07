'use strict'
const Url = use('App/Services/Url')
const _ = use('lodash')
const Config = use('Config')
const Database = use('Database')

class Destinations {
  constructor () {
    this.config = Config.get('sailchecker')
  }

  /**
   * 
   * 
   * @param {any} [obj={}] 
   * @memberof Destinations
   */
  locationToLowestType (obj = {}) {
    const locationTypes = [
      'country_id',
      'area_id',
      'base_id'
    ]
    // TODO: i18n
    const tableTypes = {
      country_id: 'country_name',
      area_id: 'area_name',
      base_id: 'base_name'
    }

    let locationType = false
    let tableType = false
    // find the lowest category of location country > area > base break when there's no further match
    for (let type of locationTypes) {
      if (obj[type]) {
        locationType = type
      } else {
        break
      }
    }

    if (locationType) {
      tableType = tableTypes[locationType]
    }

    return {
      locationType: locationType,
      tableType: tableType
    }
  }
  /**
   * Takes a list of locations (take a look at regions table) 
   * and turns it into an associate array with props of countries, areas and bases
   * 
   * @param {array} [destinations=[]] 
   * @param {string} [prefix='yacht-charter'] 
   * @memberof Destinations
   */
  sortDestinationsToArray (destinations = [], prefix = 'yacht-charter') {
    let result = []

    _.each(destinations, (value) => {
      // create the url for each of the locations, typically prefix will be yacht-charter or search
      const countryUrl = Url.stringToUrl({
        urlParts: [value.country_name_en],
        prefix: prefix
      })
      const areaUrl = Url.stringToUrl({
        urlParts: [value.country_name_en, value.area_name_en],
        prefix: prefix
      })
      const baseUrl = Url.stringToUrl({
        urlParts: [value.country_name_en, value.area_name_en, value.base_name],
        prefix: prefix
      })

      // info is what will be returned to client
      const cInfo = {
        url: countryUrl,
        popular: 0, // 0 for now, as of 2018 april 2nd, chris wants only greece/bvi/croatia as popular
        name: value.country_name_en,
        areas: []
      }
      const aInfo = {
        url: areaUrl,
        name: value.area_name_en,
        bases: []
      }
      const bInfo = {
        url: baseUrl,
        name: value.base_name
      }

      let countryIndex = _.findIndex(result, (o) => {
        return o.name === value.country_name_en
      })

      let areaIndex = -1

      if (countryIndex !== -1) {
        areaIndex = _.findIndex(result[countryIndex].areas, (o) => {
          return o.name === value.area_name_en
        })
      }

      // add country
      if (countryIndex === -1) {
        result.push(cInfo)

        countryIndex = _.findIndex(result, (o) => { return o.name === value.country_name_en })
        result[countryIndex].areas.push(aInfo)

        areaIndex = _.findIndex(result[countryIndex].areas, (o) => { return o.name === value.area_name_en })
        result[countryIndex].areas[areaIndex].bases.push(bInfo)
      } else if (areaIndex === -1) {
        result[countryIndex].areas.push(aInfo)

        areaIndex = _.findIndex(result[countryIndex].areas, (o) => { return o.name === value.area_name_en })
        result[countryIndex].areas[areaIndex].bases.push(bInfo)
      } else {
        result[countryIndex].areas[areaIndex].bases.push(bInfo)
      }
    })

    return result
  }

  /*
    creates a list of locations with boat counts based on "Popular" column in countries table
    TODO clean this query up, make it into a view (it looks pretty dodgy, does it work properly?)
  */
  async popularLocations () {
    let result = await Database.raw(`
      SELECT 
        IF(p.title, p.title, c.Name) as 'title',
          c.Name as 'country_name',
          pi.fileName as 'fileName',
          pi.altText as 'altText',
          pi.titleText as 'titleText',
          COUNT(DISTINCT b.ID) as 'boat_count'
      FROM
          countries c
              LEFT JOIN
          pages p ON c.ID = p.country_id and p.type = 'yacht-charter'
              LEFT JOIN
          page_images pi ON p.id = pi.page_id
              LEFT JOIN
          regions r ON c.ID = r.country_id
              JOIN
          boat_bases bb ON r.base_id = bb.ID
              JOIN
          boats b ON bb.BoatID = b.ID
      WHERE
          c.Popular = 1
      GROUP BY c.ID
    `)

    let popularLocations = result[0]

    // create a path to yacht charter
    _.each(popularLocations, (value, index) => {
      popularLocations[index].url = this.config.yachtCharterPrefix + Url.stringToUrl({ urlParts: [value.country_name] })
    })

    return popularLocations
  }
}

module.exports = new Destinations()
