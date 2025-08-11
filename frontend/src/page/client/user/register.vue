<template>
  <div class="account-login section pt-2">
    <div class="container">
      <div class="row">
        <div class="col-lg-6 offset-lg-3 col-md-10 offset-md-1 col-12">
          <form class="card login-form" @submit.prevent="register">
            <div class="card-body">
              <div class="title">
                <h3>Đăng ký</h3>
              </div>
              <div class="social-login">
                <div class="row">
                  <div class="col-lg-12">
                    <button @click="redirectGG" type="button" class="btn google-btn">
                      <i class="lni lni-google"></i> Google login
                    </button>
                  </div>
                </div>
              </div>
              <div class="alt-option">
                <span>Hoặc</span>
              </div>
              <div class="form-group input-group">

                <label for="reg-fn">Email</label>
                <small class="text-danger" v-if="errors.email">{{ errors.email[0] }}</small>
                <input class="form-control" type="email" id="reg-email" v-model="email" />
              </div>
              <div class="form-group input-group">
                <label for="reg-fn">Mật khẩu</label>
                <small class="text-danger" v-if="errors.password">{{ errors.password[0] }}</small>
                <input class="form-control" type="password" id="reg-pass" v-model="password" />
              </div>
              <div class="form-group input-group">

                <label for="reg-fn1">Nhập lại mật khẩu</label>
                <input class="form-control" type="password" id="reg-pass1" v-model="password_confirm" />
              </div>
              <div class="d-flex flex-wrap justify-content-between bottom-content">
                <!-- <div class="form-check">
                  <input type="checkbox" class="form-check-input width-auto" id="exampleCheck1">
                  <label class="form-check-label">Remember me</label>
                </div> -->
                <router-link to="/forgot-password" class="lost-pass">Quên mật khẩu?</router-link>
              </div>
              <div class="button">
                <button class="btn button-submit" type="submit">
                  <svg aria-hidden="true" role="status" class="inline w-4 h-4 mr-3 text-white animate-spin"
                    v-if="isLoading" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                      d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                      fill="#E5E7EB"></path>
                    <path
                      d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                      fill="currentColor"></path>
                  </svg>
                  Đăng ký
                </button>
              </div>
              <p class="outer-link">
                Đã có tài khoản?
                <router-link to="/login">Đăng nhập </router-link>
              </p>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useTokenUser } from '@/component/store/useTokenUser';
import axios from 'axios';
import Swal from 'sweetalert2';
import { ref } from 'vue';
import { useRouter } from 'vue-router'
const isLoading = ref(false)
function redirectGG() {
  window.location.href = 'http://127.0.0.1:8000/api/google/redirect'
}

const email = ref('')
const password = ref('')
const password_confirm = ref('')
const errors = ref({})
const router = useRouter();
const store = useTokenUser()
const register = async () => {
  try {
    isLoading.value = true
    const res = await axios.post('http://127.0.0.1:8000/api/register', {
      email: email.value,
      password: password.value,
      password_confirmation: password_confirm.value,
    },
      {
        headers: {
          'Content-Type': 'application/json',
        }
      })
    const userStr = {
      username: res.data.username,
      id: res.data.user
    };
    store.setAuth(res.data.token, userStr);
    errors.value = {};
    router.push('/home')
    Swal.fire({
      toast: true,
      position: 'top-end',
      icon: 'success',
      title: 'Đăng ký thành công!',
      showConfirmButton: false,
      timer: 1000,
      timerProgressBar: true,
    });
  } catch (error) {
    if (error.response && error.response.status === 422) {
      errors.value = error.response.data.errors;
    }
  } finally {
    isLoading.value = false
  }
}
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

.account-login {
  background-color: #f9f9f9;
}

.account-login .login-form {
  padding: 35px;
  border: 1px solid #e6e6e6;
  border-radius: 4px;
  background-color: #fff;
  -webkit-box-shadow: 0px 5px 40px #00000008;
  box-shadow: 0px 5px 40px #00000008;
}

@media (max-width: 767px) {
  .account-login .login-form {
    padding: 30px;
  }
}

.account-login .login-form .card-body {
  padding: 0;
}

.account-login .login-form .title {
  margin-bottom: 20px;
}

.account-login .login-form .title h3 {
  font-size: 22px;
  font-weight: 700;
  color: #081828;
  margin-bottom: 10px;
}

.account-login .login-form .social-login .btn {
  padding: 10px 12px;
  font-size: 12px;
  border-radius: 4px;
  border: 1px solid #eee;
  display: block;
  font-weight: 600;
  width: 100%;
}

.account-login .login-form .social-login .btn i {
  display: inline-block;
  margin-right: 4px;
  font-size: 15px;
}

@media (max-width: 767px) {
  .account-login .login-form .social-login .btn {
    margin: 6px 0;
  }
}

.account-login .login-form .social-login .facebook-btn {
  border-color: #3b5998;
  background-color: transparent;
  color: #3b5998;
}

.account-login .login-form .social-login .facebook-btn:hover {
  background-color: #3b5998;
  border-color: transparent;
  color: #fff;
}

.account-login .login-form .social-login .twitter-btn {
  border-color: #55acee;
  background-color: transparent;
  color: #55acee;
}

.account-login .login-form .social-login .twitter-btn:hover {
  background-color: #55acee;
  border-color: transparent;
  color: #fff;
}

.account-login .login-form .social-login .google-btn {
  border-color: #dd4b39;
  background-color: transparent;
  color: #dd4b39;
}

.account-login .login-form .social-login .google-btn:hover {
  background-color: #dd4b39;
  border-color: transparent;
  color: #fff;
}

.account-login .alt-option {
  margin: 10px 0;
  text-align: center;
  display: inline-block;
  position: relative;
  width: 100%;
  z-index: 1;
}

@media (max-width: 767px) {
  .account-login .alt-option {
    margin: 20px 0;
  }
}

.account-login .alt-option span {
  font-size: 14px;
  background: #fff;
  color: #888;
  padding: 5px 15px;
}

.account-login .alt-option:before {
  position: absolute;
  left: 0;
  top: 50%;
  height: 1px;
  width: 100%;
  background: #e6e6e6;
  content: "";
  z-index: -1;
}

.account-login .form-group {
  margin-bottom: 10px;
}

.account-login .form-group label {
  display: block;
  margin-bottom: 8px;
  width: 100%;
}

.account-login .form-group .form-control {
  padding: 0 10px;
  -webkit-transition: color 0.25s, background-color 0.25s, border-color 0.25s;
  transition: color 0.25s, background-color 0.25s, border-color 0.25s;
  border: 1px solid #e0e0e0;
  background-color: #fff;
  color: #505050;
  font-size: 14px;
  -webkit-appearance: none;
  -moz-appearance: none;
  appearance: none;
  height: 40px;
  border-radius: 4px !important;
  overflow: hidden;
  width: 100%;
}

.account-login .form-group .form-control:focus {
  border-color: #0167f3;
}

.account-login .lost-pass {
  color: #5c5c5c;
  font-size: 14px;
}

.account-login .lost-pass:hover {
  color: #0167f3;
}

.account-login .button {
  margin-top: 30px;
}

.account-login .button .btn {
  width: 100%;
}

.account-login .outer-link {
  display: block;
  font-size: 14px;
  font-weight: 500;
  color: #081828;
  margin-top: 30px;
  text-align: center;
}

.account-login .outer-link a {
  color: #0167f3;
}

.account-login .outer-link a:hover {
  text-decoration: underline;
}
</style>
