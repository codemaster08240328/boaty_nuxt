import Axios from 'axios'

export default (context, inject) => {
  let options = {
    headers: {
      Authorization: ''
    }
  }
  let cookies = '' // need cookie in scope

  // do server stuff
  if (process.server) {
    cookies = context.req.headers.cookie // see adonisjs request object for full details
    options.baseURL = 'http://127.0.0.1:8080/' // server requires a full uri, and in our case we're talking between docker containers
  } else { // do client stuff
    options.baseURL = process.env.API_BASE_URL // handy for later
    cookies = document.cookie
  }

  /*
    Set the auth header with the cookie
    TODO make this more full proof
  */
  if (cookies && cookies.includes('sc_auth')) {
    options.headers.Authorization = `Bearer ${cookies.split('sc_auth=')[1].split(';')[0]}`
  }
  console.log(options)
  const axios = Axios.create(options)
  // app.$axios = axios
  inject('axios', axios)
}
