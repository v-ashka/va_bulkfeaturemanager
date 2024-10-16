import Vue from 'vue'
import App from './App.vue'

Vue.config.productionTip = false

new Vue({
  render: h => h(App),
}).$mount('#app')

// const { yourModule } = window;
// const { keyA, keyB } = yourModule;
// //
// // export default {
// //   state: {
// //     keyA,
// //     keyB,
// //     yourModule
// //   },
// //   getters: {
// //     keyA: (state) => state.keyA,
// //     keyB: (state) => state.keyB,
// //     yourModule: (state) => state.yourModule,
// //   }
// // }

