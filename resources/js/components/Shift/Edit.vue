<script setup>
import { reactive, ref, onMounted } from "vue";
import { format, parse } from "date-fns";
import api from "./../../utils/api";

const props = defineProps({
  shift: {
    type: Object,
    required: true,
  },
});

const isSubmitted = ref(false);
const isLoading = ref(false);

const form = reactive({
  name: "",
  start_time: "",
  end_time: "",
});

onMounted(() => {
  const { name, start_time, end_time } = props.shift;
  form.name = name;
  form.start_time = format(parse(start_time, "HH:mm:ss", new Date()), "HH:mm");
  form.end_time = format(parse(end_time, "HH:mm:ss", new Date()), "HH:mm");
});

const submitForm = async () => {
  isSubmitted.value = true;

  if (form.name == "" || form.start_time == "" || form.end_time == "") return;

  try {
    isLoading.value = true;
    await api.put(`/api/shifts/${props.shift.id}`, form);
    window.toastr.success("Updated Successfully");
    isSubmitted.value = false;
    window.location.replace(`${api.baseURL}/shifts`);
  } catch (error) {
    console.log(error);
    window.toastr.error("Something went wrong");
  } finally {
    isLoading.value = false;
  }
};
</script>

<template>
  <form @submit.prevent="submitForm">
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
      <div class="invalid-feedback" v-if="isSubmitted && form.name == ''">Name is required.</div>
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
    <button type="submit" class="btn btn-primary">
      {{ isLoading ? "Updating..." : "Update" }}
    </button>
  </form>
</template>
