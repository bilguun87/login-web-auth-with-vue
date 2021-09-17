/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */
import Vue from 'vue'
import vSelect from 'vue-select'
//window.Vue = Vue;
require('./bootstrap');


/*window.Vue = require('vue');*/

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

/*Vuex-iig permission hadgalah zorilgoor ashiglaj bna*/
import store from "./store"
///
Vue.component('pagination', require('laravel-vue-pagination'));
Vue.component('v-select', vSelect);

/*

Vulnerabilities section

*/
Vue.component('maba-menu', require('./components/menu.vue').default);
Vue.component('maba-vulnindex', require('./components/vulner-components/vulndashboard.vue').default);
Vue.component('maba-vulnsearch', require('./components/vulner-components/vulnersearch.vue').default);
Vue.component('maba-vulnupload', require('./components/vulner-components/vulnerupload.vue').default);
Vue.component('maba-vulnallot', require('./components/vulner-components/vulnergroup.vue').default);
Vue.component('maba-vulnservers', require('./components/vulner-components/vulnerhosts.vue').default);
Vue.component('maba-vulnreport', require('./components/vulner-components/generatereport.vue').default);
//Vue.component('vulnreport-result', require('./components/vulner-components/reportresult.vue').default);
/*

Backup section

*/
Vue.component('backup-index', require('./components/backups/backupindex.vue').default);
Vue.component('backup-types', require('./components/backups/backuptypes.vue').default);
Vue.component('backup-places', require('./components/backups/backupplaces.vue').default);
Vue.component('backup-moves', require('./components/backups/backupmoves.vue').default);

/*

User management section

*/
Vue.component('user-index', require('./components/users/userindex.vue').default);
Vue.component('user-new', require('./components/users/newuser.vue').default);
// Vue.component('user-edit', require('./components/users/edituserperm.vue').default);

/*

Role management

*/
Vue.component('role-index', require('./components/roles/roleindex.vue').default);

/* OraDB check */

Vue.component('oradb-index', require('./components/oradb/oradbindex.vue').default);
Vue.component('oradb-cons', require('./components/oradb/oradbcons.vue').default);

/* ADDC check */

Vue.component('addc-index', require('./components/addc/addcindex.vue').default);
Vue.component('addc-group', require('./components/addc/groupmatch.vue').default);
Vue.component('addc-exch', require('./components/addc/exchange.vue').default);

/*Links*/

Vue.component('link-index', require('./components/links/linkindex.vue').default);
/*

Test permissions

*/
Vue.component('test-index', require('./components/testpermission/testindex.vue').default);

export

const app = new Vue({
    el: '#app',
    store,
});
