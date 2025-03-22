<script setup>
import InputError from '@/Components/InputError.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
  mustVerifyEmail: {
    type: Boolean,
  },
  status: {
    type: String,
  },
});

const user = usePage().props.auth.user;

const form = useForm({
  name: user.name,
  email: user.email,
  department: user.department || '', // Add department field
});

const departments = [
  'College of Technology',
  'Human Resources',
  'College of Business',
  'College of Education',
  'College of Arts & Sciences',
  'Research & Development',
  'College of Nursing',
  'College of Public Administration and Governance',
];

const updateProfile = () => {
  form.patch(route('profile.update'), {
    preserveScroll: true,
    onSuccess: () => {
      if (props.status) {
        setTimeout(() => (props.status = ''), 2000);
      }
    },
  });
};
</script>

<template>
  <section>
    <header class="mb-6">
      <h3 class="text-lg font-medium">Profile Information</h3>
      <p class="mt-1 text-sm text-gray-500">
        Update your account's profile information and email address.
      </p>
    </header>

    <form @submit.prevent="updateProfile" class="space-y-6">
      <!-- Name -->
      <div class="form-control w-full">
        <label class="label">
          <span class="label-text">Name</span>
        </label>
        <input
          type="text"
          v-model="form.name"
          class="input-bordered input w-full"
          required
          autofocus
        />
        <label class="label" v-if="form.errors.name">
          <span class="label-text-alt text-error">{{ form.errors.name }}</span>
        </label>
      </div>

      <!-- Email -->
      <div class="form-control w-full">
        <label class="label">
          <span class="label-text">Email</span>
        </label>
        <input
          type="email"
          v-model="form.email"
          class="input-bordered input w-full"
          required
        />
        <label class="label" v-if="form.errors.email">
          <span class="label-text-alt text-error">{{ form.errors.email }}</span>
        </label>

        <div
          v-if="mustVerifyEmail && user.email_verified_at === null"
          class="mt-2"
        >
          <p class="text-sm text-gray-500">
            Your email address is unverified.
            <Link
              :href="route('verification.send')"
              method="post"
              as="button"
              class="link-primary text-sm"
            >
              Click here to re-send verification email.
            </Link>
          </p>
        </div>
      </div>

      <!-- Department -->
      <div class="form-control w-full">
        <label class="label">
          <span class="label-text">Department</span>
        </label>
        <select
          v-model="form.department"
          class="select-bordered select w-full"
          required
        >
          <option value="">Select Department</option>
          <option v-for="dept in departments" :key="dept" :value="dept">
            {{ dept }}
          </option>
        </select>
        <label class="label" v-if="form.errors.department">
          <span class="label-text-alt text-error">{{
            form.errors.department
          }}</span>
        </label>
      </div>

      <div class="flex items-center gap-4">
        <button
          type="submit"
          class="btn btn-primary"
          :disabled="form.processing"
        >
          Save Changes
        </button>

        <transition
          enter-active-class="transition ease-in-out"
          enter-from-class="opacity-0"
          leave-active-class="transition ease-in-out"
          leave-to-class="opacity-0"
        >
          <p v-if="form.recentlySuccessful" class="text-sm text-success">
            Saved successfully.
          </p>
        </transition>
      </div>
    </form>
  </section>
</template>
