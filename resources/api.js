import axios from "axios";

function getBaseUrlWithFirstSubdirectory(url) {
  const parsedUrl = new URL(url);
  const pathSegments = parsedUrl.pathname.split("/").filter((segment) => segment);
  const firstSubdirectory = pathSegments[0] || "";
  return `${parsedUrl.origin}/${firstSubdirectory}`;
}

let webUrl = getBaseUrlWithFirstSubdirectory(window.location.href);

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
