<template>
    <ul class="review p-0">
        <li v-for="star in fullStars" :key="'fill-' + star">
            <i class="bi bi-star-fill text-warning"></i>
        </li>

        <li v-for="star in emptyStars" :key="'empty-' + star">
            <i class="bi bi-star-half text-warning"></i>
        </li>

        <li><span>{{ ratings }} Review(s)</span></li>
    </ul>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    ratings: {
        type: Array,
        required: true,
        default: () => []
    }
});

const fullStars = computed(() => Math.min(5, Math.max(0, Math.round(props.ratings))));
const emptyStars = computed(() => 5 - fullStars.value);
</script>
<style>
.single-product .product-info .review {
    margin-top: 5px;
}

.single-product .product-info .review li {
    display: inline-block;
}

.single-product .product-info .review li i {
    color: #fecb00;
    font-size: 13px;
}

.single-product .product-info .review li span {
    display: inline-block;
    margin-left: 4px;
    color: #888;
    font-size: 13px;
}
</style>