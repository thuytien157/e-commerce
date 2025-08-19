<script setup>
import { useCartStore } from "@/component/store/cart";
import FormatData from "@/component/store/FormatData";
import { useTokenUser } from "@/component/store/useTokenUser";
import axios from "axios";
import Swal from "sweetalert2";
import { useRouter } from "vue-router";
import { computed, onMounted, ref, watch } from "vue";
const route = useRouter()
const store = useTokenUser();
const cartStore = useCartStore();
const address = ref({
    customer_name: "",
    phone: "",
    province_id: "",
    district_id: "",
    ward_code: "",
    address: "",
    email: "",
});
const defaultAddressFromApi = ref(null);

const provinces = ref([]);
const districts = ref([]);
const wards = ref([]);
const services = ref([]);
const selectedServiceId = ref(null);
const selectedProvince = ref(null);
const selectedDistrict = ref(null);
const selectedWard = ref(null);
const ghnToken = import.meta.env.VITE_GHN_TOKEN;
const shippingFee = ref(0);

const totalPayment = computed(() => {
    return cartStore.totalPrice + shippingFee.value;
});
const email = ref('')
const getUserById = async () => {
    try {
        if (store.token) {
            const res = await axios.get("http://127.0.0.1:8000/api/user", {
                headers: {
                    Authorization: `Bearer ${store.token}`,
                },
            });
            defaultAddressFromApi.value = res.data.defaultAddress;
            email.value = res.data.user.email
        } else {
            defaultAddressFromApi.value = {};
        }
    } catch (error) {
        console.error("Lỗi khi lấy dữ liệu người dùng:", error);
    }
};

const fetchProvinces = async () => {
    try {
        const res = await axios.get(
            "https://online-gateway.ghn.vn/shiip/public-api/master-data/province",
            { headers: { Token: ghnToken } }
        );
        provinces.value = res.data.data;
    } catch (err) {
        console.error("fetchProvinces error:", err);
    }
};

const fetchDistricts = async (provinceId = selectedProvince.value) => {
    selectedDistrict.value = null;
    selectedWard.value = null;
    districts.value = [];
    wards.value = [];
    shippingFee.value = 0;

    if (!provinceId) return;

    const provinceIdAsNumber = Number(provinceId);

    try {
        const res = await axios.post(
            "https://online-gateway.ghn.vn/shiip/public-api/master-data/district",
            { province_id: provinceIdAsNumber },
            { headers: { Token: ghnToken } }
        );
        districts.value = res.data.data;
    } catch (err) {
        console.error("fetchDistricts error:", err);
    }
};

const fetchWards = async (districtId = selectedDistrict.value) => {
    selectedWard.value = null;
    wards.value = [];
    shippingFee.value = 0;

    if (!districtId) return;

    const districtIdAsNumber = Number(selectedDistrict.value);

    try {
        const res = await axios.post(
            "https://online-gateway.ghn.vn/shiip/public-api/master-data/ward",
            { district_id: districtIdAsNumber },
            { headers: { Token: ghnToken } }
        );
        wards.value = res.data.data;
    } catch (err) {
        console.error("fetchWards error:", err);
    }
};

const fetchServices = async () => {
    if (!selectedDistrict.value) {
        services.value = [];
        return;
    }
    try {
        const res = await axios.post("http://127.0.0.1:8000/api/ghn/service", {
            to_district_id: selectedDistrict.value,
        });
        services.value = res.data;
        selectedServiceId.value =
            services.value.length > 0 ? services.value[0].service_id : null;
    } catch (err) {
        console.error("Lỗi khi lấy dịch vụ vận chuyển:", err);
    }
};

const calculateShippingFee = async () => {
    if (
        !selectedDistrict.value ||
        !selectedWard.value ||
        !selectedServiceId.value
    ) {
        shippingFee.value = 0;
        return;
    }

    try {
        const res = await axios.post(
            "https://online-gateway.ghn.vn/shiip/public-api/v2/shipping-order/fee",
            {
                service_id: Number(selectedServiceId.value),
                to_district_id: Number(selectedDistrict.value),
                to_ward_code: selectedWard.value.toString(),
                insurance_value: cartStore.totalPrice,
                from_district_id: 1454,
                weight: 100,
                length: 20,
                width: 20,
                height: 10,
            },
            {
                headers: {
                    Token: ghnToken,
                    "Content-Type": "application/json",
                },
            }
        );

        shippingFee.value = res.data.data.total;
    } catch (err) {
        console.error("Lỗi khi tính phí ship:", err);
        shippingFee.value = 0;
    }
};

