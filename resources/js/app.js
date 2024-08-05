import "./bootstrap";
import { createApp } from "vue";

import DashboardCounts from "./components/Dashboard/DashboardCounts.vue";

import InwardCreate from "./components/InwardGeneral/Create.vue";
import InwardEdit from "./components/InwardGeneral/Edit.vue";

if (document.querySelector("#widgets")) {
  createApp(DashboardCounts).mount("#widgets");
}

if (document.querySelector("#create-inward-general")) {
  createApp(InwardCreate).mount("#create-inward-general");
}

if (document.querySelector("#edit-inward-general")) {
  const inwardGeneral = JSON.parse(document.querySelector("#edit-inward-general").dataset.inward);
  createApp(InwardEdit, { inwardGeneral: inwardGeneral }).mount("#edit-inward-general");
}
