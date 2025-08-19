<template>
  <div class="row">
    <div class="col-md-12">
      <div class="card card-stats card-raised p-3">
        <h3 class="text-primary fw-bold">
          {{ isEdit ? "Sửa sản phẩm" : "Thêm sản phẩm" }}
        </h3>
        <form @submit.prevent="saveProduct">
          <div v-if="isLoading" class="loading-overlay">
            <div class="spinner-border text-primary" role="status">
              <span class="visually-hidden">Loading...</span>
            </div>
            <div class="loading-text">Đang xử lý...</div>
          </div>

          <div class="pb-0 border-bottom pb-2">
            <h5 class="mb-0">Thông tin sản phẩm</h5>
          </div>

          <div class="card-body">
            <div class="mb-3 row">
              <div class="col-6">
                <label for="name" class="form-label">Tên sản phẩm</label><br />
                <input type="text" class="form-control" id="name" v-model="productform.name" />
              </div>
              <div class="col-6">
                <label for="price" class="form-label">Giá</label><br />
                <input type="number" class="form-control" id="price" v-model="productform.price" />
              </div>
            </div>
            <div class="mb-3 row">
              <div class="col-6">
                <label for="category" class="form-label">Danh mục</label><br />
                <select class="form-select rounded-2" v-model="productform.category_id">
                  <option :value="null">Chọn danh mục</option>
                  <template v-for="category in categories" :key="category.id">
                    <option :value="category.id">
                      {{ category.name }}
                    </option>
                    <template v-for="child in category.children" :key="child.id">
                      <option :value="child.id">-- {{ child.name }}</option>
                    </template>
                  </template>
                </select>
              </div>
              <div class="col-6">
                <label for="price" class="form-label">Trạng thái sản phẩm</label><br />
                <select class="form-select" id="category" v-model="productform.status">
                  <option :value="null" disabled>
                    Chọn trạng thái sản phẩm
                  </option>
                  <option value="discontinued">Ẩn</option>
                  <option value="published">Hiện</option>
                </select>
              </div>
            </div>
            <div class="mb-3">
              <label for="long_description" class="form-label">Mô tả ngắn</label><br />
              <QuillEditor ref="quillRef" v-model:content="productform.description" :toolbar="toolbarOptions"
                content-type="html" theme="snow" style="height: 300px" />
            </div>
          </div>
          <div class="card p-3 mb-3">
            <div class="pb-0 d-flex justify-content-between align-items-center">
              <h5 class="mb-0">Tạo biến thể</h5>
            </div>
            <div class="card-body">
              <div class="row">
                <div v-for="attribute in allAttributes" :key="attribute.id" class="col-6">
                  <label class="form-label">{{ attribute.name }}</label>
                  <v-select :options="attribute.attribute_values" label="value_name" :reduce="(option) => option.id"
                    v-model="selectedAttributeValues[attribute.id]"
                    :placeholder="`-- Chọn ${attribute.name.toLowerCase()} --`">
                    <template #option="option">
                      <div v-if="attribute.name === 'Màu sắc'" style="display: flex; align-items: center">
                        <div class="rounded-circle me-2" :style="{
                          width: '1.25rem',
                          height: '1.25rem',
                          backgroundColor: option.value_name,
                          border: '1px solid #ccc',
                        }"></div>
                        {{ option.value_name }}
                      </div>
                      <div v-else>
                        {{ option.value_name }}
                      </div>
                    </template>
                  </v-select>
                </div>
              </div>
              <button type="button" class="btn btn-primary mt-3" @click="addManualVariant">
                Thêm biến thể
              </button>
            </div>
          </div>
          <hr />

          <div class="card p-3">
            <div class="pb-0 d-flex justify-content-between align-items-center">
              <h5 class="mb-0">
                Danh sách biến thể đã thêm ({{ variantfrom.length }} biến thể)
              </h5>
            </div>
            <div v-for="(variant, index) in variantfrom" :key="index">
              <div class="mb-4 p-3 rounded border">
                <div class="d-flex justify-content-between align-items-center mb-3">
                  <h6 class="mb-0">Biến thể #{{ index + 1 }}</h6>
                  <button v-if="variant.order_count == 0" type="button" class="btn btn-danger btn-sm"
                    @click="removeVariant(index)">
                    <i class="bi bi-x"></i> Xóa biến thể
                  </button>
                </div>
                <div class="row">
                  <div class="mb-3 row">
                    <div class="col-2">
                      <label for="stock_quantity" class="form-label">Số lượng tồn kho</label><br />
                      <input type="number" class="form-control" id="name" min="1" v-model="variant.stock_quantity" />
                    </div>
                    <div class="col-5">
                      <label for="slug" class="form-label">Slug</label><br />
                      <input type="text" class="form-control" :value="variant.slug" disabled />
                    </div>
                    <div class="col-5">
                      <label for="sku" class="form-label">SKU</label><br />
                      <input type="text" class="form-control" :value="variant.sku" disabled />
                    </div>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Ảnh chính</label><br />
                    <input type="file" class="form-control" @change="onMainImageChange($event, index)" />
                    <div v-if="variant.image_preview" class="mt-2 text-center">
                      <img :src="variant.image_preview" @click="openImage(variant.image_preview)" alt="Variant Image"
                        class="img-fluid rounded" style="max-height: 150px" />
                    </div>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Ảnh chi tiết</label>
                    <input type="file" class="form-control" multiple @change="onDetailImagesChange($event, index)" />
                    <div v-if="variant.images_preview.length" class="mt-2 text-center">
                      <div v-for="(img, i) in variant.images_preview" :key="i" @click="openImage(img)"
                        class="d-inline-block me-2">
                        <img :src="img" alt="Variant Image" class="img-fluid rounded" style="max-height: 100px" />
                      </div>
                    </div>
                  </div>
                </div>
                <div class="card mt-3">
                  <div class="card-body p-3">
                    <h6 class="mb-3">Thuộc tính đã chọn:</h6>
                    <ul class="list-group">
                      <li v-for="attribute in variant.attributes" :key="attribute.id"
                        class="list-group-item d-flex justify-content-between">
                        <span>{{ attribute.name }}:</span>
                        <span class="fw-bold">{{ attribute.attribute_value_name }}</span>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>
              <hr />
            </div>
          </div>
          <button type="submit" class="btn btn-primary mt-2" :disabled="isLoading">
            <span v-if="isLoading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            <span v-if="isLoading">Đang xử lý...</span>
            <span v-else>Lưu sản phẩm</span>
          </button>
        </form>
      </div>
    </div>
  </div>
  <div v-if="showModal" class="modal-backdrop" @click="closeImage">
    <img :src="selectedImage" class="modal-image" />
  </div>
