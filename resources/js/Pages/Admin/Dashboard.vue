<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, reactive } from 'vue';
import * as XLSX from 'xlsx';
import { formatCurrency, formatDate } from '@/utils/excel';

const props = defineProps({
  submittedReports: {
    type: Array,
    required: true,
  },
});

// Convert props.submittedReports to a reactive array
const reports = reactive([...props.submittedReports]);

const approveReport = (reportId) => {
  router.put(
    `/expense-reports/${reportId}/status`,
    {
      status: 'Approved',
    },
    {
      onSuccess: () => {
        showAlert('success', 'Report approved successfully');
      },
    },
  );
};

// New refs for rejection confirmation
const showRejectModal = ref(false);
const reportToReject = ref(null);

// New method to open rejection modal
const confirmReject = (report) => {
  reportToReject.value = report;
  showRejectModal.value = true;
};

// Modified reject method
const rejectReport = () => {
  router.put(
    `/expense-reports/${reportToReject.value.id}/status`,
    {
      status: 'Rejected',
    },
    {
      preserveScroll: true,
      onSuccess: () => {
        showAlert('success', 'Report rejected successfully');
        showRejectModal.value = false;

        // Find and remove the rejected report from our reactive array
        const index = reports.findIndex(
          (r) => r.id === reportToReject.value.id,
        );
        if (index !== -1) {
          reports.splice(index, 1);
        }

        reportToReject.value = null;
      },
      onError: () => {
        showAlert('error', 'Failed to reject report');
        showRejectModal.value = false;
      },
    },
  );
};

const alert = ref({ show: false, type: '', message: '' });

const showAlert = (type, message) => {
  alert.value = { show: true, type, message };
  setTimeout(() => {
    alert.value.show = false;
  }, 3000);
};

const selectedReport = ref(null);
const showDetailsModal = ref(false);

const viewReport = (report) => {
  selectedReport.value = report;
  showDetailsModal.value = true;
};

const downloadExcel = (report) => {
  // Prepare the data for the expense report
  const reportData = [
    ['Expense Report'],
    [''],
    ['User Information'],
    ['Email:', report.user_email],
    ['Department:', report.user_department],
    ['Submission Date:', formatDate(report.submission_date)],
    ['Status:', report.status],
    [''],
    ['Expenses'],
    ['Category', 'Expense Type', 'Amount', 'Date'],
    ...report.expenses.map((expense) => [
      expense.category,
      expense.expense_type,
      formatCurrency(expense.amount),
      formatDate(expense.created_at),
    ]),
    [''],
    [
      'Total:',
      '',
      formatCurrency(report.expenses.reduce((sum, exp) => sum + exp.amount, 0)),
      '',
    ],
  ];

  // Create a new workbook and worksheet
  const wb = XLSX.utils.book_new();
  const ws = XLSX.utils.aoa_to_sheet(reportData);

  // Add some styling
  const mergeConfig = [
    { s: { r: 0, c: 0 }, e: { r: 0, c: 3 } }, // Merge first row
  ];

  if (!ws['!merges']) ws['!merges'] = [];
  ws['!merges'] = mergeConfig;

  // Set column widths
  ws['!cols'] = [
    { wch: 20 }, // Category
    { wch: 20 }, // Expense Type
    { wch: 15 }, // Amount
    { wch: 15 }, // Date
  ];

  // Add the worksheet to the workbook
  XLSX.utils.book_append_sheet(wb, ws, 'Expense Report');

  // Generate filename with sanitized email
  const sanitizedEmail = report.user_email.replace(/[^a-zA-Z0-9]/g, '_');
  const fileName = `expense_report_${sanitizedEmail}_${new Date().toISOString().split('T')[0]}.xlsx`;

  // Save the file
  XLSX.writeFile(wb, fileName);
};
</script>

