<template>
    <el-button type="primary" size="default" icon="Plus" style="margin-bottom:20px" @click="showDialog(1)"> 添加</el-button>
    <el-table :data="tableData" size="default" border stripe>
        <el-table-column prop="group_id" label="ID" min-width="60" />
        <el-table-column prop="group_name" label="部门名" min-width="120" />
        <el-table-column prop="status_s" label="状态" min-width="60" />
        <el-table-column prop="time_add" label="创建时间" min-width="100" />
        <el-table-column prop="cz" label="操作" min-width="150">
            <template #default="scope">
                <el-button type="primary" size="default" icon="Edit" @click="showDialog(2,scope.row)">修改</el-button>
                <el-button type="danger" size="default" icon="Delete" @click="delData(scope.row)">删除</el-button>
            </template>
        </el-table-column>
    </el-table>
    <el-dialog v-model="dialog" title="添加">
        <el-form :model="form" label-width="120px" size="default">
            <el-form-item label="部门名">
                <el-input v-model="form.group_name" placeholder="请输入部门名" />
            </el-form-item>
            <el-form-item label="状态">
                <el-select v-model="form.status" placeholder="请选择状态">
                    <el-option label="开启" :value="1" />
                    <el-option label="关闭" :value="0" />
                </el-select>
            </el-form-item>
            <el-form-item label="权限" :label-width="formLabelWidth" prop="menus">
                <el-checkbox-group v-model="form.menus" style="width:100%;">
                    <template v-for="(item,index,key) in menu" :key="key">
                        <el-checkbox :label="item.mid">{{item.label}}</el-checkbox>
                        <br />
                        <template v-for="(items,indexs,keys) in item.son" :key="keys">
                            <el-checkbox :label="items.mid">{{items.label}}</el-checkbox>
                        </template>
                        <el-divider />
                    </template>
                </el-checkbox-group>
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
import { GroupLists, GroupSave, GroupDel } from "@/network/bewadmin";
import { ElMessage, ElMessageBox } from "element-plus";
export default {
    name: "GroupList",
    setup() {
        const state = reactive({
            tableData: [],
            menu : [],
            dialog : false,
            title : "添加",
            form: {
                group_name: "",
                status: 1,
                menus: [],
            }
        });
        GroupLists().then( (e)=>{
            state.tableData = e.lists;
            state.menu = e.menu;
        } )
        const showDialog = (e,row)=>{
            state.dialog = true;
            if(e == 1){
                state.form = {
                    group_name: "",
                    status: 1,
                    menus: []
                };
                state.title = "添加";
            }else{
                state.title = "修改";
                state.form = row;
            }
        }
        const submitForm = ()=>{
            GroupSave(state.form).then( (e)=>{
                if (e != 1) {
                    ElMessage({ message:'成功', type: "success" });
                    GroupLists().then( (e)=>{
                        state.tableData = e.lists;
                        state.dialog = false;
                    } )
                }
            } )
        }
        const delData = (row={})=>{
            ElMessageBox.confirm("删除后不能恢复，确定删除吗？", "警告", {
                confirmButtonText: "确定",
                cancelButtonText: "取消",
                type: "warning",
            }).then( ()=>{
                GroupDel({group_id:row.group_id}).then( (e)=>{
                    if (e != 1) {
                        ElMessage({ message:'成功', type: "success" });
                        GroupLists().then( (e)=>{
                            state.tableData = e.lists;
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