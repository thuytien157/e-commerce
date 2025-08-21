const admin = [
  {
    path: "/admin",
    component: () => import("../layouts/AdminLayout.vue"),
    redirect: "/admin/dashboard",
    meta: {
          requiresAdmin: true
      },
    children: [
      {
        path: "dashboard",
        name: "admin-dashboard",
        component: () => import("../page/admin/dashboard/index.vue"),
        meta: { title: "Thống kê" },
      },
      {
        path: "category",
        name: "admin-categories",
        component: () => import("../page/admin/category/index.vue"),
        meta: { title: "Danh mục" },
        props: true,
      },
      {
        path: "category/insert",
        name: "admin-categories-insert",
        component: () => import("../page/admin/category/insert.vue"),
        meta: { title: "Thêm danh mục" },
      },
      {
        path: "category/edit/:categoryId",
        name: "admin-categories-edit",
        component: () => import("../page/admin/category/insert.vue"),
        meta: { title: "Sửa danh mục" },
        props: true,
      },
      {
        path: "product",
        name: "admin-products",
        component: () => import("../page/admin/product/index.vue"),
        meta: { title: "Sản phẩm" },
        props: true,
      },
      {
        path: "product/insert",
        name: "admin-products-insert",
        component: () => import("../page/admin/product/insert.vue"),
        meta: { title: "Thêm sản phẩm" },
      },
      {
        path: "product/edit/:productId",
        name: "admin-products-edit",
        component: () => import("../page/admin/product/insert.vue"),
        meta: { title: "Sửa sản phẩm" },
        props: true,
      },
      {
        path: "attribute",
        name: "admin-attributes",
        component: () => import("../page/admin/attribute/index.vue"),
        meta: { title: "Thuộc tính" },
        props: true,
      },
      {
        path: "attribute/insert",
        name: "admin-attributes-insert",
        component: () => import("../page/admin/attribute/insert.vue"),
        meta: { title: "Thêm thuộc tính" },
      },
      {
        path: "attribute/edit/:attributeId",
        name: "admin-attributes-edit",
        component: () => import("../page/admin/attribute/insert.vue"),
        meta: { title: "Sửa thuộc tính" },
        props: true,
      },
      {
        path: "review",
        name: "admin-reviews",
        component: () => import("../page/admin/review/index.vue"),
        meta: { title: "Đánh giá" },
        props: true,
      },
      {
        path: "order",
        name: "admin-orders",
        component: () => import("../page/admin/order/index.vue"),
        meta: { title: "Đơn hàng" },
        props: true,
      },
      {
        path: "user",
        name: "admin-users",
        component: () => import("../page/admin/user/index.vue"),
        meta: { title: "Người dùng" },
        props: true,
      },
      
    ],
  },
];

export default admin;
