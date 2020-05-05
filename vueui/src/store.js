import Vue from 'vue';
import Vuex from 'vuex';
import app from './store/app';
import superReport from './store/superReport';
import tagsView from './store/tagsView';
import getters from './getters';

Vue.use(Vuex);

export default new Vuex.Store({
  strict: process.env.NODE_ENV !== 'production',
  state: {},
  modules: {
    app: app,
    superReport: superReport,
    tagsView: tagsView,
  },
  mutations: {},
  actions: {},
  getters,
});