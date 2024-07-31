import "./bootstrap";
import { createApp } from "vue";
import DashboardCounts from "./components/dashboard/DashboardCounts.vue";

// Create a Vue app for the comments section
const dashboardCounts = createApp(DashboardCounts);
dashboardCounts.mount("#widgets");