</template>

<script setup>
import axios from "axios";
import Swal from "sweetalert2";
import { computed, onMounted, ref, watch } from "vue";
import { useRoute } from "vue-router";
const props = defineProps({
  productId: {
    type: [Number, String],
    default: null,
  },
});
const isLoading = ref(false);
const route = useRoute();
const isEdit = computed(() => {
  return route.name && String(route.name).includes("edit");
});
const showModal = ref(false);
const selectedImage = ref("");

function openImage(img) {
  selectedImage.value = img;
  showModal.value = true;
}

function closeImage() {
  showModal.value = false;
}
const categories = ref([]);
const allAttributes = ref([]);

const fetchCategories = async () => {
  try {
    const response = await axios.get("http://127.0.0.1:8000/api/category");
    categories.value = response.data.categories;
  } catch (error) {
    console.log(error);
  }
};

const fetchAttributes = async () => {
  try {
    const response = await axios.get("http://127.0.0.1:8000/api/attribute");
    allAttributes.value = response.data.attributes;
  } catch (error) {
    console.log(error);
  }
};

const productform = ref({
  name: "",
  price: null,
  category_id: null,
  status: null,
  description: "",
});

const toolbarOptions = [
  ["bold", "italic", "underline", "strike"],
  [{ header: 1 }, { header: 2 }],
  [{ list: "ordered" }, { list: "bullet" }],
  [{ script: "sub" }, { script: "super" }],
  [{ indent: "-1" }, { indent: "+1" }],
  [{ direction: "rtl" }],
  [{ size: ["small", false, "large", "huge"] }],
  [{ header: [1, 2, 3, 4, 5, 6, false] }],
  [{ font: [] }],
  [{ align: [] }],
  ["clean"],
  ["link"],
  ["code-block"],
];

const variantfrom = ref([]);
const variantFiles = ref([]);
const selectedAttributeValues = ref({});

