'use strict'
const Database = use('Database')
const Contact = use('App/Models/Contact')
const _ = use('lodash')
const Url = use('App/Services/Url')

class ContactController {
  async index () {
    let result = await Database.raw(`
    SELECT 
      c.*,
      b.BrandName,
      b.ModelName,
      bb.Name as 'base_name'
    FROM
      contacts c
          LEFT JOIN
      boats b ON c.boat_id = b.ID
          LEFT JOIN
      boat_bases bb ON c.boat_id = bb.BoatID 
    WHERE
      c.status != 0
    `)

    let contacts = result[0]

    _.each(contacts, (value, index) => {
      let boatPrefix = '/boat' // make dynamic
      if (value.boat_id) {
        contacts[index].url = boatPrefix + Url.stringToUrl({ urlParts: [ value.BrandName, value.ModelName + '-' + value.boat_id ] })
      }
    })

    return contacts
  }

  async delete ({ params }) {
    const { id } = params
    const contact = await Contact.find(id)
    contact.status = 0
    return contact.save()
  }
}

module.exports = ContactController
