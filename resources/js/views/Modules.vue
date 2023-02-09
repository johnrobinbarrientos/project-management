<template>
    <div>
        <div id="modules-page">
            <div style="margin-top:40px; margin-bottom:10px; display:flex; justify-content:space-between;">
                <div style="display:flex;">
                    <button @click="toggleModal(true)" class="bp-bordered-boxed"> <span class="ti-plus"></span></button>
                    <button @click="toggleFilter()" class="bp-bordered-boxed"> <span class="ti-filter"></span></button>
                </div>
            </div>

            <div v-if="show_filter" style="background:#efefef; padding:10px; margin-bottom:10px;">
                <input v-model="filter.keyword" style="width:300px; margin-right:10px; padding:5px 10px;" type="text">
            </div>
            
            <table class="bp-table">
               <thead>
                   <tr>
                       <th>Name</th>
                       <th>Key</th>
                       <th width="220">Action</th>
                   </tr>
               </thead>
               <tbody>
                   <tr v-for="row in items" :key="row.id">
                       <td>{{ row.name }}</td>
                       <td>{{ row.key || 'Not Set' }}</td>
                       <td>
                           <button @click="toggleRoleModal(true,row)" style="cursor:pointer; margin:0px; margin-right:5px; background:#2a3042; color:#fff; border:none; font-size:12px; width:auto; padding:5px; display:inline-block;">Roles</button>
                           <button @click="toggleBoardModal(true,row)" style="cursor:pointer; margin:0px; margin-right:5px; background:#2a3042; color:#fff; border:none; font-size:12px; width:auto; padding:5px; display:inline-block;">Boards</button>
                           <button @click="toggleStatusModal(true,row)" style="cursor:pointer; margin:0px; margin-right:5px; background:#2a3042; color:#fff; border:none; font-size:12px; width:auto; padding:5px; display:inline-block;">Status</button>
                           <button @click="toggleModal(true,row)" style="cursor:pointer; margin:0px; margin-right:5px; background:#2a3042; color:#fff; border:none; font-size:12px; width:auto; padding:5px; display:inline-block;">Edit</button>
                           <button @click="toggleModal(true)" style="cursor:pointer; margin:0px; background:#f73830; color:#fff; border:none; font-size:12px; width:auto; padding:5px; display:inline-block;">Delete</button>
                       </td>
                   </tr>
               </tbody>
            </table>
        </div>

        
        <div :class="{'open' : is_modal_open}" class="bp-modal">
            <div class="bp-modal-wrap">
                <div @click="toggleModal(false)" class="bp-modal-overlay"></div>
                <div class="bp-modal-content">
                    <div style="padding-bottom:10px; border-bottom:1px solid #efefef; margin-bottom:20px;">
                        <h4 v-if="selected.id != null">Update</h4>
                        <h4 v-else>New</h4>
                    </div>
                    <form v-on:submit.prevent="save()" action="/" class="form-validate is-alter" autocomplete="off" novalidate="novalidate">
                        <div class="form-group">
                            <div class="form-label-group">
                                <label class="form-label" for="email-address">Module Name</label>
                            </div>
                            <div class="form-control-wrap">
                                <input v-model="selected.name" type="text" class="form-control form-control-lg" required="" id="email-address" placeholder="Enter module name...">
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="form-label-group">
                                <label class="form-label" for="email-address">Module Key</label>
                            </div>
                            <div class="form-control-wrap">
                                <input v-model="selected.key" type="text" class="form-control form-control-lg" required="" id="email-address" placeholder="Enter module key..">
                            </div>
                        </div>

                        <div class="bp-modal-form-actions">
                            <button type="submit" class="bp-btn bp-btn-blue">Save</button>
                            <button @click="toggleModal(false)" type="button"  class="bp-btn bp-btn-gray">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>


        <div  :class="{'open' : is_modal_roles_open}" class="bp-modal">
            <div class="bp-modal-wrap">
                <div @click="toggleRoleModal(false)" class="bp-modal-overlay"></div>
                <div class="bp-modal-content">
                    <div style="padding-bottom:10px; border-bottom:1px solid #efefef; margin-bottom:20px; font-weight:600;">
                        Assign Roles Capabilities
                    </div>
                    <form v-on:submit.prevent="saveModuleRoles()" action="/" class="form-validate is-alter" autocomplete="off" novalidate="novalidate">
                        <table class="bp-table">
                            <thead>
                                <tr>
                                    <th>Role</th>
                                    <th width="70">Create</th>
                                    <th width="70">Read</th>
                                    <th width="70">Update</th>
                                    <th width="70">Delete</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="row in roles" :key="'role-' + row.id">
                                    <td>{{ row.title }}</td>
                                    <td class="text-center">
                                        <input type="checkbox" v-model="row.module_role.C" :value="row.module_role.C">
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" v-model="row.module_role.R">
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" v-model="row.module_role.U">
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" v-model="row.module_role.D">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <br/>
                        <div class="bp-modal-form-actions">
                            <button type="submit" class="bp-btn bp-btn-blue">Save Changes</button>
                            <button @click="toggleRoleModal(false)" type="button"  class="bp-btn bp-btn-gray">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div  :class="{'open' : is_modal_boards_open}" class="bp-modal">
            <div class="bp-modal-wrap">
                <div @click="toggleBoardModal(false)" class="bp-modal-overlay"></div>
                <div class="bp-modal-content">
                    <div style="padding-bottom:10px; border-bottom:1px solid #efefef; margin-bottom:20px; font-weight:600;">
                        {{ selected.name }} Boards
                    </div>
                    <form v-on:submit.prevent="saveModuleBoard()" action="/" class="form-validate is-alter" autocomplete="off" novalidate="novalidate">
                        <table class="bp-table">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th width="120">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr >
                                    <td><input v-model="board.title" style="width:100%; padding:3px 8px;" type="text"></td>
                                    <td @click="saveModuleBoard()" style="background:#2a3042; color:#fff; cursor:pointer;" class="text-center">
                                        ADD
                                    </td>
                                </tr>
                                <tr v-for="row in boards" :key="'board-' + row.id">
                                    <td>{{ row.title }}</td>
                                    <td class="text-center">
                                        EDIT
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <br/>
                        <div class="bp-modal-form-actions">
                            <button @click="toggleBoardModal(false)" type="button"  class="bp-btn bp-btn-gray">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div  :class="{'open' : is_modal_status_open}" class="bp-modal">
            <div class="bp-modal-wrap">
                <div @click="toggleStatusModal(false)" class="bp-modal-overlay"></div>
                <div class="bp-modal-content">
                    <div style="padding-bottom:10px; border-bottom:1px solid #efefef; margin-bottom:20px; font-weight:600;">
                        {{ selected.name }} Status
                    </div>
                    <form v-on:submit.prevent="saveModuleStatus()" action="/" class="form-validate is-alter" autocomplete="off" novalidate="novalidate">
                        <table class="bp-table">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th width="120">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr >
                                    <td><input v-model="status.title" style="width:100%; padding:3px 8px;" type="text"></td>
                                    <td @click="saveModuleStatus()" style="background:#2a3042; color:#fff; cursor:pointer;" class="text-center">
                                        ADD
                                    </td>
                                </tr>
                                <tr v-for="row in statuses" :key="'status-' + row.id">
                                    <td>{{ row.title }}</td>
                                    <td class="text-center">
                                        EDIT
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <br/>
                        <div class="bp-modal-form-actions">
                            <button @click="toggleStatusModal(false)" type="button"  class="bp-btn bp-btn-gray">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
       
         
    </div>
