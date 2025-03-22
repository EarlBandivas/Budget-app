<script setup>
import { Head, Link } from '@inertiajs/vue3';
import Footer from '@/Components/Footer.vue';
import LoginModal from '@/Pages/Auth/LoginModal.vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { ref } from 'vue';

import { onMounted } from 'vue';

// Dynamically load the reCAPTCHA script
onMounted(() => {
  const script = document.createElement('script');
  script.src = `https://www.google.com/recaptcha/api.js?render=explicit`;
  script.async = true;
  script.defer = true;
  document.head.appendChild(script);
});

defineProps({
  canLogin: Boolean,
  canRegister: Boolean,
  laravelVersion: {
    type: String,
    required: true,
  },
  phpVersion: {
    type: String,
    required: true,
  },
});

const loginModalRef = ref(null);

const openLoginModal = () => {
  if (loginModalRef.value) {
    loginModalRef.value.openModal('login'); // Explicitly pass 'login' mode
  }
};
const openRegisterModal = () => {
  if (loginModalRef.value) {
    loginModalRef.value.openModal('register');
  }
};
</script>

<template>
  <Head title="BudgetDU - Smart Budget Tracking" />
  <div class="min-h-screen bg-sky-50">
    <!-- Navigation -->
    <nav class="fixed w-full bg-white/80 backdrop-blur-md dark:bg-gray-900/80">
      <div class="mx-2 max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between">
          <div class="flex items-center">
            <ApplicationLogo class="h-10 w-auto" />
            <span class="ml-3 text-xl font-bold"
              >Bukidnon State University</span
            >
          </div>

          <div v-if="canLogin" class="flex items-center gap-4">
            <Link
              v-if="$page.props.auth.user"
              :href="route('dashboard')"
              class="rounded-full bg-gray-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-gray-700 dark:bg-white dark:text-gray-900 dark:hover:bg-gray-100"
            >
              Dashboard
            </Link>
          </div>
        </div>
      </div>
    </nav>

    <!-- Hero Section -->
    <div class="relative isolate px-6 pt-14 lg:px-8">
      <div class="mx-auto max-w-2xl py-32 sm:py-48 lg:py-56">
        <div class="text-center">
          <h1
            class="text-4xl font-bold tracking-tight text-zinc-950 sm:text-6xl"
          >
            Budgetary Receipts and Expenitures Monitoring System
          </h1>
          <p class="mt-6 text-lg leading-8 text-zinc-950 dark:text-zinc-950">
            Take control of your finances with our intuitive budget tracking
            tool. Monitor expenses, set goals, and make informed financial
            decisions.
          </p>
          <div class="mt-10 flex items-center justify-center gap-x-6">
            <button
              @click="openRegisterModal"
              class="rounded-full bg-gray-900 px-6 py-3 text-sm font-semibold text-white transition hover:bg-gray-700 dark:bg-gray-900/80 dark:text-white dark:hover:bg-gray-800"
            >
              Get Started
            </button>
            <button
              @click="openLoginModal"
              class="rounded-full bg-gray-900 px-6 py-3 text-sm font-semibold text-white transition hover:bg-gray-700 dark:bg-gray-900/80 dark:text-white dark:hover:bg-gray-800"
            >
              Sign In
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Features Section -->
    <div id="features" class="mx-auto max-w-7xl px-6 py-24 sm:py-32 lg:px-8">
      <div class="grid grid-cols-1 gap-12 sm:grid-cols-2 lg:grid-cols-3">
        <div class="flex flex-col items-start">
          <div class="rounded-lg bg-gray-100 p-3 dark:bg-gray-800">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-6 w-6"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"
              />
            </svg>
          </div>
          <h3
            class="mt-4 text-lg font-semibold text-gray-900 dark:text-zinc-950"
          >
            Easy Tracking
          </h3>
          <p class="mt-2 dark:text-zinc-900">
            Record your expenses quickly and efficiently with our user-friendly
            interface.
          </p>
        </div>

        <div class="flex flex-col items-start">
          <div class="rounded-lg bg-gray-100 p-3 dark:bg-gray-800">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-6 w-6"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
              />
            </svg>
          </div>
          <h3 class="mt-4 text-lg font-semibold dark:text-zinc-950">
            Insightful Analytics
          </h3>
          <p class="mt-2 dark:text-zinc-900">
            Visualize your spending patterns with detailed charts and reports.
          </p>
        </div>

        <div class="flex flex-col items-start">
          <div class="rounded-lg bg-gray-100 p-3 dark:bg-gray-800">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-6 w-6"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M13 10V3L4 14h7v7l9-11h-7z"
              />
            </svg>
          </div>
          <h3
            class="mt-4 text-lg font-semibold text-gray-900 dark:text-zinc-950"
          >
            Smart Goals
          </h3>
          <p class="mt-2 text-gray-600 dark:text-zinc-900">
            Set and achieve your financial goals with our smart tracking system.
          </p>
        </div>
      </div>
    </div>

    <Footer />
    <LoginModal ref="loginModalRef" />
  </div>
</template>
