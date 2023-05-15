<template>
    <el-button type="primary" size="default" icon="Plus" style="margin-bottom:20px" @click="showDialog(1)"> 添加</el-button>
    <el-table :data="tableData" size="default" border stripe>
        <el-table-column prop="uid" label="UID" min-width="60" />
        <el-table-column prop="group_name" label="部门" min-width="120" />
        <el-table-column prop="account" label="账号" min-width="120" />
        <el-table-column prop="name" label="姓名" min-width="80" />
        <el-table-column prop="phone" label="手机号" min-width="100" />
        <el-table-column prop="qq" label="QQ" min-width="100" />
        <el-table-column prop="sex_s" label="性别" min-width="60" />
        <el-table-column prop="status_s" label="状态" min-width="60" />
        <el-table-column prop="times_login" label="登录次数" min-width="80" />
        <el-table-column prop="time_add" label="创建时间" min-width="100" />
        <el-table-column prop="time_last" label="最后登录时间" min-width="100" />
        <el-table-column prop="cz" label="操作" min-width="150">
            <template #default="scope">
                <el-button type="primary" size="default" icon="Edit" @click="showDialog(2,scope.row)">修改</el-button>
                <el-button type="danger" size="default" icon="Delete" @click="delData(scope.row)">删除</el-button>
            </template>
        </el-table-column>
    </el-table>
    <el-dialog v-model="dialog" :title="title">
        <el-form :model="form" label-width="120px" size="default">
            <el-form-item label="部门">
                <el-select v-model="form.group_id" placeholder="请选择部门">
                <el-option v-for="item in group" :key="item.group_id" :label="item.group_name" :value="item.group_id" />
                </el-select>
            </el-form-item>
            <el-form-item label="账号">
                <el-input v-model="form.account" placeholder="请输入账号，使用邮箱" />
            </el-form-item>
            <el-form-item label="密码">
                <el-input type="password" v-model="form.password" show-password :placeholder="password_placeholder" />
            </el-form-item>
            <el-form-item label="姓名">
                <el-input v-model="form.name" placeholder="请输入姓名" />
            </el-form-item>
            <el-form-item label="手机号">
                <el-input v-model="form.phone" placeholder="请输入手机号" />
            </el-form-item>
            <el-form-item label="QQ">
                <el-input v-model="form.qq" placeholder="请输入qq" />
            </el-form-item>
            <el-form-item label="性别">
                <el-select v-model="form.sex" placeholder="请选择性别">
                    <el-option label="男" :value="1" />
                    <el-option label="女" :value="2" />
                </el-select>
            </el-form-item>
            <el-form-item label="状态">
                <el-select v-model="form.status" placeholder="请选择状态">
                    <el-option label="开启" :value="1" />
                    <el-option label="关闭" :value="0" />
                </el-select>
            </el-form-item>
        </el-form>
        <template #footer>
            <span class="dialog-footer">
                <el-button size="default" @click="dialog = false">取消</el-button>
                <el-button size="default" type="primary" @click="submitForm()">确认</el-button>
            </span>
        </template>
    </el-dialog>
</template>
<script>
import { toRefs, reactive } from "vue";
import { UserLists, UserSave, UserDel } from "@/network/bewadmin";
import { ElMessage, ElMessageBox } from "element-plus";
export default {
    name: "UserList",
    setup() {
        const state = reactive({
            tableData: [],
            group : [],
            dialog : false,
            title : "添加",
            password_placeholder : "请输入密码",
            form: {
                uid : 0,
                group_id : 0,
                account: "",
                password: "",
                name: "",
                phone: "",
                qq: "",
                sex: 1,
                status: 1,
            }
        });
        UserLists().then( (e)=>{
            state.tableData = e.lists;
            state.group = e.group;
        } )
        const showDialog = (e,row)=>{
            state.dialog = true;
            if(e == 1){
                state.form = {
                    uid : 0,
                    group_id : "",
                    account: "",
                    password: "",
                    name: "",
                    phone: "",
                    qq: "",
                    sex: 1,
                    status: 1
                };
                state.title = "添加";
                state.password_placeholder = "请输入密码";
            }else{
                state.title = "修改";
                state.password_placeholder = "不修改，不要填";
                state.form = row;
                state.form.password = "";
            }
        }
        const submitForm = ()=>{
            UserSave(state.form).then( (e)=>{
                if (e != 1) {
                    ElMessage({ message:'成功', type: "success" });
                    UserLists().then( (e)=>{
                        state.tableData = e.lists;
                        state.group = e.group;
                        state.dialog = false;
                    } )
                }
            } )
        }
        const delData = (row)=>{
            ElMessageBox.confirm("删除后不能恢复，确定删除吗？", "警告", {
                confirmButtonText: "确定",
                cancelButtonText: "取消",
                type: "warning",
            }).then( ()=>{
                UserDel({uid:row.uid}).then( (e)=>{
                    if (e != 1) {
                        ElMessage({ message:'成功', type: "success" });
                        UserLists().then( (e)=>{
                            state.tableData = e.lists;
                            state.group = e.group;
                        } )
                    }
                } )
            } ).catch(() => {
                ElMessage({
                    type: "info",
                    message: "取消删除"
                })
            })
        }
        return {
            ...toRefs(state),
            showDialog,
            submitForm,
            delData
        };
    }
};
</script>