import { createRouter, createWebHistory, createWebHashHistory } from "vue-router";
import Index from "../views/Index/Index.vue";
import Home from "../views/Index/Home.vue";
import Login from "../views/Login.vue";
import Userinfo from "../views/Index/Userinfo.vue";
import MenuList from "../views/Bew/MenuList.vue";
import GroupList from "../views/Bewadmin/GroupList.vue";
import UserList from "../views/Bewadmin/UserList.vue";
import HobbyUser from "../views/Hobby/HobbyUser.vue";
import HobbyList from "../views/Hobby/HobbyList.vue";

const routes = [
    {
        path: "/",
        alias: "/login",
        name: "Login",
        component: Login,
        meta: {
            title: "itxnh - 登录"
        }
    },
    {
        path: "/index",
        name: "Index",
        component: Index,
        meta: {
            title: "itxnh - 首页"
        },
        children: [
            {
                // 子路由的path，没有/
                path: "home",
                name: "Home",
                component: Home,
                meta: {
                    title: "itxnh - 首页"
                }
            },
            {
                path: "userinfo",
                name: "Userinfo",
                component: Userinfo,
                meta: {
                    title: "itxnh - 个人中心"
                }
            },
            {
                path: "menulist",
                name: "MenuList",
                component: MenuList,
                meta: {
                    title: "itxnh - 导航管理"
                }
            },
            {
                path: "grouplist",
                name: "GroupList",
                component: GroupList,
                meta: {
                    title: "itxnh - 部门管理"
                }
            },
            {
                path: "userlist",
                name: "UserList",
                component: UserList,
                meta: {
                    title: "itxnh - 管理员管理"
                }
            },
            {
                path: "hobbyuser",
                name: "HobbyUser",
                component: HobbyUser,
                meta: {
                    title: "itxnh - 用户管理"
                }
            },
            {
                path: "hobbylist",
                name: "HobbyList",
                component: HobbyList,
                meta: {
                    title: "itxnh - 兴趣管理"
                }
            }
        ]
    }
];

const router = createRouter({
    // process.env.BASE_URL
    history: createWebHashHistory(),
    routes
});

router.beforeEach((to, from) => {
    document.title = to.meta.title;
});

export default router;