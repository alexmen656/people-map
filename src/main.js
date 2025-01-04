import { createApp } from "vue";
import App from "./App.vue";
import axios from "./axios";
import qs from "qs";

const app = createApp(App);
app.config.globalProperties.$axios = axios;
app.config.globalProperties.$qs = qs;
app.mount("#app");
