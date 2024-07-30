import "./bootstrap";
import { createApp } from "vue";
import InfoWidget from "./components/dashboard/InfoWidget.vue";

// Create a Vue app for the comments section
const infoWidgets = createApp(InfoWidget);
infoWidgets.mount("#widgets");
