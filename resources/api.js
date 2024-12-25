import axios from "axios";

const webUrl = window.location.protocol + "//" + window.location.host;

let baseUrl;

if (webUrl == "http://localhost:8000") {
  baseUrl = "http://localhost:8000/";
} else if (webUrl == "https://ahmedfabrics.com.pk/paramount") {
  baseUrl = "https://ahmedfabrics.com.pk/paramount/";
} else if (webUrl == "https://ahmedfabrics.com.pk/paramount2") {
  baseUrl = "https://ahmedfabrics.com.pk/paramount2/";
} else {
  baseUrl = "http://localhost:8000/";
}

console.log(baseUrl);
console.log(webUrl);

const api = axios.create({
  baseURL: baseUrl,
});

export default api;