<script setup>
import { onMounted, computed, reactive, ref } from "vue";
import { baseUrl } from "../../utils/constants";

const isSubmitted = ref(false);

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

const departments = reactive([]);
const productTypes = reactive([]);
const materials = reactive([]);
const particulars = reactive([]);

const getDepartments = async () => {
  try {
    const response = await axios.get(`${baseUrl}/api/departments`);
    departments.push(...response.data);
  } catch (error) {
    console.error(error);
  }
};

const getProductTypes = async () => {
  try {
    const response = await axios.get(`${baseUrl}/api/product-types`);
    productTypes.push(...response.data);
  } catch (error) {
    console.error(error);
  }
};

const getParticulars = async () => {
  try {
    const response = await axios.get(`${baseUrl}/api/particulars`);
    particulars.splice(0, particulars.length, ...response.data);
  } catch (error) {
    console.error(error);
  }
};

const getMaterials = async () => {
  try {
    if (!product.particular_id) return;
    const response = await axios.get(`${baseUrl}/api/particular-materials/${product.particular_id}`);
    materials.push(...response.data);
  } catch (error) {
    console.error(error);
  }
};

onMounted(() => {
  getDepartments();
  getProductTypes();
  getParticulars();
});

const totalPrice = computed(() => {
  return product.qty * product.inventory_price || 0;
});

const submitForm = async () => {
  isSubmitted.value = true;
  const form = { ...product, total_price: totalPrice.value };
  try {
    await axios.post(`${baseUrl}/api/products/store`, form);
    window.toastr.success("Created Successfully");
    isSubmitted.value = false;
    window.location.replace(`${baseUrl}/products`);
  } catch (error) {
    console.log(error);
    if (error.response && error.response.status === 422) {
      window.toastr.error("Something went wrong");
    } else {
      window.toastr.error("Something went wrong");
    }
  }
};
</script>

<template>
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12">
        <div class="card card-secondary">
          <div class="card-header">
            <h3 class="card-title">Create</h3>
          </div>
          <div class="card-body">
            <form @submit.prevent="submitForm">
              <div class="form-group">
                <label for="name">Product Name</label>
                <input
                  type="text"
                  v-model="product.name"
                  class="form-control"
                  id="name"
                  :class="{
                    'is-invalid': isSubmitted && product.name == '',
                  }"
                  required
                />
                <div class="invalid-feedback" v-if="isSubmitted && product.name == ''">
                  Name is required.
                </div>
              </div>
              <div class="form-group">
                <label for="department">Department</label>
                <select
                  v-model="product.department_id"
                  class="form-control"
                  id="department"
                  :class="{
                    'is-invalid': isSubmitted && product.department_id == '',
                  }"
                  required
                >
                  <option value="" disabled>Select Department</option>
                  <option v-for="dept in departments" :key="dept.id" :value="dept.id">
                    {{ dept.name }}
                  </option>
                </select>
                <div class="invalid-feedback" v-if="isSubmitted && product.name == ''">
                  Department is required.
                </div>
              </div>
              <div class="form-group">
                <label for="productType">Product Type</label>
                <select
                  v-model="product.product_type_id"
                  class="form-control"
                  id="productType"
                  :class="{
                    'is-invalid': isSubmitted && product.product_type_id == '',
                  }"
                  required
                >
                  <option value="" disabled>Select Product Type</option>
                  <option v-for="type in productTypes" :key="type.id" :value="type.id">
                    {{ type.name }}
                  </option>
                </select>
                <div class="invalid-feedback" v-if="isSubmitted && product.product_type_id == ''">
                  Product Type is required.
                </div>
                <p class="mt-2" @click="openModal">Create Product Type</p>
              </div>
              <div class="form-group">
                <label for="particular">Particular</label>
                <select
                  v-model="product.particular_id"
                  @change="getMaterials()"
                  class="form-control"
                  id="particular"
                  :class="{
                    'is-invalid': isSubmitted && product.particular_id == '',
                  }"
                  required
                >
                  <option value="" disabled>Select Particular</option>
                  <option v-for="part in particulars" :key="part.id" :value="part.id">
                    {{ part.name }}
                  </option>
                </select>
                <div class="invalid-feedback" v-if="isSubmitted && product.particular_id == ''">
                  Particular is required.
                </div>
              </div>
              <div class="form-group">
                <label for="material">Material</label>
                <select
                  v-model="product.material_id"
                  class="form-control"
                  id="material"
                  :class="{
                    'is-invalid': isSubmitted && product.material_id == '',
                  }"
                  required
                >
                  <option value="" disabled>Select Material</option>
                  <option v-for="mat in materials" :key="mat.id" :value="mat.id">
                    {{ mat.name }}
                  </option>
                </select>
                <div class="invalid-feedback" v-if="isSubmitted && product.material_id == ''">
                  Material is required.
                </div>
              </div>

              <div class="form-group">
                <label for="openingQuantity">Opening Quantity</label>
                <input
                  type="number"
                  v-model="product.qty"
                  class="form-control"
                  id="openingQuantity"
                  :class="{
                    'is-invalid': isSubmitted && product.qty == null,
                  }"
                  required
                />
                <div class="invalid-feedback" v-if="isSubmitted && product.qty == null">
                  Quantity is required.
                </div>
              </div>
              <div class="form-group">
                <label for="openingInventory">Opening Inventory Price</label>
                <input
                  type="number"
                  v-model="product.inventory_price"
                  class="form-control"
                  id="openingInventory"
                  :class="{
                    'is-invalid': isSubmitted && product.inventory_price == null,
                  }"
                  required
                />
                <div class="invalid-feedback" v-if="isSubmitted && product.inventory_price == null">
                  Price is required.
                </div>
              </div>
              <div class="form-group">
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
              <div class="form-group">
                <label for="min_qty_limit">Min Quantity Limit</label>
                <input
                  type="text"
                  v-model="product.min_qty_limit"
                  class="form-control"
                  id="min_qty_limit"
                  :class="{
                    'is-invalid': isSubmitted && product.min_qty_limit == null,
                  }"
                  required
                />
                <div class="invalid-feedback" v-if="isSubmitted && product.min_qty_limit == null">
                  Min Quantity is required.
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
