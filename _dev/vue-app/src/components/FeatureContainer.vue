<template>
  <div class="container-fluid bg-container min-h-screen">
    <div class="row gap-4">
      <div class="col flex gap-4 flex-wrap">
        <ActionButton
          label="Add feature"
          icon="add"
          @click="() => {}"
          class="max-sm:grow"
        />
        <ActionButton
          label="Remove feature"
          icon="delete"
          @click="() => {}"
        />
        <ActionButton
          label="Filter"
          icon="filter_alt"
          @click="() => {}"
          class="max-sm:grow"
        />

        <div class="v-dropdown">
          <button class="dropdown-toggle grow px-2 py-1 rounded-lg border-2 border-slate-400/30 bg-slate-200/10 flex flex-wrap gap-2 hover:bg-slate-200/50 hover:border-slate-400/50 ease-in duration-100 active:bg-slate-200 active:border-slate-400 focus:border-slate-400 items-center" type="button" data-toggle="dropdown" aria-expanded="false">
            <i class="material-icons">settings</i> Import/Export
          </button>
          <div class="dropdown-menu text-black rounded-lg border-2 border-slate-400/30 bg-slate-100">
            <a class="dropdown-item" href="#">Import files</a>
            <a class="dropdown-item" href="#">Export</a>
          </div>
        </div>
      </div>
      <div class="col-12 flex gap-4 flex-wrap">
        <SearchForm
          label="Search"
          icon="search"
          placeholder="Search for products..."
          @click="onSearchClick"
          @change="onSearchChange"
        />
        <SelectButton
          :modelValue="selectedLimit"
          :options="availableLimits"
          label="Set product limit:"
          @update:modelValue="(value) => (selectedLimit = value)"
          @change="changeLimit"
        />
      </div>



    </div>

    <div v-if="productsLoading" class="flex justify-center py-48 h-fit">
      <div class="spinner spinner-primary">
      </div>
    </div>
    <div v-else-if="productsError">Error loading products: {{ productsError }}</div>
    <div v-else class="py-6">
      <table class="table">
        <thead>
        <tr>
          <th>#</th>
          <th>Product name</th>
          <th>Feature name</th>
          <th>Feature category</th>
          <th>Feature value</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="(product) in products" :key="product.id">
          <td>{{ product.id_product  }}</td>
          <td>{{ product.name }}</td>
          <td>{{ product.category }}</td>
          <td>{{ product.feature_id_name }}</td>
          <td>{{ product.feature_value }}</td>
        </tr>
        </tbody>
      </table>
    </div>

<!--    <div class="post">-->
<!--      <div v-if="loading" class="loading">Loading...</div>-->

<!--      <div v-if="error" class="error">{{ error }}</div>-->
<!--<div class="d-flex">-->
<!--  <div v-if="features" class="content">-->
<!--    <ul v-for="(item, index) in features" :key="index">-->
<!--      <li>{{ index }}: {{ item }}</li>-->
<!--    </ul>-->
<!--  </div>-->


<!--  <div v-if="featureValues" class="content">-->
<!--    <ul v-for="(item, index) in featureValues" :key="index">-->
<!--      <li>{{ index }}: {{ item }}</li>-->
<!--    </ul>-->
<!--  </div>-->
<!--</div>-->
<!--    </div>-->
  </div>
</template>

<script>
import { useFeatures } from '@/api/composables/useFeatures';
import { useProducts } from '@/api/composables/useProducts';
import {computed, ref} from 'vue';
import SelectButton from "./utils/SelectButton.vue";
import ActionButton from "./utils/ActionButton.vue";
import SearchForm from "@/components/utils/SearchForm.vue";
export default {
  name: 'FeatureContainer',
  components: {SearchForm, ActionButton, SelectButton},
  props: {
    getFeatures: {
      type: String,
      required: true
    },
    getProductsList: {
      type: String,
      required: true
    }
  },

  setup(props) {
    const availableLimits = [10,20,50,100];
    const selectedLimit = ref(10);
    const defaultValue = 0;
    console.log('before init url')
    console.log(props.getProductsList)
    let initialUrl = props.getProductsList.replace(defaultValue, selectedLimit.value);

    // variables for features and his values
    const {features, featureValues, error: featuresError, loading: featuresLoading } = useFeatures(props.getFeatures)

    const {products, error: productsError, loading: productsLoading, loadProducts } = useProducts(initialUrl)


    const changeLimit = () => {
      console.log('props')
      console.log(props.getProductsList, selectedLimit.value)
      const newUrl = props.getProductsList.replace(defaultValue, selectedLimit.value)
      console.log('new url')
      console.log(newUrl)
      loadProducts(newUrl)

    }

    const searchQuery = ref("");

    const onSearchClick = () => {
      console.log("Search clicked!");
      // Możesz tutaj dodać logikę wyszukiwania, np. filtrowanie produktów
    };

    const onSearchChange = (value) => {
      searchQuery.value = value;
      console.log("Search query:", value);
      // Możesz dodać logikę aktualizowania wyników na podstawie query
    };

    const filteredProducts = computed(() => {
      if (!searchQuery.value) {
        return products;
      }
      return products.filter((product) =>
        product.name.toLowerCase().includes(searchQuery.value.toLowerCase())
      );
    });

    return{
    // features
      features,
      featureValues,
      featuresError,
      featuresLoading,
    //   products
      products,
      productsError,
      productsLoading,
      changeLimit,
      availableLimits,
      selectedLimit,
      onSearchClick,
      onSearchChange,
      searchQuery,
      filteredProducts
    }

  },
};
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style>

:root{
  --clr-primary: #42b983;
  --r-1: 0.1rem;
  --r-2: 0.125rem;
  --r-3: 0.150rem;
  --r-4: 0.250rem;

  --p-1: 0.25rem;
  --p-2: 0.5rem;
}
a {
  color: var(--clr-primary);
}

.bg-container{
  background-color: #fcfcff;
  border: 1px solid #fafbfc;

  .table, thead, th{
    border-bottom-color: var(--clr-primary);
  }

  .v-btn{
    background-color: var(--clr-primary);
    color: white;
  }
}
</style>
