<template>
  <section>
    
<!-- The first one offers 3 options Like
Which lead to forms Like
Where I want ot use the second layout Like
SOme reasons why to get in touch Like
Plus we get a click on the site Like
oh okay, so no form? Like -->
    <v-containuer fluid>
      <v-layout class="my-4 display-1 text-xs-center" align-center justify-center>
        <h1 class="cyan--text text--darken-3">Meet The SailChecker Crew</h1>
      </v-layout>
    </v-containuer>
    <v-breadcrumbs id="breadcrumbs" justify-center>
      <v-icon slot="divider">forward</v-icon>
      <v-breadcrumbs-item :href="breadcrumb['@id']" v-for="breadcrumb in breadcrumbs" :key="breadcrumb['@id']">
        {{ breadcrumb.name }}
      </v-breadcrumbs-item>
    </v-breadcrumbs>

    <v-container>
      <v-layout class="text-xs-center my-4" row wrap>
        <v-flex xs12 md12>
          <div class="dodeca">
            <div class="dode1">
              <div class="dode2">
                <img src="https://s3.eu-west-2.amazonaws.com/sc30/uploads/2018/02/chris-1-1.png" alt="" width="210" height="207" />
              </div>
            </div>
          </div>
          <div class="person-info">
            <div class="name headline">Chris Lait</div>
            <div class="position body-2">CEO</div>
            <div class="description my-3 body-1">
              <p>
                Chris founded SailChecker in 2011 and is our CEO. With over 25 years of experience in the yachting industry, he is the man working behind the scenes to make sure everything runs as smoothly as possible.
              </p>
            </div>
          </div>
          <v-btn class="cyan darken-3 white--text" large href="tel:+448000988118">Call Chris</v-btn>
          <v-btn class="cyan darken-3 white--text" large href="mailto:chris@sailchecker.com">Email Chris</v-btn>
        </v-flex>
      </v-layout>
    </v-container>

    <full-width-page-title :title="'Charter Consultants'"></full-width-page-title>

    <v-container>
      <v-layout class="text-xs-center my-4" row wrap>
        <v-flex xs12 sm6>
          <div class="dodeca">
            <div class="dode1">
              <div class="dode2">
                <img src="https://s3-eu-west-2.amazonaws.com/sc30/uploads/2018/02/16110425/kate-expo-2-general-manager.jpg" alt="" width="210" height="207" />
              </div>
            </div>
          </div>
          <div class="person-info">
            <div class="name headline">Kate Ioannidou</div>
            <div class="position body-2">General Manager & Charter Consultant</div>
            <div class="description my-3 body-1">
              <p>
                Kate is the oldest member of our team and now our General Manager. She is leading our sales and media teams and making sure that you don’t come across any inconveniences at all before, during and after your holiday. Kate has an extensive knowledge of the yacht charter world and hopes to sail in as many places as possible.
              </p>
            </div>
          </div>
          <v-btn class="cyan darken-3 white--text" large href="tel:+308008481290">Call Kate</v-btn>
          <v-btn class="cyan darken-3 white--text" large href="mailto:kate@sailchecker.com">Email Kate</v-btn>
        </v-flex>

        <v-flex xs12 sm6 :class="{'mt-4': $vuetify.breakpoint.xsOnly }">
          <div class="dodeca">
            <div class="dode1">
              <div class="dode2">
                <img src="https://s3.eu-west-2.amazonaws.com/sc30/uploads/2018/02/elpida.jpg" alt="" width="210" height="207" />
              </div>
            </div>
          </div>
          <div class="person-info">
            <div class="name headline">Elpida Barbaresou</div>
            <div class="position body-2">Charter Consultant</div>
            <div class="description my-3 body-1">
              <p>
                After 3 years of working as a holiday representative and hostess for one of the largest Greek yacht charter operators, Elpida set her sights on new horizons and joined the SailChecker team in 2016. She is now part of our charter consultants team, making sure that from the word go, SailChecker offers first class service to all clients.
              </p>
            </div>
          </div>
          <v-btn class="cyan darken-3 white--text" large href="tel:+308008481290">Call Elpida</v-btn>
          <v-btn class="cyan darken-3 white--text" large href="mailto:elpida@sailchecker.com">Email Elpida</v-btn>
        </v-flex>
      </v-layout>
    </v-container>

    <full-width-page-title :title="'Finance & Administration'"></full-width-page-title>
    
    <v-container>
      <v-layout class="text-xs-center my-4" row wrap>
        <v-flex xs12 sm6>
          <div class="dodeca">
            <div class="dode1">
              <div class="dode2">
                <img src="https://s3.eu-west-2.amazonaws.com/sc30/uploads/2018/02/LACGidpic.jpg" alt="" width="210" height="207" />
              </div>
            </div>
          </div>
          <div class="person-info">
            <div class="name headline">Leslie Galano</div>
            <div class="position body-2">Financial Coordinator</div>
            <div class="description my-3 body-1">
              <p>
                Leslie is our financial accounts guru and an essential member of the team. She will be the one helping you with making and tracking your payments promptly and safely. She is responsible for every financial transaction and keeping things on track.
              </p>
            </div>
          </div>
          <v-btn class="cyan darken-3 white--text" large href="mailto:leslie@sailchecker.com">Email Leslie</v-btn>
        </v-flex>

        <v-flex xs12 sm6 :class="{'mt-4': $vuetify.breakpoint.xsOnly }">
          <div class="dodeca">
            <div class="dode1">
              <div class="dode2">
                <img src="https://s3.eu-west-2.amazonaws.com/sc30/uploads/2018/02/464105_116080538520307_450773061_o.jpg" alt="" width="210" height="207" />
              </div>
            </div>
          </div>
          <div class="person-info">
            <div class="name headline">Joanne Lait</div>
            <div class="position body-2">Administration</div>
            <div class="description my-3 body-1">
              <p>
                Joanne has been involved with SailChecker since they founded and is a true asset to the team. She manages our back office, providing administrative support for various duties such as legal, financial, marketing – related, as well as customer support. In short, she is our team’s right hand.
              </p>
            </div>
          </div>
        </v-flex>
      </v-layout>
    </v-container>

    <full-width-page-title :title="'Technology'"></full-width-page-title>

    <v-container>
      <v-layout class="text-xs-center my-4" row wrap>
        <v-flex xs12 sm6>
          <div class="dodeca">
            <div class="dode1">
              <div class="dode2">
                <img src="https://s3.eu-west-2.amazonaws.com/sc30/uploads/2018/02/matt-1.jpg" alt="" width="210" height="207" />
              </div>
            </div>
          </div>
          <div class="person-info">
            <div class="name headline">Matthew Gould</div>
            <div class="position body-2">CTO</div>
            <div class="description my-3 body-1">
              <p>
                Matthew is our full stack developer with a deep passion for computing and electronics. He ensures that SailChecker.com and all microservices are running safely, and securely. We are lucky to have him on our team.
              </p>
            </div>
          </div>
          <v-btn class="cyan darken-3 white--text" large href="mailto:matthew@sailchecker.com">Email Matthew</v-btn>
        </v-flex>
      </v-layout>
    </v-container>

    <full-width-page-title :title="'Content & Social Marketing'"></full-width-page-title>

    <v-container>
      <v-layout class="text-xs-center my-4" row wrap>
        <v-flex xs12 sm6>
          <div class="dodeca">
            <div class="dode1">
              <div class="dode2">
                <img src="https://s3.eu-west-2.amazonaws.com/sc30/uploads/2018/02/mobile_20180214_111949.jpg" alt="" width="210" height="207" />
              </div>
            </div>
          </div>
          <div class="person-info">
            <div class="name headline">Anny Wooldridge</div>
            <div class="position body-2">Content Writer</div>
            <div class="description my-3 body-1">
              <p>
                Anny was born and raised in a small town in Norfolk, England. She is a passionate traveller and has visited over 29 countries as well as living and working in Greece, France, USA and Colombia. She started a travel blog inspiring others to travel and has pursued her passion for writing ever since as a freelance travel writer and editor. We are delighted to have her on our social media and content team.
              </p>
            </div>
          </div>
        </v-flex>
      </v-layout>
    </v-container>

  </section>
