<template>
    <el-button type="primary" size="default" icon="Plus" style="margin-bottom:20px" @click="showDialog(1)"> 添加</el-button>
    <el-table :data="tableData" size="default" border stripe>
        <el-table-column type="expand">
            <template #default="scope">
                <el-table :data="scope.row.son" size="default" border stripe>
                    <el-table-column prop="sort" label="排序" min-width="80" />
                    <el-table-column prop="mid" label="ID" min-width="80" />
                    <el-table-column prop="label" label="菜单名" min-width="120" />
                    <el-table-column prop="type_s" label="类型" min-width="80" />
                    <el-table-column prop="src" label="跳转地址" min-width="180" />
                    <el-table-column prop="icon_class" label="图标" min-width="120" />
                    <el-table-column prop="status_s" label="状态" min-width="80" />
                    <el-table-column prop="cz" label="操作" min-width="150">
                        <template #default="scope">
                            <el-button type="primary" size="default" icon="Edit" @click="showDialog(2,scope.row)">修改</el-button>
                            <el-button type="danger" size="default" icon="Delete" @click="delData(scope.row)">删除</el-button>
                        </template>
                    </el-table-column>
                </el-table>
            </template>
        </el-table-column>
        <el-table-column prop="sort" label="排序" min-width="80" />
        <el-table-column prop="mid" label="ID" min-width="80" />
        <el-table-column prop="label" label="菜单名" min-width="120" />
        <el-table-column prop="type_s" label="类型" min-width="80" />
        <el-table-column prop="src" label="跳转地址" min-width="180" />
        <el-table-column prop="icon_class" label="图标" min-width="120" />
        <el-table-column prop="status_s" label="状态" min-width="80" />
        <el-table-column prop="cz" label="操作" min-width="150">
            <template #default="scope">
                <el-button type="primary" size="default" icon="Edit" @click="showDialog(2,scope.row)">修改</el-button>
                <el-button type="danger" size="default" icon="Delete" @click="delData(scope.row)">删除</el-button>
            </template>
        </el-table-column>
    </el-table>
    <el-dialog v-model="dialog" :title="title">
        <el-form :model="form" label-width="120px" size="default">
            <el-form-item label="菜单名">
                <el-input v-model="form.label" placeholder="请输入菜单名" />
            </el-form-item>
            <el-form-item label="归属" v-if="parent_show">
                <el-select v-model="form.parent_id" placeholder="请选择类型">
                    <el-option label="根目录" :value="0" />
                    <el-option v-for="(item,index,key) in tableData" :key="index" :label="item.label" :value="item.mid" />
                </el-select>
            </el-form-item>
            <el-form-item label="类型" v-if="parent_show">
                <el-select v-model="form.type" placeholder="请选择类型">
                    <el-option label="分组" :value="0" />
                    <el-option label="模块" :value="1" />
                    <el-option label="外网" :value="2" />
                </el-select>
            </el-form-item>
            <el-form-item label="跳转地址" v-if="src_show">
                <el-input v-model="form.src" placeholder="请输入跳转地址" />
            </el-form-item>
            <el-form-item label="图标">
                <el-select v-model="form.icon_class" placeholder="请选择图标">
                    <el-option value="List" label="List" />
                    <el-option value="Link" label="Link" />
                    <el-option value="Setting" label="Setting" />
                    <el-option value="Folder" label="Folder" />
                    <el-option value="User" label="User" />
                    <el-option value="UserFilled" label="UserFilled" />
                    <el-option value="Connection" label="Connection" />
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
import { MenuLists, MenuSave, MenuDel } from "@/network/bew";
import { ElMessage, ElMessageBox } from "element-plus";
export default {
    name: "MenuList",
    setup() {
        const state = reactive({
            tableData: [],
            dialog : false,
            parent_show : true,
            src_show : true,
            title : "添加",
            form: {
                mid : 0,
                parent_id : 0,
                label: "",
                type: 0,
                src: "",
                icon_class: "SetUp",
                sort: 0,
                status: 1,
            }
        });

        MenuLists().then( (e)=>{
            state.tableData = e.lists;
        } )
        const showDialog = (e,row={})=>{
            state.dialog = true;
            if(e == 1){
                state.parent_show = true;
                state.title = "添加";
                state.form = {
                    mid : 0,
                    parent_id : 0,
                    label: "",
                    type: 0,
                    src: "",
                    icon_class: "SetUp",
                    sort: 0,
                    status: 1
                };
            }else{
                state.parent_show = false;
                if(row.type == 0){
                    state.src_show = false;
                }else{
                    state.src_show = true;
                }
                state.title = "修改";
                state.form = row;
            }
        }
        const submitForm = ()=>{
            MenuSave(state.form).then( (e)=>{
                if (e != 1) {
                    ElMessage({ message:'成功', type: "success" });
                    MenuLists().then( (e)=>{
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
                MenuDel({mid:row.mid}).then( (e)=>{
                    if (e != 1) {
                        ElMessage({ message:'成功', type: "success" });
                        MenuLists().then( (e)=>{
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