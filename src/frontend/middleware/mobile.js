export default function (context) {
  let userAgent = process.server ? context.req.headers['user-agent'] : navigator.userAgent

  if (userAgent.match(/Android/i) ||
      userAgent.match(/webOS/i) ||
      userAgent.match(/iPhone/i) ||
      // || userAgent.match(/iPad/i) ||
      userAgent.match(/iPod/i) ||
      userAgent.match(/BlackBerry/i) ||
      userAgent.match(/Windows Phone/i)
  ) {
    context.isMobile = true
  } else {
    context.isMobile = false
  }
}
