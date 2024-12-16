<template>
  <TopMenu/>
  <FeatureContainer :getFeatures="getFeaturesDataUrl" :getProductsList="getProductsListDataUrl"></FeatureContainer>
</template>

<script>
import FeatureContainer from './components/FeatureContainer.vue'
import TopMenu from "@/components/header/menu/TopMenu.vue";

const { getFeaturesDataUrl, getProductsListDataUrl } = window;
console.log(getFeaturesDataUrl, getProductsListDataUrl)
export default {
  name: 'App',
  components: {TopMenu, FeatureContainer},
  data() {
    return {
      getFeaturesDataUrl, // Zapisz wartość w danych komponentu
      getProductsListDataUrl
    };
  },

}
</script>

<style>
</style>
