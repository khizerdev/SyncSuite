<template>
  <CountBox :count="counts.products" iconClass="ion ion-bag" title="Products" />
  <CountBox
    :count="counts.vendors"
    iconClass="ion ion-person"
    boxClass="bg-success"
    title="Vendors"
  />
  <CountBox
    :count="counts.customers"
    iconClass="ion ion-person"
    boxClass="bg-warning"
    title="Customers"
  />
  <CountBox
    :count="counts.employees"
    iconClass="ion ion-person"
    boxClass="bg-primary"
    title="Employees"
  />
</template>

<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";
import { baseUrl } from "../../utils/constants";
import CountBox from "./CountBox.vue";

const counts = ref({
  products: 0,
  vendors: 0,
  customers: 0,
  employees: 0,
});

onMounted(() => {
  console.log(baseUrl);
  axios
    .get(`${baseUrl}/api/products/count`)
    .then((response) => {
      counts.value = response.data;
    })
    .catch((error) => {
      console.error(error);
    });
});
</script>
