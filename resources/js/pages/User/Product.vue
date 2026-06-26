<template>
  <div class="dashboard-layout">
    <Usernavbar /> 

    <main class="main-content">
      
      <Transition name="fade">
        <div v-if="$page.props.flash?.message" class="custom-alert-banner" role="alert">
          <span class="alert-icon">✓</span>
          <span class="alert-text">{{ $page.props.flash.message }}</span>
        </div>
      </Transition>

      <div class="content-header-panel">
        <div class="header-titles">
          <h1>Products Inventory</h1>
          <p class="subtitle-text">Manage, monitor, and update your catalog storage stock items.</p>
        </div>
        <button @click="openAddModal" class="action-btn-primary">
          <span class="plus-icon">+</span> Add New Product
        </button>
      </div>

      <div class="metrics-grid">
        <div class="metric-card">
          <div class="metric-info">
            <span class="metric-label">Total SKUs Registered</span>
            <span class="metric-value">{{ products.length }}</span>
          </div>
          <div class="metric-icon blue-theme">📦</div>
        </div>
        <div class="metric-card">
          <div class="metric-info">
            <span class="metric-label">Low Stock Alerts</span>
            <span class="metric-value text-danger">
              {{ products.filter(p => p.quantity <= 10).length }}
            </span>
          </div>
          <div class="metric-icon red-theme">⚠️</div>
        </div>
      </div>

      <div class="inventory-table-card">
        <table class="inventory-data-table">
          <thead>
            <tr>
              <th style="width: 10%;">Preview</th>
              <th style="width: 15%;">SKU </th>
              <th style="width: 30%;">Product Name</th>
              <th style="width: 15%;">Price</th>
              <th style="width: 15%;">Stock</th>
              <th style="width: 15%; text-align: right;">Action     </th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="product in products" :key="product.id" class="table-data-row">
              <td>
                <div class="image-preview-wrapper">
                  <img :src="'/' + product.image" alt="Catalog Preview" />
                </div>
              </td>
              <td>
                <span class="sku-badge">{{ product.sku_code }}</span>
              </td>
              <td>
                <div class="product-identity-cell">
                  <span class="product-name-txt">{{ product.name }}</span>
                  <span class="product-desc-snippet" v-if="product.description">
                    {{ product.description.substring(0, 45) }}{{ product.description.length > 45 ? '...' : '' }}
                  </span>
                </div>
              </td>
              <td class="price-data-cell">
                ${{ parseFloat(product.price).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}
              </td>
              <td>
                <span :class="['stock-status-tag', product.quantity > 10 ? 'in-stock' : 'low-stock']">
                  <span class="status-dot"></span>
                  {{ product.quantity }} Units
                </span>
              </td>
              <td>
                <div class="row-action-buttons">
                  <button @click="openEditModal(product)" class="action-btn edit-trigger" title="Edit Product Specs">
                    Edit
                  </button>
                  <button @click="deleteProduct(product.id)" class="action-btn delete-trigger" title="Delete Permanent Record">
                    Delete
                  </button>
                </div>
              </td>
            </tr>
            
            <tr v-if="products.length === 0">
              <td colspan="6" class="empty-table-state-cell">
                <div class="empty-state-graphic">📥</div>
                <h3>Your product database records storage is currently empty</h3>
                <p>Get started by clicking the "+ Add New Product" button above to register stock assets items.</p>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </main>

    <div v-if="isModalOpen" class="modal-backdrop-layer" @click.self="closeModal">
      <div class="modal-form-window">
        <div class="modal-form-header">
          <h3>{{ isEditMode ? 'Modify Active Product File' : 'Register New Inventory Asset' }}</h3>
          <button @click="closeModal" class="modal-dismiss-close-x">&times;</button>
        </div>

        <form @submit.prevent="submitForm" enctype="multipart/form-data" class="modal-interactive-form">
          <div class="form-input-grid">
            <div class="form-field-block span-6">
              <label>Product Name</label>
              <input v-model="form.name" type="text" placeholder="e.g. Mechanical Keyboard" :class="{'field-input-error': form.errors.name}" />
              <span v-if="form.errors.name" class="field-error-msg-txt">{{ form.errors.name }}</span>
            </div>

            <div class="form-field-block span-6">
              <label>SKU Code</label>
              <input v-model="form.sku_code" type="text" placeholder="e.g. HW-KEY-99" :class="{'field-input-error': form.errors.sku_code}" />
              <span v-if="form.errors.sku_code" class="field-error-msg-txt">{{ form.errors.sku_code }}</span>
            </div>

            <div class="form-field-block span-6">
              <label>Unit Price ($)</label>
              <input v-model="form.price" type="number" step="0.01" placeholder="0.00" :class="{'field-input-error': form.errors.price}" />
              <span v-if="form.errors.price" class="field-error-msg-txt">{{ form.errors.price }}</span>
            </div>
            
            <div class="form-field-block span-6">
              <label>Category</label>
              <select
                v-model="form.category_id"
                :class="{ 'field-input-error': form.errors.category_id }"
              >
                <option value="">Select Category</option>
                <option
                  v-for="category in categories"
                  :key="category.id"
                  :value="category.id"
                >
                  {{ category.name }}
                </option>
              </select>
              <span v-if="form.errors.category_id" class="field-error-msg-txt">
                {{ form.errors.category_id }}
              </span>
            </div>

            <div class="form-field-block span-6">
              <label>Opening Inventory Quantity</label>
              <input v-model="form.quantity" type="number" placeholder="0" :class="{'field-input-error': form.errors.quantity}" />
              <span v-if="form.errors.quantity" class="field-error-msg-txt">{{ form.errors.quantity }}</span>
            </div>

            <div class="form-field-block span-12">
              <label>Product Specifications Description</label>
              <textarea v-model="form.description" rows="3" placeholder="Provide optional details regarding item sizing, color parameters, storage or specifications..."></textarea>
            </div>

            <div class="form-field-block span-12">
              <label>Upload Product Catalog Image <span v-if="!isEditMode" class="required-flag">*</span></label>
              <div class="file-upload-drag-zone">
                <input type="file" @input="form.image = $event.target.files[0]" accept="image/*" class="ghost-file-input" />
                <div class="upload-zone-prompt">
                  <span class="upload-cloud-icon">📷</span>
                  <span class="upload-main-prompt-text">Click here to attach or swap product display image file</span>
                  <span class="upload-subtext-constraints">Supports JPEG, PNG, WEBP files up to 2MB allocation limit.</span>
                </div>
              </div>
              <span v-if="form.errors.image" class="field-error-msg-txt mt-1">{{ form.errors.image }}</span>
              <p v-if="isEditMode" class="field-info-help-txt">Keep item input field empty to leave the existing database record thumbnail untouched.</p>
            </div>
          </div>

          <div class="modal-action-footer-tray">
            <button type="button" @click="closeModal" class="tray-btn-secondary">Cancel</button>
            <button type="submit" class="tray-btn-primary" :disabled="form.processing">
              {{ form.processing ? 'Saving Changes...' : 'Save Product Data' }}
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
import Swal from 'sweetalert2';

defineProps({
  products: {
    type: Array,
    default: () => []
  },
  categories: {
    type: Array,
    default: () => []
  }
});

const isModalOpen = ref(false);
const isEditMode = ref(false);
const selectedProductId = ref(null);

const form = useForm({
  name: '',
  sku_code: '',
  price: '',
  category_id: '',
  quantity: '',
  description: '',
  image: null,
});

const openAddModal = () => {
  isEditMode.value = false;
  form.reset();
  form.clearErrors();
  isModalOpen.value = true;
};

const openEditModal = (product) => {
  isEditMode.value = true;
  selectedProductId.value = product.id;
  form.clearErrors();
  
  form.name = product.name;
  form.sku_code = product.sku_code;
  form.price = product.price;
  form.category_id = product.category_id || '';
  form.quantity = product.quantity;
  form.description = product.description;
  form.image = null; 
  isModalOpen.value = true;
};

const closeModal = () => {
  isModalOpen.value = false;
  form.reset();
};

const submitForm = () => {
  form.clearErrors();
  let clientErrors = {};

  if (!form.name || !form.name.trim()) clientErrors.name = 'The product name field is required.';
  if (!form.sku_code || !form.sku_code.trim()) clientErrors.sku_code = 'The unique SKU code reference field is required.';
  if (!form.price || form.price <= 0) clientErrors.price = 'Please input a valid price valuation number.';
  if (form.category_id === '') clientErrors.category_id = 'Please select a product category.';
  if (form.quantity === '' || form.quantity < 0) clientErrors.quantity = 'Stock cannot be a negative value.';
  if (!isEditMode.value && !form.image) clientErrors.image = 'An original image attachment file upload is mandatory.';

  if (Object.keys(clientErrors).length > 0) {
    form.setError(clientErrors);
    return;
  }

  if (isEditMode.value) {
    // Handling multipart files with Laravel via a POST method request mapping override flag
    form.transform((data) => ({
      ...data,
      _method: 'PUT'
    })).post(`/products/${selectedProductId.value}`, {
        onSuccess: () => {
            closeModal();
        },
    });
  } else {
       form.post('/products', {
            onSuccess: () => {
                closeModal();
            },
        });
    }
};

const deleteProduct = (id) => {
  Swal.fire({
    title: 'Are you sure?',
    text: 'You are about to permanently delete this product.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: 'Yes, delete it!',
    cancelButtonText: 'Cancel',
  }).then((result) => {
    if (result.isConfirmed) {
      router.delete(`/products/${id}`, {
        onSuccess: () => {
          Swal.fire({
            icon: 'success',
            title: 'Deleted!',
            text: 'Product deleted successfully.',
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
/* Master Structure Layout Settings */
.dashboard-layout {
  display: block;
  min-height: 100vh;
  width: 100vw;
  background-color: #f8fafc;
  font-family: 'Inter', system-ui, -apple-system, sans-serif;
  overflow-x: hidden;
}

.main-content {
  margin-left: 260px;
  width: calc(100% - 260px);
  padding: 3rem;
  box-sizing: border-box;
}

/* Panel Header Layout Formatting Rules */
.content-header-panel {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2.5rem;
}

.content-header-panel h1 {
  font-size: 1.75rem;
  font-weight: 700;
  color: #0f172a;
  margin: 0 0 0.375rem 0;
  letter-spacing: -0.02em;
}

.subtitle-text {
  font-size: 0.925rem;
  color: #64748b;
  margin: 0;
}

.action-btn-primary {
  background-color: #2563eb;
  color: #ffffff;
  border: none;
  font-weight: 600;
  font-size: 0.925rem;
  padding: 0.75rem 1.5rem;
  border-radius: 0.5rem;
  cursor: pointer;
  box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.15);
  display: flex;
  align-items: center;
  gap: 0.5rem;
  transition: background-color 0.2s, transform 0.1s;
}
.action-btn-primary:hover { background-color: #1d4ed8; }
.action-btn-primary:active { transform: scale(0.98); }

/* Analytical Metrics Grid Row Formatting Setup */
.metrics-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2.5rem;
}

.metric-card {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 0.75rem;
  padding: 1.25rem 1.5rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  box-shadow: 0 1px 3px rgba(0,0,0,0.02);
}

.metric-info {
  display: flex;
  flex-direction: column;
}

.metric-label {
  font-size: 0.825rem;
  font-weight: 600;
  color: #64748b;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  margin-bottom: 0.25rem;
}

.metric-value {
  font-size: 1.5rem;
  font-weight: 700;
  color: #0f172a;
}

.metric-icon {
  width: 44px;
  height: 44px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.25rem;
}
.blue-theme { background-color: #eff6ff; }
.red-theme { background-color: #fef2f2; }

/* Table Component View Elements Formatting Layout */
.inventory-table-card {
  background: #ffffff;
  border-radius: 0.75rem;
  border: 1px solid #e2e8f0;
  box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02), 0 2px 4px -1px rgba(0,0,0,0.01);
  overflow: hidden;
}

.inventory-data-table {
  width: 100%;
  border-collapse: collapse;
  text-align: left;
}

.inventory-data-table th {
  background-color: #f8fafc;
  color: #475569;
  padding: 1rem 1.5rem;
  font-weight: 600;
  font-size: 0.825rem;
  text-transform: uppercase;
  letter-spacing: 0.04em;
  border-bottom: 1px solid #e2e8f0;
}

.table-data-row {
  border-bottom: 1px solid #f1f5f9;
  transition: background-color 0.15s;
}
.table-data-row:hover { background-color: #f8fafc; }

.image-preview-wrapper {
  width: 48px;
  height: 48px;
  border-radius: 0.375rem;
  overflow: hidden;
  border: 1px solid #e2e8f0;
  background-color: #f8fafc;
}

.image-preview-wrapper img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.sku-badge {
  font-family: 'SFMono-Regular', Consolas, monospace;
  background-color: #f1f5f9;
  color: #475569;
  padding: 0.25rem 0.5rem;
  border-radius: 0.25rem;
  font-size: 0.85rem;
  font-weight: 500;
}

.product-identity-cell {
  display: flex;
  flex-direction: column;
}

.product-name-txt {
  font-weight: 600;
  color: #0f172a;
  font-size: 0.95rem;
}

.product-desc-snippet {
  font-size: 0.825rem;
  color: #64748b;
  margin-top: 0.125rem;
}

.price-data-cell {
  font-weight: 600;
  color: #0f172a;
  font-size: 0.95rem;
}

/* Status Pill Design Configurations */
.stock-status-tag {
  display: inline-flex;
  align-items: center;
  gap: 0.375rem;
  padding: 0.25rem 0.625rem;
  border-radius: 50px;
  font-size: 0.825rem;
  font-weight: 600;
}
.stock-status-tag.in-stock { background-color: #dcfce7; color: #15803d; }
.stock-status-tag.low-stock { background-color: #fee2e2; color: #b91c1c; }

.status-dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
}
.in-stock .status-dot { background-color: #16a34a; }
.low-stock .status-dot { background-color: #dc2626; }

.row-action-buttons {
  display: flex;
  gap: 0.5rem;
  justify-content: flex-end;
}

.action-btn {
  padding: 0.375rem 0.75rem;
  border-radius: 0.375rem;
  font-size: 0.85rem;
  font-weight: 600;
  cursor: pointer;
  border: 1px solid transparent;
  background: transparent;
  transition: all 0.2s;
}

.action-btn.edit-trigger { color: #d97706; border-color: #fef3c7; background-color: #fef9c3; }
.action-btn.edit-trigger:hover { background-color: #fef3c7; }

.action-btn.delete-trigger { color: #dc2626; border-color: #fee2e2; background-color: #fef2f2; }
.action-btn.delete-trigger:hover { background-color: #fee2e2; }

/* Empty Table Presentation View Block rules */
.empty-table-state-cell {
  text-align: center;
  padding: 5rem 2rem !important;
}

.empty-state-graphic {
  font-size: 3rem;
  margin-bottom: 1rem;
}

.empty-table-state-cell h3 {
  font-size: 1.15rem;
  font-weight: 600;
  color: #334155;
  margin: 0 0 0.5rem 0;
}

.empty-table-state-cell p {
  font-size: 0.9rem;
  color: #64748b;
  margin: 0;
  max-width: 420px;
  display: inline-block;
}

/* Modal Popup Core Element Layout Layers Styles */
.modal-backdrop-layer {
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  background: rgba(15, 23, 42, 0.3);
  backdrop-filter: blur(4px);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 999;
}

.modal-form-window {
  background: #ffffff;
  border-radius: 0.75rem;
  width: 100%;
  max-width: 620px;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
  overflow: hidden;
  animation: modalEnter 0.2s ease-out;
}

@keyframes modalEnter {
  from { transform: scale(0.96); opacity: 0; }
  to { transform: scale(1); opacity: 1; }
}

.modal-form-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.25rem 2rem;
  border-bottom: 1px solid #e2e8f0;
  background-color: #f8fafc;
}

.modal-form-header h3 {
  font-size: 1.15rem;
  font-weight: 700;
  color: #0f172a;
  margin: 0;
}

.modal-dismiss-close-x {
  background: none;
  border: none;
  font-size: 1.75rem;
  color: #94a3b8;
  cursor: pointer;
  line-height: 1;
}
.modal-dismiss-close-x:hover { color: #475569; }

.modal-interactive-form {
  padding: 2rem;
}

.form-input-grid {
  display: grid;
  grid-template-columns: repeat(12, 1fr);
  gap: 1.25rem;
}

.form-field-block {
  display: flex;
  flex-direction: column;
}
.span-6 { grid-column: span 6; }
.span-12 { grid-column: span 12; }

.form-field-block label {
  font-size: 0.85rem;
  font-weight: 600;
  color: #334155;
  margin-bottom: 0.375rem;
}

.form-field-block input[type="text"],
.form-field-block input[type="number"],
.form-field-block select,
.form-field-block textarea {
  width: 100%;
  padding: 0.625rem 0.75rem;
  border: 1px solid #cbd5e1;
  border-radius: 0.375rem;
  box-sizing: border-box;
  font-size: 0.925rem;
  color: #0f172a;
  background-color: #ffffff;
  transition: border-color 0.15s, box-shadow 0.15s;
}

.form-field-block input:focus,
.form-field-block select:focus,
.form-field-block textarea:focus {
  outline: none;
  border-color: #2563eb;
  box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.field-input-error { border-color: #ef4444 !important; background-color: #fef2f2 !important; }
.field-error-msg-txt { color: #dc2626; font-size: 0.8rem; margin-top: 0.25rem; font-weight: 500; }
.required-flag { color: #ef4444; }
.field-info-help-txt { color: #64748b; font-size: 0.775rem; margin: 0.375rem 0 0 0; }

/* Drag Drop Styled Zone Interface */
.file-upload-drag-zone {
  position: relative;
  border: 2px dashed #cbd5e1;
  border-radius: 0.5rem;
  background-color: #f8fafc;
  padding: 1.5rem;
  text-align: center;
  transition: border-color 0.2s;
}
.file-upload-drag-zone:hover { border-color: #2563eb; }

.ghost-file-input {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  opacity: 0;
  cursor: pointer;
  z-index: 2;
}

.upload-zone-prompt {
  display: flex;
  flex-direction: column;
  align-items: center;
}

.upload-cloud-icon { font-size: 1.5rem; margin-bottom: 0.25rem; }
.upload-main-prompt-text { font-size: 0.875rem; font-weight: 600; color: #2563eb; }
.upload-subtext-constraints { font-size: 0.775rem; color: #64748b; margin-top: 0.125rem; }

.modal-action-footer-tray {
  display: flex;
  justify-content: flex-end;
  gap: 0.75rem;
  margin-top: 2rem;
  padding-top: 1.25rem;
  border-top: 1px solid #e2e8f0;
}

.tray-btn-primary {
  background-color: #2563eb;
  color: #ffffff;
  border: none;
  font-weight: 600;
  font-size: 0.9rem;
  padding: 0.625rem 1.25rem;
  border-radius: 0.375rem;
  cursor: pointer;
}
.tray-btn-primary:hover { background-color: #1d4ed8; }

.tray-btn-secondary {
  background-color: #ffffff;
  color: #475569;
  border: 1px solid #cbd5e1;
  font-weight: 600;
  font-size: 0.9rem;
  padding: 0.625rem 1.25rem;
  border-radius: 0.375rem;
  cursor: pointer;
}
.tray-btn-secondary:hover { background-color: #f8fafc; }

/* Custom Action Banner Notification Setup */
.custom-alert-banner {
  background-color: #ecfdf5;
  border: 1px solid #a7f3d0;
  color: #065f46;
  padding: 1rem 1.25rem;
  border-radius: 0.5rem;
  margin-bottom: 2rem;
  display: flex;
  align-items: center;
  gap: 0.75rem;
  font-size: 0.925rem;
  font-weight: 500;
  box-shadow: 0 2px 4px rgba(6, 95, 70, 0.02);
}

.alert-icon {
  background-color: #10b981;
  color: #ffffff;
  width: 20px;
  height: 20px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.75rem;
  font-weight: bold;
}

.fade-enter-active, .fade-leave-active { transition: opacity 0.3s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>