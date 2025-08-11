const client = [
  {
    path: "/",
    component: () => import("../page/client/home/index.vue"),
    redirect: "/home",
    children: [
      {
        path: "home",
        name: "client-home",
        component: () => import("../page/client/home/index.vue"),
        meta: { title: "Trang chủ" },
      },
    ],
  },

  {
    path: "/login",
    component: () => import("../page/client/user/login.vue"),
    meta: { title: "Đăng nhập" },
  },
  {
    path: "/register",
    component: () => import("../page/client/user/register.vue"),
    meta: { title: "Đăng ký" },
  },
  {
    path: "/login-success",
    component: () => import("../page/client/user/loginSuccess.vue"),
  },
  {
    path: "/forgot-password",
    component: () => import("../page/client/user/forgotPassword.vue"),
    meta: { title: "Quên mật khẩu" },
  },
  {
    path: "/account/address-new",
    component: () => import("../page/client/user/address-new.vue"),
    meta: { title: "Thêm địa chỉ" },
  },
  {
    path: "/account/address-edit/:addressId",
    component: () => import("../page/client/user/address-new.vue"),
    meta: { title: "Sửa địa chỉ" },
    props: true,
  },
  {
    path: "/account/address-list",
    component: () => import("../page/client/user/address-list.vue"),
    meta: { title: "Danh sách địa chỉ" },
    props: true,
  },
  {
    path: "/order-history",
    component: () => import("../page/client/order/history.vue"),
    meta: { title: "Lịch sử đơn hàng" },
  },
  {
    path: "/order-history-detail",
    component: () => import("../page/client/order/detail.vue"),
    meta: { title: "Chi tiết đơn hàng" },
  },
  {
    path: "/forgot-password",
    component: () => import("../page/client/user/forgotPassword.vue"),
    meta: { title: "Quên mật khẩu" },
  },
  {
    path: "/products",
    component: () => import("../page/client/product/index.vue"),
    meta: { title: "Sản phẩm" },
  },
  {
    path: "/product-detail",
    component: () => import("../page/client/product/detail.vue"),
    meta: { title: "Chi tiết" },
  },
  {
    path: "/cart",
    component: () => import("../page/client/cart/index.vue"),
    meta: { title: "Giỏ hàng" },
  },
  {
    path: "/checkout",
    component: () => import("../page/client/order/checkout.vue"),
    meta: { title: "Tạo đơn hàng" },
  },
];

export default client;
