import axios from "axios";

const webUrl = window.location.protocol + "//" + window.location.host;

let baseUrl;

if (webUrl == "http://localhost:8000/") {
  baseUrl = "http://localhost:8000/";
} else if (webUrl == "https://ahmedfabrics.com.pk/paramount") {
  baseUrl = "https://ahmedfabrics.com.pk/paramount";
} else if (webUrl == "https://ahmedfabrics.com.pk/paramount2") {
  baseUrl = "https://ahmedfabrics.com.pk/paramount2";
} else {
  baseUrl = "http://localhost:8000/";
}

const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL,
});

export default api;
