<template>
  <div class="container-fluid bg-container min-h-screen">
    <div class="row ">
      <div class="col">
        <button class="v-btn"><i class="material-icons">add</i>Add feature</button>
      </div>
      <div class="col">
        <button class="v-btn"><i class="material-icons">delete</i>Remove feature</button>
      </div>

      <div class="col">
        <button class="v-btn" @click="usePro"><i class="material-icons">filter_alt</i>Filter</button>
      </div>
      <div class="col">
        <label for="underline_select" class="block mb-2 text-sm font-medium text-gray-900">Set product limit:</label>
        <select id="underline_select" v-model="selectedLimit" @change="changeLimit" class="v-btn	">
          <option
            v-for="limit in availableLimits"
            :key="limit"
            :value="limit"
            :selected="limit === selectedLimit">
            {{ limit }}
          </option>
        </select>
      </div>
      <div class="col">
        <div class="dropdown">
          <button class="v-btn dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
            <i class="material-icons">settings</i> Import/Export
          </button>
          <div class="dropdown-menu">
            <a class="dropdown-item" href="#">Import files</a>
            <a class="dropdown-item" href="#">Export</a>
          </div>
        </div>
      </div>
      <div class="col-12">
        <form class="w-100">
          <div>
            <input type="text" placeholder="Search in products..." aria-label="Search in products..." aria-describedby="basic-addon1">
            <div class="input-group-prepend">
              <button class="v-btn">Search</button>
            </div>
          </div>
        </form>
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
import { ref } from 'vue';
export default {
  name: 'FeatureContainer',
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
    console.log(products)

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
      selectedLimit
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
