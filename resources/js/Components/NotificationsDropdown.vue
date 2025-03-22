<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';

const notifications = ref([]);
const unreadCount = ref(0);
const isOpen = ref(false);

const fetchNotifications = async () => {
  try {
    const response = await axios.get('/notifications');
    notifications.value = response.data.notifications;
    unreadCount.value = response.data.unreadCount;
  } catch (error) {
    console.error('Failed to fetch notifications:', error);
  }
};

const markAsRead = async (notificationId) => {
  try {
    await axios.post(`/notifications/${notificationId}/mark-as-read`);
    await fetchNotifications();
  } catch (error) {
    console.error('Failed to mark notification as read:', error);
  }
};

const markAllAsRead = async () => {
  try {
    await axios.post('/notifications/mark-all-as-read');
    await fetchNotifications();
  } catch (error) {
    console.error('Failed to mark all notifications as read:', error);
  }
};

// Close dropdown when clicking outside
const dropdownRef = ref(null);
const handleClickOutside = (event) => {
  if (dropdownRef.value && !dropdownRef.value.contains(event.target)) {
    isOpen.value = false;
  }
};

onMounted(() => {
  fetchNotifications();
  setInterval(fetchNotifications, 30000);
  document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside);
});
</script>

<template>
  <div class="relative" ref="dropdownRef">
    <button
      @click.stop="isOpen = !isOpen"
      class="btn btn-ghost btn-circle relative"
    >
      <span class="sr-only">View notifications</span>
      <svg
        class="h-6 w-6"
        fill="none"
        viewBox="0 0 24 24"
        stroke="currentColor"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"
        />
      </svg>
      <span
        v-if="unreadCount > 0"
        class="absolute -right-1 -top-1 flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-xs text-white"
      >
        {{ unreadCount }}
      </span>
    </button>

    <!-- Dropdown -->
    <Transition
      enter-active-class="transition duration-100 ease-out"
      enter-from-class="transform scale-95 opacity-0"
      enter-to-class="transform scale-100 opacity-100"
      leave-active-class="transition duration-75 ease-in"
      leave-from-class="transform scale-100 opacity-100"
      leave-to-class="transform scale-95 opacity-0"
    >
      <div
        v-if="isOpen"
        class="absolute right-0 mt-2 w-80 rounded-lg bg-base-100 shadow-xl ring-1 ring-black ring-opacity-5"
        style="z-index: 9999"
      >
        <div class="p-4">
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-medium">Notifications</h3>
            <button
              v-if="unreadCount > 0"
              @click.stop="markAllAsRead"
              class="hover:text-primary-focus text-sm text-primary"
            >
              Mark all as read
            </button>
          </div>

          <div class="mt-4 divide-y divide-base-200">
            <template v-if="notifications.length > 0">
              <div
                v-for="notification in notifications"
                :key="notification.id"
                :class="['py-4', { 'bg-base-200': !notification.read_at }]"
              >
                <div class="flex items-start">
                  <div class="flex-1">
                    <p class="text-sm">
                      {{ notification.data.message }}
                    </p>
                    <p class="text-base-content/60 mt-1 text-xs">
                      {{
                        new Date(notification.created_at).toLocaleDateString()
                      }}
                    </p>
                  </div>
                  <button
                    v-if="!notification.read_at"
                    @click.stop="markAsRead(notification.id)"
                    class="hover:text-primary-focus ml-4 text-xs text-primary"
                  >
                    Mark as read
                  </button>
                </div>
              </div>
            </template>
            <div v-else class="text-base-content/60 py-4 text-center text-sm">
              No notifications
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </div>
</template>
