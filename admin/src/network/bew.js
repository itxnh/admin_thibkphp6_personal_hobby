import { request } from "./request.js";
export function MenuLists(data = {}) {
    return request({
        url: "Bew/menulists",
        method: "post",
        data
    });
}
export function MenuSave(data = {}) {
    return request({
        url: "Bew/menusave",
        method: "post",
        data,
    });
}
export function MenuDel(data = {}) {
    return request({
        url: "Bew/menudel",
        method: "post",
        data,
    });
}
