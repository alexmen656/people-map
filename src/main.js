import { createApp } from "vue";
import App from "./App.vue";
import i18n from "./i18n";
import axios from "./axios";
import qs from "qs";

const app = createApp(App);
app.config.globalProperties.$axios = axios;
app.config.globalProperties.$qs = qs;
app.use(i18n);
app.mount("#app");
