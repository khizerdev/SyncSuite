<script setup>
import { reactive, ref } from "vue";
import { baseUrl } from "../../utils/constants";

const emit = defineEmits([
  "get-pariculars",
  "update-particularId",
  "fetch-material-modal-particular",
]);

const partModal = ref(null);
let partModalInstance = null;

const openModal = () => {
  if (!partModalInstance) {
    partModalInstance = new bootstrap.Modal(partModal.value);
  }
  partModalInstance.show();
};

defineExpose({ openModal });

const closeModal = () => {
  if (partModalInstance) {
    partModalInstance.hide();
  }
  partForm.name = "";
  isSubmitted.value = false;
};

const partForm = reactive({
  name: "",
});

const isSubmitted = ref(false);

const submitpartForm = async () => {
  isSubmitted.value = true;
  if (partForm.name == "") return;
  try {
    await axios.post(`${baseUrl}/api/particulars/store`, partForm);
    window.toastr.success("Created Successfully");
    isSubmitted.value = false;
    emit("fetch-material-modal-particular");
    emit("get-pariculars");
    emit("update-particularId", "");
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
    ref="partModal"
    tabindex="-1"
    role="dialog"
    aria-labelledby="partModalLabel"
    aria-hidden="true"
  >
    <div class="modal-dialog" role="document">
      <form class="modal-content" @submit.prevent="submitpartForm">
        <div class="modal-header">
          <h5 class="modal-title" id="partModalLabel">Create Particular</h5>
          <button
            part="button"
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
              part="text"
              v-model="partForm.name"
              class="form-control"
              id="name"
              :class="{
                'is-invalid': isSubmitted && partForm.name == '',
              }"
            />
            <div class="invalid-feedback" v-if="isSubmitted && partForm.name == ''">
              Name is required.
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button part="button" class="btn btn-secondary" @click="closeModal">Close</button>
          <button part="submit" class="btn btn-primary">Create</button>
        </div>
      </form>
    </div>
  </div>
</template>
