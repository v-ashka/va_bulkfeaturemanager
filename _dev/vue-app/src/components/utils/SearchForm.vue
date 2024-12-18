<template>
  <div class="flex flex-row rounded-lg border-2 border-slate-400/30 bg-slate-100">
    <input
      type="text"
      :placeholder="placeholder"
      :aria-label="label"
      @input="handleChange"
      class="px-2 py-2"
    />
    <div class="input-group-prepend">
      <button
        @click="handleClick"
        class="rounded-lg border-0 py-2 px-2 flex flex-wrap align-items-center gap-1 hover:bg-slate-200/50 hover:border-slate-400/50 ease-in duration-100 active:bg-slate-200 active:border-slate-400 focus:border-slate-400"
      >
        <i class="material-icons">{{ icon }}</i>
        <span class="hidden md:block">{{ label }}</span>
      </button>
    </div>
  </div>
</template>

<script>
import { defineComponent } from "vue";

export default defineComponent({
  name: "SearchForm",
  props: {
    label: {
      type: String,
      required: true,
    },
    icon: {
      type: String,
      default: "search",
    },
    placeholder: {
      type: String,
      default: "Search...",
    },
  },
  emits: ["click", "change"],
  setup(props, {emit}) {
    const handleClick = () => {
      emit("click");
    };

    const handleChange = (event) => {
      emit("change", event.target.value);
    };

    return {
      handleClick,
      handleChange,
    };
  },
});
</script>

