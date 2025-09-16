export default function getAxios() {
    const axios = require("axios").default;

    const instance = axios.create({
        baseURL: import.meta.env.VITE_API_URL,
        headers: {
            "Content-Type": "application/json",
        },
    });
    return instance;
}