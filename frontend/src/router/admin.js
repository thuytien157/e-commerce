const admin = [
  {
    path: "/admin",
    component: () => import("../layouts/AdminLayout.vue"),
    redirect: "/admin/dashboard",
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
        meta: { title: "Thêm sản phẩm" },
      },
      {
        path: "attribute/edit/:attributeId",
        name: "admin-attributes-edit",
        component: () => import("../page/admin/attribute/insert.vue"),
        meta: { title: "Sửa sản phẩm" },
        props: true,
      },
      
    ],
  },
];

export default admin;
