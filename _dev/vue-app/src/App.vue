<template>
  <TopMenu/>
  <HelloWorld msg="hi"></HelloWorld>
</template>

<script>
// import HelloWorld from './components/HelloWorld.vue'
import HelloWorld from "@/components/HelloWorld.vue";
import TopMenu from "@/components/header/menu/TopMenu.vue";

const { yourModule } = window;
console.log(yourModule.columns);
export default {
  name: 'App',
  components: {TopMenu, HelloWorld},
  state: {
    yourModule
  },
}
</script>

<style>
</style>
