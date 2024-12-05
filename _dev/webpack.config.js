/**
 * 2007-2020 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0).
 * It is also available through the world-wide-web at this URL: https://opensource.org/licenses/AFL-3.0
 */

const webpack = require('webpack');
const path = require('path');
// const BundleAnalyzerPlugin = require('webpack-bundle-analyzer').BundleAnalyzerPlugin;
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const FileManagerPlugin = require('filemanager-webpack-plugin');

const config = {
  externals: {
    jquery: 'jQuery',
  },
  entry: {
    'va_bulkfeaturemanager.import': './import',
    'va_bulkfeaturemanager.utils': './utils/utils.js'
  },
  output: {
    path: path.resolve(__dirname, 'dist'),
    filename: '[name].bundle.js',
    chunkFilename: '[name].[hash].js',
    // clean: true,
  },
  resolve: {
    extensions: ['.ts', '.js', '.vue', '.json'], // Upewnij się, że kolejność jest właściwa
    alias: {
      '@app': path.resolve(__dirname, '../../../admin-dev/themes/new-theme/js/app'),
      '@js': path.resolve(__dirname, '../../../admin-dev/themes/new-theme/js'),
      '@pages': path.resolve(__dirname, '../../../admin-dev/themes/new-theme/js/pages'),
      '@components': path.resolve(__dirname, '../../../admin-dev/themes/new-theme/js/components'),
      '@PSVue': path.resolve(__dirname, '../../../admin-dev/themes/new-theme/js/vue'),
      '@PSTypes': path.resolve(__dirname, '../../../admin-dev/themes/new-theme/js/types'),
    },
  },
  module: {
    rules: [
      {
        test: /\.ts$/,
        include: [
          path.resolve(__dirname, '../views/js'),
          path.resolve(__dirname, '../../../admin-dev/themes/new-theme/js/pages'),
        ],
        loader: 'esbuild-loader',
        options: {
          loader: 'ts',
          target: 'es2015',
        },
        exclude: /node_modules/,
      },
      {
        test: /\.js$/,
        include: [
          path.resolve(__dirname, '../views/js'),
          path.resolve(__dirname, '../../../admin-dev/themes/new-theme/js/pages'),
        ],
        loader: 'esbuild-loader',
        options: {
          loader: 'js',
          target: 'es2015',
        },
        exclude: /node_modules/,
      },
      {
        test: /\.css$/,
        use: [
          MiniCssExtractPlugin.loader,
          'css-loader'
        ]
      },
      {
        test: /\.scss$/,
        use: [
          MiniCssExtractPlugin.loader,
          'css-loader',
          'sass-loader'
        ]
      }
    ]
  },
  plugins: [
    new MiniCssExtractPlugin({
      filename: '[name].min.css'
    }),
    new FileManagerPlugin({
      events: {
        onStart: {
          delete: [
            { source: '../views/js/*.*', options: { force: true } },
            { source: '../views/css/*.*', options: { force: true } }
          ]
        },
        onEnd: {
          copy: [
            { source: './dist/*.js', destination: '../views/js' },
            { source: './dist/*.css', destination: '../views/css' },
          ]
        }
      }
    })
  ]
};

module.exports = (env, argv) => {
  if (argv.mode === 'development') {
    config.devtool = 'eval-source-map';
  }

  if (argv.mode === 'production') {
    config.optimization = {
      minimize: true,
      splitChunks: {
        cacheGroups: {
          vendor: {
            test: /[\\/]node_modules[\\/]/,
            name: 'vendors',
            filename: "vendor.bundle.js",
            chunks: 'all'
          }
        }
      }
    }
  }

  return config;
}
