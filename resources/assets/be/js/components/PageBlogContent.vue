<template>
  <form :action="saveUrl" method="post" class="form-horizontal" role="form">

    <input hidden name="id" :value="form.id" />
    <input hidden name="backURL" :value="form.backURL" />
    <input hidden name="actionPerformed" :value="form.actionPerformed" />
    <input hidden name="insert_by" :value="form.insert_by" />
    <input hidden name="update_by" :value="form.insert_by" />
    <input hidden name="site_id" :value="form.site_id" />

    <div class="form-group">

      <div class="col-sm-2">
        <label for="content_type">Content Type</label>
      </div>
      <div class="col-sm-10">
        <select name="content_type" id="content_type" v-model="form.content_type">
          <option value="">Select</option>
          <option v-for="contentType in contentTypes">{{contentType}}</option>
        </select>
      </div>
    </div>

    <div class="form-group">

      <div class="col-sm-2">
        <label for="category_id">Content Category</label>
      </div>

      <div class="col-sm-10">
        <select name="category_id" id="category_id" v-model="form.category_id">
          <option value="">Select</option>
          <option v-for="contentCategory in contentCategories" :value="contentCategory.id">{{contentCategory.lang.name}}</option>
        </select>
      </div>
    </div>

    <div class="form-group">

      <div class="col-sm-2">
        <label for="lang_name">Name</label>
      </div>

      <div class="col-sm-10">
        <input type="text" required id="lang_name" name="lang_name" v-model="form.lang_name" />
      </div>
    </div>

    <div class="form-group">

      <div class="col-sm-2">
        <label for="lang_title">Title</label>
      </div>

      <div class="col-sm-10">
        <input type="text" required id="lang_title" name="lang_title" v-model="form.lang_title" />
      </div>
    </div>

    <div class="form-group hide">

      <div class="col-sm-2">
        <label for="alias">Alias</label>
      </div>

      <div class="col-sm-10">
        <input type="text" required id="alias" name="alias" v-model="form.alias" />
      </div>
    </div>

    <div class="form-group hide">

      <div class="col-sm-2">
        <label for="lang_active_key">Active Key</label>
      </div>

      <div class="col-sm-10">
        <input type="text" required id="lang_active_key" name="lang_active_key" v-model="form.lang_active_key" />
      </div>
    </div>

    <div class="form-group">

      <div class="col-sm-2">
        <label for="tenant_id">Tenant</label>
      </div>

      <div class="col-sm-10">
        <select name="tenant_id" id="tenant_id" v-model="form.tenant_id">
          <option value="">Select</option>
          <option v-for="tenant in tenants" :value="tenant.id">{{tenant.name}}</option>
        </select>

      </div>
    </div>

    <div class="form-group">

      <div class="col-sm-2">
        <label for="link_rewrite">Url</label>
      </div>

      <div class="col-sm-10">
        <input type="text" required id="link_rewrite" name="link_rewrite" v-model="form.link_rewrite" />
      </div>

    </div>
    <div class="form-group">

      <div class="col-sm-2">
        <label for="menu_placement">Menu Group</label>
      </div>

      <div class="col-sm-10">
        <select name="menu_placement" id="menu_placement" v-model="form.menu_placement">
          <option value="">Select</option>
          <option v-for="menuPlacement in menuPlacements">{{menuPlacement}}</option>
        </select>

      </div>

    </div>

    <div class="form-group">
      <div class="col-sm-2">
        <label for="link_navigation">Full Url [optional]</label>
      </div>

      <div class="col-sm-10">
        <input type="text" id="link_navigation" name="link_navigation" v-model="form.link_navigation" />
      </div>
    </div>

    <div class="form-group">

      <div class="col-sm-2">
        <label for="lang_page_content">Body</label>
      </div>

      <div class="col-sm-10">
        <textarea id="lang_page_content" class="form-control" rows="20" cols="80">{{form.lang_page_content}}</textarea>
      </div>

    </div>

    <div class="form-group">

      <div class="col-sm-2">
        <label for="lang_description">Description</label>
      </div>

      <div class="col-sm-10">
        <textarea id="lang_description" class="form-control" rows="10" cols="80">{{form.lang_description}}</textarea>
      </div>
    </div>

    <div class="form-group">

      <div class="col-sm-2">
        <label for="header_content">Header Content</label>
      </div>

      <div class="col-sm-10">
        <textarea id="header_content" class="form-control" rows="5" cols="80">{{form.header_content}}</textarea>
      </div>
    </div>

    <div class="form-group">

      <div class="col-sm-2">
        <label for="footer_content">Footer Content</label>
      </div>

      <div class="col-sm-10">
        <textarea id="footer_content" class="form-control" rows="5" cols="80">{{form.footer_content}}</textarea>
      </div>

    </div>

    <div class="form-group">

      <div class="col-sm-2">
        <label for="exclude_in_listing">Exclude in Listing?</label>
      </div>

      <div class="col-sm-10">
        <input type="checkbox" id="exclude_in_listing" name="exclude_in_listing" v-model="form.exclude_in_listing" />
      </div>

    </div>

    <div class="form-group">
      <fieldset class="fieldset">
        <legend>SEO Settings</legend>

        <div class="form-group">

          <div class="col-sm-2">
            <label for="lang_target">Target</label>
          </div>

          <div class="col-sm-10">
            <select name="lang_target" id="lang_target" v-model="form.lang_target">
              <option value="">Select</option>
              <option v-for="targetType in targetTypes">{{targetType}}</option>
            </select>
          </div>
        </div>

        <div class="form-group">

          <div class="col-sm-2">
            <label for="lang_link_relation">Relation</label>
          </div>

          <div class="col-sm-10">
            <select name="lang_link_relation" id="lang_link_relation" v-model="form.lang_link_relation">
              <option value="">Select</option>
              <option v-for="relationType in relationTypes">{{relationType}}</option>
            </select>
          </div>
        </div>

        <div class="form-group">

          <div class="col-sm-2">
            <label for="lang_meta_title">Page Title</label>
          </div>

          <div class="col-sm-10">
            <input type="text" id="lang_meta_title" name="lang_meta_title" v-model="form.lang_meta_title" />
          </div>
        </div>

        <div class="form-group">

          <div class="col-sm-2">
            <label for="lang_meta_keywords">Meta Keywords</label>
          </div>

          <div class="col-sm-10">
            <input type="text" id="lang_meta_keywords" name="lang_meta_title" v-model="form.lang_meta_keywords" />
          </div>

        </div>

        <div class="form-group">

          <div class="col-sm-2">
            <label for="lang_meta_description">Meta Description</label>
          </div>

          <div class="col-sm-10">
            <textarea id="lang_meta_description" class="form-control" rows="5" cols="80">{{form.lang_meta_description}}</textarea>
          </div>
        </div>

        <div class="form-group">

          <div class="col-sm-2">
            <label for="lang_meta_robots">Meta Robots</label>
          </div>

          <div class="col-sm-10">
            <input type="text" id="lang_meta_robots" name="lang_meta_robots" v-model="form.lang_meta_robots" />
          </div>
        </div>

        <div class="form-group">

          <div class="col-sm-2">
            <label for="lang_meta_canonical">Meta Canonical</label>
          </div>

          <div class="col-sm-10">
            <input type="text" id="lang_meta_canonical" name="lang_meta_canonical" v-model="form.lang_meta_canonical" />
          </div>
        </div>
      </fieldset>
    </div>

    <div class="form-group">
      <fieldset class="fieldset">
        <legend>Publishing Options</legend>
        <div class="form-group">
          <div class="col-sm-2">
            <label for="required_login">Required Login?</label>
          </div>
          <div class="col-sm-10">
            <input type="checkbox" id="required_login" name="required_login" v-model="form.required_login" />
          </div>
        </div>
        <div class="form-group">
          <div class="col-sm-2">
            <label for="publish_status">Published</label>
          </div>
          <div class="col-sm-10">
            <input type="checkbox" id="publish_status" name="publish_status" v-model="form.publish_status" />
          </div>
        </div>
      </fieldset>
    </div>

    <div class="row">
      <div class="form-group center-align">
        <input type="submit" name="submit" value="Save" class="btn btn-success" />
        <a :href="backURL" class="btn btn-default">Cancel</a>
      </div>
    </div>
  </form>
</template>

<script>
  export default {
      mounted() {

      },
      props:[
          'dataResult',
          'dataContentType',
          'dataBackUrl',
      ],
      data() {
        return {
            result: JSON.parse(this.dataResult),
            contentType: JSON.parse(this.dataContentType),
            contentTypes:[],
            contentCategories:[],
            tenants:[],
            form: new Form({
              id:0,
              name: "",
              actionPerformed:this.dataActionPerformed,
              backURL:this.dataBackUrl
            }),
            errors:{},
        }
      },
      methods: {
          save() {

          }
      }
}

</script>
