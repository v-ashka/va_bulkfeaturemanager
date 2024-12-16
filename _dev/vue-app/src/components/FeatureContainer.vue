<template>
  <div class="container-fluid bg-container">
    <div class="row">
      <div class="col">
        <button class="v-btn"><i class="material-icons">add</i>Add feature</button>
      </div>
      <div class="col">
        <button class="v-btn"><i class="material-icons">delete</i>Remove feature</button>
      </div>
      <div class="col">
        <form class="form-inline">
          <div class="input-group">
            <input type="text" class="form-control" placeholder="Search in products..." aria-label="Search in products..." aria-describedby="basic-addon1">
            <div class="input-group-prepend">
              <button class="input-group-text" id="basic-addon1">Search</button>
            </div>
          </div>
        </form>
      </div>
      <div class="col">
        <button class="v-btn"><i class="material-icons">filter_alt</i>Filter</button>
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
    </div>

    <div v-if="productsLoading">Loading table products...</div>
    <div v-else-if="productsError">Error loading products: {{ productsError }}</div>
    <div v-else>
      <h3>Products</h3>
      <table class="table">
        <thead>
        <tr>
          <th>#</th>
          <th>Product name</th>
          <th>Feature name</th>
          <th>Feature value</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="(product) in products" :key="product.id">
          <td>{{ product.id_product  }}</td>
          <td>{{ product.name }}</td>
          <td>{{ product.feature_id_name }}</td>
          <td>{{ product.feature_value }}</td>
        </tr>
        </tbody>
      </table>
    </div>

    <div class="post">
      <div v-if="loading" class="loading">Loading...</div>

      <div v-if="error" class="error">{{ error }}</div>
<div class="d-flex">
  <div v-if="features" class="content">
    <ul v-for="(item, index) in features" :key="index">
      <li>{{ index }}: {{ item }}</li>
    </ul>
  </div>


  <div v-if="featureValues" class="content">
    <ul v-for="(item, index) in featureValues" :key="index">
      <li>{{ index }}: {{ item }}</li>
    </ul>
  </div>
</div>
    </div>
  </div>
</template>

<script>
import { useFeatures } from '../api/composables/useFeatures';
import { useProducts } from '../api/composables/useProducts';
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
    // variables for features and his values
    const {features, featureValues, error: featuresError, loading: featuresLoading } = useFeatures(props.getFeatures)

    const {products, error: productsError, loading: productsLoading } = useProducts(props.getProductsList)

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
      productsLoading
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
    border: none;
    background-color: var(--clr-primary);
    color: white;
    border-radius: var(--r-4);
    padding: var(--p-2);
  }
}
</style>
