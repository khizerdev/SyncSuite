<script setup>
import { reactive, ref } from "vue";
import { baseUrl } from "../../utils/constants";

const shiftModal = ref(null);
let shiftModalInstance = null;
const isSubmitted = ref(false);
const isLoading = ref(false);

const openModal = () => {
  if (!shiftModalInstance) {
    shiftModalInstance = new bootstrap.Modal(shiftModal.value);
  }
  shiftModalInstance.show();
};

const closeModal = () => {
  console.log(isSubmitted.value);
  if (shiftModalInstance) {
    shiftModalInstance.hide();
  }
  form.name = "";
  form.start_time = "";
  form.end_time = "";
  isSubmitted.value = false;
};

const form = reactive({
  name: "",
  start_time: "",
  end_time: "",
});

const submitForm = async () => {
  console.log(baseUrl);
  isSubmitted.value = true;
  if (form.name == "" || start_time == "" || end_time == "") return;
  try {
    isLoading.value = true;
    await axios.post(`${baseUrl}/api/shifts`, form);
    window.toastr.success("Created Successfully");
    isSubmitted.value = false;
    window.location.replace(`${baseUrl}/shifts`);
  } catch (error) {
    window.toastr.error("Something went wrong");
  } finally {
    isLoading.value = false;
  }
};
</script>

<template>
  <div>
    <button class="btn btn-primary" @click="openModal">Add New Shift</button>
    <div
      class="modal fade"
      ref="shiftModal"
      tabindex="-1"
      role="dialog"
      aria-labelledby="shiftModalLabel"
      aria-hidden="true"
    >
      <div class="modal-dialog" role="document">
        <form class="modal-content" @submit.prevent="submitForm">
          <div class="modal-header">
            <h5 class="modal-title" id="shiftModalLabel">Create Shift</h5>
            <button type="button" class="close" @click="closeModal">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label for="name">Name</label>
              <input
                part="text"
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
              <label for="start_time">Start Time</label>
              <input
                type="time"
                v-model="form.start_time"
                class="form-control"
                id="start_time"
                :class="{
                  'is-invalid': isSubmitted && form.start_time == '',
                }"
              />
              <div class="invalid-feedback" v-if="isSubmitted && form.start_time == ''">
                Start Time is required.
              </div>
            </div>
            <div class="form-group">
              <label for="end_time">End Time</label>
              <input
                type="time"
                v-model="form.end_time"
                class="form-control"
                id="end_time"
                :class="{
                  'is-invalid': isSubmitted && form.end_time == '',
                }"
              />
              <div class="invalid-feedback" v-if="isSubmitted && form.end_time == ''">
                End Time is required.
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" @click="closeModal">Close</button>
            <button type="submit" class="btn btn-primary">
              {{ isLoading ? "Creating..." : "Create" }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>
