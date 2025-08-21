<template>
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">
            <li class="page-item" :class="{ disabled: currentPage === 1 }">
                <a class="page-link" href="#" @click.prevent="$emit('update:page', currentPage - 1)">Trước</a>
            </li>

            <li class="page-item" v-for="(page, index) in displayedPages" :key="index"
                :class="{ active: page === currentPage, disabled: page === null }">
                <a class="page-link" href="#" @click.prevent="page !== null && $emit('update:page', page)">
                    <span v-if="page !== null">{{ page }}</span>
                    <span v-else>...</span>
                </a>
            </li>

            <li class="page-item" :class="{ disabled: currentPage === totalPages }">
                <a class="page-link" href="#" @click.prevent="$emit('update:page', currentPage + 1)">Tiếp</a>
            </li>
        </ul>
    </nav>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    currentPage: {
        type: Number,
        required: true,
    },
    totalPages: {
        type: Number,
        required: true,
    },
});

const emit = defineEmits(['update:page']);

const displayedPages = computed(() => {
    const pages = [];
    const total = props.totalPages;
    const current = props.currentPage;
    const maxDisplayed = 5;

    if (total <= maxDisplayed) {
        for (let i = 1; i <= total; i++) {
            pages.push(i);
        }
    } else {
        const start = Math.max(2, current - 2);
        const end = Math.min(total - 1, current + 2);
        pages.push(1);
        if (start > 2) {
            pages.push(null);
        }
        for (let i = start; i <= end; i++) {
            pages.push(i);
        }
        if (end < total - 1) {
            pages.push(null);
        }
        pages.push(total);
    }
    return pages;
});
</script>

<style scoped>
/* Bạn có thể thêm các style tùy chỉnh tại đây nếu cần */
</style>