const addManualVariant = () => {
  const selectedValues = Object.entries(selectedAttributeValues.value)
    .filter(([_, value]) => value !== null && value !== undefined)
    .map(([attrId, valueId]) => {
      const attribute = allAttributes.value.find((a) => a.id == attrId);
      const value = attribute?.attribute_values.find((v) => v.id == valueId);
      return {
        id: attribute?.id,
        name: attribute?.name,
        value_id: value?.id,
        attribute_value_name: value?.value_name
      };
    });

  if (selectedValues.length === 0) {
    Swal.fire({
      toast: true,
      position: "top-end",
      icon: "error",
      title: "Vui lòng chọn thuộc tính.",
      showConfirmButton: false,
      timer: 2000,
      timerProgressBar: true,
    });
    return;
  }

  const exists = variantfrom.value.some((variant) => {
    return selectedValues.every((selectedAttr) => {
      const existingAttr = variant.attributes.find((attr) => attr.id === selectedAttr.id);
      return existingAttr && existingAttr.value_id === selectedAttr.value_id;
    });
  });

  if (exists) {
    Swal.fire({
      toast: true,
      position: "top-end",
      icon: "error",
      title: "Biến thể này đã được thêm.",
      showConfirmButton: false,
      timer: 2000,
      timerProgressBar: true,
    });
    return;
  }

  const variantName = selectedValues.map((sv) => sv.attribute_value_name).join("-");
  const newVariant = {
    image_preview: null,
    images_preview: [],
    attributes: selectedValues,
    slug: `${slugify(productform.value.name)}-${slugify(variantName)}`,
    sku: `SKU-${slugify(productform.value.name)}-${slugify(variantName)}-${Math.floor(
      Math.random() * 10000
    )}`,
    stock_quantity: 100,
    order_count: 0
  };
  variantfrom.value.push(newVariant);
  variantFiles.value.push({ main_image: null, detail_images: [] });
  selectedAttributeValues.value = {};
};

const removeVariant = (index) => {
  variantfrom.value.splice(index, 1);
  variantFiles.value.splice(index, 1);
};

const onMainImageChange = (e, index) => {
  const file = e.target.files[0];
  if (file) {
    variantFiles.value[index].main_image = file;
    variantfrom.value[index].image_preview = URL.createObjectURL(file);
  }
};

const onDetailImagesChange = (e, index) => {
  const files = Array.from(e.target.files);
  variantFiles.value[index].detail_images = files;
  const oldImageUrls = variantfrom.value[index].images_preview.filter(
    (url) => !url.startsWith("blob:")
  );
  const newPreviews = files.map((file) => URL.createObjectURL(file));
  variantfrom.value[index].images_preview = [...oldImageUrls, ...newPreviews];
};

const slugify = (str) => {
  if (!str) return "";
  str = str
    .toString()
    .normalize("NFD")
    .replace(/[\u0300-\u036f]/g, "");
  str = str.toLowerCase().trim();
  str = str.replace(/\s+/g, "-");
  str = str.replace(/[^\w-]+/g, "");
  return str;
};

watch(
  () => productform.value.name,
  (newName) => {
    const slugProduct = slugify(newName);
    variantfrom.value.forEach((v, idx) => {
      const variantName = v.attributes.map(attr => attr.attribute_value_name).join('-');

      v.slug = `${slugProduct}-${slugify(variantName)}`;
      v.sku = `SKU-${slugProduct}-${slugify(variantName)}-${Math.floor(
        Math.random() * 10000
      )}`;
    });
  }
);


const errors = ref({});

const fetchProductDetail = async (id) => {
  try {
    const res = await axios.get(
      `http://127.0.0.1:8000/api/products_detail/${id}`
    );
    const product = res.data.product;

    productform.value = {
      name: product.name,
      price: product.price,
      category_id: product.category_id,
      status: product.status,
      description: product.description,
    };

    variantfrom.value = product.variants.map((v) => ({
      ...v,
      attributes: v.attributes.map((att) => ({
        id: att.attribute_id,
        name: att.attribute_name_display,
        value_id: att.attribute_value_id,
        attribute_value_name: att.attribute_name,
      })),
      existing_images: v.images.map((img) => ({
        id: img.id,
        image_url: img.image_url,
      })),
      image_preview: v.image || null,
      images_preview: v.images.map((img) => img.image_url) || [],
      order_count: v.order_count
    }));

    variantFiles.value = product.variants.map(() => ({
      main_image: null,
      detail_images: [],
    }));
  } catch (err) {
    console.log(err);
  }
};

