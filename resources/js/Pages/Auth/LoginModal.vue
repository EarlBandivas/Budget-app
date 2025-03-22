<script setup>
import { ref, onMounted } from 'vue';
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import GoogleButton from '@/Components/GoogleButton.vue';
import TextInput from '@/Components/TextInput.vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { useForm } from '@inertiajs/vue3';

onMounted(() => {
  // Load reCAPTCHA script
  const script = document.createElement('script');
  script.src = `https://www.google.com/recaptcha/api.js`;
  script.async = true;
  script.defer = true;
  document.head.appendChild(script);
});

const isOpen = ref(false);
const mode = ref('login'); // 'login' or 'register'

const openModal = (newMode = 'login') => {
  mode.value = newMode || 'login';
  isOpen.value = true;
};

const closeModal = () => {
  isOpen.value = false;
  resetRecaptcha();
};

defineExpose({ openModal });

const recaptchaSiteKey = import.meta.env.VITE_RECAPTCHA_SITE_KEY;

const loginForm = useForm({
  email: '',
  password: '',
  remember: false,
  recaptcha: '',
});

const registerForm = useForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  recaptcha: '',
});

// Reset reCAPTCHA on form submission or modal close
const resetRecaptcha = () => {
  if (typeof grecaptcha !== 'undefined') {
    grecaptcha.reset();
  }
  loginForm.recaptcha = '';
  registerForm.recaptcha = '';
};

const loginSubmit = () => {
  loginForm.recaptcha = grecaptcha.getResponse();
  loginForm.post(route('login'), {
    onFinish: () => {
      loginForm.reset('password');
      resetRecaptcha();
    },
  });
  console.log('reCAPTCHA Response:', loginForm.recaptcha);
};

const registerSubmit = () => {
  if (!route('register')) {
    console.error('Register route not found');
    return;
  }
  registerForm.post(route('register'), {
    onFinish: () => {
      registerForm.reset('password', 'password_confirmation');
    },
  });
};
</script>

<template>
  <!-- DaisyUI Modal -->
  <dialog :open="isOpen" class="modal">
    <div class="modal-box">
      <ApplicationLogo class="mx-auto h-16 w-auto" />
      <h3 class="flex justify-center text-lg font-bold">
        {{ mode === 'login' ? 'Log In' : 'Register' }}
      </h3>

      <form v-if="mode === 'login'" @submit.prevent="loginSubmit">
        <div>
          <InputLabel for="email" value="Email" />
          <TextInput
            id="email"
            type="email"
            class="mt-1 block w-full"
            v-model="loginForm.email"
            required
            autofocus
          />
          <InputError class="mt-2" :message="loginForm.errors.email" />
        </div>

        <div class="mt-4">
          <InputLabel for="password" value="Password" />
          <TextInput
            id="password"
            type="password"
            class="mt-1 block w-full"
            v-model="loginForm.password"
            required
          />
          <InputError class="mt-2" :message="loginForm.errors.password" />
        </div>

        <div class="mt-4">
          <label class="flex items-center">
            <Checkbox name="remember" v-model:checked="loginForm.remember" />
            <span class="ml-2 text-sm text-gray-600">Remember me</span>
          </label>
        </div>

        <div class="mt-4 flex justify-center">
          <div class="scale-90 transform">
            <div
              class="g-recaptcha"
              :data-sitekey="recaptchaSiteKey"
              data-theme="light"
              data-size="normal"
            ></div>
            <InputError
              class="mt-2 text-center"
              :message="loginForm.errors.recaptcha"
            />
          </div>
        </div>

        <div class="mt-6 flex justify-center">
          <PrimaryButton
            class="w-full justify-center"
            :class="{ 'opacity-25': loginForm.processing }"
            :disabled="loginForm.processing"
          >
            Log in
          </PrimaryButton>
        </div>
      </form>

      <form v-else @submit.prevent="registerSubmit">
        <div>
          <InputLabel for="name" value="Name" />
          <TextInput
            id="name"
            type="text"
            class="mt-1 block w-full"
            v-model="registerForm.name"
            required
            autofocus
          />
          <div class="mt-4">
            <div class="g-recaptcha" :data-sitekey="recaptchaSiteKey"></div>
            <InputError class="mt-2" :message="registerForm.errors.recaptcha" />
          </div>

          <InputError class="mt-2" :message="registerForm.errors.name" />
        </div>

        <div class="mt-4">
          <InputLabel for="email" value="Email" />
          <TextInput
            id="email"
            type="email"
            class="mt-1 block w-full"
            v-model="registerForm.email"
            required
          />
          <InputError class="mt-2" :message="registerForm.errors.email" />
        </div>

        <div class="mt-4">
          <InputLabel for="password" value="Password" />
          <TextInput
            id="password"
            type="password"
            class="mt-1 block w-full"
            v-model="registerForm.password"
            required
          />
          <InputError class="mt-2" :message="registerForm.errors.password" />
        </div>

        <div class="mt-4">
          <InputLabel for="password_confirmation" value="Confirm Password" />
          <TextInput
            id="password_confirmation"
            type="password"
            class="mt-1 block w-full"
            v-model="registerForm.password_confirmation"
            required
          />
          <InputError
            class="mt-2"
            :message="registerForm.errors.password_confirmation"
          />
        </div>

        <div class="mt-4 flex justify-center">
          <PrimaryButton
            :class="{ 'opacity-25': registerForm.processing }"
            :disabled="registerForm.processing"
          >
            Register
          </PrimaryButton>
        </div>
      </form>

      <div class="divider divider-neutral"></div>

      <div v-if="mode === 'login'" class="mt-4 flex justify-center">
        <GoogleButton />
      </div>

      <div class="modal-action flex justify-between">
        <button
          class="btn btn-ghost"
          @click="mode = mode === 'login' ? 'register' : 'login'"
        >
          {{
            mode === 'login'
              ? "Don't have an account? Register"
              : 'Already have an account? Log in'
          }}
        </button>
        <button class="btn" @click="closeModal">Close</button>
      </div>
    </div>
  </dialog>
</template>
