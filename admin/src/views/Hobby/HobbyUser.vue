<template>
    <el-table :data="tableData" size="default" border stripe>
        <el-table-column prop="user_info_id" label="ID" min-width="40" />
        <el-table-column prop="name" label="用户名" min-width="80" />
        <el-table-column prop="userName" label="真实姓名" min-width="80" />
        <el-table-column prop="password" label="密码" min-width="80" />
        <el-table-column prop="studentType_s" label="学生类型" min-width="60" />
        <el-table-column prop="sex_s" label="性别" min-width="40">
            <template #default="scope">
                <el-tag :type="scope.row.sex == 1 ? 'success' : ''">{{scope.row.sex_s}}</el-tag>
            </template>
        </el-table-column>
        <el-table-column label="城市" min-width="120">
            <template #default="scope">
                {{scope.row.province.city}}/{{scope.row.city.city}}
            </template>
        </el-table-column>
        <el-table-column label="兴趣" min-width="100">
            <template #default="scope">
                <span v-for="item in scope.row.hobby_s" :key="item.id">
                    <el-tag type="success">{{item.hobby }}</el-tag>
                </span>
            </template>
        </el-table-column>
        <el-table-column prop="ip_address" label="真实地址" min-width="90" />
        <el-table-column prop="comment" label="备注" min-width="260" />
        <el-table-column prop="cz" label="操作" min-width="150">
            <template #default="scope">
                <el-button type="primary" size="default" icon="Edit" :disabled="scope.row.status == 2 ? disabled : true" @click="showDialog(scope.row)">审核</el-button>
                <el-button type="danger" size="default" icon="Delete" @click="delData(scope.row)">删除</el-button>
            </template>
        </el-table-column>
    </el-table>
</template>
<script>
import { toRefs, reactive } from "vue";
import { ElMessage, ElMessageBox } from "element-plus";
import {HobbyUserlist, HobbyUserDel, HobbyUserState} from "@/network/hobby";
export default {
    name: "HobbyUser",
    setup() {
        const state = reactive({
            tableData: [], // 表单数据
            disabled:false, // 审核按钮状态
            form: {
                user_info_id: 0,
                name:'',
                userName:'',
                password:'',
                passwordSet:'',
                studentType:1,
                sex:1,
                comment:'',
                hobby: [],
            }
        });
        HobbyUserlist().then( (e)=>{
            state.tableData = e;
        } )
        // 审核用户
        const showDialog = (row)=>{
            ElMessageBox.confirm("确定审核通过该用户吗？", "警告", {
                confirmButtonText: "确定",
                cancelButtonText: "取消",
                type: "warning",
            }).then( ()=>{
                HobbyUserState({user_info_id:row.user_info_id}).then( (e)=>{
                    if (e != 1) {
                        ElMessage({ message:'成功', type: "success" });
                        state.disabled = true
                        // 审核通过之后 刷新列表 重新发送请求
                        HobbyUserlist().then( (e)=>{
                            state.tableData = e;
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
        const delData = (row)=>{
            ElMessageBox.confirm("删除后不能恢复，确定删除吗？", "警告", {
                confirmButtonText: "确定",
                cancelButtonText: "取消",
                type: "warning",
            }).then( ()=>{
                HobbyUserDel({user_info_id:row.user_info_id}).then( (e)=>{
                    if (e != 1) {
                        ElMessage({ message:'成功', type: "success" });
                        HobbyUserlist().then( (e)=>{
                            state.tableData = e;
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
            delData
        };
    }
};
</script>