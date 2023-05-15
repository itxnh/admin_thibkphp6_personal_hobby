import { request } from "./request.js";
export function Index(data = {}) {
    return request({
        url: "Index/index",
        method: "post",
        data
    });
}
export function UserInfo(data = {}) {
    return request({
        url: "Index/userinfo",
        method: "post",
        data
    });
}