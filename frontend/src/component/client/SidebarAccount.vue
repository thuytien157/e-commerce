<template>
    <div class="col-12 col-md-4 col-lg-3">
        <div v-if="isLoading" class="card h-100 shadow-sm border-0 text-center p-4">
            <div class="skeleton-avatar mb-3 mx-auto"></div>

            <div class="skeleton-line w-75 mb-1 mx-auto"></div>
            <div class="skeleton-line w-50 mb-3 mx-auto"></div>

            <hr class="my-3" />

            <div class="list-group list-group-flush text-start">
                <div class="list-group-item">
                    <div class="skeleton-line w-100"></div>
                </div>
                <div class="list-group-item">
                    <div class="skeleton-line w-100"></div>
                </div>
                <div class="list-group-item">
                    <div class="skeleton-line w-100"></div>
                </div>
            </div>
        </div>

        <div v-else class="card h-100 shadow-sm border-0 text-center p-4 fade-in">
            <div class="avatar-wrapper mb-3 mx-auto">
                <img :src="user.avatar || '/images/products/product-1.jpg'" alt="Ảnh đại diện"
                    class="rounded-circle avatar-img" />
                <div class="avatar-overlay">
                    <label class="btn btn-light btn-sm m-0 cursor-pointer">
                        Đổi ảnh
                        <input type="file" ref="avatarInput" @change="handleFileChange" hidden />
                    </label>
                </div>
            </div>

            <h5 class="fw-bold mb-1">{{ user.username }}</h5>
            <p class="text-muted small mb-3">
                {{ user.email }}
            </p>

            <hr class="my-3" />

            <div class="list-group list-group-flush text-start">
                <a href="/update-user" class="list-group-item list-group-item-action text-decoration-none rounded mb-2">
                    <i class="bi bi-person me-2"></i>
                    Thông tin tài khoản
                </a>
                <router-link to="/order-history"
                    class="list-group-item list-group-item-action text-decoration-none rounded mb-2">
                    <i class="bi bi-box-seam me-2"></i>
                    Lịch sử mua hàng
                </router-link>
                <a href="#" class="list-group-item list-group-item-action text-primary rounded">
                    <i class="bi bi-box-arrow-right me-2"></i>
                    Đăng xuất
                </a>
            </div>
        </div>
    </div>
</template>
<script setup>
import axios from "axios";
import { useTokenUser } from "../store/useTokenUser";
import { onMounted, ref } from "vue";
import Swal from "sweetalert2";

const isLoading = ref(false);
const user = ref({});
const address = ref([]);
const store = useTokenUser();
const emits = defineEmits(["user-loaded"]);
const getUserById = async () => {
    isLoading.value = true;
    try {
        const res = await axios.get("http://127.0.0.1:8000/api/user", {
            headers: {
                Authorization: `Bearer ${store.token}`,
            },
        });

        user.value = res.data.user;
        address.value = res.data.user.addresses;
        emits("user-loaded", { address: address.value });
    } catch (error) {
        console.error("Lỗi khi lấy dữ liệu người dùng:", error);
    } finally {
        isLoading.value = false;
    }
};

const updateAvatar = async (file) => {
    const formData = new FormData();
    formData.append("avatar", file);

    try {
        const res = await axios.post(
            "http://127.0.0.1:8000/api/update-avatar",
            formData,
            {
                headers: {
                    Authorization: `Bearer ${store.token}`,
                    "Content-Type": "multipart/form-data",
                },
            }
        );
        user.value.avatar = res.data.avatar;
        Swal.fire({
            toast: true,
            position: "top-end",
            icon: "success",
            title: "Cập nhật thành công!",
            showConfirmButton: false,
            timer: 1000,
            timerProgressBar: true,
        });
    } catch (error) {
        if (error.response && error.response.status === 422) {
            const validationErrors = error.response.data.errors;
            let errorMessages = "";
            if (validationErrors) {
                for (const field in validationErrors) {
                    if (Array.isArray(validationErrors[field])) {
                        validationErrors[field].forEach((message) => {
                            errorMessages += `${message}<br>`;
                        });
                    } else {
                        errorMessages += `${validationErrors[field]}<br>`;
                    }
                }
            }

            Swal.fire({
                toast: true,
                position: "top-end",
                icon: "error",
                title: "Lỗi validation",
                html: errorMessages,
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
            });
        }
    }
};
const avatarInput = ref(null);

const handleFileChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        updateAvatar(file);
    }
};

onMounted(() => {
    getUserById();
});
</script>
<style scoped>
.skeleton-line {
    background-color: #e2e5e7;
    border-radius: 4px;
    height: 1rem;
    animation: pulse 1.5s infinite ease-in-out;
}

.skeleton-line.w-75 {
    width: 75%;
}

.skeleton-line.w-50 {
    width: 50%;
}

.skeleton-avatar {
    background-color: #e2e5e7;
    width: 120px;
    height: 120px;
    border-radius: 50%;
    animation: pulse 1.5s infinite ease-in-out;
}

@keyframes pulse {
    0% {
        background-color: #e2e5e7;
    }

    50% {
        background-color: #f2f3f5;
    }

    100% {
        background-color: #e2e5e7;
    }
}

:root {
    --primary-color: #3866bc;
    --secondary-color: #f8f9fa;
    --text-color: #495057;
    --shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.05);
}

.account-page {
    background-color: #f0f2f5;
    /* Màu nền nhẹ nhàng hơn */
}

.card {
    border-radius: 12px;
}

/* Khu vực Avatar */
.avatar-wrapper {
    width: 120px;
    height: 120px;
    position: relative;
    transition: transform 0.3s ease;
}

.avatar-wrapper:hover {
    transform: scale(1.05);
}

.avatar-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border: 4px solid var(--primary-color);
    box-shadow: 0 0 0 2px rgba(17, 35, 202, 0.2);
}

.avatar-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    border-radius: 50%;
    background-color: rgba(0, 0, 0, 0.6);
    display: flex;
    justify-content: center;
    align-items: center;
    opacity: 0;
    transition: opacity 0.3s ease;
    cursor: pointer;
}

.avatar-wrapper:hover .avatar-overlay {
    opacity: 1;
}

/* Menu điều hướng */
.list-group-item {
    border: none;
    border-radius: 8px;
    color: var(--text-color);
    transition: all 0.3s ease;
}

.list-group-item:hover {
    background-color: var(--secondary-color);
    color: #000;
}

.list-group-item.active {
    background-color: var(--primary-color);
    color: #fff;
    font-weight: bold;
}

.list-group-item.text-danger:hover {
    background-color: rgba(202, 17, 31, 0.1);
    color: var(--primary-color) !important;
}

/* Form inputs */
.form-control:focus,
.form-control:active {
    border-color: var(--primary-color) !important;
    box-shadow: 0 0 0 0.25rem rgba(202, 17, 31, 0.25);
}

/* Responsive adjustments */
@media (max-width: 767.98px) {
    .list-group {
        overflow-x: auto;
        flex-wrap: nowrap;
        -webkit-overflow-scrolling: touch;
    }

    .list-group::-webkit-scrollbar {
        display: none;
    }

    .list-group-item {
        display: flex;
        flex-wrap: wrap;
        text-align: center;
    }

    .list-group-item i {
        display: none;
        /* Ẩn icon trên mobile để tiết kiệm diện tích */
    }

    .list-group-item p {
        margin-bottom: 0;
    }
}

.fade-in {
    animation: fadeIn 0.4s ease-in-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(8px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
