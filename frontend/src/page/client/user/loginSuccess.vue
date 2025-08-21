<template>
    <main class="container-fluid d-flex justify-content-center align-items-center vh-100">
        <div v-if="loading" class="text-center">
            <div class="spinner">
                <span></span>
                <span></span>
                <span></span>
                <span></span>
            </div>
            <p class="mt-3 fs-5 fw-bold text-secondary">Đang xử lý...</p>
        </div>
        <div v-else>
            </div>
    </main>
</template>

<script setup>
import { onMounted, ref } from "vue";
import { useRoute, useRouter } from "vue-router";
import Swal from 'sweetalert2';
import { useTokenUser } from "@/component/store/useTokenUser";

const route = useRoute();
const router = useRouter();
const loading = ref(true);
const store = useTokenUser();

const handleLoginCallback = async () => {
    const token = route.query.token;
    const loginExistingAccount = route.query.login_existing_account;
    const banned = route.query.banned;
    const user = {
        username: route.query.username,
        id: route.query.id,
        role: route.query.role
    };

    if (banned === 'true') {
        loading.value = false;
        await Swal.fire({
            title: 'Tài khoản bị khoá!',
            text: "Tài khoản này đã bị khoá! Bạn không thể đăng nhập",
            icon: 'warning',
            showCancelButton: false,
            confirmButtonColor: '#55acee',
            confirmButtonText: 'Quay về trang chủ',
        });
        router.push('/login'); 
        return;
    }

    if (loginExistingAccount === 'true' && token) {
        loading.value = false;
        const result = await Swal.fire({
            title: 'Đã có tài khoản!',
            text: "Email này đã được đăng ký. Bạn có muốn đăng nhập không?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Đăng nhập',
            cancelButtonText: 'Hủy'
        });

        if (result.isConfirmed) {
            store.setAuth(token, user);
            await Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'Đăng nhập thành công!',
                showConfirmButton: false,
                timer: 1000,
                timerProgressBar: true,
            });
            
            if (user.role === 'customer') {
                router.push('/home');
            } else {
                router.push('/admin');
            }
        } else {
            router.push('/login');
        }
        return;
    }

    if (token && user.username) {
        store.setAuth(token, user);
        
        await Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: 'Đăng nhập thành công!',
            showConfirmButton: false,
            timer: 1000,
            timerProgressBar: true,
        });

        if (user.role === 'customer') {
            router.push('/home');
        } else {
            router.push('/admin');
        }
        loading.value = false; 
        return;
    }
    
    loading.value = false;
    router.push('/login');
};

onMounted(() => {
    handleLoginCallback();
});
</script>
<style scoped>
.spinner {
    --gap: 5px;
    --clr: #55acee;
    --height: 23px;
    width: 100px;
    height: 100px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: var(--gap);
}

.spinner span {
    background: var(--clr);
    width: 6px;
    height: var(--height);
    animation: grow 1s ease-in-out infinite;
}

.spinner span:nth-child(2) {
    animation: grow 1s ease-in-out 0.15s infinite;
}

.spinner span:nth-child(3) {
    animation: grow 1s ease-in-out 0.3s infinite;
}

.spinner span:nth-child(4) {
    animation: grow 1s ease-in-out 0.475s infinite;
}

@keyframes grow {

    0%,
    100% {
        transform: scaleY(1);
    }

    50% {
        transform: scaleY(1.8);
    }
}
</style>