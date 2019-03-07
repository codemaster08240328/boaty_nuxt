'use strict'
const LinkGroup = use('App/Models/LinkGroup')
const PageLink = use('App/Models/PageLink')
const Page = use('App/Models/Page')
const _ = use('lodash')

class ReviewController {
  async index () {
    return LinkGroup.all()
  }

  async pageLinkIndex ({ params }) {
    return PageLink
      .query()
      .where('group_id', params.group_id)
      .with('page', (builder) => {
        builder.setVisible(['title', 'slug', 'id'])
      })
      .fetch()

    // query for frontend
    // select * from page_links where group_id IN (select group_id from page_links pl where pl.page_id = 15)
  }

  async storeGroup ({ request, auth }) {
    const data = request.only('name')
    data.user_id = auth.user.id
    return LinkGroup.create(data)
  }

  async updateGroup ({ request, params }) {
    const { name } = request.only('name')
    const { group_id } = params
    const linkGroup = await LinkGroup.find(group_id)
    linkGroup.name = name
    return linkGroup.save()
  }

  async deleteGroup ({ params }) {
    const { group_id } = params
    await LinkGroup
      .query()
      .where('id', group_id)
      .delete()

    await PageLink
      .query()
      .where('group_id', group_id)
      .delete()
    return true
  }

  async deleteLink ({ request, params }) {
    const { group_id, page_id } = params

    const pageLink = await PageLink.query()
      .where('group_id', group_id)
      .where('page_id', page_id)
      .first()

    return pageLink.delete()
  }

  async orderLinks ({ request, params }) {
    const data = request.all()
    let pageLink = {}
    for (let link of data.ordered) {
      pageLink = await PageLink.findBy('page_id', link.page_id)
      pageLink.order = link.order
      await pageLink.save()
    }

    return pageLink
  }

  async storeLink ({ request, params, auth }) {
    const data = request.only(['title', 'order', 'page_id'])
    data.group_id = params.group_id
    data.user_id = auth.user.id

    const pageLink = await PageLink.create(data)

    return PageLink.query()
      .where('page_id', pageLink.page_id)
      .where('group_id', pageLink.group_id)
      .with('page', (builder) => {
        builder.setVisible(['title', 'slug', 'id'])
      })
      .first()
  }

  async pageTitles () {
    return Page.query().setVisible(['id', 'title', 'type']).whereNotIn('type', ['yacht-charter', 'search']).fetch()
  }
}

module.exports = ReviewController
