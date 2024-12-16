const path = require('path');
const { defineConfig } = require('@vue/cli-service')
const fs = require('fs');

// Funkcja do usuwania plików w katalogu
function cleanOutputDir(directory) {
  if (fs.existsSync(directory)) {
    fs.readdirSync(directory).forEach((file) => {
      const filePath = path.join(directory, file);
      if (fs.lstatSync(filePath).isDirectory()) {
        cleanOutputDir(filePath); // Rekurencyjnie usuwaj podfoldery
        fs.rmdirSync(filePath);
      } else {
        fs.unlinkSync(filePath); // Usuwaj pliki
      }
    });
  }
}


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
  publicPath: '../views/vue',
  configureWebpack: (config) => {
    const outputDirPath = path.resolve(__dirname, '../../views/vue');
    cleanOutputDir(outputDirPath); // Czyszczenie katalogu przed budowaniem
  },
})