const saveProduct = async () => {
  isLoading.value = true;

  try {
    const formData = new FormData();
    for (const key in productform.value) {
      formData.append(key, productform.value[key]);
    }

    variantfrom.value.forEach((variant, index) => {
      formData.append(`variants[${index}][sku]`, variant.sku);
      formData.append(`variants[${index}][slug]`, variant.slug);
      formData.append(
        `variants[${index}][stock_quantity]`,
        variant.stock_quantity
      );
      variant.attributes.forEach((attr) => {
        formData.append(`variants[${index}][attributes][${attr.id}]`, attr.value_id);
      });

      const mainImageFile = variantFiles.value[index]?.main_image;
      if (mainImageFile) {
        formData.append(`variants[${index}][image]`, mainImageFile);
      } else if (variant.image_preview) {
        formData.append(`variants[${index}][image_url]`, variant.image_preview);
      }

      const detailImageFiles = variantFiles.value[index]?.detail_images;
      if (detailImageFiles && detailImageFiles.length > 0) {
        detailImageFiles.forEach((file) => {
          formData.append(`variants[${index}][images][]`, file);
        });
      }

      variant.images_preview.forEach((url) => {
        if (!url.startsWith("blob:")) {
          formData.append(`variants[${index}][image_urls][]`, url);
        }
      });
    });

    let res;
    if (isEdit.value) {
      formData.append("_method", "PUT");
      res = await axios.post(
        `http://127.0.0.1:8000/api/product/${props.productId}`,
        formData,
        {
          headers: {
            "Content-Type": "multipart/form-data",
          },
        }
      );
      Swal.fire({
        icon: "success",
        title: "Cập nhật sản phẩm thành công",
        toast: true,
        position: "top-end",
        showConfirmButton: false,
        timer: 2000,
        timerProgressBar: true,
      });
    } else {
      res = await axios.post("http://127.0.0.1:8000/api/product", formData, {
        headers: {
          "Content-Type": "multipart/form-data",
        },
      });
      Swal.fire({
        icon: "success",
        title: "Thêm sản phẩm thành công",
        toast: true,
        position: "top-end",
        showConfirmButton: false,
        timer: 2000,
        timerProgressBar: true,
      });
    }

    errors.value = {};
    if (!isEdit.value) {
      productform.value = {
        name: "",
        price: null,
        category_id: null,
        status: null,
        description: "",
      };
      variantfrom.value = [];
      variantFiles.value = [];
    }
  } catch (err) {
    if (err.response && err.response.status === 422) {
      const allErrors = err.response.data.errors;
      let errorMessage = "Thông tin nhập không hợp lệ:<br>";

      for (const key in allErrors) {
        const errorMessages = allErrors[key].join(", ");
        if (key === "name") {
          errorMessage += ` - Tên sản phẩm: ${errorMessages}<br>`;
        } else if (key === "price") {
          errorMessage += ` - Giá: ${errorMessages}<br>`;
        } else if (key === "category_id") {
          errorMessage += ` - Danh mục: ${errorMessages}<br>`;
        } else if (key === "status") {
          errorMessage += ` - Trạng thái: ${errorMessages}<br>`;
        } else if (key === "description") {
          errorMessage += ` - Mô tả: ${errorMessages}<br>`;
        } else if (key.startsWith("variants.")) {
          const parts = key.split(".");
          const variantIndex = parts[1];
          const fieldName = parts[2];
          let fieldNameVietnamese = fieldName;
          if (fieldName === "stock_quantity") {
            fieldNameVietnamese = "Số lượng tồn kho";
          } else if (fieldName === "image") {
            fieldNameVietnamese = "Ảnh chính";
          } else if (fieldName === "images") {
            fieldNameVietnamese = "Ảnh chi tiết";
          } else if (fieldName === "attributes") {
            fieldNameVietnamese = "Thuộc tính";
          }
          errorMessage += ` - Biến thể ${Number(variantIndex) + 1
            } - ${fieldNameVietnamese}: ${errorMessages}<br>`;
        } else {
          errorMessage += ` - ${key}: ${errorMessages}<br>`;
        }
      }

      Swal.fire({
        icon: "error",
        title: "Lỗi Validation",
        html: errorMessage,
        showConfirmButton: true,
        confirmButtonText: "Đóng",
      });
    } else {
      Swal.fire({
        icon: "error",
        title: "Đã có lỗi xảy ra",
        text: "Vui lòng thử lại sau.",
      });
    }
  } finally {
    isLoading.value = false;
  }
};

onMounted(() => {
  fetchCategories();
  fetchAttributes();
  if (isEdit.value && props.productId) {
    fetchProductDetail(props.productId);
  }
});
</script>

<style scoped>
/* Giữ nguyên các CSS đã có */
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
  z-index: 999;
}

.modal-image {
  max-width: 90%;
  max-height: 90%;
  border-radius: 8px;
}

/* Thêm CSS cho loading overlay */
.loading-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(255, 255, 255, 0.6);
  /* Lớp nền trắng mờ */
  backdrop-filter: blur(3px);
  /* Hiệu ứng làm mờ nội dung phía sau */
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  z-index: 10;
  border-radius: 8px;
}

.loading-text {
  margin-top: 10px;
  font-size: 1.2rem;
  font-weight: bold;
  color: #007bff;
  /* Màu chữ phù hợp với spinner */
}
</style>