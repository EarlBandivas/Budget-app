<script setup>
defineProps({
  value: {
    type: String,
  },
});
</script>

<template>
  <label class="text-black-700 block text-sm font-medium">
    <span v-if="value">{{ value }}</span>
    <span v-else><slot /></span>
  </label>
</template>
