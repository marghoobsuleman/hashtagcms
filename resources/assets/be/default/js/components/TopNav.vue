<template>
<nav class="navbar navbar-inverse" id="topNavBar">
        <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">

                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" aria-expanded="false">
                    <left-menu-toggle data-icon-css="fa fa-bars hand" data-icon-css-off="fa fa-bars hand"></left-menu-toggle>
                    <span class="sr-only">Toggle navigation</span>
                </button>
                <a class="navbar-brand" href="/" target="_blank"><img align :src="logo" height="50" /> {{siteName}}</a>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse">
                <ul class="nav navbar-nav" v-if="siteCombo">
                    <li style="padding-top: 16px"><global-site-button :data-current-site="dataCurrentSite"></global-site-button></li>
                </ul>
                <form class="navbar-form navbar-left hide">
                    <div class="form-group">
                        <input type="text" class="form-control" placeholder="Search">
                    </div>
                    <button type="submit" class="btn btn-default">Submit</button>
                </form>
                <ul class="nav navbar-nav navbar-right">
                    <li><a href="#"> Welcome {{userName}}! </a></li>
                    <li class="dropdown">
                        <a href="/logout" @click.prevent="logout">Logout</a>
                    </li>
                </ul>
            </div><!-- /.navbar-collapse -->
        </div><!-- /.container-fluid -->
    </nav>

</template>

<script>

export default {
  mounted() {
     //console.log('dataCurrentSite '+this.dataCurrentSite);
  },
  props:[
    'dataUsername',
    'dataSitename',
    'dataCurrentSite',
    'dataSiteCombo',
    'dataLogo'
  ],
    computed: {
      logo() {
          return (typeof this.dataLogo !== "undefined" && this.dataLogo !== "") ? this.dataLogo : AdminConfig.admin_asset("img/logo-transparent.png");
      }
    },
  data() {
    return {
        siteName:(this.dataSitename || "MonsterIndia.com"),
        userName:(this.dataUsername),
        isLoggedIn:(this.dataUsername && this.dataUsername!="") ? true : false,
        siteCombo:(typeof this.dataSiteCombo == "undefined" || this.dataSiteCombo == "false") ? false : true

    }
  },
  methods: {
    logout(event) {
      document.getElementById('logout-form').submit();
    }
  }
}

</script>
