import {createApp} from 'vue'
import App from './views/App.vue'
import axios from 'axios'
import VueAxios from 'vue-axios'
import router from './routes/index.js'

import mixins from './utils/mixins'
import getters from './utils/GETTERS'
import access from './utils/ACCESS'
import global from './utils/GLOBAL'
import middleware from './routes/middleware'


import { store }  from './stores/store.js'
import VueMultiselect from 'vue-multiselect'

import 'vue-color-kit/dist/vue-color-kit.css'


import PerfectScrollbar from 'vue3-perfect-scrollbar'
import 'vue3-perfect-scrollbar/dist/vue3-perfect-scrollbar.css'


window.TOKEN_KEY = 'tk-pm'


const app = createApp(App)
app.config.globalProperties.$axios = axios;
app.use(router)
app.use(VueAxios, axios)

app.component('multiselect', VueMultiselect )





app.use(store)
app.use(PerfectScrollbar)

app.mixin(mixins)
app.mixin(middleware)
app.mixin(getters)
app.mixin(access)
app.mixin(global)

app.mount('#app')




