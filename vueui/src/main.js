import Vue from 'vue';
import App from './App.vue';
import './plugins/element.js';
import './plugins/fontawesome.js';
import momentPlugin from './plugins/moment.js';
import router from './router';
import store from './store';
import vueLoggerPlugin from 'vue-logger';
import i18n from './i18n';

Vue.config.productionTip = false;

Vue.use(vueLoggerPlugin, {
  prefix: () => new Date(),
  dev: true,
  shortname: true,
  levels: ['log', 'warn', 'debug', 'error', 'dir', ],
  forceLevels: [],
});

Vue.use(momentPlugin);

new Vue({
  router,
  store,
  i18n,
  render: h => h(App),
  created() {
    // this.$log(this.$moment);
    //init data first
    this.$store.dispatch('remoteInitApi');
  },
}).$mount('#app');
//init data first
// store.dispatch('remoteInitApi');