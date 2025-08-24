<script setup>
import axios from "axios";
import Swal from "sweetalert2";
import { computed, onMounted, ref, watch } from "vue";
import { useRoute, useRouter } from "vue-router";

const props = defineProps({
  attributeId: {
    type: [Number, String],
    default: null,
  },
});

const route = useRoute();
const router = useRouter();

const isEdit = computed(() => {
  return route.name && String(route.name).includes("edit");
});

const errors = ref({});

const newAttribute = ref({
  name: "",
  type: null,
  values: [],
});

const addValueInput = () => {
  newAttribute.value.values.push({
    value_name: "",
    variants_count: 0,
  });
};

const removeValueInput = (index) => {
  newAttribute.value.values.splice(index, 1);
};

const insertAttribute = async () => {
  const filteredValues = newAttribute.value.values
    .filter((valueObj) => valueObj.value_name.trim() !== "")
    .map((valueObj) => valueObj.value_name);

  try {
    if (isEdit.value) {
      const id = props.attributeId;
      await axios.put(`${import.meta.env.VITE_URL_API}api/attribute/${id}`, {
        name: newAttribute.value.name,
        type: newAttribute.value.type,
        values: filteredValues,
      });
      Swal.fire({
        toast: true,
        position: "top-end",
        icon: "success",
        title: "Cập nhật thuộc tính thành công!",
        showConfirmButton: false,
        timer: 2000,
        timerProgressBar: true,
      });
    } else {
      await axios.post(`${import.meta.env.VITE_URL_API}api/attribute`, {
        name: newAttribute.value.name,
        type: newAttribute.value.type,
        values: filteredValues,
      });
      Swal.fire({
        toast: true,
        position: "top-end",
        icon: "success",
        title: "Thêm thuộc tính thành công!",
        showConfirmButton: false,
        timer: 2000,
        timerProgressBar: true,
      });
    }

    router.push("/admin/attribute");
    errors.value = {};
  } catch (error) {
    if (error.response && error.response.status === 422) {
      errors.value = {};
      errors.value = error.response.data.errors;
    }
  }
};

const fetchAttribute = async (id) => {
  try {
    const res = await axios.get(
      `${import.meta.env.VITE_URL_API}api/attribute/${id}/edit`
    );
    const attributeData = res.data.attribute;
    newAttribute.value.name = attributeData.name;
    newAttribute.value.type = attributeData.type;
    newAttribute.value.values = attributeData.attribute_values.map((val) => ({
      value_name: val.value_name,
      variants_count: val.variants_count,
      id: val.id,
    }));
  } catch (error) {
    console.log(error);
  }
};

onMounted(() => {
  if (isEdit.value && props.attributeId) {
    fetchAttribute(props.attributeId);
  } else {
    addValueInput();
  }
});

watch(
  () => newAttribute.value.type,
  (newType, oldType) => {
    newAttribute.value.values = [];
    addValueInput();
  }
);

onMounted(() => {
  if (isEdit.value) {
    fetchAttribute(props.attributeId);
  }
});
</script>
<template>
  <div class="row">
    <div class="col-md-12">
      <div class="card card-stats card-raised">
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <h3 class="text-primary fw-bold">
              {{ isEdit ? "Sửa thuộc tính" : "Thêm thuộc tính" }}
            </h3>
            <div>
              <button
                @click="$router.back()"
                class="btn btn-outline-secondary rounded-0"
              >
                <i class="bi bi-arrow-counterclockwise"></i> Quay lại
              </button>
            </div>
          </div>
          <form
            class="row mt-2 justify-content-center"
            @submit.prevent="insertAttribute"
          >
            <div class="col-md-8">
              <div class="card rounded-0 border-0 shadow mb-4">
                <div class="card-body">
                  <div class="row">
                    <div class="col-12 mb-3">
                      <label for="name" class="form-label"
                        >Tên thuộc tính
                        <span class="text-danger">*</span></label
                      ><br />
                      <small class="text-danger" v-if="errors.name">{{
                        errors.name[0]
                      }}</small>
                      <input
                        type="text"
                        class="form-control rounded-0"
                        id="name"
                        v-model="newAttribute.name"
                      />
                    </div>
                    <div class="col-12 mb-3">
                      <label for="type" class="form-label"
                        >Loại thuộc tính
                        <span class="text-danger">*</span></label
                      ><br />
                      <small class="text-danger" v-if="errors.type">{{
                        errors.type[0]
                      }}</small>
                      <select
                        class="form-select rounded-0"
                        v-model="newAttribute.type"
                      >
                        <option :value="null">
                          -- Chọn loại thuộc tính --
                        </option>
                        <option value="select">Select</option>
                        <option value="button">Button</option>
                        <option value="color">Color</option>
                      </select>
                    </div>
                    <div class="col-12 mb-3">
                      <div
                        class="d-flex align-items-center justify-content-between"
                      >
                        <label for="value_name" class="form-label mb-0"
                          >Giá trị thuộc tính
                          <span class="text-danger">*</span></label
                        >
                        <button
                          type="button"
                          class="btn btn-sm btn-outline-success"
                          @click="addValueInput"
                        >
                          Thêm giá trị
                        </button>
                      </div>
                      <small class="text-danger" v-if="errors.values">{{
                        errors.values[0]
                      }}</small>
                      <div
                        v-for="(valueObj, index) in newAttribute.values"
                        :key="index"
                        class="d-flex gap-2 mt-2"
                      >
                        <input
                          v-if="newAttribute.type === 'color'"
                          type="color"
                          class="form-control form-control-color w-100"
                          v-model="valueObj.value_name"
                          :disabled="valueObj.variants_count > 0"
                        />
                        <input
                          v-else
                          type="text"
                          class="form-control rounded-0"
                          :placeholder="`Nhập giá trị ${index + 1}`"
                          v-model="valueObj.value_name"
                          :disabled="valueObj.variants_count > 0"
                        />
                        <button
                          type="button"
                          class="btn btn-sm btn-outline-danger"
                          v-if="valueObj.variants_count === 0"
                          @click="removeValueInput(index)"
                        >
                          Xóa
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <button type="submit" class="btn btn-primary">
                {{ isEdit ? "Cập nhật" : "Thêm thuộc tính" }}
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
