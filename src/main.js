window.$ = window.jQuery = require('jquery');

import Vue from 'vue'
import App from './App.vue'
import { routes } from './routes';

import BootstrapVue from "bootstrap-vue"
import "bootstrap/dist/css/bootstrap.min.css"
import "bootstrap-vue/dist/bootstrap-vue.css"
Vue.use(BootstrapVue)

import VueRouter from 'vue-router';
Vue.use(VueRouter)

import { library } from '@fortawesome/fontawesome-svg-core'
import { faPlus, faPlusSquare, faTrashAlt, faListUl, faPenAlt, faTimes, faSearch, faCopy, faFilter, faRedoAlt, faFolderMinus, faTimesCircle, faTrash, faChevronDown, faChevronUp, faArchive, faCheck, faArrowLeft, faRibbon, faAward, faFunnelDollar } from '@fortawesome/free-solid-svg-icons'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
library.add(faPlus, faPlusSquare, faTrashAlt, faListUl, faPenAlt, faTimes, faSearch, faCopy, faFilter, faRedoAlt, faFolderMinus, faTimesCircle, faTrash, faChevronDown, faChevronUp, faArchive, faCheck, faArrowLeft, faRibbon, faAward, faFunnelDollar  )
Vue.component('font-awesome-icon', FontAwesomeIcon)

import VueToastr2 from 'vue-toastr-2'
import 'vue-toastr-2/dist/vue-toastr-2.min.css'
window.toastr = require('toastr')
Vue.use(VueToastr2)



import Loading from 'vue-loading-overlay';
import 'vue-loading-overlay/dist/vue-loading.css';
Vue.use(Loading);

import vueCustomElement from 'vue-custom-element'
Vue.use(vueCustomElement)

import 'document-register-element/build/document-register-element'

import VueObserveVisibility from 'vue-observe-visibility'
Vue.use(VueObserveVisibility)

import VueFormWizard from 'vue-form-wizard'
import 'vue-form-wizard/dist/vue-form-wizard.min.css'
Vue.use(VueFormWizard)

import Vuelidate from 'vuelidate'
Vue.use(Vuelidate)

const router = new VueRouter({
  routes,
  mode: 'history'
});

// new Vue({
//   el: '#app',
//   router,
//   render: h => h(App)
// })

App.router = router

Vue.customElement('vue-widget', App)

