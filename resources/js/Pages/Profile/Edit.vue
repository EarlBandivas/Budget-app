<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
  mustVerifyEmail: {
    type: Boolean,
  },
  status: {
    type: String,
  },
});
</script>

<template>
  <Head title="Profile" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-2xl font-light">Profile Settings</h2>
    </template>

    <div class="py-6">
      <div class="mx-auto max-w-3xl">
        <!-- Profile Information -->
        <div class="card bg-base-100 shadow-lg">
          <div class="card-body">
            <UpdateProfileInformationForm
              :must-verify-email="mustVerifyEmail"
              :status="status"
            />
          </div>
        </div>

        <!-- Password Update -->
        <div class="card mt-6 bg-base-100 shadow-lg">
          <div class="card-body">
            <UpdatePasswordForm />
          </div>
        </div>

        <!-- Delete Account -->
        <div class="card mt-6 bg-base-100 shadow-lg">
          <div class="card-body">
            <DeleteUserForm />
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
