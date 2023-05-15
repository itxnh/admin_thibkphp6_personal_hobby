import { request } from "./request.js";
export function GroupLists(data = {}) {
    return request({
        url: "Bewadmin/grouplists",
        method: "post",
        data
    });
}
export function GroupSave(data = {}) {
    return request({
        url: "Bewadmin/groupsave",
        method: "post",
        data,
    });
}
export function GroupDel(data = {}) {
    return request({
        url: "Bewadmin/groupdel",
        method: "post",
        data,
    });
}
export function UserLists(data = {}) {
    return request({
        url: "Bewadmin/userlists",
        method: "post",
        data
    });
}
export function UserSave(data) {
    return request({
        url: "Bewadmin/usersave",
        method: "post",
        data,
    });
}
export function UserDel(data) {
    return request({
        url: "Bewadmin/userdel",
        method: "post",
        data,
    });
}