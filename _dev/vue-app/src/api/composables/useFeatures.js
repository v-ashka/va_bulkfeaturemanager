import { ref, onMounted, watch } from 'vue';
import { fetchData } from '../api.js';

export function useFeatures(url) {
  const features = ref([]);
  const featureValues = ref([]);
  const error = ref(null);
  const loading = ref(true);

  const loadFeatures = async (fetchUrl) => {
    try {
      const data = await fetchData(fetchUrl);
      features.value = data.features;
      featureValues.value = data.featureValues;
    } catch (err) {
      error.value = err.message;
    } finally {
      loading.value = false;
    }
  };

  onMounted(() => {
    if (url) {
      loadFeatures(url);
    }
  });

  watch(
    () => url,
    (newUrl) => {
      loading.value = true;
      loadFeatures(newUrl);
    }
  );

  return {
    features,
    featureValues,
    error,
    loading,
  };
}