<template>
  <Head title="Admin Dashboard" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">
        Admin Dashboard
      </h2>
    </template>

    <div class="py-12">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <!-- Alert Component -->
        <div
          v-if="alert.show"
          :class="{
            'border-green-400 bg-green-100 text-green-700':
              alert.type === 'success',
            'border-red-400 bg-red-100 text-red-700': alert.type === 'error',
          }"
          class="mb-4 border-l-4 p-4"
        >
          {{ alert.message }}
        </div>

        <div
          class="border-base-content/5 overflow-hidden overflow-x-auto rounded-box border bg-base-100"
        >
          <div class="p-6">
            <h3 class="mb-4 text-lg font-semibold">
              Submitted Expense Reports
            </h3>

            <div class="overflow-x-auto">
              <table class="table w-full">
                <thead>
                  <tr>
                    <th>User Email</th>
                    <th>Department</th>
                    <th>Submission Date</th>
                    <th>Status</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="report in reports" :key="report.id">
                    <td>{{ report.user_email }}</td>
                    <td>{{ report.user_department }}</td>
                    <td>
                      {{
                        new Date(report.submission_date).toLocaleDateString()
                      }}
                    </td>
                    <td>
                      <span
                        :class="{
                          'badge-success': report.status === 'Approved',
                          'badge-warning': report.status === 'Pending',
                          'badge-error': report.status === 'Rejected',
                        }"
                        class="badge"
                      >
                        {{ report.status }}
                      </span>
                    </td>
                    <td>
                      <button
                        @click="approveReport(report.id)"
                        class="btn btn-success btn-sm m-2"
                        :disabled="report.status === 'Approved'"
                      >
                        Approve
                      </button>
                      <button
                        @click="confirmReject(report)"
                        class="btn btn-error btn-sm m-2"
                        :disabled="report.status === 'Rejected'"
                      >
                        Reject
                      </button>
                      <button
                        @click="viewReport(report)"
                        class="btn btn-neutral btn-sm m-2"
                      >
                        View
                      </button>
                    </td>
                  </tr>
                  <tr v-if="reports.length === 0">
                    <td colspan="5" class="py-4 text-center">
                      No reports submitted yet
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>

  <!-- Add the modal -->
  <div v-if="showDetailsModal" class="modal-open modal">
    <div class="modal-box w-11/12 max-w-5xl">
      <h3 class="text-lg font-bold">Expense Report Details</h3>
      <div class="py-4">
        <div class="mb-4">
          <p><strong>User:</strong> {{ selectedReport.user_email }}</p>
          <p>
            <strong>Department:</strong> {{ selectedReport.user_department }}
          </p>
          <p>
            <strong>Submission Date:</strong>
            {{ new Date(selectedReport.submission_date).toLocaleDateString() }}
          </p>
          <p>
            <strong>Status:</strong>
            <span
              :class="{
                'badge-success': selectedReport.status === 'Approved',
                'badge-warning': selectedReport.status === 'Pending',
                'badge-error': selectedReport.status === 'Rejected',
              }"
              class="badge"
            >
              {{ selectedReport.status }}
            </span>
          </p>
        </div>

        <div class="overflow-x-auto">
          <table class="table w-full">
            <thead>
              <tr>
                <th>Category</th>
                <th>Expense Type</th>
                <th>Amount</th>
                <th>Date</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="expense in selectedReport.expenses" :key="expense.id">
                <td>{{ expense.category }}</td>
                <td>{{ expense.expense_type }}</td>
                <td>₱{{ expense.amount.toLocaleString() }}</td>
                <td>{{ new Date(expense.created_at).toLocaleDateString() }}</td>
              </tr>
              <tr v-if="selectedReport.expenses.length === 0">
                <td colspan="4" class="text-center">No expenses found</td>
              </tr>
            </tbody>
            <tfoot>
              <tr>
                <td colspan="2" class="text-right"><strong>Total:</strong></td>
                <td colspan="2">
                  ₱{{
                    selectedReport.expenses
                      .reduce((sum, exp) => sum + exp.amount, 0)
                      .toLocaleString()
                  }}
                </td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
      <div class="modal-action">
        <button
          v-if="selectedReport.status === 'Approved'"
          @click="downloadExcel(selectedReport)"
          class="btn btn-success"
        >
          Download Excel
        </button>
        <button @click="showDetailsModal = false" class="btn">Close</button>
      </div>
    </div>
  </div>

  <!-- Add the rejection confirmation modal -->
  <div v-if="showRejectModal" class="modal-open modal">
    <div class="modal-box">
      <h3 class="text-lg font-bold">Confirm Rejection</h3>
      <p class="py-4">
        Are you sure you want to reject this expense report from
        {{ reportToReject?.user_email }}? This action will remove the report
        from the list.
      </p>
      <div class="modal-action">
        <button @click="showRejectModal = false" class="btn">Cancel</button>
        <button @click="rejectReport" class="btn btn-error">
          Reject Report
        </button>
      </div>
    </div>
  </div>
</template>
