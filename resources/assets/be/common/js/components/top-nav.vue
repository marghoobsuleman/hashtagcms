<template>
  <nav class="navbar navbar-expand-lg bg-light border-bottom">
    <div class="container-fluid">
      <a class="navbar-brand" href="/" target="_blank"><img class="cms-logo" :src="logo" :height="logoHeight" /> {{siteName}} </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
              data-bs-target="#navbarScroll" aria-controls="navbarScroll" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarScroll">
        <ul class="navbar-nav me-auto my-2 my-lg-0 navbar-nav-scroll" style="--bs-scroll-height: 100px;">
          <li class="nav-item">
            <global-site-button :data-current-site="dataCurrentSite"
                                :data-supported-sites="dataSupportedSites" :data-is-admin="dataIsAdmin"></global-site-button>
          </li>
        </ul>
        <div class="d-flex">
          Welcome &nbsp; <span class="text-success">{{ userName }}!</span>
          <ul class="navbar-nav me-auto my-2 my-lg-0 navbar-nav-scroll ms-1" style="--bs-scroll-height: 100px;">
            <li class="nav-item">
              <a href="/logout" @click.prevent="logout">Logout</a>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </nav>
</template>

<script>
import GlobalSiteButton from "./global-site-button.vue";
export default {
  components: {
    'global-site-button':GlobalSiteButton
  },
  mounted() {
    //console.log('dataCurrentSite '+this.dataCurrentSite);
  },
  props:[
    'dataUsername',
    'dataSiteName',
    'dataCurrentSite',
    'dataSupportedSites',
    'dataIsAdmin',
    'dataSiteCombo',
    'dataLogo',
    'dataLogoHeight'
  ],
  computed: {
    logo() {
      return (typeof this.dataLogo !== "undefined" && this.dataLogo !== "") ? this.dataLogo : AdminConfig.admin_asset("img/logo-transparent.png");
    }
  },
  data() {
    return {
      siteName:(this.dataSiteName || "hashtagcms.org"),
      userName:(this.dataUsername),
      isLoggedIn:!!(this.dataUsername && this.dataUsername !== ""),
      siteCombo:(!(typeof this.dataSiteCombo == "undefined" || this.dataSiteCombo === "false")),
      logoHeight:(typeof this.dataLogoHeight === "undefined" || this.dataLogoHeight === "") ? 50 : this.dataLogoHeight

    }
  },
  methods: {
    logout(event) {
      document.getElementById('logout-form').submit();
    }
  }
}

</script>
