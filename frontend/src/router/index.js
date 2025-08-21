import { createRouter, createWebHistory } from "vue-router";
import admin from "./admin";
import client from "./client";
import { useTokenUser } from "@/component/store/useTokenUser";

const routes = [...client, ...admin];

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior(to, from, savedPosition) {
    if (savedPosition) {
      return savedPosition;
    } else {
      return { top: 0 };
    }
  },
});

router.beforeEach((to, from, next) => {
  document.title = to.meta.title || "SHOPGRID";
  const store = useTokenUser();

  if (to.meta.requiresAdmin) {
    const isAuthenticated = store.token && store.role;
    const hasPermission = ["staff", "manager"].includes(store.role);

    if (isAuthenticated && hasPermission) {
      next();
    } else {
      next("/");
    }
  } else {
    next();
  }
});

export default router;
