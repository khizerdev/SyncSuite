<script setup>
import { onMounted, reactive, ref } from "vue";
import api from "./../../utils/api";

const emit = defineEmits(["get-product-types", "update-productTypeId"]);

const typeModal = ref(null);
let typeModalInstance = null;

const openModal = () => {
  if (!typeModalInstance) {
    typeModalInstance = new bootstrap.Modal(typeModal.value);
  }
  typeModalInstance.show();
};

defineExpose({ openModal });

const closeModal = () => {
  if (typeModalInstance) {
    typeModalInstance.hide();
  }
  typeForm.name = "";
  typeForm.material_id = "";
  typeForm.particular_id = "";
};

const typeForm = reactive({
  name: "",
  material_id: "",
  particular_id: "",
});

const isSubmitted = ref(false);

const materials = reactive([]);
const particulars = reactive([]);

const getMaterials = async () => {
  console.log("reaching");
  try {
    const response = await api.get(`/api/materials`);
    materials.splice(0, materials.length, ...response.data);
  } catch (error) {
    console.error(error);
  }
};

const getParticulars = async () => {
  try {
    if (!typeForm.material_id) return;
    const response = await api.get(`/getParticulars/${typeForm.material_id}`);
    particulars.splice(0, particulars.length, ...response.data);
    typeForm.particular_id = "";
  } catch (error) {
    console.error(error);
  }
};

onMounted(() => {
  getMaterials();
});

const submitTypeForm = async () => {
  isSubmitted.value = true;
  if (typeForm.material_id == "" || typeForm.name == "" || typeForm.particular_id == "") return;
  try {
    await api.post(`/api/productsType/store`, typeForm);
    window.toastr.success("Created Successfully");
    isSubmitted.value = false;
    emit("get-product-types");
    emit("update-productTypeId", "");
    closeModal();
  } catch (error) {
    console.log(error);
    window.toastr.error("Something went wrong");
  }
};
</script>

<template>
  <div
    class="modal fade"
    ref="typeModal"
    tabindex="-1"
    role="dialog"
    aria-labelledby="typeModalLabel"
    aria-hidden="true"
  >
    <div class="modal-dialog" role="document">
      <form class="modal-content" @submit.prevent="submitTypeForm">
        <div class="modal-header">
          <h5 class="modal-title" id="typeModalLabel">Create Product Type</h5>
          <button
            type="button"
            class="close"
            data-bs-dismiss="modal"
            aria-label="Close"
            @click="closeTypeModal"
          >
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label for="name">Name</label>
            <input
              type="text"
              v-model="typeForm.name"
              class="form-control"
              id="name"
              :class="{
                'is-invalid': isSubmitted && typeForm.name == '',
              }"
              required
            />
            <div class="invalid-feedback" v-if="isSubmitted && typeForm.name == ''">
              Name is required.
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <label for="Material">Material</label>
              <select
                v-model="typeForm.material_id"
                class="form-control"
                id="material"
                :class="{
                  'is-invalid': isSubmitted && typeForm.material_id == '',
                }"
                @change="getParticulars()"
                required
              >
                <option value="" disabled>Select Material</option>
                <option v-for="mat in materials" :key="mat.id" :value="mat.id">
                  {{ mat.name }}
                </option>
              </select>
              <div class="invalid-feedback" v-if="isSubmitted && typeForm.material_id == ''">
                Material is required.
              </div>
            </div>
            <div class="col-md-6">
              <label for="particular">Particular</label>
              <select
                v-model="typeForm.particular_id"
                class="form-control"
                id="particular"
                :class="{
                  'is-invalid': isSubmitted && typeForm.particular_id == '',
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
              <div class="invalid-feedback" v-if="isSubmitted && typeForm.particular_id == ''">
                Particular is required.
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" @click="closeModal">Close</button>
          <button type="submit" class="btn btn-primary">Create</button>
        </div>
      </form>
    </div>
  </div>
</template>
