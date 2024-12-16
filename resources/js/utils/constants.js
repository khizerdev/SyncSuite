export let baseUrl = import.meta.env.VITE_APP_URL;

if (import.meta.env.MODE === "production") {
  baseUrl = import.meta.env.VITE_APP_URL;
} else {
  baseUrl = "http://localhost:8000";
}
