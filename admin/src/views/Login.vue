<template>
    <el-container>
        <el-main>
            <div class="container">
                <div style="text-align: center">
<!--                    <img src="@/assets/logo.jpg" alt="logo" />-->
                    <h3>个人兴趣网后台管理</h3>
                </div>
                <div class="main">
                    <!-- label-width="auto" 导致ElementPlusError: [ElForm] unexpected width 0 -->
                    <el-form :model="state" size="large" :rules="loginRules" ref="ruleFormsss">
                        <el-form-item prop="account">
                            <el-input v-model="state.account" name="account" class="w-50 m-2" placeholder="请输入账号">
                                <template #prefix>
                                    <el-icon class="el-input__icon" style="color: #1890ff"><Avatar /></el-icon>
                                </template>
                            </el-input>
                        </el-form-item>
                        <el-form-item prop="password">
                            <el-input v-model="state.password" name="password" type="password" class="w-50 m-2" placeholder="请输入密码" show-password>
                                <template #prefix>
                                    <el-icon class="el-input__icon" style="color: #1890ff"><Lock /></el-icon>
                                </template>
                            </el-input>
                        </el-form-item>
                        <el-form-item prop="remember">
                            <el-checkbox v-model="state.remember" label="1" size="default">自动登录</el-checkbox>
                        </el-form-item>
                        <el-form-item>
                            <el-button type="primary" style="width: 100%" @click="onSubmit(ruleFormsss)">登录</el-button>
                        </el-form-item>
                    </el-form>
                </div>
            </div>
        </el-main>
    </el-container>
    <el-footer>
        <div style="text-align:center;color:#7e7e7e;margin-top: 50px">
            Copyright &copy; 2022-2023 IT小男孩 | <a href="http://beian.miit.gov.cn" target="_blank" style="color:#7e7e7e">鄂ICP备20003004号-5</a>
        </div>
    </el-footer>
</template>
<script>
import { reactive, ref } from "vue";
import { Login } from "@/network/login";
import { ElMessage } from "element-plus";
import { useRouter } from "vue-router";
export default {
    name: "Login",
    setup() {
        const state = reactive({
            account: "",
            password: "",
            remember: true,
        });

        const router = useRouter();
        const onSubmit = () => {
            Login(state).then((res) => {
              console.log(res)
                if (res.ticket) {
                    window.localStorage.setItem("token", res.ticket);
                    window.localStorage.setItem("user", JSON.stringify(res));
                    ElMessage({ message: "登录成功", type: "success" });
                    router.push("/index/home");
                }
            });
        };
        const token = window.localStorage.getItem("token");
        if (token) {
            ElMessage({ message: "您已登录", type: "success" });
            router.push("/index/home");
        }
        return {
            state,
            onSubmit
        };
    }
};
</script>
<style>
.container {
    position: relative;
    width: 100%;
    padding: 110px 0 110px;
}
.main {
    width: 368px;
    min-width: 260px;
    margin: 50px auto;
}
.el-icon {
    color: #359eff;
}
</style>