const fillFormWithDefaultAddress = async () => {
    const defaultAddress = defaultAddressFromApi.value;

    address.value.customer_name = defaultAddress.customer_name;
    address.value.phone = defaultAddress.phone;
    address.value.address = defaultAddress.address;

    selectedProvince.value = defaultAddress.province_id;
    await fetchDistricts();
    selectedDistrict.value = defaultAddress.district_id;
    await fetchWards();
    selectedWard.value = defaultAddress.ward_code;

    await fetchServices();
    await calculateShippingFee();
};
const errors = ref({});
const payment_method = ref("COD");

const order = async () => {
    try {
        if (cartStore.items.length <= 0) {
            Swal.fire({
                toast: true,
                position: "top-end",
                icon: "error",
                title: "Giỏ hàng trống",
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true,
            });
            return
        }
        const provinceName =
            provinces.value.find((p) => p.ProvinceID === selectedProvince.value)
                ?.ProvinceName || "";
        const districtName =
            districts.value.find((d) => d.DistrictID === selectedDistrict.value)
                ?.DistrictName || "";
        const wardName =
            wards.value.find((w) => w.WardCode === String(selectedWard.value))
                ?.WardName || "";
        const orderData = {
            shipping_address: defaultAddressFromApi.value.id,
            payment_method: payment_method.value,
            guest_name: address.value.customer_name,
            customer_id: store.userId,
            guest_phone: address.value.phone,
            guest_email: address.value.email || email.value,
            wards: selectedWard.value,
            provinces: selectedProvince.value,
            districts: selectedDistrict.value,
            address: address.value.address,
            guest_address: `${address.value.address || ""
                }, ${wardName}, ${districtName}, ${provinceName}`,
            total_amount: totalPayment.value,
            shipping_money: shippingFee.value,
            cartItems: cartStore.items,
        };
        const emailRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
        if (!store.token && address.value.email == "") {
            errors.value.email = "Vui lòng nhập email";
            return;
        }

        if (!store.token && !emailRegex.test(address.value.email)) {
            errors.value.email = "Email không đúng định dạng";
            return;
        }
        const res = await axios.post("http://127.0.0.1:8000/api/order", orderData);

        if (res.data.return_url) {
            window.location.href = res.data.return_url;
            return;
        }
        if (res.data.order_id) {
            Swal.fire({
                title: 'Đặt hàng thành công',
                icon: 'success',
                showCancelButton: true,
                confirmButtonColor: '#55acee',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Xem chi tiết đơn hàng',
                cancelButtonText: 'Tiếp tục mua sắm'
            }).then((result) => {
                if (result.isConfirmed) {
                    route.push(`/order-history-detail/${res.data.order_id}`);
                } else {
                    route.push(`/home`);
                }
            })
        }

        cartStore.removeCart()
    } catch (error) {
        if (error.response && error.response.status === 422) {
            errors.value = {};
            errors.value = error.response.data.errors;
        }
    }
};

onMounted(async () => {
    cartStore.loadCart();
    await fetchProvinces();
    await getUserById();

    if (defaultAddressFromApi.value) {
        await fillFormWithDefaultAddress();
    }
});

watch([selectedDistrict, selectedWard], async () => {
    if (selectedDistrict.value) {
        await fetchServices();
    }
    if (selectedDistrict.value && selectedWard.value) {
        await calculateShippingFee();
    }
});
</script>

