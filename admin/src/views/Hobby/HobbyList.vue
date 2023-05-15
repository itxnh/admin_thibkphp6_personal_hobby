<template>
    <el-button type="primary" size="default" icon="Plus" style="margin-bottom:20px" @click="showDialog(1)"> 添加</el-button>
    <el-table :data="tableData" size="default" border stripe>
        <el-table-column prop="hobby_id" label="HobbyID" min-width="60" />
        <el-table-column prop="hobby" label="兴趣" min-width="120" />
        <el-table-column prop="status_s" label="状态" min-width="60" />
        <el-table-column prop="create_time" label="创建时间" min-width="100" />
        <el-table-column prop="update_time" label="更新时间" min-width="100" />
        <el-table-column prop="cz" label="操作" min-width="150">
            <template #default="scope">
                <el-button type="primary" size="default" icon="Edit" @click="showDialog(2,scope.row)">修改</el-button>
                <el-button type="danger" size="default" icon="Delete" @click="delData(scope.row)">删除</el-button>
            </template>
        </el-table-column>
    </el-table>
    <el-dialog v-model="dialog" :title="title">
        <el-form :model="form" label-width="120px" size="default">
            <el-form-item label="兴趣">
                <el-input v-model="form.hobby" placeholder="请输入兴趣" />
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
import { ElMessage, ElMessageBox } from "element-plus";
import {Hobbylist,HobbySave,HobbyDel} from "@/network/hobby";
export default {
    name: "HobbyList",
    setup() {
        const state = reactive({
            tableData: [], // 表单数据
            dialog : false,
            title : "添加",
            form: {
                hobby_id: 0,
                hobby: '',
                status: 1,
            }
        });
        Hobbylist().then( (e)=>{
            state.tableData = e;
        } )
        const showDialog = (e,row)=>{
            state.dialog = true;
            if(e == 1){
                state.form = {
                    hobby_id: 0,
                    hobby: '',
                    status: 1,
                };
                state.title = "添加";
            }else{
                state.title = "修改";
                state.form = row;
            }
        }
        const submitForm = ()=>{
            HobbySave(state.form).then( (e)=>{
                if (e != 1) {
                    ElMessage({ message:'成功', type: "success" });
                    state.dialog = false;
                    Hobbylist().then( (e)=>{
                        state.tableData = e;
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
                HobbyDel({hobby_id:row.hobby_id}).then( (e)=>{
                    if (e != 1) {
                        ElMessage({ message:'成功', type: "success" });
                        Hobbylist().then( (e)=>{
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
            submitForm,
            delData
        };
    }
};
</script>