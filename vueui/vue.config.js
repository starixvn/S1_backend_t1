const path = require('path');

module.exports = {
  publicPath: '',
  outputDir: path.resolve(__dirname, '../admin/public/resources/vue/live'),
  filenameHashing: false,

  devServer: {
    port: 10088,
    disableHostCheck: true,
    host: '0.0.0.0',
    hot: true,
  },

  pluginOptions: {
    i18n: {
      locale: 'en',
      fallbackLocale: 'en',
      localeDir: 'locales',
      enableInSFC: false,
    },

    transpileDependencies: [
      /\bvue-awesome\b/,
    ],
  },
};