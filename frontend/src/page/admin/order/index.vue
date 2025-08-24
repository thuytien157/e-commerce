<script setup>
import FormatData from "@/component/store/FormatData";
import axios from "axios";
import Swal from "sweetalert2";
import { onMounted, ref, computed, watch } from "vue";
import { Modal } from "bootstrap";
import Pagination from "@/component/admin/Pagination.vue";

const orders = ref([]);
const pageSize = ref(5);
const currentPage = ref(1);
const selectedOrder = ref(null);
const selectedStatus = ref(null);
const selectedStatusPayment = ref(null);
const cancellation_reason = ref("Đơn vị vận chuyển lấy hàng không thành công");
const orderToCancel = ref(null);
const check = ref(false);
const isLoading = ref(true);

const getAllOrders = async () => {
  try {
    const res = await axios.get(`${import.meta.env.VITE_URL_API}api/order`);
    orders.value = res.data.orders;
  } catch (error) {
    console.log(error);
  } finally {
    isLoading.value = false;
  }
};

const getVariantAttributes = (attributes) => {
  if (!attributes || attributes.length === 0) {
    return "N/A";
  }
  const sizeAttribute = attributes.find((attr) => attr.attribute_id === 1);
  return sizeAttribute ? sizeAttribute.value_name : "N/A";
};

const calculateSubtotal = (items) => {
  return items.reduce((sum, item) => sum + item.subtotal, 0);
};

const getStatusClass = (status) => {
  switch (status) {
    case "Chờ xác nhận":
      return "text-bg-secondary";
    case "Đã xác nhận":
      return "text-bg-info";
    case "Đang xử lý":
      return "text-bg-primary";
    case "Đang giao hàng":
      return "text-bg-warning";
    case "Đã thanh toán":
    case "Hoàn thành":
      return "text-bg-success";
    case "Thất bại":
    case "Chưa thanh toán":
      return "text-bg-danger";
    default:
      return "text-bg-light";
  }
};

const updateStatus = async (id, status, cancellationReason = null) => {
  try {
    if (status === "Thất bại" && !cancellationReason) {
      Swal.fire({
        toast: true,
        position: "top-end",
        icon: "error",
        title: "Vui lòng nhập lý do hủy đơn hàng",
        showConfirmButton: false,
        timer: 2000,
      });
      return;
    }
    const result = await Swal.fire({
      title: `Bạn có chắc chắn muốn cập nhật sang trạng thái ${status}?`,
      icon: "question",
      showCancelButton: true,
      confirmButtonText: "Đồng ý",
      cancelButtonText: "Hủy",
    });
    if (result.isConfirmed) {
      isLoading.value = true;
      await axios.put(`${import.meta.env.VITE_URL_API}api/order-update-status/${id}`, {
        status: status,
        cancellation_reason: cancellationReason,
      });
      Swal.fire({
        toast: true,
        position: "top-end",
        icon: "success",
        title: "Cập nhật thành công",
        showConfirmButton: false,
        timer: 2000,
      });
      await getAllOrders();
    }
  } catch (error) {
    Swal.fire({
      toast: true,
      position: "top-end",
      icon: "error",
      title: "Có lỗi xảy ra",
      showConfirmButton: false,
      timer: 2000,
    });
    console.log(error);
  } finally {
    isLoading.value = false;
  }
};

const canSelectStatus = (currentStatus, optionStatus) => {
  const statusOrder = [
    "Chờ xác nhận",
    "Đã xác nhận",
    "Đang xử lý",
    "Đang giao hàng",
    "Hoàn thành",
    "Thất bại",
  ];
  const currentIndex = statusOrder.indexOf(currentStatus);
  const optionIndex = statusOrder.indexOf(optionStatus);
  if (currentIndex === -1 || optionIndex === -1) return false;
  if (optionStatus === currentStatus) return true;
  if (currentStatus === "Hoàn thành" || currentStatus === "Thất bại")
    return false;
  if (optionIndex === currentIndex + 1) return true;
  if (optionStatus === "Thất bại") return true;
  return false;
};

const handleStatusChange = (order) => {
  if (order.status === "Thất bại") {
    orderToCancel.value = order;
    const cancelModal = new Modal(document.getElementById("cancelModal"));
    cancelModal.show();
  } else {
    updateStatus(order.id, order.status);
  }
};

