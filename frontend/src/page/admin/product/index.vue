<script setup>
import Pagination from "@/component/admin/Pagination.vue";
import axios from "axios";
import Swal from "sweetalert2";
import { onMounted, ref, computed, watch } from "vue";

const allProducts = ref([]);
const allCategories = ref([]);
const searchQuery = ref("");
const selectedCategoryId = ref(null);
const pageSize = ref(5);
const currentPage = ref(1);
const expandedRows = ref({});

const getAllProduct = async () => {
  try {
    const res = await axios.get("http://127.0.0.1:8000/api/product");
    allProducts.value = res.data.products;

    const response = await axios.get("http://127.0.0.1:8000/api/category");
    allCategories.value = response.data.categories;
  } catch (error) {
    console.log(error);
  }
};
const showModal = ref(false);
const selectedImage = ref("");

function openImage(img) {
  selectedImage.value = img;
  showModal.value = true;
}

function closeImage() {
  showModal.value = false;
}

const filteredProducts = computed(() => {
  return allProducts.value.filter((product) => {
    const isselectedCategoryId =
      !selectedCategoryId.value ||
      product.category_id === selectedCategoryId.value;
    const lowerCaseSearch = searchQuery.value.toLowerCase();
    const issearchQuery =
      !searchQuery.value ||
      product.name.toLowerCase().includes(lowerCaseSearch);

    return isselectedCategoryId && issearchQuery;
  });
});

const paginatedAndFilteredProducts = computed(() => {
  const startIndex = (currentPage.value - 1) * pageSize.value;
  const endIndex = startIndex + pageSize.value;
  return filteredProducts.value.slice(startIndex, endIndex);
});

const totalPages = computed(() => {
  return Math.ceil(filteredProducts.value.length / pageSize.value);
});

const updateCurrentPage = (page) => {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page;
  }
};

const toggleVariants = (productId) => {
  expandedRows.value[productId] = !expandedRows.value[productId];
};

