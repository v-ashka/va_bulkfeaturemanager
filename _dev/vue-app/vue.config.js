const path = require('path');
const { defineConfig } = require('@vue/cli-service')
module.exports = defineConfig({
  chainWebpack: (config) => {
    config.plugins.delete('html');
    config.plugins.delete('preload');
    config.plugins.delete('prefetch');

  //   Allow resolving images in the subfolder src/assets
    config.resolve.alias.set('@', path.resolve(__dirname, 'src'));
  },
  css: {
    extract: false,
  },
  runtimeCompiler: true,
  productionSourceMap: false,
  filenameHashing: false,
  outputDir: '../../views/vue',
  assetsDir: '',
  publicPath: '../modules/va_bulkfeaturemanager/views/vue'
  // transpileDependencies: true
})
