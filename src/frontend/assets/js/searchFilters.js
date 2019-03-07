'use strict'
import { each } from 'lodash'

const filters = {
  /**
   * Takes an array of filters and searches for the ID of each
   */
  valueToId: function (currentArray, currentValues) {
    let ids = []
    each(currentArray, (value, index) => {
      each(currentValues, (value1, index1) => {
        if (value1.toString() === value.base.toString()) {
          ids.push(value.value) // push the value (ID)
        }
      })
    })

    return ids
  },
  processed: { // vuejs wants you to use primitive values as keys, for that reason we process them to find the base values
    prices: [],
    lengths: []
  },
  boatType: [{
    ID: 3,
    Name: 'Gulet'
  },
  {
    ID: 4,
    Name: 'Sailing Yacht'
  },
  {
    ID: 5,
    Name: 'Catamaran'
  },
  {
    ID: 6,
    Name: 'Motor Yacht'
  }],
  years: [
    {
      value: 1,
      base: [2017, 2100],
      text: '2017 +'
    },
    {
      value: 2,
      base: [2016, 2017],
      text: '2016 - 2017'
    },
    {
      value: 3,
      base: [2012, 2015],
      text: '2012 - 2015'
    },
    {
      value: 4,
      base: [2008, 2011],
      text: '2008 - 2011'
    },
    {
      value: 5,
      base: [2004, 2007],
      text: '2004 - 2007'
    },
    {
      value: 6,
      base: [1980, 2000],
      text: '< 2000'
    }
  ],
  toilets: [1, 2, 3, 4, 5, { value: 100, text: '6+' }],
  cabins: [1, 2, 3, 4, 5, { value: 100, text: '6+' }],
  lengths: [
    {
      value: 1,
      base: [0, 8],
      text: '< 8m'
    },
    {
      value: 2,
      base: [8, 10],
      text: '8m - 10m'
    },
    {
      value: 3,
      base: [10, 12],
      text: '10m - 12m'
    },
    {
      value: 4,
      base: [12, 14],
      text: '12m - 14m'
    },
    {
      value: 5,
      base: [14, 16],
      text: '14m - 16m'
    },
    {
      value: 6,
      base: [16, 100],
      text: '16m +'
    }
  ],
  prices: {
    symbol: '',
    currency: 'EUR',
    originalCurrency: 'EUR',
    options: [
      {
        value: 1,
        base: [
          500, 1000
        ],
        current: [
          500, 1000
        ]
      },
      {
        value: 2,
        base: [
          1000, 2000
        ],
        current: [
          1000, 2000
        ]
      },
      {
        value: 3,
        base: [
          2000, 4000
        ],
        current: [
          2000, 4000
        ]
      },
      {
        value: 4,
        base: [
          4000, 6000
        ],
        current: [
          4000, 6000
        ]
      },
      {
        value: 5,
        base: [
          6000, 10000
        ],
        current: [
          6000, 10000
        ]
      },
      {
        value: 6,
        base: [
          10000, 15000
        ],
        current: [
          10000, 15000
        ]
      },
      {
        value: 7,
        base: [
          15000, 1000000
        ],
        current: [
          15000, 1000000
        ]
      }
    ]
  }
}

export default filters
