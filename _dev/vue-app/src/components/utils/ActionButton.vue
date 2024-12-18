<template>
  <button
    class="px-2 py-1 rounded-lg border-2 border-slate-400/30 bg-slate-200/10 flex flex-wrap gap-2 hover:bg-slate-200/50 hover:border-slate-400/50 ease-in duration-100 active:bg-slate-200 active:border-slate-400 focus:border-slate-400 items-center"
    @click="onClick"
  >
    <i class="material-icons">{{ icon }}</i>
    {{ label }}
  </button>
</template>

<script>
import { defineComponent } from "vue";

export default defineComponent({
  name: "ActionButton",
  props: {
    label: {
      type: String,
      required: true,
    },
    icon: {
      type: String,
      required: true,
    },
  },
  emits: ["click"],
  setup(_, { emit }) {
    const onClick = () => {
      emit("click");
    };

    return {
      onClick,
    };
  },
});
</script>

