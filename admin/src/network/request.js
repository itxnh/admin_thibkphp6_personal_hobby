import axios from "axios";
import { ElMessage } from "element-plus";
export function request(config) {
    const instance = axios.create({
        baseURL: "https://api.itxnh.com/admin/",
        timeout: 5000,
    });
    //请求拦截
    instance.interceptors.request.use(
        (config) => {
            //如果API需要认证，在此统一设置
            const token = window.localStorage.getItem("token");
            if (token) {
                config.headers.Authorization = token;
                config.data.Authorization = token;
            }
            return config;
        },
        (err) => {
            console.log(err);
        }
    );

    //响应拦截
    instance.interceptors.response.use(
        (res) => {
            if (res.data.code != 0) {
                ElMessage.error(res.data.msg);
                // ElMessage.error("网络错误，请稍后再试");
                return 1;
            } else {
                return res.data.data;
            }
        },
        (err) => {
            // 如果没有授权， 去login
            // 如果有错误， 在这里可以提示
            console.log(err);
        }
    );
    return instance(config);
}