</template>



<script>
export default {
    props: ['user_id'],
    data: function () {
        return {
            show_filter: false,
            is_modal_open: false,
            is_modal_roles_open: false,
            is_modal_boards_open: false,
            is_modal_status_open: false,


            rows: [],
            roles: [],
            boards: [],
            statuses: [],

            
            selected: {
                id: null,
                name: '',
                key: ''
            },

            filter: {
                keyword: '',
                timeout: null,
            },

            board: {
                id: null,
                title: '',
                module_id: null
            },

            status: {
                id: null,
                title: '',
                module_id: null
            }
        }
    },
    components: {

    },
    computed: {
        items: function () {
            var scope = this
            return scope.rows.filter(item => (item.name.toLowerCase().includes(scope.filter.keyword) ));
        }
    },
    methods: {
        toggleModal: function (is_open,row = null) {
            this.is_modal_open = is_open
            if (row != null) {
                this.selected = row
            } else {
                this.selected = {
                    id: null,
                    name: '',
                    key: ''
                }
            }
        },
        toggleRoleModal: function (is_open,row = null) {
            this.is_modal_roles_open = is_open

            if (row != null) {
                this.selected = row
                this.getRoles(row)
            } else {
                this.selected = {
                    id: null,
                    title: ''
                }
            }
        },
        toggleBoardModal: function (is_open,row = null) {
            this.is_modal_boards_open = is_open

            if (row != null) {
                this.selected = row
                this.getBoards(row)
            } else {
                this.selected = {
                    id: null,
                    title: ''
                }

                this.board = {
                    id: null,
                    title: '',
                    module_id: null
                }
            }
        },
        toggleFilter: function (show_filter) {
            this.show_filter = !this.show_filter
        },

        toggleStatusModal: function (is_open,row = null) {
            this.is_modal_status_open = is_open

            if (row != null) {
                this.selected = row
                this.getStatus(row)
            } else {
                this.selected = {
                    id: null,
                    title: ''
                }

                this.status = {
                    id: null,
                    title: '',
                    module_id: null
                }
            }
        },


        save: function () {
            var scope = this

            if (scope.selected.id !== null) {
                scope.PUT('api/modules/' + scope.selected.id, scope.selected).then(function (res) {
                    if (res.success) {
                        alert(res.message)
                        scope.selected = null
                        scope.toggleModal(false)
                        scope.getRows();
                    } else {
                        alert(res.message);
                    }
                });
            } else {
                scope.POST('api/modules', scope.selected).then(function (res) {
                    if (res.success) {
                        alert(res.message)
                        scope.selected.title = '';
                        scope.toggleModal(false)
                        scope.getRows();
                    } else {
                        alert(res.message);
                    }
                });
            }
            
        },
        searchTimeOut() {  
            var scope = this
            if (scope.filter.timeout) {
                clearTimeout(scope.filter.timeout);
                scope.filter.timeout = null;
            }
            scope.filter.timeout = setTimeout(() => {
               scope.getRows();
            }, 300);
        },
        getRows: function () {
            var scope = this
            scope.GET('api/modules?keyword=' + scope.filter.keyword).then(function (res) {
                scope.rows = res.rows
            });
        },
        getRoles: function (module) {
            var scope = this
            scope.GET('api/modules/' + module.id + '/roles').then(function (res) {
                scope.roles = res.rows
            });
        },
        getBoards: function (module) {
            var scope = this
            scope.board.module_id = module.id
            scope.GET('api/modules/' + module.id + '/boards').then(function (res) {
                scope.boards = res.rows
            });
        },
        getStatus: function (module) {
            var scope = this
            scope.status.module_id = module.id
            scope.GET('api/modules/' + module.id + '/status').then(function (res) {
                scope.statuses = res.rows
            });
        },
        saveModuleBoard: function () {
            var scope = this
            scope.POST('api/modules/' + scope.selected.id + '/boards',scope.board).then(function (res) {
                 if (res.success) {
                    alert(res.message)
                    scope.getBoards(scope.selected);
                    scope.board.title = ''
                } else {
                    alert(res.message);
                }
            });
        },
        saveModuleStatus: function () {
            var scope = this
            scope.POST('api/modules/' + scope.selected.id + '/status',scope.status).then(function (res) {
                 if (res.success) {
                    alert(res.message)
                    scope.getStatus(scope.selected);
                    scope.status.title = ''
                } else {
                    alert(res.message);
                }
            });
        },
        saveModuleRoles: function () {
            var scope = this

            scope.PUT('api/modules/' + scope.selected.id + '/roles', { roles: scope.roles }).then(function (res) {
                if (res.success) {
                    alert(res.message)
                    scope.selected = null
                    scope.toggleRoleModal(false)
                    scope.getRoles();
                } else {
                    alert(res.message);
                }
            });
            
            
        },
    },
    created() {

    },
    mounted() {
        var scope = this
        scope.getRows();
        // scope.getRoles();
        // scope.VALIDATE_MODULE_modules('milestones');

        scope.$store.dispatch('changeHeader','Modules')
    }
}
</script>
<style scoped>
</style>