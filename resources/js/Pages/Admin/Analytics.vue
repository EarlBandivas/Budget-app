<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import Chart from 'chart.js/auto';

const props = defineProps({
  reportsByDepartment: {
    type: Object,
    required: true,
  },
  reportsByStatus: {
    type: Object,
    required: true,
  },
  monthlyExpenses: {
    type: Object,
    required: true,
  },
  topCategories: {
    type: Object,
    required: true,
  },
});

onMounted(() => {
  // Department Chart
  const departmentCtx = document.getElementById('departmentChart');
  new Chart(departmentCtx, {
    type: 'pie',
    data: {
      labels: Object.keys(props.reportsByDepartment),
      datasets: [
        {
          data: Object.values(props.reportsByDepartment),
          backgroundColor: [
            '#FF6384',
            '#36A2EB',
            '#FFCE56',
            '#4BC0C0',
            '#9966FF',
            '#FF9F40',
            '#C9CBCF',
            '#7BC043',
            '#F37735',
            '#FFC425',
          ],
        },
      ],
    },
    options: {
      responsive: true,
      plugins: {
        legend: {
          position: 'right',
        },
        title: {
          display: true,
          text: 'Reports by Department',
        },
      },
    },
  });

  // Status Chart
  const statusCtx = document.getElementById('statusChart');
  new Chart(statusCtx, {
    type: 'doughnut',
    data: {
      labels: Object.keys(props.reportsByStatus),
      datasets: [
        {
          data: Object.values(props.reportsByStatus),
          backgroundColor: ['#4BC0C0', '#FF6384', '#FFCE56'],
        },
      ],
    },
    options: {
      responsive: true,
      plugins: {
        legend: {
          position: 'right',
        },
        title: {
          display: true,
          text: 'Reports by Status',
        },
      },
    },
  });

  // Monthly Expenses Chart
  const monthlyCtx = document.getElementById('monthlyChart');
  new Chart(monthlyCtx, {
    type: 'bar',
    data: {
      labels: Object.keys(props.monthlyExpenses),
      datasets: [
        {
          label: 'Total Expenses',
          data: Object.values(props.monthlyExpenses),
          backgroundColor: '#36A2EB',
        },
      ],
    },
    options: {
      responsive: true,
      plugins: {
        title: {
          display: true,
          text: 'Monthly Expense Totals',
        },
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            callback: function (value) {
              return '₱' + value.toLocaleString();
            },
          },
        },
      },
    },
  });

  // Top Categories Chart
  const categoriesCtx = document.getElementById('categoriesChart');
  new Chart(categoriesCtx, {
    type: 'bar', // Changed from 'horizontalBar' to 'bar'
    data: {
      labels: Object.keys(props.topCategories),
      datasets: [
        {
          label: 'Amount',
          data: Object.values(props.topCategories),
          backgroundColor: '#FF9F40',
        },
      ],
    },
    options: {
      indexAxis: 'y', // This makes it horizontal
      responsive: true,
      plugins: {
        title: {
          display: true,
          text: 'Top Expense Categories',
        },
      },
      scales: {
        x: {
          beginAtZero: true,
          ticks: {
            callback: function (value) {
              return '₱' + value.toLocaleString();
            },
          },
        },
      },
    },
  });
});
</script>

<template>
  <Head title="Analytics Dashboard" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">
        Analytics Dashboard
      </h2>
    </template>

    <div class="py-12">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <!-- Summary Cards -->
        <div class="mb-6 grid grid-cols-1 gap-6 md:grid-cols-4">
          <div class="rounded-lg bg-white p-6 shadow-md">
            <h3 class="text-lg font-semibold text-gray-700">Total Reports</h3>
            <p class="text-3xl font-bold text-blue-600">
              {{
                Object.values(reportsByDepartment).reduce((a, b) => a + b, 0)
              }}
            </p>
          </div>

          <div class="rounded-lg bg-white p-6 shadow-md">
            <h3 class="text-lg font-semibold text-gray-700">Departments</h3>
            <p class="text-3xl font-bold text-green-600">
              {{ Object.keys(reportsByDepartment).length }}
            </p>
          </div>

          <div class="rounded-lg bg-white p-6 shadow-md">
            <h3 class="text-lg font-semibold text-gray-700">
              Approved Reports
            </h3>
            <p class="text-3xl font-bold text-teal-600">
              {{ reportsByStatus['Approved'] || 0 }}
            </p>
          </div>

          <div class="rounded-lg bg-white p-6 shadow-md">
            <h3 class="text-lg font-semibold text-gray-700">Pending Reports</h3>
            <p class="text-3xl font-bold text-amber-600">
              {{ reportsByStatus['Pending'] || 0 }}
            </p>
          </div>
        </div>

        <!-- Charts -->
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
          <!-- Department Distribution -->
          <div class="rounded-lg bg-white p-6 shadow-md">
            <canvas id="departmentChart"></canvas>
          </div>

          <!-- Status Distribution -->
          <div class="rounded-lg bg-white p-6 shadow-md">
            <canvas id="statusChart"></canvas>
          </div>

          <!-- Monthly Expenses -->
          <div class="rounded-lg bg-white p-6 shadow-md">
            <canvas id="monthlyChart"></canvas>
          </div>

          <!-- Top Categories -->
          <div class="rounded-lg bg-white p-6 shadow-md">
            <canvas id="categoriesChart"></canvas>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
