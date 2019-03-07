import Axios from 'axios'

export default (context, inject) => {
  let options = {
    headers: {
      Authorization: '',
      'accept-language': 'en-us'
    }
  }

  let cookies = ''

  if (process.server) {
    options.baseURL = process.env.API_BASE_URL
    cookies = context.req.headers.cookie
    options.headers.cookie = (context.req.headers.cookie) ? context.req.headers.cookie : ''
  } else {
    options.baseURL = process.env.BASE_URL
    cookies = document.cookie
  }

  if (cookies) {}
  /*
    TODO: make sense of this entire file
  */
  // if (cookies) {
  //   if (cookies.includes('sc_auth')) {
  //     options.headers.Authorization = `Bearer ${cookies.split('sc_auth=')[1].split(';')[0]}`
  //   }
  // }

  const axios = Axios.create(options)
  inject('axios', axios)
}
