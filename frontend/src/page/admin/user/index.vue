<script setup>
import axios from "axios";
import { onMounted, ref, computed, watch } from "vue";
import Pagination from "@/component/admin/Pagination.vue";
import { useTokenUser } from "@/component/store/useTokenUser";
import Swal from "sweetalert2";

const users = ref([]);
const pageSize = ref(5);
const currentPage = ref(1);
const selectedStatus = ref(null);
const selectedRole = ref(null);
const selectedUser = ref(null);
const store = useTokenUser();
const isLoading = ref(true);

const getAllUsers = async () => {
  try {
    const res = await axios.get(`${import.meta.env.VITE_URL_API}api/user-info`, {
      headers: { Authorization: `Bearer ${store.token}` },
    });
    users.value = res.data.users;
  } catch (error) {
    console.log(error);
  } finally {
    isLoading.value = false;
  }
};

const getStatusClass = (status) => {
  switch (status) {
    case "active":
      return "text-bg-success";
    case "banned":
      return "text-bg-danger";
    default:
      return "text-bg-light";
  }
};

const filteredUsers = computed(() => {
  return users.value.filter((user) => {
    const isStatusMatch =
      !selectedStatus.value || user.status == selectedStatus.value;
    const isRoleMatch =
      !selectedRole.value || user.role == selectedRole.value;
    return isStatusMatch && isRoleMatch;
  });
});

const paginatedAndFilteredUsers = computed(() => {
  const startIndex = (currentPage.value - 1) * pageSize.value;
  const endIndex = startIndex + pageSize.value;
  return filteredUsers.value.slice(startIndex, endIndex);
});

const totalPages = computed(() => {
  return Math.ceil(filteredUsers.value.length / pageSize.value);
});

const updateCurrentPage = (page) => {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page;
  }
};

watch([selectedStatus, pageSize], () => {
  currentPage.value = 1;
});

const provinces = ref([]);
const districts = ref([]);
const wards = ref([]);
const ghnToken = import.meta.env.VITE_GHN_TOKEN;

const fetchProvinces = async () => {
  try {
    const res = await axios.get(
      "https://online-gateway.ghn.vn/shiip/public-api/master-data/province",
      { headers: { Token: ghnToken } }
    );
    provinces.value = res.data.data || [];
  } catch (err) {
    console.error("fetchProvinces error:", err);
  }
};

const fetchDistricts = async (provinceId) => {
  try {
    const res = await axios.post(
      "https://online-gateway.ghn.vn/shiip/public-api/master-data/district",
      { province_id: provinceId },
      { headers: { Token: ghnToken } }
    );
    districts.value = res.data.data || [];
  } catch (err) {
    console.log("fetchDistricts error:", err);
  }
};

const fetchWards = async (districtId) => {
  try {
    const res = await axios.post(
      "https://online-gateway.ghn.vn/shiip/public-api/master-data/ward",
      { district_id: districtId },
      { headers: { Token: ghnToken } }
    );
    wards.value = res.data.data;
  } catch (err) {
    console.log("fetchWards error:", err);
  }
};

const formattedAddresses = ref([]);
const isLoadingAddresses = ref(false);
const selectUser = async (user) => {
  try {
    selectedUser.value = user;
    isLoadingAddresses.value = true;
    if (!user.addresses || user.addresses.length === 0) {
      formattedAddresses.value = [];
    } else {
      await fetchProvinces();

      const addressPromises = user.addresses.map(async (addr) => {
        await fetchDistricts(addr.province_id);
        await fetchWards(addr.district_id);

        const provinceName =
          provinces.value.find((p) => p.ProvinceID === addr.province_id)
            ?.ProvinceName || "N/A";
        const districtName =
          districts.value.find((d) => d.DistrictID === addr.district_id)
            ?.DistrictName || "N/A";
        const wardName =
          wards.value.find((w) => w.WardCode === String(addr.ward_code))
            ?.WardName || "N/A";

        return {
          ...addr,
          provinceName,
          districtName,
          wardName,
        };
      });

      formattedAddresses.value = await Promise.all(addressPromises);
    }
  } catch (error) {
    console.log(error);
  } finally {
    isLoadingAddresses.value = false;
  }
};

const assignRole = async (id) => {
  try {
    const result = await Swal.fire({
      title: `Bạn có chắc chắn muốn chuyển tài khoản này thành tài khoản nhân viên?`,
      icon: "question",
      showCancelButton: true,
      confirmButtonText: "Đồng ý",
      cancelButtonText: "Hủy",
    });
    if (result.isConfirmed) {
      await axios.put(`${import.meta.env.VITE_URL_API}api/assign-role/${id}`, null, {
        headers: { Authorization: `Bearer ${store.token}` },
      });
      Swal.fire({
        toast: true,
        position: "top-end",
        icon: "success",
        title: "Cập nhật thành công",
        showConfirmButton: false,
        timer: 2000,
      });
    }

    await getAllUsers();
  } catch (error) {
    console.log(error);
    Swal.fire({
      toast: true,
      position: "top-end",
      icon: "error",
      title: "Có lỗi xảy ra",
      showConfirmButton: false,
      timer: 2000,
    });
  }
};

