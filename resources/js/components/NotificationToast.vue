<template>
  <div
    v-if="visible"
    class="fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 transition-all duration-300"
    :class="notificationClass"
  >
    <div class="flex items-center">
      <span class="mr-2">{{ icon }}</span>
      <span class="flex-1">{{ message }}</span>
      <button
        class="ml-4 text-white hover:text-gray-200 focus:outline-none"
        @click="close"
      >
        &times;
      </button>
    </div>
  </div>
</template>

<script>
export default {
  name: 'NotificationToast',
  props: {
    message: {
      type: String,
      required: true,
    },
    type: {
      type: String,
      default: 'info',
      validator: (value) => ['info', 'success', 'warning', 'error'].includes(value),
    },
    duration: {
      type: Number,
      default: 5000,
    },
  },
  data() {
    return {
      visible: false,
    };
  },
  computed: {
    notificationClass() {
      const classes = {
        info: 'bg-blue-500 text-white',
        success: 'bg-green-500 text-white',
        warning: 'bg-yellow-500 text-white',
        error: 'bg-red-500 text-white',
      };
      return classes[this.type] || classes.info;
    },
    icon() {
      const icons = {
        info: 'ℹ️',
        success: '✅',
        warning: '⚠️',
        error: '❌',
      };
      return icons[this.type] || icons.info;
    },
  },
  mounted() {
    this.visible = true;
    if (this.duration > 0) {
      setTimeout(() => {
        this.close();
      }, this.duration);
    }
  },
  methods: {
    close() {
      this.visible = false;
      this.$emit('close');
    },
  },
};
</script>
