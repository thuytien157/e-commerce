<template>
    <ul class="review p-0">
        <li v-for="star in fullStars" :key="'fill-' + star">
            <i class="bi bi-star-fill text-warning"></i>
        </li>

        <li v-for="star in emptyStars" :key="'empty-' + star">
            <i class="bi bi-star-half text-warning"></i>
        </li>

        <li><span>{{ averageRating.toFixed(1) }} Review(s)</span></li>
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

const averageRating = computed(() => {
    if (!props.ratings || props.ratings.length === 0) {
        return 0;
    }
    const sum = props.ratings.reduce((total, score) => total + score, 0);
    return sum / props.ratings.length;
});

const fullStars = computed(() => Math.min(5, Math.max(0, Math.round(averageRating.value))));
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