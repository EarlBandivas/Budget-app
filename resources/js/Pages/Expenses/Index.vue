<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Alert from '@/Components/Alert.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
  expenses: {
    type: Array,
    required: true,
  },
});

const page = usePage();

const alert = ref({
  show: false,
  type: 'success',
  message: '',
});

const editingExpense = ref(null);
const showEditModal = ref(false);

const editForm = ref({
  category: '',
  expense_type: '',
  amount: '',
});

const deleteExpense = (id) => {
  if (confirm('Are you sure you want to delete this expense?')) {
    router.delete(`/expenses/${id}`, {
      onSuccess: () => {
        showAlert('success', 'Expense deleted successfully');
      },
    });
  }
};

const openEditModal = (expense) => {
  editingExpense.value = expense;
  editForm.value = {
    category: expense.category,
    expense_type: expense.expense_type,
    amount: expense.amount,
  };
  showEditModal.value = true;
};

const updateExpense = () => {
  router.put(`/expenses/${editingExpense.value.id}`, editForm.value, {
    onSuccess: () => {
      showEditModal.value = false;
      showAlert('success', 'Expense updated successfully');
    },
  });
};

const showAlert = (type, message) => {
  alert.value = {
    show: true,
    type,
    message,
  };
  setTimeout(() => {
    alert.value.show = false;
  }, 3000);
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

const submitToAdmin = () => {
  router.post(
    '/expenses/submit-report',
    {},
    {
      onSuccess: () => {
        showAlert('success', 'Report submitted to admin successfully');
      },
      onError: () => {
        showAlert('error', 'Failed to submit report');
      },
    },
  );
};
</script>

<template>
  <Head title="Expenses" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">
        Expenses List
      </h2>
    </template>

    <!-- Alert Component -->
    <div class="fixed right-4 top-4 z-50">
      <Alert :show="alert.show" :type="alert.type" :message="alert.message" />
    </div>

    <!-- Edit Modal -->
    <div v-if="showEditModal" class="modal-open modal">
      <div class="modal-box">
        <h3 class="mb-4 text-lg font-bold">Edit Expense</h3>
        <form @submit.prevent="updateExpense">
          <div class="form-control w-full">
            <label class="label">
              <span class="label-text">Category</span>
            </label>
            <input
              type="text"
              v-model="editForm.category"
              class="input-bordered input w-full"
              required
            />
          </div>

          <div class="form-control mt-4 w-full">
            <label class="label">
              <span class="label-text">Expense Type</span>
            </label>
            <input
              type="text"
              v-model="editForm.expense_type"
              class="input-bordered input w-full"
              required
            />
          </div>

          <div class="form-control mt-4 w-full">
            <label class="label">
              <span class="label-text">Amount</span>
            </label>
            <input
              type="number"
              v-model="editForm.amount"
              class="input-bordered input w-full"
              required
            />
          </div>

          <div class="modal-action">
            <button type="submit" class="btn btn-primary">Save Changes</button>
            <button type="button" class="btn" @click="showEditModal = false">
              Cancel
            </button>
          </div>
        </form>
      </div>
    </div>

    <div class="py-12">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
          <div class="p-6">
            <div
              class="border-base-content/5 overflow-x-auto rounded-box border bg-base-100"
            >
              <table class="table">
                <thead>
                  <tr>
                    <th></th>
                    <th>Category</th>
                    <th>Expense Type</th>
                    <th>Amount</th>
                    <th>Date</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(expense, index) in expenses" :key="expense.id">
                    <th>{{ index + 1 }}</th>
                    <td>{{ expense.category }}</td>
                    <td>{{ expense.expense_type }}</td>
                    <td>₱{{ expense.amount.toLocaleString() }}</td>
                    <td>
                      {{ new Date(expense.created_at).toLocaleDateString() }}
                    </td>
                    <td>
                      <div class="flex gap-2">
                        <button
                          @click="openEditModal(expense)"
                          class="btn btn-info btn-sm"
                        >
                          Edit
                        </button>
                        <button
                          @click="deleteExpense(expense.id)"
                          class="btn btn-error btn-sm"
                        >
                          Delete
                        </button>
                      </div>
                    </td>
                  </tr>
                  <tr v-if="expenses.length === 0">
                    <td colspan="6" class="py-4 text-center">
                      No expenses found
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Add Submit Button -->
            <div class="mt-6 flex justify-center">
              <button
                @click="submitToAdmin"
                class="btn bg-green-400"
                :disabled="expenses.length === 0"
              >
                Submit Report to Admin
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
