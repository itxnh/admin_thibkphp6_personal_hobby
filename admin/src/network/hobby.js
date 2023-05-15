import { request } from "./request.js";
export function Hobbylist(data = {}) {
    return request({
        url: "Hobby/hobbylist",
        method: "post",
        data
    });
}

export function HobbySave(data = {}) {
    return request({
        url: "Hobby/hobbysave",
        method: "post",
        data
    });
}

export function HobbyDel(data) {
    return request({
        url: "Hobby/hobbydel",
        method: "post",
        data
    });
}
// -----------兴趣爱好-用户管理----------------
export function HobbyUserlist(data = {}) {
    return request({
        url: "Hobby/hobbyuserlist",
        method: "post",
        data
    });
}

export function HobbyUserState(data) {
    return request({
        url: "Hobby/hobbyuserstate",
        method: "post",
        data
    });
}

export function HobbyUserDel(data) {
    return request({
        url: "Hobby/hobbyuserdel",
        method: "post",
        data
    });
}
