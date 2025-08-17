<script setup>
import axios from "axios";
import Swal from "sweetalert2";
import { computed, onMounted, ref, watch } from "vue";
import { useRoute, useRouter } from "vue-router";

const props = defineProps({
  categoryId: {
    type: [Number, String],
    default: null,
  },
});

const categories = ref([]);
const newCategory = ref({
  name: "",
  parent_id: null,
  children_id: [],
  slug: "",
});

const route = useRoute();
const router = useRouter();

const isEdit = computed(() => {
  return route.name && String(route.name).includes("edit");
});

const findCategoryInTree = (categories, id) => {
  for (const category of categories) {
    if (category.id == id) {
      return category;
    }

    if (category.children && category.children.length > 0) {
      const foundChild = findCategoryInTree(category.children, id);
      if (foundChild) {
        return foundChild;
      }
    }
  }
  return null;
};

const getAllCategory = async () => {
  try {
    const res = await axios.get("http://127.0.0.1:8000/api/category");
    categories.value = res.data.categories;

    if (isEdit.value) {
      const id = props.categoryId;
      const categoryToEdit = findCategoryInTree(categories.value, id);

      if (categoryToEdit) {
        newCategory.value.name = categoryToEdit.name;
        newCategory.value.parent_id = categoryToEdit.parent_id;
        newCategory.value.slug = categoryToEdit.slug;
      } else {
        router.push("/admin/category");
      }
    }
  } catch (error) {
    console.log(error);
  }
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

const getParentSlug = (id) => {
  const parentCategory = findCategoryInTree(categories.value, id);
  if (parentCategory) {
    return slugify(parentCategory.name);
  }
  return "";
};

watch(
  [() => newCategory.value.name, () => newCategory.value.parent_id],
  ([newCategoryName, newCategoryParentId]) => {
    const slugCategoryParent = getParentSlug(newCategoryParentId);
    const slugCategory = slugify(newCategoryName);

    if (slugCategoryParent) {
      newCategory.value.slug = `${slugCategoryParent}/${slugCategory}`;
    } else {
      newCategory.value.slug = slugCategory;
    }
  }
);

const errors = ref({});

const insertCategory = async () => {
  try {
    if (isEdit.value) {
      const id = props.categoryId;
      await axios.put(`http://127.0.0.1:8000/api/category/${id}`, {
        name: newCategory.value.name,
        slug: newCategory.value.slug,
        parent_id: newCategory.value.parent_id,
        children_id: newCategory.value.children_id,
      });
      Swal.fire({
        toast: true,
        position: "top-end",
        icon: "success",
        title: "Cập nhật danh mục thành công!",
        showConfirmButton: false,
        timer: 2000,
        timerProgressBar: true,
      });
    } else {
      await axios.post("http://127.0.0.1:8000/api/category", {
        name: newCategory.value.name,
        slug: newCategory.value.slug,
        parent_id: newCategory.value.parent_id,
        children_id: newCategory.value.children_id,
      });
      Swal.fire({
        toast: true,
        position: "top-end",
        icon: "success",
        title: "Thêm danh mục thành công!",
        showConfirmButton: false,
        timer: 2000,
        timerProgressBar: true,
      });
    }

    router.push("/admin/category");
    errors.value = {};
  } catch (error) {
    if (error.response && error.response.status === 422) {
      errors.value = {};
      errors.value = error.response.data.errors;
    }
  }
};

onMounted(async () => {
  await getAllCategory();
});
</script>

<template>
  <div class="row">
    <div class="col-md-12">
      <div class="card card-stats card-raised">
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <h3 class="text-primary fw-bold">
              {{ isEdit ? "Sửa danh mục" : "Thêm danh mục" }}
            </h3>
            <div>
              <button @click="$router.back()" class="btn btn-outline-secondary rounded-0">
                <i class="bi bi-arrow-counterclockwise"></i> Quay lại
              </button>
            </div>
          </div>
          <form class="row mt-2 justify-content-center" @submit.prevent="insertCategory">
            <div class="col-md-8">
              <div class="card rounded-0 border-0 shadow mb-4">
                <div class="card-body">
                  <div class="row">
                    <div class="col mb-3">
                      <label for="name" class="form-label">Tên danh mục <span class="text-danger">*</span></label><br />
                      <small class="text-danger" v-if="errors.name">{{
                        errors.name[0]
                      }}</small>
                      <input type="text" class="form-control rounded-0" id="name" required v-model="newCategory.name" />
                    </div>
                  </div>

                  <div class="row">
                    <div class="col mb-3">
                      <label for="category" class="form-label">Danh mục cha</label>
                      <select class="form-select rounded-0" v-model="newCategory.parent_id">
                        <option :value="null">-- Chọn danh mục --</option>
                        <template v-for="category in categories" :key="category.id">
                          <option v-if="
                            isEdit ? category.id != props.categoryId : true
                          " :value="category.id">
                            {{ category.name }}
                          </option>
                          <template v-for="child in category.children" :key="child.id">
                            <option v-if="
                              isEdit ? child.id != props.categoryId : true
                            " :value="child.id">
                              -- {{ child.name }}
                            </option>
                          </template>
                        </template>
                      </select>
                    </div>
                    <div class="col mb-3">
                      <label for="instock" class="form-label">Slug <span class="text-danger">*</span></label><br />
                      <small class="text-danger" v-if="errors.slug">{{
                        errors.slug[0]
                      }}</small>
                      <input type="text" disabled class="form-control rounded-0" id="instock" required
                        v-model="newCategory.slug" />
                    </div>
                  </div>

                  <div class="mb-3">
                    <label for="description" class="form-label">Danh mục con</label>
                    <div class="category-scroll-list">
                      <template v-for="category in categories" :key="category.id">
                        <div class="form-check" v-if="category.id != categoryId">
                          <input class="form-check-input" type="checkbox" :id="`child-${category.id}`"
                            :value="category.id" v-model="newCategory.children_id" />
                          <label class="form-check-label" :for="`child-${category.id}`" style="font-size: 14px">
                            {{ category.name }}
                          </label>
                        </div>
                      </template>
                    </div>
                  </div>
                </div>
              </div>
              <button type="submit" class="btn btn-primary">
                {{ isEdit ? "Cập nhật" : "Thêm danh mục" }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>
<style scoped>
.category-scroll-list {
  max-height: 200px;
  overflow-y: auto;
  padding-right: 15px;
}
</style>