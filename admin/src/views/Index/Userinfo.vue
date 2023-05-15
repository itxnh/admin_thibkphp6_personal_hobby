<template>
    <el-form :model="form" label-width="120px" size="large">
        <el-form-item label="账户">
            <el-col :span="16">
                <el-input v-model="form.account" />
            </el-col>
            </el-form-item>
            <el-form-item label="姓名">
            <el-col :span="16">
                <el-input v-model="form.name" />
            </el-col>
            </el-form-item>
            <el-form-item label="手机号">
            <el-col :span="16">
                <el-input v-model="form.phone" />
            </el-col>
            </el-form-item>
            <el-form-item label="QQ号">
            <el-col :span="16">
                <el-input v-model="form.qq" />
            </el-col>
            </el-form-item>
            <el-form-item label="密码">
            <el-col :span="16">
                <el-input v-model="form.password" placeholder="如果不修改，可以不用填写" />
            </el-col>
            </el-form-item>
            <el-form-item label="性别">
            <el-radio-group v-model="form.sex">
                <el-radio :label="1">男</el-radio>
                <el-radio :label="2">女</el-radio>
            </el-radio-group>
            </el-form-item>
            <el-form-item>
            <el-button type="primary" @click="onSubmit">保存</el-button>
        </el-form-item>
    </el-form>
</template>
<script>
import { reactive } from "vue";
import { UserInfo } from "@/network/index";
import { useRouter } from "vue-router";
import { ElMessage } from "element-plus";
export default {
    setup() {
        const user = JSON.parse(window.localStorage.getItem("user"));
        const form = reactive({
            account: user.account,
            name: user.name,
            phone: user.phone,
            qq: user.qq,
            sex: user.sex,
            password: ""
        });
        const router = useRouter();
        const onSubmit = () => {
            UserInfo(form).then((res) => {
                if (res != 1) {
                    window.localStorage.setItem("user", JSON.stringify(res));
                    ElMessage({ message: "修改成功", type: "success" });
                    setTimeout(() => {
                        router.go(0);
                    }, 1000);
                }
            });
        };
        return {
            form,
            onSubmit,
        };
    }
};
</script>