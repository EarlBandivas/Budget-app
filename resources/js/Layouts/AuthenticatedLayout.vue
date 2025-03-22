<script setup>
import { Head } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import NotificationsDropdown from '@/Components/NotificationsDropdown.vue';
import { Link, usePage } from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);

// Check if user is admin by email
const isAdmin = computed(() => {
  const user = usePage().props.auth.user;
  return user.email.toLowerCase().includes('admin');
});
</script>

<template>
  <div class="min-h-screen bg-gray-100">
    <!-- Sidebar -->
    <div class="drawer lg:drawer-open">
      <input id="my-drawer-2" type="checkbox" class="drawer-toggle" />
      <div class="drawer-content flex flex-col">
        <!-- Navbar -->
        <div class="navbar w-full bg-base-100 shadow-sm">
          <div class="flex-none lg:hidden">
            <label for="my-drawer-2" class="btn btn-ghost btn-square">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                class="inline-block h-6 w-6 stroke-current"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M4 6h16M4 12h16M4 18h16"
                ></path>
              </svg>
            </label>
          </div>
          <div class="mx-2 flex-1 px-2">BudgetDU</div>
          <div class="flex flex-row">
            <NotificationsDropdown v-if="!isAdmin" class="mr-4" />
            <div class="dropdown dropdown-end">
              <label tabindex="0" class="btn btn-ghost btn-circle avatar">
                <div class="w-10 rounded-full">
                  <img src="https://ui-avatars.com/api/?name=User" />
                </div>
              </label>
              <ul
                tabindex="0"
                class="dropdown-content menu menu-sm z-[1] mt-3 w-52 rounded-box bg-base-100 p-2 shadow"
              >
                <li>
                  <Link :href="route('profile.edit')" class="justify-between"
                    >Profile</Link
                  >
                </li>
                <li>
                  <Link :href="route('logout')" method="post" as="button"
                    >Logout</Link
                  >
                </li>
              </ul>
            </div>
          </div>
        </div>
        <!-- Page Content -->
        <main class="flex-grow">
          <slot />
        </main>
      </div>

      <!-- Sidebar -->
      <div class="drawer-side">
        <label for="my-drawer-2" class="drawer-overlay"></label>
        <div class="min-h-full w-80 bg-base-100 p-4 text-base-content">
          <!-- Logo and User Info -->
          <div class="mb-6 flex flex-col items-center space-y-2">
            <ApplicationLogo class="h-20 w-20" />
            <div class="text-center">
              <h2 class="text-xl font-semibold">
                {{ $page.props.auth.user.name }}
              </h2>
              <p class="text-base-content/70 text-sm">
                {{ $page.props.auth.user.email }}
              </p>
            </div>
          </div>

          <div class="divider"></div>

          <!-- Admin Menu -->
          <ul v-if="isAdmin" class="menu rounded-box">
            <li>
              <Link
                :href="route('admin.dashboard')"
                class="btn btn-ghost justify-start gap-3 normal-case"
                :class="{ 'btn-active': route().current('admin.dashboard') }"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"
                  />
                </svg>
                Reports
              </Link>
            </li>
            <!-- <li class="mt-2">
              <Link
                :href="route('admin.users')"
                class="btn btn-ghost justify-start gap-3 normal-case"
                :class="{ 'btn-active': route().current('admin.users') }"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"
                  />
                </svg>
                Users
              </Link>
            </li> -->
            <!-- <li class="mt-2">
              <Link
                :href="route('admin.statistics')"
                class="btn btn-ghost justify-start gap-3 normal-case"
                :class="{ 'btn-active': route().current('admin.statistics') }"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
                  />
                </svg>
                Statistics
              </Link>
            </li> -->
          </ul>

          <!-- User Menu -->
          <ul v-else class="menu rounded-box">
            <li>
              <Link
                :href="route('dashboard')"
                class="btn btn-ghost justify-start gap-3 normal-case"
                :class="{ 'btn-active': route().current('dashboard') }"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"
                  />
                </svg>
                Dashboard
              </Link>
            </li>
            <li class="mt-2">
              <Link
                :href="route('expenses.index')"
                class="btn btn-ghost w-full justify-start gap-3 normal-case"
                :class="{ 'btn-active': route().current('expenses.index') }"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                  />
                </svg>
                Expenses
              </Link>
            </li>
            <li class="mt-2">
              <Link
                :href="route('calendar.index')"
                class="btn btn-ghost justify-start gap-3 normal-case"
                :class="{ 'btn-active': route().current('calendar.index') }"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                  />
                </svg>
                Calendar
              </Link>
            </li>
          </ul>

          <div class="divider mt-auto"></div>

          <!-- Footer Links -->
          <div class="mt-2">
            <Link
              :href="route('profile.edit')"
              class="btn btn-ghost btn-sm w-full justify-start normal-case"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="mr-2 h-5 w-5"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                />
              </svg>
              Profile Settings
            </Link>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
