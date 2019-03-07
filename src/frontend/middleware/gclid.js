export default function ({ isServer, res, query }) {
  if (query.gclid !== undefined) {
    if (isServer) {
      res.setHeader('Set-Cookie', [`gclid=${query.gclid};Path=/`])
    } else {
      document.cookie = `access_token=${query.gclid};Path=/`
    }
  }
}
