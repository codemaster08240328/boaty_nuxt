'use strict'

class Url {
  /**
   * Turns an array of strings into a qualified uri
   *
   * @param { urlParts = [], prefix = '' }
   * @returns string
   * @memberof Url
   */
  stringToUrl ({ urlParts = [], prefix = '' }) {
    let url = ''
    if (prefix) {
      prefix = '/' + prefix + '/'
    } else {
      prefix = '/'
    }

    for (let part of urlParts) {
      part = part.replace(/-|\//g, '_')
      url += part + '/'
    }
    url = prefix.concat(url).toLowerCase().replace(/\s/g, '-')

    return url
  }

  /**
   *  Changes a url section like rogač-(split)
   *  into rogač (split)
   * @param {string} { section }
   * @returns
   * @memberof Url
   */
  urlSectionToString ({ section }) {
    const hyphenSpace = new RegExp('-', 'g')
    const underHyphen = new RegExp('_', 'g')

    return decodeURI(section.replace(hyphenSpace, ' ').replace(underHyphen, '-'))
  }
}

module.exports = new Url()
