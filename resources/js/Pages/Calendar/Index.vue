<template>
  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">
        Calendar
      </h2>
    </template>

    <div class="py-12">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
          <div class="p-6">
            <div
              v-if="error"
              class="mb-4 rounded-lg bg-red-100 p-4 text-red-700"
            >
              {{ error }}
            </div>

            <FullCalendar
              ref="fullCalendar"
              :options="calendarOptions"
              class="calendar-container"
            />
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import FullCalendar from '@fullcalendar/vue3';
import googleCalendarPlugin from '@fullcalendar/google-calendar';
import dayGridPlugin from '@fullcalendar/daygrid';

const error = ref(null);

const calendarOptions = {
  plugins: [dayGridPlugin, googleCalendarPlugin],
  initialView: 'dayGridMonth',
  headerToolbar: {
    left: 'prev,next today',
    center: 'title',
    right: 'dayGridMonth,dayGridWeek,dayGridDay',
  },
  googleCalendarApiKey: import.meta.env.VITE_GOOGLE_CALENDAR_API_KEY,
  events: {
    googleCalendarId: import.meta.env.VITE_GOOGLE_CALENDAR_ID,
    failure: function (error) {
      console.error('Error loading events:', error);
      error.value =
        'Failed to load calendar events. Please check your configuration.';
    },
  },
  eventClick: function (info) {
    info.jsEvent.preventDefault();
    if (info.event.url) {
      window.open(info.event.url);
    }
  },
  height: 'auto',
  firstDay: 1, // Start week on Monday
  displayEventTime: true,
  displayEventEnd: true,
  slotMinTime: '00:00:00',
  slotMaxTime: '24:00:00',
};
</script>

<style>
.calendar-container {
  margin-top: 1rem;
  min-height: 700px;
}

/* Calendar specific styles */
:root {
  --fc-border-color: #ddd;
  --fc-button-bg-color: #2c3e50;
  --fc-button-border-color: #2c3e50;
  --fc-button-hover-bg-color: #1a252f;
  --fc-button-hover-border-color: #1a252f;
  --fc-button-active-bg-color: #1a252f;
  --fc-today-bg-color: rgba(44, 62, 80, 0.1);
  --fc-page-bg-color: #ffffff;
}

.fc-header-toolbar {
  margin-bottom: 1.5em !important;
  padding: 0.5em !important;
}

.fc-view-harness {
  background-color: var(--fc-page-bg-color);
}

.fc-scrollgrid {
  border: 1px solid var(--fc-border-color);
}

.fc-day {
  cursor: pointer;
}

.fc-event {
  cursor: pointer;
  padding: 2px 4px;
}

/* Responsive adjustments */
@media (max-width: 640px) {
  .fc-header-toolbar {
    flex-direction: column;
    gap: 1rem;
  }

  .fc-toolbar-chunk {
    display: flex;
    justify-content: center;
  }
}
</style>
