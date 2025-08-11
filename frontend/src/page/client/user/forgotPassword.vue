<template>
    <div class="form-wrapper">
        <div class="form-container text-center">
            <h2 class="mb-4">ĐẶT LẠI MẬT KHẨU</h2>

            <!-- BƯỚC 1: GỬI MÃ XÁC NHẬN -->
            <form v-if="step == 1" id="send-code-form shadow-sm p-3 mb-5 bg-body-tertiary rounded"
                @submit.prevent="sendMail">
                <small class="text-danger" v-if="errors.email">{{
                    errors.email[0]
                    }}</small>
                <div class="mb-3">
                    <input type="email" name="email" class="form-control" id="email"
                        placeholder="Nhập địa chỉ email của bạn" v-model="email" />
                </div>

                <button type="submit" class="btn btn-black w-100 button-submit">
                    <svg aria-hidden="true" role="status" class="inline w-4 h-4 mr-3 text-white animate-spin"
                        v-if="isLoading" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                            fill="#E5E7EB"></path>
                        <path
                            d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                            fill="currentColor"></path>
                    </svg>
                    Gửi mã xác nhận
                </button>
            </form>

            <!-- BƯỚC 2: NHẬP MÃ XÁC NHẬN -->
            <form v-if="step == 2" id="verify-code-form" @submit.prevent="verifyResetCode">
                <small class="text-danger" v-if="errors.token">{{
                    errors.token[0]
                    }}</small>
                <div class="mb-3 d-flex justify-content-between code-inputs">
                    <input type="text" maxlength="1" class="form-control text-center code-input"
                        @input="handleInput($event, 1)" @paste="handlePaste" required />
                    <input type="text" maxlength="1" class="form-control text-center code-input"
                        @input="handleInput($event, 2)" @keydown="handleKeydown()" required />
                    <input type="text" maxlength="1" class="form-control text-center code-input"
                        @input="handleInput($event, 3)" @keydown="handleKeydown()" required />
                    <input type="text" maxlength="1" class="form-control text-center code-input"
                        @input="handleInput($event, 4)" @keydown="handleKeydown()" required />
                    <input type="text" maxlength="1" class="form-control text-center code-input"
                        @input="handleInput($event, 5)" @keydown="handleKeydown()" required />
                </div>
                <div class="mb-2 text-muted" id="countdown">
                    Thời gian còn lại: {{ minutes }}:{{ seconds }}
                </div>

                <button type="submit" class="btn btn-black w-100">Xác minh mã</button>
            </form>

            <!-- BƯỚC 3: ĐẶT LẠI MẬT KHẨU -->
            <form v-if="step == 3" id="reset-password-form" @submit.prevent="resetPass">
                <small class="text-danger" v-if="errors.password">{{
                    errors.password[0]
                    }}</small>
                <div class="mb-3">
                    <input type="password" class="form-control" placeholder="Mật khẩu mới" v-model="password" />
                </div>
                <div class="mb-3">
                    <input type="password" class="form-control" placeholder="Nhập lại mật khẩu mới"
                        v-model="password_confirm" />
                </div>

                <button type="submit" class="btn btn-black w-100">
                    Đặt lại mật khẩu
                </button>
            </form>

            <!-- BƯỚC 4: HOÀN TẤT -->
            <div id="reset-complete" style="display: none">
                <p class="text-success">Mật khẩu đã được đặt lại thành công!</p>
                <a href="/login" class="btn btn-black w-100 mt-3">Quay lại đăng nhập</a>
            </div>
        </div>
    </div>
</template>
<script setup>
import { useTokenUser } from "@/component/store/useTokenUser";
import axios from "axios";
import Swal from "sweetalert2";
import { ref, watch } from "vue";
import { useRouter } from "vue-router";
const isLoading = ref(false);
const errors = ref({});
const email = ref("");
const step = ref(1);
const expires_at = ref(null);
const countdownInterval = ref(null);
const minutes = ref(5);
const seconds = ref(0);

const sendMail = async () => {
    try {
        isLoading.value = true;
        const res = await axios.post("http://127.0.0.1:8000/api/sendcode", {
            email: email.value,
        });
        Swal.fire({
            toast: true,
            position: "top-end",
            icon: "success",
            title: "Đã gửi mã về Email của bạn!",
            showConfirmButton: false,
            timer: 1000,
            timerProgressBar: true,
        });
        errors.value = {};
        expires_at.value = new Date(res.data.expires_at);
        step.value = 2;
    } catch (error) {
        if (error.response && error.response.status === 422) {
            errors.value = {};
            errors.value = error.response.data.errors;
        } else if (error.response && error.response.status === 404) {
            errors.value = {};
            errors.value = {
                email: [error.response.data.message],
            };
        }
    } finally {
        isLoading.value = false;
    }
};
const otpCode = ref(null);
const updateCountdown = () => {
    const now = new Date();
    const diff = expires_at.value - now;

    if (diff <= 0) {
        clearInterval(countdownInterval.value);
        minutes.value = 0;
        seconds.value = 0;
        Swal.fire({
            icon: "error",
            text: "Mã xác minh của bạn đã hết hạn!",
            confirmButtonText: "Quay lại",
            confirmButtonColor: "#3866bc",
        }).then((result) => {
            if (result.isConfirmed) {
                step.value = 1;
            }
        });
    } else {
        minutes.value = Math.floor((diff / 1000 / 60) % 60);
        seconds.value = Math.floor((diff / 1000) % 60);
    }
};

