<script setup>
import Pagination from "@/component/admin/Pagination.vue";
import axios from "axios";
import Swal from "sweetalert2";
import { onMounted, ref, computed, watch } from "vue";

const allAttributes = ref([]);
const searchQuery = ref("");
const pageSize = ref(5);
const currentPage = ref(1);
const expandedRows = ref({});

const getAllAttributes = async () => {
    try {
        const res = await axios.get("http://127.0.0.1:8000/api/attribute");
        allAttributes.value = res.data.attributes;
    } catch (error) {
        console.log(error);
    }
};


const filteredAttributes = computed(() => {
    return allAttributes.value.filter(attribute => {
        const lowerCaseSearch = searchQuery.value.toLowerCase();
        const issearchQuery =
        !searchQuery.value ||
        attribute.name.toLowerCase().includes(lowerCaseSearch);

        return issearchQuery;
    });
    
});

const paginatedAndFilteredAttributes = computed(() => {
    const startIndex = (currentPage.value - 1) * pageSize.value;
    const endIndex = startIndex + pageSize.value;
    return filteredAttributes.value.slice(startIndex, endIndex);
});

const totalPages = computed(() => {
    return Math.ceil(filteredAttributes.value.length / pageSize.value);
});

const updateCurrentPage = (page) => {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page;
  }
};

const toggleChildren = (attributeId) => {
    expandedRows.value[attributeId] = !expandedRows.value[attributeId];
};

const deleteAttribute = async (id) => {
    try {
        const result = await Swal.fire({
            title: "Bạn có chắc muốn xoá thuộc tính này?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Xác nhận",
            cancelButtonText: "Hủy",
        });

        if (result.isConfirmed) {
            await axios.delete(`http://127.0.0.1:8000/api/attribute/${id}`);
            await getAllAttributes();
            Swal.fire({
                toast: true,
                position: "top-end",
                icon: "success",
                title: "Thuộc tính đã được xóa thành công!",
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
            title: "Xóa thuộc tính không thành công!",
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true,
        });
    }
};

const deleteAttributeValue = async (id) => {
    try {
        const result = await Swal.fire({
            title: "Bạn có chắc muốn xoá giá trị thuộc tính này?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Xác nhận",
            cancelButtonText: "Hủy",
        });

        if (result.isConfirmed) {
            await axios.delete(`http://127.0.0.1:8000/api/attribute-value/${id}`);
            await getAllAttributes();
            Swal.fire({
                toast: true,
                position: "top-end",
                icon: "success",
                title: "Giá trị thuộc tính đã được xóa thành công!",
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
            title: "Xóa giá trị thuộc tính không thành công!",
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true,
        });
    }
};

watch([searchQuery, pageSize], () => {
    currentPage.value = 1;
});

onMounted(async () => {
    await getAllAttributes();
});
</script>
<template>
    <div class="d-flex justify-content-between">
        <h3 class="text-primary fw-bold">Danh sách thuộc tính</h3>

        <router-link to="/admin/attribute/insert" type="button" class="btn btn-primary h-25">Thêm thuộc
            tính</router-link>
    </div>
    <div class="d-flex gap-3 flex-wrap mb-2">
        <div>
            <label for="search-input" class="form-label">Tìm kiếm thuộc tính</label>
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
                    <th style="width: 50px"></th>
                    <th>ID</th>
                    <th>Tên thuộc tính</th>
                    <th>Loại thuộc tính</th>
                    <th>Giá trị</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <template v-for="attribute in paginatedAndFilteredAttributes" :key="attribute.id">
                    <tr>
                        <td>
                            <button v-if="
                                attribute.attribute_values &&
                                attribute.attribute_values.length > 0
                            " @click="toggleChildren(attribute.id)" class="btn btn-sm">
                                <i class="bi bi-caret-right-fill"
                                    :class="{ 'rotate-icon': expandedRows[attribute.id] }"></i>
                            </button>
                        </td>
                        <td>{{ attribute.id }}</td>
                        <td>{{ attribute.name }}</td>
                        <td>{{ attribute.type }}</td>
                        <td>{{ attribute.attribute_values.length }}</td>
                        <td>
                            <div class="d-flex gap-2">
                                <router-link :to="`/admin/attribute/edit/${attribute.id}`"
                                    class="btn btn-primary btn-sm">Sửa</router-link>
                                <button @click="deleteAttribute(attribute.id)" v-if="
                                    !attribute.attribute_values ||
                                    attribute.attribute_values.length === 0
                                " class="btn btn-danger btn-sm">
                                    Xoá
                                </button>
                            </div>
                        </td>
                    </tr>
                    <template v-if="expandedRows[attribute.id]">
                        <tr v-for="value in attribute.attribute_values" :key="value.id" class="table-secondary">
                            <td></td>
                            <td>{{ value.id }}</td>
                            <td class="ps-5">— {{ value.value_name }}</td>
                            <td></td>
                            <td></td>
                            <td>
                                <div class="d-flex gap-2">
                                    <button v-if="value.variants_count == 0" @click="deleteAttributeValue(value.id)"
                                        class="btn btn-danger btn-sm">
                                        Xoá
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </template>
            </tbody>
        </table>
    </div>

    <Pagination :current-page="currentPage" @update:page="updateCurrentPage" :total-pages="totalPages" />

</template>

<style scoped>
.rotate-icon {
    transform: rotate(90deg);
}
</style>
