<template>
  <div class="select-button flex flex-row items-baseline gap-2 relative w-56 px-4" style="height: 50px">
    <label for="underline_select" class="absolute top-0 z-0 left-2 m-0 py-2">{{ label }}</label>
    <select
      id="underline_select"
      v-model="internalValue"
      @change="onChange"
      class="bg-slate-300/10 border-2 rounded-lg py-2 w-full absolute right-0 ps-32"
    >
      <option v-for="limit in options" :key="limit" :value="limit" class="text-left">
        {{ limit }}
      </option>
    </select>
  </div>
</template>

<script>
import { computed, defineComponent } from "vue";

export default defineComponent({
  name: "SelectButton",
  props: {
    modelValue: {
      type: Number,
      required: true,
    },
    options: {
      type: Array,
      required: true,
    },
    label: {
      type: String,
      default: "Set product limit:",
    },
  },
  emits: ["update:modelValue", "change"],
  setup(props, { emit }) {
    // Obsługa v-model
    const internalValue = computed({
      get: () => props.modelValue,
      set: (value) => emit("update:modelValue", value),
    });

    // Obsługa zmiany
    const onChange = () => {
      emit("change", internalValue.value);
    };

    return {
      internalValue,
      onChange,
    };
  },
});
</script>
