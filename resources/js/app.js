import "./bootstrap";

import { createApp } from "vue";

import DashboardCounts from "./components/Dashboard/DashboardCounts.vue";

import InwardCreate from "./components/InwardGeneral/Create.vue";
import InwardEdit from "./components/InwardGeneral/Edit.vue";

import ProductCreate from "./components/Product/Create.vue";
import ShiftCreate from "./components/Shift/Create.vue";
import EditShift from "./components/Shift/Edit.vue";

import GazetteCalendar from "./components/GazetteCalendar/GazetteCalendar.vue";

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

if (document.querySelector("#create-product")) {
  createApp(ProductCreate).mount("#create-product");
}

if (document.querySelector("#create-shift")) {
  createApp(ShiftCreate).mount("#create-shift");
}

if (document.querySelector("#edit-shift")) {
  const editShift = JSON.parse(document.querySelector("#edit-shift").dataset.shift);
  createApp(EditShift, { shift: editShift }).mount("#edit-shift");
}

if (document.querySelector("#gazette-calendar")) {
  createApp(GazetteCalendar).mount("#gazette-calendar");
}