</template>

<style scoped>
.person-info {
  width: 300px;
  margin: 0 auto;
}

/*
 * ==== dodecagon
 */
.dodeca, .dodeca div {
  margin: 0 auto;
  transform-origin: 50% 50%;
  overflow: hidden;
  width: 200px;
  height: 200px;
}
.dodeca {
  transform: rotate(120deg);
}
.dode1 {
  transform: rotate(-60deg);
}
.dode2 {
  transform: rotate(-60deg);
}
</style>

<script>
import MetaService from '~/services/meta'
import FullWidthPageTitle from '~/components/FullWidthPageTitle'

export default {
  components: {
    FullWidthPageTitle
  },
  transition: {
    name: 'page',
    mode: 'out-in'
  },
  async asyncData ({ breadcrumbs, error }) {
    try {
      return {
        breadcrumbs: breadcrumbs
      }
    } catch (e) {
      error({ statusCode: 404, message: 'Page not found' })
    }
  },
  data () {
    return {}
  },
  head () {
    const breadcrumbsJSON = MetaService.breadcrumbsJSON(this.breadcrumbs)

    const meta = MetaService.base({
      title: 'Meet the SailChecker Crew',
      description: 'We really want you to meet a few of our passionate SailChecker.com crew who navigate our ship from day-to-day. Looking forward to creating amazing yacht charter',
      image: 'https://s3.eu-west-2.amazonaws.com/sc30/uploads/2018/02/chris-1-1.png',
      creator: 'Chris Lait',
      type: 'website',
      url: this.$i18n.path(this.$route.path)
    })

    return {
      title: 'Meet the SailChecker Crew',
      meta: meta,
      link: [
        {
          hid: 'en',
          rel: 'alternate',
          href: this.baseUrl + this.$i18n.hreflang(this.$route.path),
          hreflang: 'x-default'
        }
        // {
        //   hid: 'engb',
        //   rel: 'alternate',
        //   href: this.baseUrl + `gb/${this.$i18n.hreflang(this.$route.path)}`,
        //   hreflang: 'en-gb'
        // }
      ],
      script: [
        { innerHTML: breadcrumbsJSON, type: 'application/ld+json' }
      ],
      __dangerouslyDisableSanitizers: ['script']
    }
  }
}
</script>