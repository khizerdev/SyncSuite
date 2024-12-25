<script setup>
import { onMounted, computed, reactive, ref } from "vue";
import TypeModal from "./TypeModal.vue";
import MaterialModal from "./MaterialModal.vue";
import ParticularModal from "./ParticularModal.vue";
import api from "./../../../api";

const isFormSubmitted = ref(false);
const product = reactive({
  name: "",
  department_id: "",
  product_type_id: "",
  material_id: "",
  particular_id: "",
  qty: null,
  inventory_price: null,
  min_qty_limit: null,
});

const typeModalRef = ref(null);
const openTypeModal = () => {
  typeModalRef.value.openModal();
};

const materialModalRef = ref(null);
const openMaterialModal = () => {
  materialModalRef.value.openModal();
};

const particularModalRef = ref(null);
const openParticularModal = () => {
  particularModalRef.value.openModal();
};

const fetchMaterialModalParticulars = () => {
  materialModalRef.value.getParticulars();
};

const departments = reactive([]);
const productTypes = reactive([]);
const materials = reactive([]);
const particulars = reactive([]);

const getDepartments = async () => {
  try {
    const response = await api.get(`/api/departments`);
    departments.push(...response.data);
  } catch (error) {
    console.error(error);
  }
};

const getProductTypes = async () => {
  try {
    const response = await api.get(`/api/product-types`);
    productTypes.splice(0, productTypes.length, ...response.data);
  } catch (error) {
    console.error(error);
  }
};

const getParticulars = async () => {
  try {
    if (!product.material_id) return;
    const response = await api.get(`/getParticulars/${product.material_id}`);
    particulars.splice(0, particulars.length, ...response.data);
  } catch (error) {
    console.error(error);
  }
};

const getMaterials = async () => {
  try {
    const response = await api.get(`/api/materials`);
    materials.splice(0, materials.length, ...response.data);
  } catch (error) {
    console.error(error);
  }
};

onMounted(() => {
  getDepartments();
  getProductTypes();
  getMaterials();
});

const totalPrice = computed(() => {
  return product.qty * product.inventory_price || 0;
});

const submitForm = async () => {
  isFormSubmitted.value = true;
  const form = { ...product, total_price: totalPrice.value };
  try {
    await api.post(`/api/products/store`, form);
    window.toastr.success("Created Successfully");
    isFormSubmitted.value = false;
    window.location.reload();
  } catch (error) {
    console.log(error);
    window.toastr.error("Something went wrong");
  }
};
</script>

