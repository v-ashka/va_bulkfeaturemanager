import { ref, onMounted, watch } from 'vue';
import { fetchData } from '../api.js';

export function useProducts(url) {
  const products = ref([]);
  const error = ref(null);
  const loading = ref(true);

  const loadProducts = async (fetchUrl) => {
    try {
      const data = await fetchData(fetchUrl);
      // console.log(data)
      products.value = data
    } catch (err) {
      error.value = err.message;
    } finally {
      loading.value = false;
    }
  };

  onMounted(() => {
    if (url) {
      loadProducts(url);
    }
  });

  watch(
    () => url,
    (newUrl) => {
      loading.value = true;
      loadProducts(newUrl);
    }
  );

  return {
    products,
    error,
    loading,
  };
}
