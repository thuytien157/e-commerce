import { createRouter, createWebHistory } from 'vue-router';
import admin from './admin';
import client from './client';

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
  }
});

// router.beforeEach((to, from, next) => {
//   // document.title = to.meta.title || "DONEZO TASKMANAGEMENT";

//   const token = localStorage.getItem("token_HUCE");
//   const userString = localStorage.getItem("user_HUCE");
//   const user = userString ? JSON.parse(userString) : null;

//   const isAuthenticated = token && user;

//   if ((to.path === "/login" || to.path === "/register") && isAuthenticated) {
//     next("/home");
//   } else if (to.meta.requiresAuth && !isAuthenticated) {
//     next("/login");
//   } else {
//     next();
//   }
// });

export default router;
