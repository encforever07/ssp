require('./bootstrap');

import Vue from 'vue'
import {AlertError, Form, HasError} from 'vform'
import VueProgressBar from 'vue-progressbar'
import Snotify, {SnotifyPosition} from 'vue-snotify'

window.Form = Form

const options = {
    toast: {
        position: SnotifyPosition.centerTop
    }
}

Vue.use(Snotify, options)

const VueProgressBarOptions = {
    color: '#17a2b8',
    failedColor: '#874b4b',
    thickness: '3px',
    transition: {
        speed: '0.2s',
        opacity: '0.6s',
        termination: 100
    },
    autoRevert: true,
    location: 'top',
    inverse: false
}


Vue.use(VueProgressBar, VueProgressBarOptions)

Vue.component('pagination', require('./components/partial/PaginationComponent.vue').default);
Vue.component('adminComponent', require('./components/admin_users/AdminComponent.vue').default);
Vue.component('especialidadComponent', require('./components/admin_users/EspecialidadComponent.vue').default);


Vue.component(HasError.name, HasError)
Vue.component(AlertError.name, AlertError)

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
});