const lockRole = async (id, status) => {
  try {
    const result = await Swal.fire({
      title: `Bạn có chắc chắn muốn khóa tài khoản này ?`,
      icon: "question",
      showCancelButton: true,
      confirmButtonText: "Đồng ý",
      cancelButtonText: "Hủy",
    });
    if (result.isConfirmed) {
      await axios.put(`${import.meta.env.VITE_URL_API}api/lock/${id}`, {
        status: status
      }, {
        headers: { Authorization: `Bearer ${store.token}` },
      });
      Swal.fire({
        toast: true,
        position: "top-end",
        icon: "success",
        title: "Cập nhật thành công",
        showConfirmButton: false,
        timer: 2000,
      });
    }

    await getAllUsers();
  } catch (error) {
    console.log(error);
    Swal.fire({
      toast: true,
      position: "top-end",
      icon: "error",
      title: "Có lỗi xảy ra",
      showConfirmButton: false,
      timer: 2000,
    });
  }
};

onMounted(async () => {
  isLoading.value = true;
  await getAllUsers();
});
</script>

<template>
  <div class="d-flex justify-content-between">
    <h3 class="text-primary fw-bold">Danh sách đánh giá</h3>
  </div>
  <div class="d-flex gap-3 flex-wrap mb-2">
    <div>
      <label for="rating-select" class="form-label">Trạng thái tài khoản</label>
      <select id="rating-select" class="form-select rounded-2" v-model="selectedStatus">
        <option :value="null">Mặc định</option>
        <option value="active">Đang hoạt động</option>
        <option value="banned">Đã khoá</option>
      </select>
    </div>

    <div>
      <label for="rating-select" class="form-label">Vai trò tài khoản</label>
      <select id="rating-select" class="form-select rounded-2" v-model="selectedRole">
        <option :value="null">Mặc định</option>
        <option value="customer">Khách hàng</option>
        <option value="staff">Nhân viên</option>
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
          <th>ID</th>
          <th>Thông tin cơ bản</th>
          <th>Danh sách địa chỉ</th>
          <th>Vai trò tài khoản</th>
          <th>Trạng thái tài khoản</th>
          <th>Hành động</th>
        </tr>
      </thead>
      <tbody>
        <tr v-if="isLoading" v-for="n in pageSize" :key="n">
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
          <td>
            <div class="skeleton-box"></div>
          </td>
        </tr>
        <template v-else-if="paginatedAndFilteredUsers.length > 0" v-for="user in paginatedAndFilteredUsers"
          :key="user.id">
          <tr>
            <td>{{ user.id }}</td>
            <td>
              <div class="d-flex align-items-center gap-2">
                <img :src="user.avatar" alt="avatar" class="rounded-circle"
                  style="width: 30px; height: 30px; object-fit: cover" />
                <div class="d-flex flex-column">
                  <span>{{ user.username }}</span>
                  <small class="text-muted">{{ user.email }}</small>
                </div>
              </div>
            </td>

            <td>
              <button class="btn btn-info btn-sm" data-bs-toggle="offcanvas" data-bs-target="#addressSidebar"
                @click="selectUser(user)">
                Xem
              </button>
            </td>
            <td>
              <select name="" id="" class="form-select" v-model="user.role" @change="assignRole(user.id)"
                :disabled="!user.role == 'manager'">
                <option value="customer">Khách hàng</option>
                <option value="staff">Nhân viên</option>
              </select>
            </td>
            <td>
              <span class="badge rounded-pill fw-bold" :class="getStatusClass(user.status)">
                {{ user.status }}
              </span>
            </td>
            <td>
              <div class="d-flex gap-2">
                <button class="btn btn-primary btn-sm" @click="lockRole(user.id, 'banned')"
                  v-if="user.status == 'active'"><i class="bi bi-lock-fill"></i></button>
                <button class="btn btn-danger btn-sm" @click="lockRole(user.id, 'active')" v-else><i
                    class="bi bi-unlock2-fill"></i></button>
              </div>
            </td>
          </tr>
        </template>
        <tr v-else>
          <td colspan="6" class="text-center text-secondary">
            Không có người dùng nào
          </td>
        </tr>
      </tbody>
    </table>
  </div>

  <Pagination :current-page="currentPage" @update:page="updateCurrentPage" :total-pages="totalPages" />

  <div class="offcanvas offcanvas-end" tabindex="-1" id="addressSidebar" aria-labelledby="addressSidebarLabel">
    <div class="offcanvas-header">
      <h5 class="offcanvas-title" id="addressSidebarLabel">
        Danh sách địa chỉ
      </h5>
      <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <div class="offcanvas-body">
      <div v-if="isLoadingAddresses" class="d-flex justify-content-center my-5">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
      </div>

      <div v-else-if="formattedAddresses.length > 0">
        <div v-for="(address, index) in formattedAddresses" :key="index" class="card mb-3">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
              <h6 class="card-title fw-bold">Địa chỉ {{ index + 1 }}</h6>
              <span v-if="address.default" class="badge text-bg-primary">Mặc định</span>
            </div>
            <p class="card-text">
              <strong>Người nhận:</strong> {{ address.customer_name }}<br />
              <strong>Số điện thoại:</strong> {{ address.phone }}<br />
              <strong>Phường/Xã:</strong> {{ address.wardName }}<br />
              <strong>Quận/Huyện:</strong> {{ address.districtName }}<br />
              <strong>Tỉnh/Thành phố:</strong> {{ address.provinceName }}<br />
              <strong>Địa chỉ chi tiết:</strong> {{ address.address }}<br />
            </p>
          </div>
        </div>
      </div>

      <div v-else class="alert alert-info" role="alert">
        Người dùng này chưa có địa chỉ nào được lưu.
      </div>
    </div>
  </div>
</template>

<style scoped>
.rotate-icon {
  transform: rotate(90deg);
}

.image-backdrop {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 99999;
}

.modal-image {
  max-width: 90%;
  max-height: 90%;
  border-radius: 8px;
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
