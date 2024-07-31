<template>
    <div class="col-lg-3 col-6">
        <div class="small-box" :class="boxClass">
            <div class="inner">
                <h3>{{ count }}</h3>
                <p>{{ title }}</p>
            </div>
            <div class="icon">
                <i :class="iconClass"></i>
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    count: {
        type: Number,
        required: true,
    },
    title: {
        type: String,
        required: true,
    },
    iconClass: {
        type: String,
        required: true,
    },
    boxClass: {
        type: String,
        default: "bg-danger",
    },
});
</script>
