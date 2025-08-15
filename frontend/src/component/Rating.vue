<template>
    <ul class="review p-0">
        <!-- Sao đầy -->
        <li v-for="n in fullStars" :key="'full-' + n">
            <i class="bi bi-star-fill text-warning"></i>
        </li>

        <!-- Sao nửa -->
        <li v-if="hasHalfStar">
            <i class="bi bi-star-half text-warning"></i>
        </li>

        <!-- Sao rỗng -->
        <li v-for="n in emptyStars" :key="'empty-' + n">
            <i class="bi bi-star text-warning"></i>
        </li>

        <!-- Số review (nếu truyền vào) -->
        <li v-if="reviewCount !== null">
            <span>{{ rating }} Review(s)</span>
        </li>
    </ul>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    rating: { // trung bình số sao
        type: Number,
        required: true,
        default: 0
    },
    reviewCount: { // số review (có thể null)
        type: Number,
        default: null
    }
});

const fullStars = computed(() => Math.floor(props.rating));
const hasHalfStar = computed(() => props.rating - fullStars.value >= 0.5);
const emptyStars = computed(() => 5 - fullStars.value - (hasHalfStar.value ? 1 : 0));
</script>

<style scoped>
.review {
    margin-top: 5px;
}
.review li {
    display: inline-block;
}
.review li i {
    color: #fecb00;
    font-size: 13px;
}
.review li span {
    display: inline-block;
    margin-left: 4px;
    color: #888;
    font-size: 13px;
}
</style>