<template>
    <section class="checkout-wrapper section pt-2">
        <div class="container py-4">
            <div class="row gx-5">
                <div class="col-md-6">
                    <div class="p-4 border rounded shadow-sm bg-white">
                        <h5 class="mb-4" style="color: blue">Thông tin đặt hàng</h5>
                        <form @submit.prevent="order">
                            <div class="mb-3">
                                <small class="text-danger" v-if="errors.guest_name">{{
                                    errors.guest_name[0]
                                    }}</small>
                                <input type="text" class="form-control" placeholder="Tên của bạn"
                                    v-model="address.customer_name" />
                            </div>
                            <div class="mb-3">
                                <small class="text-danger" v-if="errors.guest_phone">{{
                                    errors.guest_phone[0]
                                    }}</small>
                                <input type="text" class="form-control" placeholder="Số điện thoại"
                                    v-model="address.phone" />
                            </div>
                            <div class="mb-3" v-if="!store.token">
                                <small class="text-danger" v-if="errors.email">{{
                                    errors.email
                                    }}</small>
                                <input type="text" class="form-control" placeholder="Email" v-model="address.email" />
                            </div>
                            <div class="mb-3">
                                <small class="text-danger" v-if="errors.provinces">{{
                                    errors.provinces[0]
                                    }}</small>
                                <select v-model="selectedProvince" @change="fetchDistricts" class="form-control">
                                    <option :value="null">Chọn tỉnh / thành</option>
                                    <option v-for="province in provinces" :key="province.ProvinceID"
                                        :value="province.ProvinceID">
                                        {{ province.ProvinceName }}
                                    </option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <small class="text-danger" v-if="errors.districts">{{
                                    errors.districts[0]
                                    }}</small>
                                <select v-model="selectedDistrict" @change="fetchWards" :disabled="!selectedProvince"
                                    class="form-control">
                                    <option :value="null">Chọn quận / huyện</option>
                                    <option v-for="district in districts" :key="district.DistrictID"
                                        :value="district.DistrictID">
                                        {{ district.DistrictName }}
                                    </option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <small class="text-danger" v-if="errors.wards">{{
                                    errors.wards[0]
                                    }}</small>
                                <select class="form-control" v-model="selectedWard" :disabled="!selectedDistrict">
                                    <option :value="null">Chọn phường / xã</option>
                                    <option v-for="ward in wards" :key="ward.WardCode" :value="ward.WardCode">
                                        {{ ward.WardName }}
                                    </option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <small class="text-danger" v-if="errors.address">{{
                                    errors.address[0]
                                    }}</small>
                                <input type="text" class="form-control" placeholder="Địa chỉ"
                                    v-model="address.address" />
                            </div>
                            <div class="mb-3">
                                <textarea class="form-control" rows="3" placeholder="Ghi chú"></textarea>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="/cart" class="btn btn-outline-primary">
                                    <i class="bi bi-chevron-left"></i> Quay về giỏ hàng
                                </a>
                                <button :disabled="!shippingFee" type="submit" class="btn btn-primary">Đặt hàng</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="col-md-6 pe-0">
                    <div class="p-4 border rounded shadow-sm bg-white">
                        <h5 class="mb-3" style="color: blue">
                            Đơn hàng ({{ cartStore.items.length }} sản phẩm)
                        </h5>
                        <hr />

                        <div class="list-product-scroll mb-2">
                            <div v-if="cartStore.items.length > 0" class="d-flex mb-3" v-for="item in cartStore.items"
                                :key="item.variantId">
                                <img :src="item.image" alt="" class="me-3 rounded" width="50" height="50" />
                                <div class="flex-grow-1">
                                    <strong class="product-name-short">{{
                                        item.productName
                                        }}</strong>

                                    <div class="text-muted small ps-2 mb-1" style="font-size: 11px">
  <div>+ {{ item.selectedAttributes.join(', ') }}</div>
</div>
                                    <div style="font-size: 12px">
                                        Số lượng: {{ item.quantity }}
                                    </div>
                                    <div style="font-size: 12px">
                                        Giá: {{ FormatData.formatNumber(item.price) }} VNĐ
                                    </div>
                                </div>
                                <div class="text-end ms-2 fw-semibold" style="color: blue">
                                    {{ FormatData.formatNumber(item.price * item.quantity) }} VNĐ
                                </div>
                            </div>
                            <small v-else class="text-danger d-flex justify-content-center">
                                Giỏ hàng trống
                            </small>
                        </div>

                        <hr />

                        <div class="d-flex justify-content-between mb-2">
                            <span>Tạm tính</span><span class="fw-semibold">{{
                                FormatData.formatNumber(cartStore.totalPrice) }} VNĐ</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Phí ship</span><span class="fw-semibold">{{ FormatData.formatNumber(shippingFee) }}
                                VNĐ</span>
                        </div>
                        <div style="color: blue" class="d-flex justify-content-between mb-2 fw-bold">
                            <span>Tổng thanh toán:</span><span class="fw-semibold" style="color: blue">{{
                                FormatData.formatNumber(totalPayment) }} VNĐ</span>
                        </div>

                        <div>
                            <h6 class="mb-2">Phương thức thanh toán</h6>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment" id="vnpay" value="VNPAY"
                                    v-model="payment_method" />
                                <label class="form-check-label d-flex align-items-center" for="vnpay">
                                    <span class="me-2">Thanh toán qua VNPAY</span>
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment" id="cod" value="COD"
                                    v-model="payment_method" />
                                <label class="form-check-label d-flex align-items-center" for="cod">
                                    <span class="me-2">Thanh toán khi nhận hàng (COD)</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
