import Vue from 'vue';
import Router from 'vue-router';
// import store from './store';
// import Summary2Report from './views/Summary2Report.vue';
// import PlayerReport from './views/PlayerReport.vue';
import SuperReport from './views/SuperReport.vue';

Vue.use(Router);

export default new Router({
  // beforeEach: (to, from, next) => {
  //   Vue.console.log(to.path, from.path, 'global before each');
  //   next();
  // },
  routes: [{
    path: '/',
    redirect: {
      path: '/super_report/summary2',
    },
  },
  {
    path: '/super_report/:reportType',
    name: 'super_report',
    component: SuperReport,
    props: true,
    // beforeEnter: (to, from, next) => {
    //   Vue.console.log(to.path, from.path, 'reset current report');
    //   // Vue.console.log(Vue);
    //   //try clear current report
    //   store.dispatch('resetCurrentReport');
    //   next();
    // },
  },
  ],
});