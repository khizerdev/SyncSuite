<script setup>
import { reactive, ref } from "vue";
import api from "./../../../api";

const initialForm = {
  ref_number: "",
  party: "",
  department: "",
  date: "",
  description: "",
  items: [
    {
      particular: "",
      qty: "",
      remarks: "",
      errors: {
        particular: "",
        qty: "",
        remarks: "",
      },
    },
  ],
};

const form = reactive({ ...initialForm });

const isSubmitted = ref(false);

const addItem = () => {
  form.items.push({
    particular: "",
    qty: "",
    remarks: "",
    errors: {
      particular: "",
      qty: "",
      remarks: "",
    },
  });
};

const removeItem = (index) => {
  if (index > 0) {
    form.items.splice(index, 1);
  }
};

const validateRow = (row) => {
  row.errors.particular = row.particular ? "" : "Particular is required.";
  row.errors.qty = !row.qty || row.qty <= 0 ? "Qty is required and must be greater than zero." : "";
  row.errors.remarks = row.remarks ? "" : "Remarks are required.";
  return !row.errors.particular && !row.errors.qty && !row.errors.remarks;
};

const submitForm = async () => {
  isSubmitted.value = true;
  let isValid = true;
  form.items.forEach((row) => {
    if (!validateRow(row)) {
      isValid = false;
    }
  });
  if (
    form.ref_number == "" ||
    form.party == "" ||
    form.department == "" ||
    form.date == "" ||
    form.description == "" ||
    !isValid
  )
    return;

  try {
    await api.post(`/api/inward-general/store`, form);
    window.toastr.success("Created Successfully");
    isSubmitted.value = false;
    window.location.replace(`${api.baseURL}/inward-general`);
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
              <div class="row">
                <div class="col-md-6 col-12">
                  <div class="mb-3">
                    <label for="ref_number" class="form-label">Reference Number</label>
                    <input
                      v-model="form.ref_number"
                      type="text"
                      class="form-control"
                      id="ref_number"
                      :class="{
                        'is-invalid': isSubmitted && form.ref_number == '',
                      }"
                    />
                    <div class="invalid-feedback" v-if="isSubmitted && form.ref_number == ''">
                      Reference Number is required.
                    </div>
                  </div>
                </div>
                <div class="col-md-6 col-12">
                  <div class="mb-3">
                    <label for="party" class="form-label">Party</label>
                    <input
                      v-model="form.party"
                      type="text"
                      class="form-control"
                      id="party"
                      :class="{
                        'is-invalid': isSubmitted && form.party == '',
                      }"
                    />
                    <div class="invalid-feedback" v-if="isSubmitted && form.party == ''">
                      Party is required.
                    </div>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6 col-12">
                  <div class="mb-3">
                    <label for="department" class="form-label">Department</label>
                    <input
                      v-model="form.department"
                      type="text"
                      class="form-control"
                      id="department"
                      :class="{
                        'is-invalid': isSubmitted && form.department == '',
                      }"
                    />
                    <div class="invalid-feedback" v-if="isSubmitted && form.department == ''">
                      Department is required.
                    </div>
                  </div>
                </div>
                <div class="col-md-6 col-12">
                  <div class="mb-3">
                    <label for="date" class="form-label">Date</label>
                    <input
                      v-model="form.date"
                      type="date"
                      class="form-control"
                      id="date"
                      :class="{
                        'is-invalid': isSubmitted && form.date == '',
                      }"
                    />
                    <div class="invalid-feedback" v-if="isSubmitted && form.date == ''">
                      Date is required.
                    </div>
                  </div>
                </div>
              </div>

              <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <input
                  v-model="form.description"
                  type="text"
                  class="form-control"
                  id="description"
                  :class="{
                    'is-invalid': isSubmitted && form.description == '',
                  }"
                />
                <div class="invalid-feedback" v-if="isSubmitted && form.description == ''">
                  Description is required.
                </div>
              </div>

              <div v-for="(item, index) in form.items" :key="index" class="row mb-3">
                <div class="col-3">
                  <input
                    v-model="item.particular"
                    type="text"
                    class="form-control"
                    placeholder="Particular"
                    :class="{ 'is-invalid': item.errors.particular }"
                  />
                  <div class="invalid-feedback" v-if="item.errors.particular">
                    {{ item.errors.particular }}
                  </div>
                </div>
                <div class="col-3">
                  <input
                    v-model="item.qty"
                    type="number"
                    class="form-control"
                    placeholder="Quantity"
                    :class="{ 'is-invalid': item.errors.qty }"
                  />
                  <div class="invalid-feedback" v-if="item.errors.qty">
                    {{ item.errors.qty }}
                  </div>
                </div>
                <div class="col-4">
                  <input
                    v-model="item.remarks"
                    type="text"
                    class="form-control"
                    placeholder="Remarks"
                    :class="{ 'is-invalid': item.errors.remarks }"
                  />
                  <div class="invalid-feedback" v-if="item.errors.remarks">
                    {{ item.errors.qty }}
                  </div>
                </div>
                <div class="col-2" v-if="index > 0">
                  <button type="button" class="btn btn-danger" @click="removeItem(index)">
                    Remove
                  </button>
                </div>
                <div class="col-2" v-else>
                  <button type="button" class="btn btn-secondary mb-3 w-100" @click="addItem">
                    Add Item
                  </button>
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