const hiddenProduct = async (id, status) => {
  try {
    const result = await Swal.fire({
      title: "Bạn có chắc muốn ẩn sản phẩm này?",
      icon: "info",
      showCancelButton: true,
      confirmButtonColor: "#55acee",
      cancelButtonColor: "#d33",
      confirmButtonText: "Xác nhận",
      cancelButtonText: "Hủy",
    });

    if (result.isConfirmed) {
      await axios.put(`http://127.0.0.1:8000/api/product/hidden/${id}`, {
        status: status,
      });
      await getAllProduct();
      Swal.fire({
        toast: true,
        position: "top-end",
        icon: "success",
        title: "Sản phẩm đã được ẩn thành công!",
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
      title: "Ẩn sản phẩm không thành công!",
      showConfirmButton: false,
      timer: 2000,
      timerProgressBar: true,
    });
  }
};

watch([searchQuery, selectedCategoryId, pageSize], () => {
  currentPage.value = 1;
});

onMounted(async () => {
  await getAllProduct();
});
</script>

<template>
  <div class="d-flex justify-content-between">
    <h3 class="text-primary fw-bold">Danh sách sản phẩm</h3>

    <router-link
      to="/admin/product/insert"
      type="button"
      class="btn btn-primary h-25"
      >Thêm sản phẩm</router-link
    >
  </div>
  <div class="d-flex gap-3 flex-wrap mb-2">
    <div>
      <label for="search-input" class="form-label">Tìm kiếm sản phẩm</label>
      <input
        type="text"
        id="search-input"
        class="form-control"
        placeholder="Nhập từ khoá..."
        v-model="searchQuery"
      />
    </div>

    <div>
      <label for="category-select" class="form-label">Danh mục </label>
      <select class="form-select rounded-2" v-model="selectedCategoryId">
        <option :value="null">Chọn danh mục</option>
        <template v-for="category in allCategories" :key="category.id">
          <option :value="category.id">
            {{ category.name }}
          </option>
          <template v-for="child in category.children" :key="child.id">
            <option :value="child.id">-- {{ child.name }}</option>
          </template>
        </template>
      </select>
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
          <th></th>
          <th>Tên sản phẩm</th>
          <th>Giá</th>
          <th>Danh mục</th>
          <th>Trạng thái</th>
          <th>Hành động</th>
        </tr>
      </thead>
      <tbody>
        <template
          v-for="product in paginatedAndFilteredProducts"
          :key="product.id"
        >
          <tr>
            <td>
              <button
                v-if="product.variants && product.variants.length > 0"
                @click="toggleVariants(product.id)"
                class="btn btn-sm"
              >
                <i class="bi bi-caret-right-fill"></i>
              </button>
            </td>
            <td>{{ product.name }}</td>
            <td>{{ product.price }}</td>
            <td>{{ product.category_name }}</td>

            <td>
              <span
                :class="[
                  'badge',
                  product.status === 'published' ? 'bg-success' : 'bg-danger',
                ]"
              >
                {{ product.status === "published" ? "Hiển thị" : "Đã ẩn" }}
              </span>
            </td>
            <td>
              <div class="d-flex gap-2">
                <router-link
                  :to="`/admin/product/edit/${product.id}`"
                  class="btn btn-primary btn-sm"
                >
                  Sửa
                </router-link>
                <button
                  v-if="product.status === 'published'"
                  @click="hiddenProduct(product.id, 'discontinued')"
                  class="btn btn-danger btn-sm"
                >
                  Ẩn
                </button>
                <button
                  v-else
                  @click="hiddenProduct(product.id, 'published')"
                  class="btn btn-danger btn-sm"
                >
                  Hiện
                </button>
              </div>
            </td>
          </tr>
          <tr v-if="expandedRows[product.id]" class="expanded-row">
            <td colspan="7" class="p-0 border-top-0">
              <div class="p-2 bg-light">
                <table class="table table-sm table-bordered mb-0">
                  <thead>
                    <tr>
                      <th>ID Variant</th>
                      <th>SKU</th>
                      <th>Ảnh chính</th>
                      <th>Ảnh chi tiết</th>
                      <th>Thuộc tính</th>
                    </tr>
                  </thead>
                  <tbody class="variant">
                    <tr v-for="variant in product.variants" :key="variant.id">
                      <td>{{ variant.id }}</td>
                      <td>{{ variant.sku }}</td>
                      <td>
                        <img
                          :src="variant.image"
                          @click="openImage(variant.image)"
                          alt="Variant Image"
                          class="variant-image"
                        />
                      </td>
                      <td>
                        <template v-for="item in variant.images">
                          <img
                            :src="item.image_url"
                            alt="Variant Image"
                            class="variant-image me-1"
                            @click="openImage(item.image_url)"
                          />
                        </template>
                      </td>
                      <td>
                        <div class="d-flex flex-wrap gap-1">
                          <span
                            v-for="attr in variant.attributes"
                            :key="attr.attribute_value_id"
                            class="badge bg-primary"
                          >
                            {{ attr.attribute_name }}
                          </span>
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </td>
          </tr>
        </template>
      </tbody>
    </table>
  </div>

  <Pagination
    :current-page="currentPage"
    @update:page="updateCurrentPage"
    :total-pages="totalPages"
  />

  <div v-if="showModal" class="modal-backdrop" @click="closeImage">
    <img :src="selectedImage" class="modal-image" />
  </div>
</template>

<style scoped>
.product-image,
.variant-image {
  width: 40px;
  height: 40px;
  object-fit: cover;
}

.table-hover tbody tr:hover {
  background-color: #f5f5f5;
}

.rotate-icon {
  transform: rotate(90deg);
}

.modal-backdrop {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(179, 177, 177, 0.377);
  display: flex;
  justify-content: center;
  align-items: center;
}

.modal-image {
  max-width: 90%;
  max-height: 90%;
  border-radius: 8px;
}

.variant {
  height: 20px;
}
</style>
