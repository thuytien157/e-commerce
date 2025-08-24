<script setup>
import Pagination from "@/component/admin/Pagination.vue";
import Rating from "@/component/client/Rating.vue";
import FormatData from "@/component/store/FormatData";
import { useTokenUser } from "@/component/store/useTokenUser";
import axios from "axios";
import Swal from "sweetalert2";
import { onMounted, ref, computed, watch } from "vue";

const reviews = ref([]);
const pageSize = ref(5);
const currentPage = ref(1);
const selectedRating = ref(null);
const selectedReview = ref(null);
const selectedStatus = ref(null);
const isLoading = ref(true);

const getAllReviews = async () => {
  try {
    const res = await axios.get(`${import.meta.env.VITE_URL_API}api/review`);
    reviews.value = res.data.reviews;
  } catch (error) {
    console.log(error);
  } finally {
    isLoading.value = false;
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

const filteredReviews = computed(() => {
  return reviews.value.filter((review) => {
    const isselectedRating =
      !selectedRating.value || review.rating == selectedRating.value;
    const isselectedStatus =
      !selectedStatus.value || review.status == selectedStatus.value;

    return isselectedRating && isselectedStatus;
  });
});
const paginatedAndFilteredReviews = computed(() => {
  const startIndex = (currentPage.value - 1) * pageSize.value;
  const endIndex = startIndex + pageSize.value;
  return filteredReviews.value.slice(startIndex, endIndex);
});

const totalPages = computed(() => {
  return Math.ceil(filteredReviews.value.length / pageSize.value);
});

const updateCurrentPage = (page) => {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page;
  }
};

const selectReview = (review) => {
  selectedReview.value = review;
};
const store = useTokenUser();
const reply_text = ref("");
const replyReview = async () => {
  const id = selectedReview.value.id;
  try {
    const result = await Swal.fire({
      title: "Bạn có chắc trả lời đánh giá này?",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: "Xác nhận",
      cancelButtonText: "Hủy",
    });

    if (result.isConfirmed) {
      await axios.put(
        `${import.meta.env.VITE_URL_API}api/review/reply/${id}`,
        {
          reply_text: reply_text.value,
        },
        {
          headers: {
            Authorization: `Bearer ${store.token}`,
          },
        }
      );
      await getAllReviews();
      Swal.fire({
        toast: true,
        position: "top-end",
        icon: "success",
        title: "Gửi trả lời thành công!",
        showConfirmButton: false,
        timer: 2000,
        timerProgressBar: true,
      });
    }
  } catch (error) {
    console.log(error);
  }
};

const hideReview = async (id) => {
  try {
    const result = await Swal.fire({
      title: "Bạn có chắc muốn duyệt đánh giá này?",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: "Xác nhận",
      cancelButtonText: "Hủy",
    });

    if (result.isConfirmed) {
      await axios.put(`${import.meta.env.VITE_URL_API}api/review/hide/${id}`);
      await getAllReviews();
      Swal.fire({
        toast: true,
        position: "top-end",
        icon: "success",
        title: "Đánh giá đã được duyệt thành công!",
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
      title: "Duyệt đánh giá không thành công!",
      showConfirmButton: false,
      timer: 2000,
      timerProgressBar: true,
    });
  }
};

watch([selectedRating, pageSize], () => {
  currentPage.value = 1;
});

onMounted(async () => {
  isLoading.value = true;
  await getAllReviews();
});
</script>
<template>
  <div class="d-flex justify-content-between">
    <h3 class="text-primary fw-bold">Danh sách đánh giá</h3>
  </div>
  <div class="d-flex gap-3 flex-wrap mb-2">
    <div>
      <label for="rating-select" class="form-label">Số sao</label>
      <select id="rating-select" class="form-select rounded-2" v-model="selectedRating">
        <option :value="null">Chọn số sao</option>
        <option value="1">1 ⭐️</option>
        <option value="2">2 ⭐️</option>
        <option value="3">3 ⭐️</option>
        <option value="4">4 ⭐️</option>
        <option value="5">5 ⭐️</option>
      </select>
    </div>
    <div>
      <label for="rating-select" class="form-label">Trạng thái</label>
      <select id="rating-select" class="form-select rounded-2" v-model="selectedStatus">
        <option :value="null">Chọn trạng thái</option>
        <option value="published">Đã duyệt</option>
        <option value="discontinued">Chưa duyệt</option>
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
          <th>Khách hàng</th>
          <th>Đánh giá</th>
          <th>Ngày đánh giá</th>
          <th>Sản phẩm</th>
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
        <template v-else-if="paginatedAndFilteredReviews.length > 0" v-for="review in paginatedAndFilteredReviews"
          :key="review.id">
          <tr>
            <td>{{ review.id }}</td>
            <td>
              <div class="d-flex align-items-center gap-2">
                <img :src="review.customer.avatar" alt="avatar" class="rounded-circle"
                  style="width: 30px; height: 30px; object-fit: cover" />
                <div class="d-flex flex-column">
                  <span>{{ review.customer.username }}</span>
                  <small class="text-muted">{{ review.customer.email }}</small>
                </div>
              </div>
            </td>
            <td>
              <Rating :rating="review.rating" />
            </td>
            <td>{{ FormatData.formatDateTime(review.create_at) }}</td>
            <td>
              <ul>
                <li>
                  Sản phẩm
                  <a
                    :href="`http://localhost:5173/product-detail/${review.product.variants[0].slug}/${review.product.id}`">{{
                    review.product.id }}</a>
                </li>
              </ul>
            </td>
            <td>
              <div class="d-flex gap-2">
                <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#reviewDetailModal"
                  @click="selectReview(review)">
                  Xem chi tiết
                </button>
                <button class="btn btn-danger btn-sm" v-if="review.status == 'discontinued'"
                  @click="hideReview(review.id)">
                  Duyệt
                </button>
                <button class="btn btn-warning btn-sm" v-if="!review.reply_text" data-bs-toggle="modal"
                  data-bs-target="#replyModal" @click="selectReview(review)">
                  Trả lời
                </button>
              </div>
            </td>
          </tr>
        </template>
        <tr v-else>
          <td colspan="6" class="text-center text-secondary">
            Không có đánh giá nào
          </td>
        </tr>
      </tbody>
    </table>
  </div>

  <Pagination :current-page="currentPage" @update:page="updateCurrentPage" :total-pages="totalPages" />

  <div class="modal fade" id="reviewDetailModal" tabindex="-1" aria-labelledby="reviewDetailModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="reviewDetailModalLabel">
            Chi tiết đánh giá
          </h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body d-flex gap-3" v-if="selectedReview">
          <div class="w-50">
            <p><strong>Nội dung đánh giá:</strong></p>
            <p>{{ selectedReview.comment }}</p>
            <div v-if="selectedReview.images && selectedReview.images.length > 0">
              <p><strong>Ảnh đính kèm:</strong></p>
              <div class="d-flex flex-wrap gap-2">
                <img v-for="image in selectedReview.images" :key="image.id" :src="image.image_url" alt="review-image"
                  class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover"
                  @click="openImage(image.image_url)" />
              </div>
            </div>
          </div>
          <div class="w-50 border-start ps-2">
            <p><strong>Phản hồi của Admin:</strong></p>
            <p>{{ selectedReview.reply_text || "Chưa có phản hồi" }}</p>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            Đóng
          </button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="replyModal" tabindex="-1" aria-labelledby="replyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="replyModalLabel">
            Trả lời đánh giá
          </h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body d-flex gap-3" v-if="selectedReview">
          <textarea name="" id="" cols="70" rows="5" v-model="reply_text"></textarea>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            Đóng
          </button>
          <button type="button" @click="replyReview()" class="btn btn-primary" data-bs-dismiss="modal">
            Gửi
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
