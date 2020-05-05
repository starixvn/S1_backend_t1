// import Vue from 'vue';
// import VueMoment from 'vue-moment';

// Vue.use(VueMoment);

import moment from 'moment';

export default {
  install: function (Vue) {
    Object.defineProperty(Vue.prototype, '$moment', {
      value: moment,
      writable: false,
    });

    Object.defineProperty(Vue.prototype, '$formatDate', {
      value: function (d) {
        return moment(d).format('YYYY-MM-DD');
      },
      writable: false,
    });

    Object.defineProperty(Vue.prototype, '$formatDateTime', {
      value: function (d) {
        return moment(d).format('YYYY-MM-DD HH:mm:ss');
      },
      writable: false,
    });

  },
};