import { request } from "./request";
export function Login(data = {}) {
    return request({
        url: "Login/login",
        method: "Post",
        data,
    });
}