<template>
  <TypeModal
    ref="typeModalRef"
    @get-product-types="getProductTypes"
    @update-productTypeId="product.product_type_id = $event"
  />
  <MaterialModal
    ref="materialModalRef"
    @get-materials="getMaterials"
    @update-materialId="product.material_id = $event"
    @update-particularId="product.particular_id = $event"
  />
  <ParticularModal
    ref="particularModalRef"
    @get-pariculars="getParticulars"
    @update-particularId="product.particular_id = $event"
    @fetch-material-modal-particular="fetchMaterialModalParticulars"
  />

  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12">
        <div class="card card-secondary">
          <div class="card-header">
            <h3 class="card-title">Create</h3>
          </div>
          <div class="card-body">
            <form @submit.prevent="submitForm">
              <div class="row mb-2">
                <div class="col-md-6">
                  <label for="name">Product Name</label>
                  <input
                    type="text"
                    v-model="product.name"
                    class="form-control"
                    id="name"
                    :class="{
                      'is-invalid': isFormSubmitted && product.name == '',
                    }"
                    required
                  />
                  <div class="invalid-feedback" v-if="isFormSubmitted && product.name == ''">
                    Name is required.
                  </div>
                </div>
                <div class="col-md-6">
                  <label for="department">Department</label>
                  <select
                    v-model="product.department_id"
                    class="form-control"
                    id="department"
                    :class="{
                      'is-invalid': isFormSubmitted && product.department_id == '',
                    }"
                    required
                  >
                    <option value="" disabled>Select Department</option>
                    <option v-for="dept in departments" :key="dept.id" :value="dept.id">
                      {{ dept.name }}
                    </option>
                  </select>
                  <div class="invalid-feedback" v-if="isFormSubmitted && product.name == ''">
                    Department is required.
                  </div>
                </div>
              </div>
              <div class="row mb-2">
                <div class="col-md-4">
                  <label for="productType">Product Type</label>
                  <select
                    v-model="product.product_type_id"
                    class="form-control"
                    id="productType"
                    :class="{
                      'is-invalid': isFormSubmitted && product.product_type_id == '',
                    }"
                    required
                  >
                    <option value="" disabled>Select Product Type</option>
                    <option v-for="type in productTypes" :key="type.id" :value="type.id">
                      {{ type.name }}
                    </option>
                  </select>
                  <div
                    class="invalid-feedback"
                    v-if="isFormSubmitted && product.product_type_id == ''"
                  >
                    Product Type is required.
                  </div>
                  <p class="mt-2 text-sm cursor-pointer" @click="openTypeModal">
                    Create Product Type
                  </p>
                </div>
                <div class="col-md-4">
                  <label for="material">Material</label>
                  <select
                    v-model="product.material_id"
                    class="form-control"
                    id="material"
                    :class="{
                      'is-invalid': isFormSubmitted && product.material_id == '',
                    }"
                    required
                    @change="getParticulars"
                  >
                    <option value="" disabled>Select Material</option>
                    <option v-for="mat in materials" :key="mat.id" :value="mat.id">
                      {{ mat.name }}
                    </option>
                  </select>
                  <div class="invalid-feedback" v-if="isFormSubmitted && product.material_id == ''">
                    Material is required.
                  </div>
                  <p class="mt-2 text-sm cursor-pointer" @click="openMaterialModal">
                    Create Material
                  </p>
                </div>
                <div class="col-md-4">
                  <label for="particular">Particular</label>
                  <select
                    v-model="product.particular_id"
                    @change="getMaterials()"
                    class="form-control"
                    id="particular"
                    :class="{
                      'is-invalid': isFormSubmitted && product.particular_id == '',
                    }"
                    required
                  >
                    <option value="" disabled>Select Particular</option>
                    <option
                      v-for="part in particulars"
                      :key="part.particular.id"
                      :value="part.particular.id"
                    >
                      {{ part.particular.name }}
                    </option>
                  </select>
                  <div
                    class="invalid-feedback"
                    v-if="isFormSubmitted && product.particular_id == ''"
                  >
                    Particular is required.
                  </div>
                  <p class="mt-2 text-sm cursor-pointer" @click="openParticularModal">
                    Create Particular
                  </p>
                </div>
              </div>
              <div class="row mb-4">
                <div class="col-md-3">
                  <label for="openingQuantity">Opening Quantity</label>
                  <input
                    type="number"
                    v-model="product.qty"
                    class="form-control"
                    id="openingQuantity"
                    :class="{
                      'is-invalid': isFormSubmitted && product.qty == null,
                    }"
                    required
                  />
                  <div class="invalid-feedback" v-if="isFormSubmitted && product.qty == null">
                    Quantity is required.
                  </div>
                </div>
                <div class="col-md-3">
                  <label for="openingInventory">Opening Inventory Price</label>
                  <input
                    type="number"
                    v-model="product.inventory_price"
                    class="form-control"
                    id="openingInventory"
                    :class="{
                      'is-invalid': isFormSubmitted && product.inventory_price == null,
                    }"
                    required
                  />
                  <div
                    class="invalid-feedback"
                    v-if="isFormSubmitted && product.inventory_price == null"
                  >
                    Price is required.
                  </div>
                </div>
                <div class="col-md-3">
                  <label for="totalPrice">Total Price</label>
                  <input
                    type="number"
                    v-model="totalPrice"
                    class="form-control"
                    id="totalPrice"
                    required
                    readonly
                  />
                </div>
                <div class="col-md-3">
                  <label for="min_qty_limit">Min Quantity Limit</label>
                  <input
                    type="text"
                    v-model="product.min_qty_limit"
                    class="form-control"
                    id="min_qty_limit"
                    :class="{
                      'is-invalid': isFormSubmitted && product.min_qty_limit == null,
                    }"
                    required
                  />
                  <div
                    class="invalid-feedback"
                    v-if="isFormSubmitted && product.min_qty_limit == null"
                  >
                    Min Quantity is required.
                  </div>
                </div>
              </div>

              <button type="submit" class="btn btn-primary">Submit</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style>
/* Add any additional custom styles here */
</style>