const handleInput = (event, nextInputIndex) => {
    const input = event.target;
    input.value = input.value.replace(/[^0-9]/g, "");

    if (input.value.length === 1 && nextInputIndex < 5) {
        document.querySelectorAll(".code-input")[nextInputIndex].focus();
    }

    updateOtpCode();
};

const handlePaste = (event) => {
    const pasteData = event.clipboardData.getData("text").slice(0, 5);
    const inputs = document.querySelectorAll(".code-input");

    if (!/^\d+$/.test(pasteData)) {
        event.preventDefault();
        return;
    }

    for (let i = 0; i < pasteData.length; i++) {
        inputs[i].value = pasteData[i];
    }
    updateOtpCode();
};

const handleKeydown = (event, prevInputIndex) => {
    if (event.key === "Backspace" && prevInputIndex >= 0) {
        const inputs = document.querySelectorAll(".code-input");
        inputs[prevInputIndex].focus();
    }
    updateOtpCode();
};

const updateOtpCode = () => {
    const inputs = document.querySelectorAll(".code-input");
    let code = "";
    inputs.forEach((input) => {
        code += input.value;
    });
    otpCode.value = code;
};

const verifyResetCode = async () => {
    if (otpCode.value.length < 6) {
        Swal.fire({
            toast: true,
            position: "top-end",
            icon: "error",
            title: "Vui lòng nhập đầy đủ 6 chữ số.",
            showConfirmButton: false,
            timer: 1000,
            timerProgressBar: true,
        });
        return;
    }
    try {
        isLoading.value = true;
        const res = await axios.post("http://127.0.0.1:8000/api/verify-code", {
            email: email.value,
            token: otpCode.value,
        });
        Swal.fire({
            toast: true,
            position: "top-end",
            icon: "success",
            title: "Mã xác minh hợp lệ!",
            showConfirmButton: false,
            timer: 1000,
            timerProgressBar: true,
        });
        errors.value = {};
        expires_at.value = new Date(res.data.expires_at);
        step.value = 3;
    } catch (error) {
        if (error.response && error.response.status === 422) {
            errors.value = {};
            errors.value = error.response.data.errors;
        } else if (error.response && error.response.status === 404) {
            errors.value = {};
            errors.value = {
                token: [error.response.data.message],
            };
        } else if (error.response && error.response.status === 410) {
            errors.value = {};
            errors.value = {
                token: [error.response.data.message],
            };
        }
    } finally {
        isLoading.value = false;
    }
};

const store = useTokenUser();
const router = useRouter();
const password = ref("");
const password_confirm = ref("");
const resetPass = async () => {
    isLoading.value = true;
    try {
        const res = await axios.post("http://127.0.0.1:8000/api/reset-password", {
            email: email.value,
            password: password.value,
            password_confirmation: password_confirm.value,
        });
        const userStr = {
            username: res.data.username,
            id: res.data.user,
        };

        store.setAuth(res.data.token, userStr);
        router.push("/home");
        Swal.fire({
            toast: true,
            position: "top-end",
            icon: "success",
            title: "Đổi mật khẩu thành công",
            showConfirmButton: false,
            timer: 1000,
            timerProgressBar: true,
        });
    } catch (error) {
        if (error.response && error.response.status === 422) {
            errors.value = {};
            errors.value = error.response.data.errors;
        } else if (error.response && error.response.status === 400) {
            errors.value = {};
            errors.value = {
                password: [error.response.data.message],
            };
        }
    } finally {
        isLoading.value = false;
    }
};

watch(expires_at, (newValue) => {
    if (newValue) {
        if (countdownInterval.value) {
            clearInterval(countdownInterval.value);
        }
        countdownInterval.value = setInterval(updateCountdown, 1000);
    }
});
</script>

<style scoped>
.button-submit svg {
    display: inline;
    width: 1.3rem;
    height: 1.3rem;
    margin-right: 0.75rem;
    color: white;
    animation: spin_357 1s linear infinite;
}

@keyframes spin_357 {
    from {
        transform: rotate(0deg);
    }

    to {
        transform: rotate(360deg);
    }
}

.form-container {
    max-width: 500px;
    width: 100%;
    padding: 20px;
    background: #fff;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.btn-black {
    background-color: #3866bc;
    color: #fff;
}

.btn-black:hover {
    background-color: #5582d6;
    color: white;
}

.form-container input,
.form-container select,
.btn {
    border-radius: 0 !important;
}

h2 {
    font-size: 1.5rem;
}

.form-wrapper {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 200px;
    padding: 50px;
}

.code-inputs input {
    font-size: 1.5rem;
    padding: 0.5rem;
    border: 1px solid rgb(192, 192, 192);
}
</style>
