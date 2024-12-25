<script setup>
import { onMounted, reactive, ref } from "vue";
import api from "./../../utils/api";

const emit = defineEmits(["get-materials", "update-materialId", "update-particularId"]);

const materialModal = ref(null);
let materialModalInstance = null;

const openModal = () => {
  if (!materialModalInstance) {
    materialModalInstance = new bootstrap.Modal(materialModal.value);
  }
  materialModalInstance.show();
};

const closeModal = () => {
  if (materialModalInstance) {
    materialModalInstance.hide();
  }
  form.name = "";
  form.particular_id = "";
};

const form = reactive({
  name: "",
  particular_id: "",
});

const isSubmitted = ref(false);

const particulars = reactive([]);

const getParticulars = async () => {
  try {
    const response = await api.get(`/api/particulars`);
    console.log(response.data);
    particulars.splice(0, particulars.length, ...response.data);
  } catch (error) {
    console.error(error);
  }
};

onMounted(() => {
  getParticulars();
});

const onSubmit = async () => {
  isSubmitted.value = true;
  if (form.name == "" || form.particular_id == "") return;
  try {
    await api.post(`/api/materials/store`, form);
    window.toastr.success("Created Successfully");
    isSubmitted.value = false;
    emit("get-materials");
    emit("update-materialId", "");
    emit("update-particularId", "");
    closeModal();
  } catch (error) {
    console.log(error);
    window.toastr.error("Something went wrong");
  }
};

defineExpose({ openModal, getParticulars });
</script>

<template>
  <div
    class="modal fade"
    ref="materialModal"
    tabindex="-1"
    role="dialog"
    aria-labelledby="materialModalLabel"
    aria-hidden="true"
  >
    <div class="modal-dialog" role="document">
      <form class="modal-content" @submit.prevent="onSubmit">
        <div class="modal-header">
          <h5 class="modal-title" id="materialModalLabel">Create Material</h5>
          <button
            type="button"
            class="close"
            data-bs-dismiss="modal"
            aria-label="Close"
            @click="closeModal"
          >
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label for="name">Name</label>
            <input
              type="text"
              v-model="form.name"
              class="form-control"
              id="name"
              :class="{
                'is-invalid': isSubmitted && form.name == '',
              }"
            />
            <div class="invalid-feedback" v-if="isSubmitted && form.name == ''">
              Name is required.
            </div>
          </div>
          <div class="form-group">
            <label for="particular">Particular</label>
            <select
              v-model="form.particular_id"
              class="form-control"
              id="particular"
              :class="{
                'is-invalid': isSubmitted && form.particular_id == '',
              }"
            >
              <option value="" disabled>Select Particular</option>
              <option v-for="part in particulars" :key="part.id" :value="part.id">
                {{ part.name }}
              </option>
            </select>
            <div class="invalid-feedback" v-if="isSubmitted && form.particular_id == ''">
              Particular is required.
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
