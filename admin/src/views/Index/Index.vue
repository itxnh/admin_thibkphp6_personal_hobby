<template>
    <el-container class="layout" style="height: 100%">
        <aside style="height: 100%">
            <el-menu default-active="-1" class="el-menu-vertical-demo" :collapse="isCollapse" active-text-color="#ffd04b" background-color="#001529" text-color="#fff">
                <div class="logo">
                    <router-link to="/index/home" class="router">
                        <img src="@/assets/logo.jpg" alt="logo" />
                        <h1>phpAdmin</h1>
                    </router-link>
                </div>
                <router-link to="/index/home">
                    <el-menu-item index="-1">
                        <el-icon><House /></el-icon>
                        <template #title>首页</template>
                    </el-menu-item>
                </router-link>
                <router-link to="/index/userinfo">
                    <el-menu-item index="-2">
                        <el-icon><User /></el-icon>
                        <template #title>个人中心</template>
                    </el-menu-item>
                </router-link>
                <template v-for="(item,key) in menu" :key="key">
                    <a :href="item.src" target="_blank" v-if="item.type == 2">
                        <el-menu-item :index="item.mid">
                            <el-icon>
                                <Icon :icon="item.icon_class" />
                            </el-icon>
                            <template #title>{{item.label}}</template>
                        </el-menu-item>
                    </a>
                    <router-link  v-else-if="item.type == 1" :to="item.src">
                        <el-menu-item :index="item.mid">
                            <el-icon>
                                <Icon :icon="item.icon_class" />
                            </el-icon>
                            <template #title>{{item.label}}</template>
                        </el-menu-item>
                    </router-link>
                    <el-sub-menu v-else :index="item.mid">
                        <template #title>
                            <el-icon>
                                <Icon :icon="item.icon_class" />
                            </el-icon>
                            <span>{{item.label}}</span>
                        </template>
                        <template v-for="(items,indexs,keys) in item.children" :key="keys">
                            <router-link :to="items.src" v-if="items.type == 1">
                                <el-menu-item :index="items.mid">
                                    <el-icon>
                                        <Icon :icon="items.icon_class" />
                                    </el-icon>
                                    <span>{{items.label}}</span>
                                </el-menu-item>
                            </router-link>
                            <a :href="items.src" target="_blank" v-if="items.type == 2">
                                <el-menu-item :index="items.mid">
                                    <el-icon>
                                        <Icon :icon="items.icon_class" />
                                    </el-icon>
                                    <span>{{items.label}}</span>
                                </el-menu-item>
                            </a>
                        </template>
                    </el-sub-menu>
                </template>
            </el-menu>
            <div class="flexible" @click="isCollapse = !isCollapse">
                <el-icon v-if="isCollapse" color="white" :size="40"><ArrowRight /></el-icon>
                <el-icon v-else color="white" :size="40"><ArrowLeft /></el-icon>
            </div>
        </aside>
        <el-container>
            <el-header style="text-align: right; font-size: 20px">
                <div class="toolbar">
                  <span style="margin-right: 10px">
                    <!--为了方便，这里使用QQ头像-->
                    <el-avatar :src="src_avatar"/>
                  </span>
                    <el-dropdown size="default" type="primary">
                        <span>
                          {{user.name}}
                          <el-icon style="margin-left: 8px; margin-top: 1px"><ArrowDown /></el-icon>
                        </span>
                        <template #dropdown>
                            <el-dropdown-menu>
                                <el-dropdown-item>个人中心</el-dropdown-item>
                                <el-dropdown-item @click="tologout">退出</el-dropdown-item>
                            </el-dropdown-menu>
                        </template>
                    </el-dropdown>
                </div>
            </el-header>
            <el-main>
                <router-view></router-view>
            </el-main>
        </el-container>
    </el-container>
</template>
<script>
import { reactive, toRefs } from "vue";
import { Index } from "@/network/index";
import Icon from "@/components/Icon.vue";
import { useRouter } from "vue-router";
import { ElMessage } from "element-plus";
export default {
    name: "Index",
    components: {
        Icon
    },
    setup() {
        const router = useRouter();
        const token = window.localStorage.getItem("token");
        if (!token) {
            ElMessage.error('请先登录');
            router.push("/login");
            router.push("/login");
        }
        const user = JSON.parse(window.localStorage.getItem("user"));
        const src_avatar = 'https://q.qlogo.cn/headimg_dl?dst_uin='+user.qq+'&spec=100&img_type=jpg';
        const state = reactive({
            isCollapse: false,
            user: user,
            menu : []
        });
        Index().then( (e)=>{
            state.menu = e.menu;
        } )
        // 退出登录
        const tologout = () => {
            window.localStorage.setItem("token", "");
            ElMessage({ message: "退出成功", type: "success" });
            setTimeout(() => {
                router.push({ path: "/login" });
            }, 500);
        };
        return {
            ...toRefs(state),
            tologout,
            src_avatar
        };
    }
};
</script>
<style>
a {
    text-decoration: none;
}
.layout {
    background-color: #f0f2f5;
}
.layout .el-header {
    position: relative;
    background-color: white;
    color: var(--el-text-color-primary);
}
.layout aside {
    color: var(--el-text-color-primary);
    background: #001529;
}
.layout .el-menu {
    border-right: none;
}
.layout .el-main {
    margin: 30px 10px;
    background-color: white;
}
.layout .toolbar {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    height: 100%;
    right: 20px;
    font-size: 20px;
}
aside {
    position: relative;
}
.flexible {
    background-color: #002140;
    text-align: center;
    position: absolute;
    bottom: 0px;
    left: 0px;
    right: 0px;
}
.el-menu-vertical-demo:not(.el-menu--collapse) {
    width: 200px;
    min-height: 400px;
}
.layout aside .logo .router {
    display: flex;
    justify-content: space-evenly;
    align-items: center;
    animation-duration: 0.1s;
    overflow: hidden;
    text-decoration: none;
    padding-left: 1px;
    position: relative;
    left: 2px;
}
.layout aside .logo .router h1 {
    margin-left: 10px;
    color: aliceblue;
    overflow: hidden;
}
.layout aside .logo .router img {
    padding-left: 5px;
    width: 48px;
    height: 48px;
}
</style>