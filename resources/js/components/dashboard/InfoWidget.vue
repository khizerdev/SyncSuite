<template>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ totalProducts }}</h3>
                <p>Products</p>
            </div>
            <div class="icon">
                <i class="ion ion-bag"></i>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";

const totalProducts = ref(0);

onMounted(() => {
    axios
        .get("/api/products/count")
        .then((response) => {
            totalProducts.value = response.data.count;
        })
        .catch((error) => {
            console.error(error);
        });
});
</script>
