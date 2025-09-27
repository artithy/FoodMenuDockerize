import axios from "axios";

export default function getAxios() {

    const instance = axios.create({
        baseURL: import.meta.env.VITE_API_URL,
        headers: {
            "Content-Type": "application/json",
        },
    });
    return instance;
}