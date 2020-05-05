import Vue from 'vue';
import Element from 'element-ui';
// import 'element-ui/lib/theme-chalk/index.css';
import './element-variables.scss';

import i18n from '@/i18n';
// import locale from 'element-ui/lib/locale/lang/en';

// Vue.use(Element, { locale, });
Vue.use(Element, {
  size: 'small',
  i18n: (key, value) => i18n.t(key, value),
});