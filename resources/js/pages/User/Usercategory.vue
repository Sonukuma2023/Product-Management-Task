<template>
  <div class="dashboard-layout">
    <Usernavbar /> 

    <main class="main-content">
      
      <div v-if="$page.props.flash?.message" class="flash-alert success">
        {{ $page.props.flash.message }}
      </div>

      <div class="content-header">
        <h1>Welcome to Categories</h1>
        <button @click="openAddModal" class="btn-primary">+ Add Category</button>
      </div>

      <div class="table-container">
        <table>
          <thead>
            <tr>
              <th style="width: 10%;">ID</th>
              <th style="width: 30%;">Category Name</th>
              <th style="width: 40%;">Description</th>
              <th style="width: 20%; text-align: right;" class="actions-column">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="category in categories" :key="category.id">
              <td>#{{ category.id }}</td>
              <td class="font-bold">{{ category.name }}</td>
              <td class="text-muted">{{ category.description || 'No description provided' }}</td>
              <td>
                <div class="action-buttons">
                  <button @click="openEditModal(category)" class="btn-edit">Edit</button>
                  <button @click="deleteCategory(category.id)" class="btn-delete">Delete</button>
                </div>
              </td>
            </tr>
            <tr v-if="categories.length === 0">
              <td colspan="4" class="text-center empty-row">
                No categories found. Click "+ Add Category" to start.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </main>

    <div v-if="isModalOpen" class="modal-overlay">
      <div class="modal-card">
        <div class="modal-header">
          <h3>{{ isEditMode ? 'Edit Category' : 'Add New Category' }}</h3>
          <button @click="closeModal" class="close-btn">&times;</button>
        </div>

        <form @submit.prevent="submitForm">
          <div class="form-group">
            <label>Category Name</label>
            <input 
              v-model="form.name" 
              type="text" 
              placeholder="e.g. Electronics"
              :class="{'input-error': form.errors.name}"
            />
            <span v-if="form.errors.name" class="error-text">
            {{ Array.isArray(form.errors.name) ? form.errors.name[0] : form.errors.name }}</span>
          </div>

          <div class="form-group">
            <label>Description</label>
            <textarea 
              v-model="form.description" 
              placeholder="Enter category description..."
              rows="4"
            ></textarea>
            <span v-if="form.errors.description" class="error-text">{{ form.errors.description }}</span>
          </div>

          <div class="modal-footer">
            <button type="button" @click="closeModal" class="btn-secondary">Cancel</button>
            <button type="submit" class="btn-primary" :disabled="form.processing">
              {{ form.processing ? 'Saving...' : 'Submit' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import Usernavbar from './Usernavbar.vue';
import { route } from 'ziggy-js';
import Swal from 'sweetalert2';

// Accept dynamic records data array directly from backend controller
defineProps({
  categories: {
    type: Array,
    default: () => []
  }
});

// UI Modal state toggles
const isModalOpen = ref(false);
const isEditMode = ref(false);
const selectedCategoryId = ref(null);

// Inertia Form initialization
const form = useForm({
  name: '',
  description: ''
});

const openAddModal = () => {
  isEditMode.value = false;
  form.reset();
  form.clearErrors();
  isModalOpen.value = true;
};

const openEditModal = (category) => {
  isEditMode.value = true;
  selectedCategoryId.value = category.id;
  form.clearErrors();
  form.name = category.name;
  form.description = category.description;
  isModalOpen.value = true;
};

const closeModal = () => {
  isModalOpen.value = false;
  form.reset();
};

const submitForm = () => {
  if (isEditMode.value) {
    form.put(`/categories/${selectedCategoryId.value}`, {
      onSuccess: () => {
        closeModal();
      },
    });
  } else {
    form.post('/categories', {
      onSuccess: () => {
        closeModal();
      },
    });
  }
};

const deleteCategory = (id) => {
  Swal.fire({
    title: 'Are you sure?',
    text: "You won't be able to revert this!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: 'Yes, delete it!',
    cancelButtonText: 'Cancel',
  }).then((result) => {
    if (result.isConfirmed) {
      router.delete(`/categories/${id}`, {
        onSuccess: () => {
          Swal.fire({
            title: 'Deleted!',
            text: 'Category has been deleted successfully.',
            icon: 'success',
            timer: 2000,
            showConfirmButton: false,
          });
        },
      });
    }
  });
};
</script>

<style scoped>
/* Main Structural Flex Layout Container */
.dashboard-layout {
  display: block; /* Change from flex to block since sidebar is fixed */
  min-height: 100vh;
  width: 100%;
  background-color: #f8fafc;
  font-family: system-ui, -apple-system, sans-serif;
}

/* Ensure the layout respects the fixed sidebar size */
:deep(.sidebar), :deep(aside) {
  width: 260px;
  min-width: 260px;
  flex-shrink: 0;
}

/* Right Content Window Pane Layout Configuration */
.main-content {
  margin-left: 260px; /* This pushes your content exactly out from behind the sidebar! */
  width: calc(100% - 260px); /* Prevents the right side from going off screen */
  padding: 2.5rem;
  background-color: #f8fafc;
  box-sizing: border-box;
}

.content-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}

.content-header h1 {
  font-size: 1.5rem;
  font-weight: 600;
  color: #1e293b;
  margin: 0;
}

/* Data Presentation Tables Formatting Rules */
.table-container {
  width: 100%;
  background: #ffffff;
  border-radius: 0.5rem;
  box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
  border: 1px solid #e2e8f0;
  overflow-x: auto; /* Adds a scrollbar ONLY if screen gets tiny */
}

table {
  width: 100%;
  border-collapse: collapse;
  text-align: left;
}

th {
  background-color: #f8fafc;
  color: #64748b;
  padding: 1rem 1.5rem;
  font-weight: 600;
  font-size: 0.85rem;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  border-bottom: 1px solid #e2e8f0;
}

td {
  padding: 1.25rem 1.5rem;
  border-bottom: 1px solid #f1f5f9;
  color: #334155;
  font-size: 0.95rem;
  vertical-align: middle;
}

.font-bold {
  font-weight: 600;
  color: #0f172a;
}

.text-muted {
  color: #64748b;
}

.empty-row {
  padding: 4rem !important;
  color: #64748b;
  font-size: 1rem;
}

.action-buttons {
  display: flex;
  gap: 0.75rem;
  justify-content: flex-end;
}

/* Buttons Theme Rules */
.btn-primary {
  background-color: #2563eb;
  color: #ffffff;
  border: none;
  padding: 0.625rem 1.25rem;
  border-radius: 0.375rem;
  font-weight: 500;
  cursor: pointer;
  transition: background-color 0.2s;
}
.btn-primary:hover { background-color: #1d4ed8; }

.btn-secondary {
  background-color: #f1f5f9;
  color: #475569;
  border: 1px solid #cbd5e1;
  padding: 0.625rem 1.25rem;
  border-radius: 0.375rem;
  font-weight: 500;
  cursor: pointer;
}
.btn-secondary:hover { background-color: #e2e8f0; }

.btn-edit {
  background-color: #f59e0b;
  color: white;
  border: none;
  padding: 0.375rem 0.875rem;
  border-radius: 0.25rem;
  font-weight: 500;
  cursor: pointer;
}
.btn-edit:hover { background-color: #d97706; }

.btn-delete {
  background-color: #ef4444;
  color: white;
  border: none;
  padding: 0.375rem 0.875rem;
  border-radius: 0.25rem;
  font-weight: 500;
  cursor: pointer;
}
.btn-delete:hover { background-color: #dc2626; }

/* Popup Form Overlay Box Rulesets */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  background: rgba(15, 23, 42, 0.4);
  backdrop-filter: blur(2px);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 999;
}

.modal-card {
  background: #ffffff;
  padding: 2rem;
  border-radius: 0.5rem;
  width: 100%;
  max-width: 480px;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
}

.modal-header h3 {
  font-size: 1.25rem;
  color: #0f172a;
  margin: 0;
}

.close-btn {
  background: none;
  border: none;
  font-size: 1.75rem;
  color: #94a3b8;
  cursor: pointer;
  line-height: 1;
}
.close-btn:hover { color: #475569; }

.form-group {
  margin-bottom: 1.25rem;
}

.form-group label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 500;
  font-size: 0.875rem;
  color: #334155;
}

.form-group input, .form-group textarea {
  width: 100%;
  padding: 0.625rem;
  border: 1px solid #cbd5e1;
  border-radius: 0.375rem;
  box-sizing: border-box;
  font-size: 0.95rem;
  color: #0f172a;
}
.form-group input:focus, .form-group textarea:focus {
  outline: none;
  border-color: #2563eb;
  box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.input-error { border-color: #ef4444 !important; }
.error-text { color: #ef4444; font-size: 0.825rem; margin-top: 0.375rem; display: block; }

.modal-footer {
  display: flex;
  justify-content: flex-end;
  gap: 0.75rem;
  margin-top: 2rem;
}

/* Success Messages Formats */
.flash-alert {
  padding: 1rem 1.25rem;
  border-radius: 0.375rem;
  margin-bottom: 1.5rem;
  font-size: 0.95rem;
}
.flash-alert.success {
  background-color: #dcfce7;
  color: #15803d;
  border: 1px solid #bbf7d0;
}
</style>