import "./assets/main.css";

import { createApp } from "vue";
import { createPinia } from "pinia";
import App from "./App.vue";
import router from "./router";
import "bootstrap/dist/css/bootstrap.min.css";
import "bootstrap/dist/js/bootstrap.bundle.min.js";
import "bootstrap-icons/font/bootstrap-icons.css";
import "bootstrap";
import "sweetalert2/dist/sweetalert2.min.css";
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
import Antd from 'ant-design-vue';
import 'ant-design-vue/dist/reset.css'; 
import vSelect from "vue-select";
import "vue-select/dist/vue-select.css";
import { QuillEditor } from "@vueup/vue-quill";
import '@vueup/vue-quill/dist/vue-quill.snow.css';
window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: true
});

const app = createApp(App);

const pinia = createPinia();
app.use(pinia);
app.use(router);
app.use(Antd);
app.component("v-select", vSelect);
app.component("QuillEditor", QuillEditor);
app.mount("#app");
