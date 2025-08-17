<template>
  <div class="row">
    <div class="col-md-12">
      <div class="card card-stats card-raised p-3">
        <h3 class="text-primary fw-bold">Thêm sản phẩm mới</h3>
        <form>
          <div class="pb-0 border-bottom pb-2">
            <h5 class="mb-0">Thông tin sản phẩm</h5>
          </div>

          <div class="card-body">
            <div class="mb-3 row">
              <div class="col-6">
                <label for="name" class="form-label">Tên sản phẩm</label>
                <input type="text" class="form-control" id="name" required />
              </div>
              <div class="col-6">
                <label for="price" class="form-label">Giá</label>
                <input type="number" class="form-control" id="price" required />
              </div>
            </div>
            <div class="mb-3 row">
              <div class="col-6">
                <label for="category" class="form-label">Danh mục</label>
                <select class="form-select rounded-2">
                  <option :value="null">Chọn danh mục</option>
                  <template v-for="category in categories" :key="category.id">
                    <option :value="category.id">
                      {{ category.name }}
                    </option>
                    <template
                      v-for="child in category.children"
                      :key="child.id"
                    >
                      <option :value="child.id">-- {{ child.name }}</option>
                    </template>
                  </template>
                </select>
              </div>
              <div class="col-6">
                <label for="price" class="form-label"
                  >Trạng thái sản phẩm</label
                >
                <select class="form-select" id="category" required>
                  <option :value="null" disabled>Chọn danh mục</option>
                  <option
                    v-for="category in categories"
                    :key="category.id"
                    :value="category.id"
                  >
                    {{ category.name }}
                  </option>
                </select>
              </div>
            </div>
            <div class="mb-3">
              <label for="long_description" class="form-label"
                >Mô tả chi tiết</label
              >
              <textarea
                class="form-control"
                id="long_description"
                rows="5"
              ></textarea>
            </div>
          </div>

          <div class="card p-3">
            <div class="pb-0 d-flex justify-content-between align-items-center">
              <h5 class="mb-0">Biến thể sản phẩm</h5>
              <button type="button" class="btn btn-success btn-sm">
                <i class="bi bi-plus-circle"></i> Thêm biến thể
              </button>
            </div>
            <div class="">
              <div class="mb-4 p-3 rounded">
                <div
                  class="d-flex justify-content-between align-items-center mb-3"
                >
                  <h6 class="mb-0">Biến thể</h6>
                  <button type="button" class="btn btn-danger btn-sm">
                    <i class="bi bi-trash"></i> Xóa
                  </button>
                </div>

                <div class="row">
                  <div class="mb-3">
                    <label :for="'variant_image_' + index" class="form-label"
                      >Ảnh chính</label
                    >
                    <input
                      type="file"
                      class="form-control"
                      :id="'variant_image_' + index"
                    />
                    <!-- <div v-if="variant.image_url" class="mt-2 text-center">
                                            <img :src="variant.image_url" alt="Variant Image" class="img-fluid rounded"
                                                style="max-height: 150px;">
                                        </div> -->
                  </div>
                  <div class="mb-3">
                    <label :for="'variant_image_' + index" class="form-label"
                      >Ảnh chi tiết</label
                    >
                    <input
                      type="file"
                      class="form-control"
                      :id="'variant_image_' + index"
                    />
                    <!-- <div v-if="variant.image_url" class="mt-2 text-center">
                                            <img :src="variant.image_url" alt="Variant Image" class="img-fluid rounded"
                                                style="max-height: 150px;">
                                        </div> -->
                  </div>
                </div>

                <div class="card mt-3">
                  <div class="card-body p-3">
                    <div
                      class="d-flex justify-content-between align-items-center mb-2"
                    >
                      <h6 class="mb-0">Thuộc tính (Attributes)</h6>
                      <button type="button" class="btn btn-secondary btn-sm">
                        <i class="bi bi-plus"></i> Thêm thuộc tính
                      </button>
                    </div>
                    <div class="d-flex align-items-center mb-2">
                      <input
                        type="text"
                        class="form-control me-2"
                        placeholder="Màu sắc"
                        required
                        disabled
                      />
                      <input type="color" class="form-control me-2" required />
                      <button type="button" class="btn btn-danger btn-sm">
                        <i class="bi bi-x"></i>
                      </button>
                    </div>
                    <div class="d-flex align-items-center mb-2">
                      <input
                        type="text"
                        class="form-control me-2"
                        placeholder="Kích thước"
                        required
                        disabled
                      />
                      <select name="" id="" class="form-select">
                        <option value="M">M</option>
                        <option value="L">L</option>
                      </select>
                      <button type="button" class="btn btn-danger btn-sm">
                        <i class="bi bi-x"></i>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <button type="submit" class="btn btn-primary mt-2">
            Lưu sản phẩm
          </button>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import axios from "axios";
import { onMounted, ref } from "vue";

const categories = ref([]);

const fetchCategories = async () => {
  try {
    const response = await axios.get("http://127.0.0.1:8000/api/category");
    categories.value = response.data.categories;
  } catch (error) {
    console.error("Lỗi khi lấy danh mục:", error);
  }
};
onMounted(() => {
  fetchCategories();
});
</script>

<style scoped></style>
