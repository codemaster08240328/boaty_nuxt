/** 
 * The Meta Service is used to create meta used on (most) pages e.g. "Rich data"
*/
const meta = {
  /**
   * Creates an array of basic meta details (description, twitter, og, fb etc)
   * 
   * @param {any} metaDetails 
   * @returns 
   */
  base (metaDetails) {
    let meta = [
      { hid: 'description', name: 'description', content: metaDetails.description },
      { hid: 'twittertitle', name: 'twitter:title', content: metaDetails.title },
      { hid: 'twitterdescription', name: 'twitter:description', content: metaDetails.description },
      { hid: 'twittercreator', name: 'twitter:creator', content: metaDetails.creator },
      { hid: 'twitterimg', name: 'twitter:image:src', content: metaDetails.image },
      { hid: 'ogtitle', property: 'og:title', content: metaDetails.title },
      { hid: 'ogtype', property: 'og:type', content: metaDetails.type },
      { hid: 'ogurl', property: 'og:url', content: metaDetails.url },
      { hid: 'ogimage', property: 'og:image', content: metaDetails.image },
      { hid: 'ogdescription', property: 'og:description', content: metaDetails.description }
    ]
    return meta
  },
  /**
   * Takes an array of breadcrumbs and creates ldjson
   * 
   * @param {array} breadcrumbs 
   * @returns 
   */
  breadcrumbsJSON (breadcrumbs) {
    let list = ''
    let pos = 0
    for (let breadcrumb of breadcrumbs) {
      pos++
      let last = (pos < breadcrumbs.length) ? ',' : ''

      list += `{
        "@type": "ListItem",
        "position": ${pos},
        "item": {
          "@id": "${breadcrumb['@id']}",
          "name": "${breadcrumb.name}"
        }
      }${last}`
    }

    return `{
      "@context": "http://schema.org",
      "@type": "BreadcrumbList",
      "itemListElement": [
        ${list}
      ]
    }`
  },
  searchResultsJSON (results, title, url) {
    let list = ''
    let pos = 0
    for (const result of results) {
      let image = (result.boatModelImagePrimary) ? `"image": "${result.boatModelImagePrimary}",` : ''
      pos++
      let last = (pos < results.length) ? ',' : ''
      list += `{
        "@type": "ListItem",
        "position": ${pos},
        "item": {
          "@type": "Product",
          "url": "${url}#${result.Name}",
          "brand": {
            "@type": "Thing",
            "name": "${result.BrandName}"
          },${image}
          "offers": {
            "@type": "Offer",
            "priceCurrency": "EUR",
            "price": "${result.boatPriceWithDiscount}",
            "availability": "http://schema.org/InStock",
            "seller": {
              "@type": "Organization",
              "name": "SailChecker"
            }
          }
        }
      }${last}`
    }

    const searchResultsJSON = `{
      "@context": "http://schema.org",
      "@type": "SearchResultsPage",
      "mainEntity": [{
        "@type": "ItemList",
        "name": "${title}",
        "itemListOrder": "http://schema.org/ItemListOrderAscending",
        "itemListElement":[
          ${list}
        ]
      }]
    }`

    return searchResultsJSON
  },
  productJSON (name, image, description, brandName, price) {
    const productJSON = `{
      "@context": "http://schema.org",
      "@type": "Product",
      "name": "${name}",
      "image": [${image}],
      "description": "${description}",
      "brand": "${brandName}",
      "offers": {
        "@type": "Offer",
        "priceCurrency": "EUR",
        "price": "${price}"
      }
    }`
    return productJSON
  }
}

export default meta
