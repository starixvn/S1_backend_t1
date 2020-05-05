import Vue from 'vue';
import i18n from '@/i18n';

import {
  RESET_CURRENT_REPORT_TO_EMPTY,
  SET_SEARCH_BY,
  SET_ORDER_BY,
  SET_GROUP_BY,
  UPDATE_CURRENT_REPORT_TYPE,
  UPDATE_CURRENT_PAGE,
  UPDATE_SIZE_PER_PAGE,
  UPDATE_ONE_PAGE_CURRENT_REPORT,
  UPDATE_COUNT_ON_CURRENT_REPORT,
  UPDATE_TOTAL_ON_CURRENT_REPORT,
  UPDATE_SUMMARY_ON_CURRENT_REPORT
} from './mutationTypes';

import {
  querySuperReport
} from '@/api/superReport';

// import {
//   cloneAnyObject
// } from '@/utils/func';

const EMPTY_REPORT = {
  // conditions
  searchBy: null,
  orderBy: {
    orderAlias: null,
    direction: null,
  },
  groupBy: null,
  limitBy: {
    currentPage: 1,
    sizePerPage: 25,
  },
  searchOptions: {
    useAssocRows: true,
  },
  // conditions
  // data
  rows: null,
  header: null,
  // currentPage: 1,
  // sizePerPage: 25,
  count: 0,
  pageSizeList: [25, 50, 100, ],
  summary: null,
  total: null,
  // data
  // ready flag
  onePageReady: true,
  countReady: false,
  totalReady: false,
  summaryReady: false,
  // ready flag
};

function cloneAnyObject(origin) {
  let originProto = Object.getPrototypeOf(origin);
  return Object.assign(Object.create(originProto), origin);
}

const initReport = cloneAnyObject(EMPTY_REPORT);

