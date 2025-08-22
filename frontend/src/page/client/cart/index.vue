<script setup>
import { useCartStore } from '@/component/store/cart';
import { ref } from 'vue';

const cartStore = useCartStore()
const formatNumber = (num) => {
    return new Intl.NumberFormat('vi-VN').format(num);
};

</script>
<template>
    <div class="shopping-cart section pt-4">
        <div class="container bg-white p-3">
            <h5 class="">Giỏ hàng của bạn</h5>
            <div class="row">
                <div class="col-12 col-lg-8">
                    <div class="list-group " id="cart" v-if="cartStore.items.length > 0">
                        <div v-for="item in cartStore.items" :key="item.variantId"
                            class="list-group-item d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center flex-grow-1">
                                <input class="me-2" type="checkbox" :id="'item-' + item.variantId"
                                    v-model="item.isCheck" @change="cartStore.checkbox(item.variantId)">
                                <div class="ms-3">
                                    <p class="mb-1 fw-bold">{{ item.productName }}</p>
                                    <div class="text-muted small"> {{ item.selectedAttributes.join(", ") }}</div>
                                    
                                </div>
                            </div>
                            <div class="cart-action-col d-flex justify-content-between">
                                <p class="fw-bold mb-2" style="color: blue;">{{ formatNumber(item.price * item.quantity)
                                }}VND</p>
                                <div class="input-group input-group-sm quantity-control mb-2">
                                    <button class="btn btn-quantity" type="button"
                                        @click="cartStore.decrement(item.variantId)">−</button>
                                    <input type="number" class="form-control text-center form-control-quantity"
                                        :value="item.quantity" min="1" readonly>
                                    <button class="btn btn-quantity" type="button"
                                        @click="cartStore.increment(item.variantId)">+</button>

                                </div>
                                <div class="d-flex gap-4">
                                    <i class="bi bi-trash3-fill" @click.prevent="cartStore.removeItem(item.variantId)"
                                        style="color: red;"></i>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div v-else class="text-center p-5">
                        <p>Giỏ hàng của bạn trống.</p>
                    </div>
                </div>

                <div class="col-12 col-lg-4 mt-lg-0">
                    <div class="card">
                        <div class="card-body" id="tomtat">
                            <h5 class="card-title fw-bold" style="color: blue;">Tóm tắt đơn hàng</h5>
                            <div class="d-flex justify-content-between">
                                <p>Tổng tiền:</p>
                                <p class="fw-bold" style="color: blue;">{{ formatNumber(cartStore.totalPrice) }} VND</p>
                            </div>
                            <router-link to="/checkout" class="btn btn-primary w-100 mt-3">Tiến hành thanh
                                toán</router-link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* CSS cho desktop */
.shopping-cart .container {
    background-color: #fff;
    padding: 1.5rem;
    border-radius: 8px;
}

.img-thumbnail {
    width: 80px;
    height: 80px;
    object-fit: cover;
}

.list-group-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border: 1px solid #dee2e6;
    margin-bottom: 1rem;
    border-radius: 8px;
}

.quantity-control {
    width: 120px;
    border: 1px solid #ced4da;
    border-radius: 0.25rem;
}

.btn-quantity {
    background-color: #f8f9fa;
    border: none;
    font-weight: bold;
    color: #333;
    padding: 0.25rem 0.5rem;
}

.form-control-quantity {
    text-align: center;
    border-left: 1px solid #ced4da;
    border-right: 1px solid #ced4da;
    border-top: none;
    border-bottom: none;
}

.form-control-quantity:focus {
    box-shadow: none;
    border-color: #ced4da;
}

/* Ẩn mũi tên mặc định của input type="number" */
input[type=number]::-webkit-inner-spin-button,
input[type=number]::-webkit-outer-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

.cart-action-col {
    width: 50%;
}

@media (max-width: 991px) {
    .list-group-item {
        flex-direction: column;
        align-items: flex-start;
        padding: 1rem;
    }

    .list-group-item .d-flex.align-items-center {
        width: 100%;
        margin-bottom: 1rem;
    }

    .cart-action-col {
        width: 100%;
        flex-direction: row !important;
        justify-content: space-between;
        align-items: center !important;
    }

    .cart-action-col .fw-bold {
        margin-bottom: 0 !important;
    }

}
</style>