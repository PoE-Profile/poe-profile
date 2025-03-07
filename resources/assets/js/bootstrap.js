import Vue from 'vue';


window._ = require('lodash');


/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
/**
 * Next we will register the CSRF Token as a common header with Axios so that
 * all outgoing HTTP requests automatically have it attached. This is just
 * a simple convenience so we don't have to attach every token manually.
 */

let token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

window.Vue = require('vue');

//For production
Vue.config.devtools = false;
Vue.config.debug = false;
Vue.config.silent = true;

//For local development
Vue.config.devtools = true;
Vue.config.debug = true;
Vue.config.silent = false;

import Echo from "laravel-echo"

// window.Pusher = require('pusher-js');
// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: process.env.MIX_PUSHER_APP_KEY,
//     cluster: process.env.MIX_PUSHER_APP_CLUSTER,
//     wsHost: process.env.MIX_PUSHER_HOST,
//     wsPort: process.env.MIX_PUSHER_PORT,
//     forceTLS: false,
//     disableStats: false,
//     enabledTransports: ['ws']
// });

// window.io = require('socket.io-client');
// window.Echo = new Echo({
//     broadcaster: 'socket.io',
//     // host: process.env.MIX_LARAVEL_ECHO_SERVER
//     host: 'http://127.0.0.1:6001/'
// });