const superReport = {
  state: {
    currentReport: initReport,
    currentReportType: null,
  },
  getters: {
    conditions(state) {
      return {
        limitBy: state.currentReport.limitBy,
        options: state.currentReport.searchOptions,
        searchBy: state.currentReport.searchBy,
        orderBy: state.currentReport.orderBy,
        groupBy: state.currentReport.groupBy,
      };
    },
  },
  mutations: {
    [RESET_CURRENT_REPORT_TO_EMPTY]: state => {
      Vue.console.log('resetCurrentReportToEmpty');

      // Object.assign(state.currentReport, EMPTY_REPORT);
      state.currentReport = cloneAnyObject(EMPTY_REPORT);
      state.currentReport.onePageReady = true;
      // state.currentReport = cloneAnyObject(EMPTY_REPORT);
    },
    [UPDATE_CURRENT_REPORT_TYPE]: (state, {
      currentReportType,
    }) => {
      state.currentReportType = currentReportType;
    },
    [SET_SEARCH_BY]: (state, {
      searchBy,
    }) => {
      state.currentReport.searchBy = searchBy;
    },
    [SET_ORDER_BY]: (state, {
      orderBy,
    }) => {
      state.currentReport.orderBy = orderBy;
    },
    [SET_GROUP_BY]: (state, {
      groupBy,
    }) => {
      state.currentReport.groupBy = groupBy;
    },
    [UPDATE_CURRENT_PAGE]: (state, {
      currentPage,
    }) => {
      state.currentReport.limitBy.currentPage = currentPage;
      state.currentReport.onePageReady = false;
    },
    [UPDATE_SIZE_PER_PAGE]: (state, {
      sizePerPage,
    }) => {
      state.currentReport.limitBy.sizePerPage = sizePerPage;
      state.currentReport.onePageReady = false;
    },
    [UPDATE_ONE_PAGE_CURRENT_REPORT]: (state, {
      header,
      rows,
      settings,
    }) => {
      state.currentReport.header = header;
      const firstColKey = header[0]['key'];
      if (settings.showSubTotal && rows !== null) {
        const subTotal = Object.assign({}, rows[0]);
        // Vue.console.log('one page sub total before', subTotal);

        //build one page sub total
        for (let key in subTotal) {
          if (subTotal.hasOwnProperty(key)) {
            if (key === firstColKey) {
              subTotal[key] = i18n.t('normal.subTotal');
              continue;
            }
            // let val = subTotal[key];
            const values = rows.map(item => Number(item[key]));
            const precisions = [];
            let notNumber = true;
            values.forEach(value => {
              if (!isNaN(value)) {
                notNumber = false;
                let decimal = `${value}`.split('.')[1];
                precisions.push(decimal ? decimal.length : 0);
              }
            });
            const precision = Math.max.apply(null, precisions);
            if (!notNumber) {
              subTotal[key] = values.reduce((prev, curr) => {
                const value = Number(curr);
                if (!isNaN(value)) {
                  return parseFloat((prev + curr).toFixed(Math.min(precision, 20)));
                } else {
                  return prev;
                }
              }, 0);
            } else {
              subTotal[key] = '';
            }
          }
        }

        // Vue.console.log('after sub total', subTotal);
        state.currentReport.rows = [...rows, subTotal, ];
      } else {
        state.currentReport.rows = rows;
      }
      state.currentReport.pageSizeList = settings.pageSizeList;
      state.currentReport.onePageReady = true;
    },
    [UPDATE_COUNT_ON_CURRENT_REPORT]: (state, {
      count,
    }) => {
      state.currentReport.count = count;
      state.currentReport.countReady = true;
    },
    [UPDATE_TOTAL_ON_CURRENT_REPORT]: (state, {
      total,
    }) => {
      state.currentReport.total = total;
      state.currentReport.totalReady = true;
    },
    [UPDATE_SUMMARY_ON_CURRENT_REPORT]: (state, {
      summary,
    }) => {
      state.currentReport.summary = summary;
      state.currentReport.summaryReady = true;
    },
  },
  actions: {
    resetCurrentReport({
      commit,
    }) {
      commit(RESET_CURRENT_REPORT_TO_EMPTY);
    },
    //change page, will refresh report
    updateCurrentPage({
      commit,
      state,
      dispatch,
    }, {
      currentPage,
    }) {
      if (state.currentReport.limitBy.currentPage !== currentPage) {
        commit(UPDATE_CURRENT_PAGE, {
          currentPage: currentPage,
        });
        //call query to refresh report
        dispatch('remoteQuerySuperReport', {
          reportType: state.currentReportType,
          searchBy: null,
          orderBy: null,
        });
      } else {
        Vue.console.log('nothing change');
      }
    },
    //update size per page, will refresh report
    updateSizePerPage({
      commit,
      state,
      dispatch,
    }, {
      sizePerPage,
    }) {
      if (state.currentReport.limitBy.sizePerPage !== sizePerPage) {
        Vue.console.log('update size per page', sizePerPage);
        commit(UPDATE_SIZE_PER_PAGE, {
          sizePerPage: sizePerPage,
        });
        //call query to refresh report
        dispatch('remoteQuerySuperReport', {
          reportType: state.currentReportType,
          searchBy: null,
          orderBy: null,
        });
      } else {
        Vue.console.log('nothing change');
      }
    },
    updateOrderBy({
      commit,
      state,
      dispatch,
    }, {
      orderAlias,
      direction,
    }) {
      if (state.currentReport.orderBy.orderAlias !== orderAlias || state.currentReport.orderBy.direction !== direction) {
        const orderBy = {
          orderAlias: orderAlias,
          direction: direction,
        };
        commit(SET_ORDER_BY, {
          orderBy: orderBy,
        });
        //call query to refresh report
        dispatch('remoteQuerySuperReport', {
          reportType: state.currentReportType,
          searchBy: null,
          orderBy: orderBy,
        });
      } else {
        Vue.console.log('nothing change');
      }
    },
    remoteQuerySuperReport({
      commit,
      getters,
      rootState,
      dispatch,
    }, {
      reportType,
      searchBy,
      orderBy,
    }) {
      if (searchBy !== null) {
        commit(SET_SEARCH_BY, {
          searchBy: searchBy,
        });
      }
      if (orderBy !== null) {
        commit(SET_ORDER_BY, {
          orderBy: orderBy,
        });
      }
      //update current report type
      commit(UPDATE_CURRENT_REPORT_TYPE, {
        currentReportType: reportType,
      });
      return new Promise((resolve, reject) => {
        // Vue.console.log(rootState.app.constants.queryReportType);
        querySuperReport(
          reportType,
          getters.conditions,
          rootState.app.constants.queryReportType.onePage
        )
          .then(data => {
            // Vue.console.log(data);
            // data.result.showSubTotal = rootState.app.runtime.showSubTotal;
            commit(UPDATE_ONE_PAGE_CURRENT_REPORT, data.result);
            if (rootState.app.runtime.debugReportMode) {
              Vue.console.log('one page sql', data.result.sql);
            }
            // Vue.console.log('call remoteQuerySuperReport');
            dispatch('remoteQueryCountOfSuperReport', {
              reportType: reportType,
            });
            resolve();
          })
          .catch(error => {
            reject(error);
          });
      });
    },
    remoteQueryCountOfSuperReport({
      commit,
      getters,
      rootState,
      dispatch,
    }, {
      reportType,
    }) {
      return new Promise((resolve, reject) => {
        querySuperReport(
          reportType,
          getters.conditions,
          rootState.app.constants.queryReportType.count
        )
          .then(data => {
            // Vue.console.log(data);
            commit(UPDATE_COUNT_ON_CURRENT_REPORT, data.result);
            if (rootState.app.runtime.debugReportMode) {
              Vue.console.log('count sql', data.result.sql);
            }
            dispatch('remoteQueryTotalOfSuperReport', {
              reportType: reportType,
            });
            resolve();
          })
          .catch(error => {
            reject(error);
          });
      });
    },
    remoteQueryTotalOfSuperReport({
      commit,
      getters,
      rootState,
      dispatch,
    }, {
      reportType,
    }) {
      return new Promise((resolve, reject) => {
        querySuperReport(
          reportType,
          getters.conditions,
          rootState.app.constants.queryReportType.total
        )
          .then(data => {
            // Vue.console.log(data);
            commit(UPDATE_TOTAL_ON_CURRENT_REPORT, data.result);
            if (rootState.app.runtime.debugReportMode) {
              Vue.console.log('total sql', data.result.sql);
            }
            dispatch('remoteQuerySummaryOfSuperReport', {
              reportType: reportType,
            });
            resolve();
          })
          .catch(error => {
            reject(error);
          });
      });
    },
    remoteQuerySummaryOfSuperReport({
      commit,
      getters,
      rootState,
    }, {
      reportType,
    }) {
      return new Promise((resolve, reject) => {
        querySuperReport(
          reportType,
          getters.conditions,
          rootState.app.constants.queryReportType.summary
        )
          .then(data => {
            // Vue.console.log(data);
            commit(UPDATE_SUMMARY_ON_CURRENT_REPORT, data.result);
            if (rootState.app.runtime.debugReportMode) {
              Vue.console.log('summary sql', data.result.sql);
            }
            resolve();
          })
          .catch(error => {
            reject(error);
          });
      });
    },
    remoteExportSuperReport({
      commit,
      getters,
      rootState,
    }, {
      reportType,
      searchBy,
    }) {
      if (searchBy !== null) {
        commit(SET_SEARCH_BY, {
          searchBy: searchBy,
        });
      }
      //call /(report name)/export
      return new Promise((resolve, reject) => {
        querySuperReport(
          reportType,
          getters.conditions,
          rootState.app.constants.queryReportType.export
        )
          .then(data => {
            // Vue.console.log(data);
            //get export data, go to queue page
            window.location.href = data.result.result_url;
            resolve();
          })
          .catch(error => {
            reject(error);
          });
      });
    },
  },
};

export default superReport;