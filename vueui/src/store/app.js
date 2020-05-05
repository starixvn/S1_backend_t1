import {
  initApi
} from '@/api/app';

const app = {
  state: {
    // errorCodes, reportTypes, queryReportType, defaultPageSizeList
    constants: {
      errorCodes: null,
      reportTypes: null,
      queryReportType: null,
      defaultPageSizeList: null,
    },
    //lang, loggedUsername, sidebar, token
    runtime: {
      showSubTotal: true,
      sidebar: {
        opened: true,
      },
      activeNavMenuIndex: '1',
      debugReportMode: true,
      inited: false,
    },
  },
  mutations: {
    TOGGLE_SIDEBAR: state => {
      state.runtime.sidebar.opened = !state.runtime.sidebar.opened;
    },
    CLOSE_SIDEBAR: state => {
      state.runtime.sidebar.opened = false;
    },
    REMOTE_INIT_API(state, data) {
      state.constants = data.constants;
      state.runtime = data.runtime;
      //set lang
      // Vue.console.log(state.runtime.lang);
      // Vue.$i18n.locale = state.runtime.lang;
    },
  },
  actions: {
    ToggleSideBar: ({
      commit,
    }) => {
      commit('TOGGLE_SIDEBAR');
    },
    CloseSideBar({
      commit,
    }) {
      commit('CLOSE_SIDEBAR');
    },
    remoteInitApi({
      commit,
    }) {
      return new Promise((resolve, reject) => {
        initApi()
          .then(data => {
            //   this.$log('get data', response.data);
            //   const data = response.data;
            // Vue.console.log(data);
            commit('REMOTE_INIT_API', data.result);
            resolve();
          })
          .catch(error => {
            reject(error);
          });
      });
    },
  },
};

export default app;