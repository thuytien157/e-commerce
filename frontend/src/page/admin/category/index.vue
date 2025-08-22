<script setup>
import axios from "axios";
import Swal from "sweetalert2";
import { onMounted, ref, computed, watch } from "vue";
import Pagination from "@/component/admin/Pagination.vue";

const allCategories = ref([]);
const searchQuery = ref("");
const pageSize = ref(5);
const currentPage = ref(1);
const expandedRows = ref({});
const isLoading = ref(true);

const getAllCategories = async () => {
    try {
        const res = await axios.get("http://127.0.0.1:8000/api/category");
        allCategories.value = res.data.categories;
    } catch (error) {
        console.log(error);
    } finally {
        isLoading.value = false;
    }
};

const filteredCategories = computed(() => {
    return allCategories.value.filter((category) => {
        const lowerCaseSearch = searchQuery.value.toLowerCase();
        const issearchQuery =
            !searchQuery.value ||
            category.name.toLowerCase().includes(lowerCaseSearch);
        return issearchQuery;
    });
});

const paginatedAndFilteredCategories = computed(() => {
    const startIndex = (currentPage.value - 1) * pageSize.value;
    const endIndex = startIndex + pageSize.value;
    return filteredCategories.value.slice(startIndex, endIndex);
});

const totalPages = computed(() => {
    return Math.ceil(filteredCategories.value.length / pageSize.value);
});

const updateCurrentPage = (page) => {
    if (page >= 1 && page <= totalPages.value) {
        currentPage.value = page;
    }
};

const toggleChildren = (categoryId) => {
    expandedRows.value[categoryId] = !expandedRows.value[categoryId];
};

const deleteCategory = async (id) => {
    try {
        const result = await Swal.fire({
            title: "Bạn có chắc muốn xoá danh mục này?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Xác nhận",
            cancelButtonText: "Hủy",
        });

        if (result.isConfirmed) {
            isLoading.value = true;
            await axios.delete(`http://127.0.0.1:8000/api/category/${id}`);
            await getAllCategories();
            Swal.fire({
                toast: true,
                position: "top-end",
                icon: "success",
                title: "Danh mục đã được xóa thành công!",
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true,
            });
        }
    } catch (error) {
        console.log(error);
        Swal.fire({
            toast: true,
            position: "top-end",
            icon: "error",
            title: "Xóa danh mục không thành công!",
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true,
        });
    } finally {
        isLoading.value = false;
    }
};

watch([searchQuery, pageSize], () => {
    currentPage.value = 1;
});

onMounted(async () => {
    isLoading.value = true;
    await getAllCategories();
});
</script>
<template>
    <div class="d-flex justify-content-between">
        <h3 class="text-primary fw-bold">Danh sách danh mục</h3>

        <router-link to="/admin/category/insert" type="button" class="btn btn-primary h-25">Thêm danh mục</router-link>
    </div>
    <div class="d-flex gap-3 flex-wrap mb-2">
        <div>
            <label for="search-input" class="form-label">Tìm kiếm danh mục</label>
            <input type="text" id="search-input" class="form-control" placeholder="Nhập từ khoá..."
                v-model="searchQuery" />
        </div>

        <div>
            <label for="pagination-select" class="form-label">Phân trang</label><br />
            <select id="pagination-select" class="form-select" v-model="pageSize">
                <option :value="5">5</option>
                <option :value="10">10</option>
                <option :value="15">15</option>
                <option :value="20">20</option>
            </select>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th style="width: 50px;"></th>
                    <th>ID</th>
                    <th>Tên danh mục</th>
                    <th>Danh mục cha</th>
                    <th>Tổng sản phẩm</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <tr v-if="isLoading" v-for="n in pageSize" :key="n">
                    <td>
                        <div class="skeleton-box" style="width: 20px;"></div>
                    </td>
                    <td>
                        <div class="skeleton-box"></div>
                    </td>
                    <td>
                        <div class="skeleton-box"></div>
                    </td>
                    <td>
                        <div class="skeleton-box"></div>
                    </td>
                    <td>
                        <div class="skeleton-box"></div>
                    </td>
                    <td>
                        <div class="skeleton-box"></div>
                    </td>
                </tr>

                <template v-else-if="paginatedAndFilteredCategories.length > 0"
                    v-for="category in paginatedAndFilteredCategories" :key="category.id">
                    <tr>
                        <td>
                            <button v-if="category.children && category.children.length > 0"
                                @click="toggleChildren(category.id)" class="btn btn-sm">
                                <i class="bi bi-caret-right-fill"
                                    :class="{ 'rotate-icon': expandedRows[category.id] }"></i>
                            </button>
                        </td>
                        <td>{{ category.id }}</td>
                        <td>{{ category.name }}</td>
                        <td>{{ category.parent_name || "Không có" }}</td>
                        <td>{{ category.all_products_count }}</td>
                        <td>
                            <div class="d-flex gap-2">
                                <router-link :to="`/admin/category/edit/${category.id}`"
                                    class="btn btn-primary btn-sm">Sửa</router-link>
                                <button @click="deleteCategory(category.id)" v-if="
                                    category.all_products_count === 0 &&
                                    (!category.children || category.children.length === 0)
                                " class="btn btn-danger btn-sm">
                                    Xoá
                                </button>
                            </div>
                        </td>
                    </tr>
                    <template v-if="expandedRows[category.id]">
                        <tr v-for="child in category.children" :key="child.id" class="table-secondary">
                            <td></td>
                            <td>{{ child.id }}</td>
                            <td>— {{ child.name }}</td>
                            <td>{{ category.name }}</td>
                            <td>{{ child.all_products_count }}</td>
                            <td>
                                <div class="d-flex gap-2">
                                    <router-link :to="`/admin/category/edit/${child.id}`"
                                        class="btn btn-primary btn-sm">Sửa</router-link>
                                    <button @click="deleteCategory(child.id)" v-if="
                                        child.all_products_count === 0 &&
                                        (!child.children || child.children.length === 0)
                                    " class="btn btn-danger btn-sm">
                                        Xoá
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </template>

                <tr v-else>
                    <td colspan="6" class="text-center text-secondary">
                        Không có danh mục nào
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <Pagination :current-page="currentPage" @update:page="updateCurrentPage" :total-pages="totalPages" />
</template>
<style scoped>
.rotate-icon {
    transform: rotate(90deg);
}

.skeleton-box {
    background-color: #e0e0e0;
    animation: pulse 1.5s infinite ease-in-out;
    height: 1.2em;
    border-radius: 4px;
    width: 100%;
}

@keyframes pulse {
    0% {
        background-color: #e0e0e0;
    }

    50% {
        background-color: #f5f5f5;
    }

    100% {
        background-color: #e0e0e0;
    }
}
</style>