const confirmCancellation = () => {
  if (orderToCancel.value) {
    updateStatus(orderToCancel.value.id, "Thất bại", cancellation_reason.value);
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

const filteredOrders = computed(() => {
  return orders.value.filter((order) => {
    const isStatusMatch =
      !selectedStatus.value || order.status == selectedStatus.value;
    const isPaymentStatusMatch =
      !selectedStatusPayment.value ||
      order.status_payments == selectedStatusPayment.value;

    return isStatusMatch && isPaymentStatusMatch;
  });
});

const paginatedAndFilteredOrders = computed(() => {
  const startIndex = (currentPage.value - 1) * pageSize.value;
  const endIndex = startIndex + pageSize.value;
  return filteredOrders.value.slice(startIndex, endIndex);
});

const totalPages = computed(() => {
  return Math.ceil(filteredOrders.value.length / pageSize.value);
});

const updateCurrentPage = (page) => {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page;
  }
};

watch([selectedStatus, pageSize], () => {
  currentPage.value = 1;
});

const selectOrder = (order) => {
  selectedOrder.value = order;
};

const printInvoice = (record) => {
  axios
    .get(`${import.meta.env.VITE_URL_API}api/invoice/${record}`, {
      responseType: "blob",
    })
    .then((response) => {
      const file = new Blob([response.data], { type: "application/pdf" });
      const fileURL = URL.createObjectURL(file);
      window.open(fileURL);
    })
    .catch((error) => {
      console.error("Lỗi in hóa đơn:", error);
    });
};

onMounted(async () => {
  isLoading.value = true;
  await getAllOrders();
});
</script>

<template>
  <div class="d-flex justify-content-between">
    <h3 class="text-primary fw-bold">Danh sách đánh giá</h3>
  </div>
  <div class="d-flex gap-3 flex-wrap mb-2">
    <div>
      <label for="rating-select" class="form-label">Trạng thái đơn hàng</label>
      <select
        id="rating-select"
        class="form-select rounded-2"
        v-model="selectedStatus"
      >
        <option :value="null">Mặc định</option>
        <option value="Chờ xác nhận">Chờ xác nhận</option>
        <option value="Đã xác nhận">Đã xác nhận</option>
        <option value="Đang xử lý">Đang xử lý</option>
        <option value="Đang giao hàng">Đang giao hàng</option>
        <option value="Hoàn thành">Hoàn thành</option>
        <option value="Thất bại">Thất bại</option>
      </select>
    </div>
    <div>
      <label for="rating-select" class="form-label"
        >Trạng thái thanh toán</label
      >
      <select
        id="rating-select"
        class="form-select rounded-2"
        v-model="selectedStatusPayment"
      >
        <option :value="null">Mặc định</option>
        <option value="Đã thanh toán">Đã thanh toán</option>
        <option value="Chưa thanh toán">Chưa thanh toán</option>
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
          <th>Ngày đặt</th>
          <th>Khách hàng</th>
          <th>Tổng tiền</th>
          <th>Trạng thái thanh toán</th>
          <th>Trạng thái đơn hàng</th>
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
          <td>
            <div class="skeleton-box"></div>
          </td>
        </tr>
        <template
          v-else-if="paginatedAndFilteredOrders.length > 0"
          v-for="order in paginatedAndFilteredOrders"
          :key="order.id"
        >
          <tr>
            <td>{{ order.id }}</td>
            <td>{{ FormatData.formatDateTime(order.order_date) }}</td>
            <td v-if="order.customer">
              <div class="d-flex align-items-center gap-2">
                <img
                  :src="order.customer.avatar"
                  alt="avatar"
                  class="rounded-circle"
                  style="width: 30px; height: 30px; object-fit: cover"
                />
                <div class="d-flex flex-column">
                  <span>{{ order.customer.username }}</span>
                  <small class="text-muted">{{ order.customer.email }}</small>
                </div>
              </div>
            </td>
            <td v-else>Khách lẽ</td>
            <td>
              {{ FormatData.formatNumber(order.total_amount) }}
            </td>
            <td>
              <span
                class="badge rounded-pill fw-bold"
                :class="getStatusClass(order.status_payments)"
              >
                {{ order.status_payments }}
              </span>
            </td>
            <td>
              <select
                v-model="order.status"
                class="form-select rounded-0"
                @change="handleStatusChange(order)"
                :disabled="
                  order.status == 'Hoàn thành' || order.status == 'Thất bại'
                "
              >
                <option
                  value="Chờ xác nhận"
                  :disabled="!canSelectStatus(order.status, 'Chờ xác nhận')"
                >
                  Chờ xác nhận
                </option>
                <option
                  value="Đã xác nhận"
                  :disabled="!canSelectStatus(order.status, 'Đã xác nhận')"
                >
                  Đã xác nhận
                </option>
                <option
                  value="Đang xử lý"
                  :disabled="!canSelectStatus(order.status, 'Đang xử lý')"
                >
                  Đang xử lý
                </option>
                <option
                  value="Đang giao hàng"
                  :disabled="!canSelectStatus(order.status, 'Đang giao hàng')"
                >
                  Đang giao hàng
                </option>
                <option
                  value="Hoàn thành"
                  :disabled="!canSelectStatus(order.status, 'Hoàn thành')"
                >
                  Hoàn thành
                </option>
                <option
                  value="Thất bại"
                  :disabled="!canSelectStatus(order.status, 'Thất bại')"
                >
                  Thất bại
                </option>
              </select>
            </td>
            <td>
              <div class="d-flex gap-2">
                <button
                  class="btn btn-primary btn-sm"
                  data-bs-toggle="modal"
                  data-bs-target="#reviewDetailModal"
                  @click="selectOrder(order)"
                >
                  Xem chi tiết
                </button>
                <button
                  class="btn btn-info btn-sm"
                  @click="printInvoice(order.id)"
                >
                  Xuất hoá đơn
                </button>
              </div>
            </td>
          </tr>
        </template>
        <tr v-else>
          <td colspan="7" class="text-center text-secondary">
            Không có đơn hàng nào
          </td>
        </tr>
      </tbody>
    </table>
  </div>

  <Pagination
    :current-page="currentPage"
    @update:page="updateCurrentPage"
    :total-pages="totalPages"
  />

  <div
    class="modal fade"
    id="reviewDetailModal"
    tabindex="-1"
    aria-labelledby="reviewDetailModalLabel"
    aria-hidden="true"
  >
    <div class="modal-dialog modal-xl modal-dialog-centered">
      <div class="modal-content" v-if="selectedOrder">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="reviewDetailModalLabel">
            Chi tiết đơn hàng #{{ selectedOrder.id }}
          </h1>
          <button
            type="button"
            class="btn-close"
            data-bs-dismiss="modal"
            aria-label="Close"
          ></button>
        </div>
        <div class="modal-body">
          <div class="row g-4 mb-4">
            <div class="col-md-6">
              <div class="card shadow-sm h-100">
                <div class="card-header bg-light fw-bold">
                  Thông tin khách hàng & vận chuyển
                </div>
                <div class="card-body">
                  <div class="mb-2">
                    <strong>Tên khách hàng:</strong>
                    {{ selectedOrder.guest_name }}
                  </div>
                  <div class="mb-2">
                    <strong>Số điện thoại:</strong>
                    {{ selectedOrder.guest_phone }}
                  </div>
                  <div class="mb-2" v-if="selectedOrder.guest_email">
                    <strong>Email:</strong> {{ selectedOrder.guest_email }}
                  </div>
                  <div class="mb-2">
                    <strong>Địa chỉ nhận hàng:</strong>
                    {{ selectedOrder.guest_address }}
                  </div>
                  <div class="mb-2">
                    <strong>Phí vận chuyển:</strong>
                    {{ FormatData.formatNumber(selectedOrder.shipping_money) }}
                    VNĐ
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="card shadow-sm h-100">
                <div class="card-header bg-light fw-bold">
                  Thông tin đơn hàng
                </div>
                <div class="card-body">
                  <div class="mb-2">
                    <strong>Ngày đặt hàng:</strong>
                    {{ FormatData.formatDateTime(selectedOrder.order_date) }}
                  </div>
                  <div class="mb-2">
                    <strong class="pe-1">Trạng thái:</strong>
                    <span
                      class="badge rounded-pill fw-bold"
                      :class="getStatusClass(selectedOrder.status)"
                    >
                      {{ selectedOrder.status }}
                    </span>
                  </div>
                  <div class="mb-2">
                    <strong class="pe-1">Trạng thái thanh toán:</strong>
                    <span
                      class="badge rounded-pill fw-bold"
                      :class="getStatusClass(selectedOrder.status_payments)"
                    >
                      {{ selectedOrder.status_payments }}
                    </span>
                  </div>
                  <div class="mb-2">
                    <strong>Phương thức thanh toán:</strong>
                    {{ selectedOrder.payment_method }}
                  </div>
                  <div class="mb-2" v-if="selectedOrder.status == 'Thất bại'">
                    <strong>Lý do hủy:</strong>
                    {{ selectedOrder.cancellation_reason }}
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="card shadow-sm">
            <div class="card-header bg-light fw-bold">Các sản phẩm đã đặt</div>
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-hover mb-0">
                  <thead>
                    <tr>
                      <th scope="col" class="text-center">#</th>
                      <th scope="col">Mặt hàng</th>
                      <th scope="col" class="text-end">Giá bán</th>
                      <th scope="col" class="text-center">Số lượng</th>
                      <th scope="col" class="text-end">Thành tiền</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr
                      v-for="(item, index) in selectedOrder.order_items"
                      :key="item.id"
                    >
                      <td class="text-center">{{ index + 1 }}</td>
                      <td>
                        <div class="d-flex align-items-center">
                          <img
                            :src="item.variant.main_image_url"
                            style="width: 30px; height: auto"
                            class="me-2"
                            :alt="item.variant.slug"
                            @click="openImage(item.variant.main_image_url)"
                          />
                          <div>
                            <div class="fw-bold">
                              {{ item.variant.product.name }}
                            </div>
                            <small class="text-muted"
                              >Kích thước:
                              {{
                                getVariantAttributes(item.variant.attributes)
                              }}</small
                            >
                          </div>
                        </div>
                      </td>
                      <td class="text-end">
                        {{ FormatData.formatNumber(item.unit_price) }} VNĐ
                      </td>
                      <td class="text-center">{{ item.quantity }}</td>
                      <td class="text-end">
                        {{ FormatData.formatNumber(item.subtotal) }} VNĐ
                      </td>
                    </tr>
                  </tbody>
                  <tfoot>
                    <tr>
                      <td colspan="4" class="fw-bold">Tổng tiền hàng:</td>
                      <td class="text-end">
                        {{
                          FormatData.formatNumber(
                            calculateSubtotal(selectedOrder.order_items)
                          )
                        }}
                        VNĐ
                      </td>
                    </tr>
                    <tr class="table-info">
                      <td colspan="4" class="fw-bold">Tổng thanh toán:</td>
                      <td class="text-end fw-bold">
                        {{
                          FormatData.formatNumber(selectedOrder.total_amount)
                        }}
                        VNĐ
                      </td>
                    </tr>
                  </tfoot>
                </table>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button
            type="button"
            class="btn btn-secondary"
            data-bs-dismiss="modal"
          >
            Đóng
          </button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="cancelModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="cancelModalLabel">
            Bạn có chắc muốn hủy đơn hàng?
          </h1>
          <button
            type="button"
            class="btn-close"
            data-bs-dismiss="modal"
            aria-label="Close"
          ></button>
        </div>
        <div class="modal-body">
          <p>Vui lòng cho chúng tôi biết lý do đơn hàng bị hủy:</p>
          <div class="form-check">
            <input
              class="form-check-input"
              type="radio"
              name="cancelReason"
              id="reason1"
              value="Hết hàng"
              v-model="cancellation_reason"
            />
            <label
              class="form-check-label"
              for="reason1"
              @click="check = false"
            >
              Hết hàng
            </label>
          </div>
          <div class="form-check">
            <input
              class="form-check-input"
              type="radio"
              name="cancelReason"
              id="reason2"
              value="Đơn vị vận chuyển lấy hàng không thành công"
              v-model="cancellation_reason"
              @click="check = false"
            />
            <label class="form-check-label" for="reason2">
              Đơn vị vận chuyển lấy hàng không thành công
            </label>
          </div>
          <div class="form-check">
            <input
              class="form-check-input"
              type="radio"
              name="cancelReason"
              id="reason3"
              value="Có lỗi xảy ra trong quá trình giao hàng"
              v-model="cancellation_reason"
              @click="check = false"
            />
            <label class="form-check-label" for="reason3">
              Có lỗi xảy ra trong quá trình giao hàng
            </label>
          </div>
          <div class="form-check">
            <input
              class="form-check-input"
              type="radio"
              name="cancelReason"
              id="reason4"
              value="Khác"
              @click="(check = true), (cancellation_reason = null)"
            />
            <label class="form-check-label" for="reason4">
              Khác (vui lòng nhập bên dưới)
            </label>
          </div>
          <textarea
            v-if="check"
            class="form-control mt-3"
            rows="3"
            placeholder="Nhập lý do khác..."
            v-model="cancellation_reason"
          ></textarea>
        </div>
        <div class="modal-footer">
          <button
            type="button"
            class="btn btn-secondary"
            data-bs-dismiss="modal"
          >
            Đóng
          </button>
          <button
            type="button"
            class="btn btn-danger"
            data-bs-dismiss="modal"
            @click="confirmCancellation"
          >
            Xác nhận hủy
          </button>
        </div>
      </div>
    </div>
  </div>
  <div v-if="showModal" class="modal-backdrop" @click="closeImage">
    <img :src="selectedImage" class="modal-image" />
  </div>
</template>

<style scoped>
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
