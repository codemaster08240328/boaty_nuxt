'use strict'

const { Command } = require('@adonisjs/ace')
const Database = use('Database')
const Moment = use('moment')
const Mail = use('Mail')

class updateHA extends Command {
  static get signature () {
    return `
      bbapi:updateHA
    `
  }

  static get description () {
    return 'Update historic availabilities, removing expired availabilities'
  }

  async handle (args, options) {
    try {
      const sixMonths = Moment().add(6, 'M').format('YYYY-MM-DD')
      const today = Moment().format('YYYY-MM-DD')
      const sql = `
      INSERT INTO historic_periods(\`BoatID\`, \`From\`, \`To\`, \`BasePrice\`, \`AvailabilityStatus\`, \`PriceWithDiscount\`, \`FirstTermPriceWithDiscount\`, \`FirstTermPriceWithoutDiscount\`,\`CurrencyCode\`, \`CurrencySymbol\`, \`BookedOrOptionBaseFrom\`, \`BookedOrOptionBaseTo\`, \`OptionExpiryDate\`) 
      (
        SELECT 
          bp.BoatID,
          bp.\`From\`,
          bp.\`To\`,
          bp.BasePrice,
          bp.AvailabilityStatus,
          bp.PriceWithDiscount,
          bp.FirstTermPricewithDiscount,
          bp.FirstTermPriceWithoutDiscount,
          bp.CurrencyCode,
          bp.CurrencySymbol,
          bp.BookedOrOptionBaseFrom,
          bp.BookedOrOptionBaseTo,
          bp.OptionExpiryDate
        FROM
            \`boat_periods\` \`bp\`
        WHERE
            \`From\` < '${sixMonths}'
        )
        ON DUPLICATE KEY UPDATE
        \`BoatID\`=\`bp\`.\`BoatID\`, 
        \`From\`=\`bp\`.\`From\`,
        \`To\`=\`bp\`.\`To\`,
        \`BasePrice\`=\`bp\`.\`BasePrice\`,
        \`AvailabilityStatus\`=\`bp\`.\`AvailabilityStatus\`,
        \`PriceWithDiscount\`=\`bp\`.\`PriceWithDiscount\`,
        \`FirstTermPriceWithDiscount\`=\`bp\`.\`FirstTermPriceWithDiscount\`,
        \`FirstTermPriceWithoutDiscount\`=\`bp\`.\`FirstTermPriceWithoutDiscount\`,
        \`CurrencyCode\`=\`bp\`.\`CurrencyCode\`,
        \`CurrencySymbol\`=\`bp\`.\`CurrencySymbol\`,
        \`BookedOrOptionBaseFrom\`=\`bp\`.\`BookedOrOptionBaseFrom\`,
        \`BookedOrOptionBaseTo\`=\`bp\`.\`BookedOrOptionBaseTo\`,
        \`OptionExpiryDate\`=\`bp\`.\`OptionExpiryDate\`
      ;`

      const deleteSql = `
      DELETE FROM \`boat_periods\`
      WHERE
          \`From\` < '${today}';
      `

      await Database.raw(sql)
      await Database.raw(deleteSql)
      console.log(today)
    } catch (e) {
      console.log(e)
    }

    console.log('completed')
    const data = {
      message: 'updated historic availabilities'
    }

    await Mail.send('emails.cron', data, (message) => {
      message
        .to('matt@saowapan.com')
        .from('info@sailchecker.com')
        .subject('Updated Historic Availabilities')
    })

    Database.close()
  }
}

module.exports = updateHA
