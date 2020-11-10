require('./bootstrap');

window.Vue = require('vue');

import moment from 'moment';
Vue.prototype.moment = moment;

/* ElementUI - Biblioteca para interfaz de usuario */
import ElementUI from 'element-ui';
import 'element-ui/lib/theme-chalk/index.css';
import locale from 'element-ui/lib/locale/lang/en';
Vue.use(ElementUI, { locale })

/* SweetAlert2 - Biblioteca para ventanas emergentes */
import Swal from 'sweetalert2';
window.Swal = Swal;
/* import VueSweetalert2 from 'vue-sweetalert2';
Vue.use(VueSweetalert2);
https://www.digitalocean.com/community/tutorials/vuejs-vue-sweetalert2#installation
*/

/* Vuesax - Biblioteca para interfaz de usuario */
import Vuesax from 'vuesax';
import 'vuesax/dist/vuesax.css';
Vue.use(Vuesax, {
// options here
});

Vue.component('pagination', require('laravel-vue-pagination'));

/* EventBus - Biblioteca para la comunicación entre componentes */
export const EventBus = new Vue();
window.EventBus = EventBus;

/*  //Moment date
window.moment = require('moment'); */

/* Componentes de la app */
Vue.component('Auth', require('./components/Auth').default);
Vue.component('App', require('./components/App').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */
import router from './routes.js'
const app = new Vue({
    el: '#app',
    router
});
