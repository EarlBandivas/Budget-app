<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Alert from '@/Components/Alert.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';

const page = usePage();

const categories = ref([]);
const expenseTypes = ref([]);
const newCategory = ref('');
const amount = ref('');
const selectedCategory = ref('');
const selectedExpenseType = ref('');
const newExpenseType = ref('');

// Add props to receive initial data
const props = defineProps({
  savedCategories: {
    type: Array,
    default: () => [],
  },
  savedExpenseTypes: {
    type: Array,
    default: () => [],
  },
});

// Initialize refs with saved data
categories.value = props.savedCategories;
expenseTypes.value = props.savedExpenseTypes;

// Add alert states
const alert = ref({
  show: false,
  type: 'success',
  message: '',
});

// Helper function to show alert
const showAlert = (type, message, duration = 3000) => {
  alert.value = {
    show: true,
    type,
    message,
  };

  // Hide alert after duration
  setTimeout(() => {
    alert.value.show = false;
  }, duration);
};

// Watch for flash messages
watch(
  () => page.props.flash,
  (flash) => {
    if (flash.message) {
      showAlert('success', flash.message);
    }
  },
);

const addCategory = () => {
  if (newCategory.value.trim()) {
    categories.value.push(newCategory.value.trim());
    newCategory.value = '';
    showAlert('success', 'Category added successfully!');
  } else {
    showAlert('error', 'Please enter a category name');
  }
};

const addExpenseType = () => {
  if (newExpenseType.value.trim()) {
    expenseTypes.value.push(newExpenseType.value.trim());
    newExpenseType.value = '';
    showAlert('success', 'Expense type added successfully!');
  } else {
    showAlert('error', 'Please enter an expense type name');
  }
};

const handleExpenseSubmit = () => {
  if (!selectedCategory.value || !selectedExpenseType.value || !amount.value) {
    showAlert('error', 'Please fill in all fields');
    return;
  }

  router.post(
    '/expenses',
    {
      category: selectedCategory.value,
      expense_type: selectedExpenseType.value,
      amount: amount.value,
    },
    {
      preserveScroll: true,
      onSuccess: () => {
        // Form will be reset when the page reloads with flash message
        selectedCategory.value = '';
        selectedExpenseType.value = '';
        amount.value = '';
        showAlert('success', 'Expense added successfully!');
      },
      onError: (errors) => {
        showAlert('error', Object.values(errors)[0]);
      },
    },
  );
};
</script>

<template>
  <Head title="Dashboard" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">
        Expense Tracker
      </h2>
    </template>

    <!-- Alert Component -->
    <div class="fixed right-4 top-4 z-50">
      <Alert :show="alert.show" :type="alert.type" :message="alert.message" />
    </div>

    <!-- Main Content -->
    <div class="py-12">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
          <div class="p-6">
            <!-- Expense Form -->
            <div class="grid gap-6 md:grid-cols-2">
              <!-- Left Column - Expense Input -->
              <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                  <h2 class="card-title mb-4">Add New Expense</h2>
                  <form @submit.prevent="handleExpenseSubmit" class="space-y-4">
                    <!-- Category Selection -->
                    <div class="form-control w-full">
                      <label class="label">
                        <span class="label-text">Category</span>
                      </label>
                      <select
                        v-model="selectedCategory"
                        class="select-bordered select w-full"
                        required
                      >
                        <option disabled value="">Select category</option>
                        <option
                          v-for="category in categories"
                          :key="category"
                          :value="category"
                        >
                          {{ category }}
                        </option>
                      </select>
                    </div>

                    <!-- Expense Type Selection -->
                    <div class="form-control w-full">
                      <label class="label">
                        <span class="label-text">Expense Type</span>
                      </label>
                      <select
                        v-model="selectedExpenseType"
                        class="select-bordered select w-full"
                        required
                      >
                        <option disabled value="">Select expense type</option>
                        <option
                          v-for="type in expenseTypes"
                          :key="type"
                          :value="type"
                        >
                          {{ type }}
                        </option>
                      </select>
                    </div>

                    <!-- Amount Input -->
                    <div class="form-control w-full">
                      <label class="label">
                        <span class="label-text me-2">Amount</span>
                      </label>
                      <label class="input-group">
                        <span>₱</span>
                        <input
                          type="number"
                          v-model="amount"
                          placeholder="0.00"
                          class="input-bordered input w-full"
                          required
                        />
                      </label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary w-full">
                      Add Expense
                    </button>
                  </form>
                </div>
              </div>

              <!-- Right Column - Add Category and Expense Type -->
              <div class="space-y-6">
                <!-- Add Category Card -->
                <div class="card bg-base-100 shadow-xl">
                  <div class="card-body">
                    <h2 class="card-title mb-4">Add New Category</h2>
                    <form @submit.prevent="addCategory" class="space-y-4">
                      <div class="form-control w-full">
                        <label class="label">
                          <span class="label-text">Category Name</span>
                        </label>
                        <input
                          type="text"
                          v-model="newCategory"
                          placeholder="Enter new category"
                          class="input-bordered input w-full"
                          autocomplete=""
                          required
                        />
                      </div>
                      <button type="submit" class="btn btn-secondary w-full">
                        Add Category
                      </button>
                    </form>

                    <!-- Categories List -->
                    <div class="mt-6">
                      <h3 class="mb-2 font-semibold">Available Categories</h3>
                      <div class="flex flex-wrap gap-2">
                        <div
                          v-for="category in categories"
                          :key="category"
                          class="badge badge-outline"
                        >
                          {{ category }}
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Add Expense Type Card -->
                <div class="card bg-base-100 shadow-xl">
                  <div class="card-body">
                    <h2 class="card-title mb-4">Add New Expense Type</h2>
                    <form @submit.prevent="addExpenseType" class="space-y-4">
                      <div class="form-control w-full">
                        <label for="expenseType" class="label">
                          <span class="label-text">Expense Type Name</span>
                        </label>
                        <input
                          type="text"
                          v-model="newExpenseType"
                          placeholder="Enter new expense type"
                          class="input-bordered input w-full"
                          autocomplete="on"
                          required
                        />
                      </div>
                      <button type="submit" class="btn btn-secondary w-full">
                        Add Expense Type
                      </button>
                    </form>

                    <!-- Expense Types List -->
                    <div class="mt-6">
                      <h3 class="mb-2 font-semibold">
                        Available Expense Types
                      </h3>
                      <div class="flex flex-wrap gap-2">
                        <div
                          v-for="type in expenseTypes"
                          :key="type"
                          class="badge badge-outline"
                        >
                          {{ type }}
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