<style scoped>
.product-name-short {
    display: block;
    width: 300px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    font-size: 14px;
    font-weight: 600;
}

.isLoading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100vh;
    background-color: rgba(148, 142, 142, 0.8);
    z-index: 9999;
    display: flex;
    justify-content: center;
    align-items: center;
}

.modal-backdrop {
    z-index: 1040;
}

.modal {
    z-index: 1050;
}

.order-tabs {
    -webkit-overflow-scrolling: touch;
    scrollbar-width: none;
}

.order-tabs::-webkit-scrollbar {
    display: none;
}

.tab-item {
    padding: 8px 16px;
    cursor: pointer;
    white-space: nowrap;
    border-bottom: 2px solid transparent;
}

.tab-item.active {
    border-color: #c92c3c;
    color: #c92c3c;
    font-weight: 600;
}

.voucher-card {
    display: flex;
    min-height: 100px;
    border-radius: 8px;
    overflow: hidden;
    background-color: #fff;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
    cursor: pointer;
    transition: box-shadow 0.2s;
}

.voucher-card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.voucher-card.disabled-voucher {
    opacity: 0.6;
    cursor: not-allowed;
}

.voucher-card-left {
    width: 26%;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    color: white;
    padding: 10px;
}

.voucher-card-left.freeship {
    background-color: #00bfa5;
}

.voucher-card-left.salefood {
    background-color: #f44336;
}

.voucher-card-left img {
    width: 32px;
    height: 32px;
    object-fit: contain;
}

.voucher-card-label {
    font-size: 12px;
    font-weight: bold;
    text-align: center;
    margin-top: 5px;
}

.voucher-card-right {
    width: 74%;
    padding: 10px 14px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.voucher-code {
    font-weight: 600;
    font-size: 14px;
    margin-bottom: 5px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.voucher-condition {
    font-size: 12px;
    color: #6c757d;
    margin-bottom: 4px;
    line-height: 1.3;
}

.voucher-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 6px;
}

.voucher-coins {
    font-size: 13px;
    color: #777;
    display: flex;
    align-items: center;
}

.voucher-coins img {
    width: 16px;
    height: 16px;
    margin-left: 5px;
}

.voucher-button {
    font-size: 13px;
    padding: 5px 12px;
    border-radius: 5px;
    border: 1px solid #c92c3c;
    color: #c92c3c;
    background-color: transparent;
    transition: all 0.2s ease;
}

.voucher-button:hover {
    background-color: #c92c3c;
    color: white;
}

.voucher-button.has-voucher {
    border-color: #007d00;
    color: #007d00;
    cursor: default;
}

.voucher-button.has-voucher:hover {
    background-color: #007d00;
    color: white;
}

/* togle btn tpoint */
.toggle-switch {
    position: relative;
    display: inline-block;
    width: 36px;
    height: 18px;
    cursor: pointer;
}

.toggle-switch input[type="checkbox"] {
    display: none;
}

.toggle-switch-background {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: #ddd;
    border-radius: 9px;
    box-shadow: inset 0 0 0 1px #ccc;
    transition: background-color 0.3s ease-in-out;
}

.toggle-switch-handle {
    position: absolute;
    top: 2px;
    left: 2px;
    width: 14px;
    height: 14px;
    background-color: #fff;
    border-radius: 50%;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
    transition: transform 0.3s ease-in-out;
}

.toggle-switch::before {
    content: "";
    position: absolute;
    top: -18px;
    right: -20px;
    font-size: 10px;
    font-weight: bold;
    color: #aaa;
    text-shadow: 1px 1px #fff;
    transition: color 0.3s ease-in-out;
}

.toggle-switch input[type="checkbox"]:checked+.toggle-switch-background {
    background-color: #05c46b;
    box-shadow: inset 0 0 0 1px #04b360;
}

.toggle-switch input[type="checkbox"]:checked+.toggle-switch-background .toggle-switch-handle {
    transform: translateX(18px);
}

/* Responsive cho thiết bị di động */
@media (max-width: 767.98px) {
    .checkout-wrapper .row {
        flex-direction: column-reverse;
    }

    .checkout-wrapper .col-md-5,
    .checkout-wrapper .col-md-7 {
        margin-bottom: 24px;
    }

    .p-4 {
        padding: 16px !important;
    }

    h4 {
        font-size: 1.25rem;
    }

    .d-flex {
        flex-direction: column;
        align-items: flex-start !important;
    }

    .d-flex img {
        margin-bottom: 8px;
    }
}
</style>
