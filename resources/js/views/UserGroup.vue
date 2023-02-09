<template>
    <div>
        <div id="priority-page">
            <div style="margin-top:40px; margin-bottom:10px; display:flex; justify-content:space-between;">
                <div style="display:flex;">
                    <button @click="toggleModal(true)" class="bp-bordered-boxed"> <span class="ti-plus"></span></button>
                    <button @click="toggleFilter()" class="bp-bordered-boxed"> <span class="ti-filter"></span></button>
                </div>
            </div>

            <div v-if="show_filter" style="background:#efefef; padding:10px; margin-bottom:10px;">
                <input v-model="filter.keyword" style="width:300px; margin-right:10px; padding:5px 10px;" type="text">
            </div>
     

            <div class="datatable">
                <div class="tr header">
                    <div class="th">Title</div>
                    <div class="th">Users</div>

                    <div class="th" style="max-width:100px;">Action</div>
                </div>
                <div v-for="row in items" :key="row.id" class="tr">
                    <div class="td">{{ row.title }}</div>

                    <div class="td">
                        <span v-if="row.group_users.length > 0">
                            <span v-for="item in row.group_users" :key="item.id">
                                <span style="background-color: #e9ebee; border-color: #afb7c1; background: #50a5f1; color: #fff;padding-top: 4px;padding-bottom: 7px;padding-left: 4px;padding-right: 4px;border-radius: 100px;margin-right: 4px;">
                                {{ item.user.firstname }}</span>
                            </span>
                        </span>
                        <span v-else>
                           
                        </span>

                    </div>
                    <div class="td" style="max-width:100px;">
                        <button @click="toggleModal(true,row)" style="cursor:pointer; margin:0px; background:#2a3042; color:#fff; border:none; font-size:12px; width:auto; padding:5px; display:inline-block;">Edit</button>
                           <button @click="toggleModal(true)" style="cursor:pointer; margin:0px; background:#f73830; color:#fff; border:none; font-size:12px; width:auto; padding:5px; display:inline-block;">Delete</button>
                    </div>
                </div>
            </div>
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
                                <label class="form-label" for="email-address">Title</label>
                            </div>
                            <div class="form-control-wrap">
                                <input v-model="selected.title" type="text" class="form-control form-control-lg" required="" id="email-address" placeholder="Enter title...">
                            </div>
                        </div>

                        <div class="form-control-wrap">
                                <multiselect  v-model="selected_users" 
                                :options="options_users" 
                                track-by="id" 
                                label="text" 
                                :allow-empty="true" 
                                deselect-label="Unselect" 
                                selectLabel="Select"
                                :searchable="true"
                                :multiple="true"
                                >
                            </multiselect>
                        </div>
        
                        <div class="bp-modal-form-actions">
                            <button type="submit" class="bp-btn bp-btn-blue">Save</button>
                            <button @click="toggleModal(false)" type="button"  class="bp-btn bp-btn-gray">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
       
         
    </div>
</template>

<style scoped>
    
</style>

<script>
export default {
    props: ['user_id'],
    data: function () {
        return {
            show_filter: false,
            is_modal_open: false,
            userGroups: [],
            users:[],
            selected: {
                id: null,
                title: '',
            },
            filter: {
                keyword: '',
                timeout: null,
            },

            selected_users: [],
            options_users: [],
        }
    },
    components: {

    },
    computed: {
        items: function () {
            var scope = this
            return scope.userGroups.filter(item => (item.title.toLowerCase().includes(scope.filter.keyword) ));
        }
    },
    methods: {
        toggleFilter: function () {
            this.show_filter = !this.show_filter
        },
        toggleModal: function (is_open,row = null) {
            var scope = this

            scope.is_modal_open = is_open

            if (row != null) {
                console.log('sulod')
                scope.selected = row
                console.log(row)

                if (row.group_users!= null){
                    row.group_users.forEach(function (data) {
                    
                        scope.selected_users.push({
                            id: data.user.id,
                            text: data.user.firstname
                        })
                     })
                }

            } else {
                console.log('else')
                scope.selected = {
                    id: null,
                    title: '',
                    description: '',
                }

                scope.selected_users = []
            }
        },
        save: function () {
            var scope = this

            console.log('asdsad')
            console.log(scope.selected_users)

            scope.selected.user_details = scope.selected_users

            if (scope.selected.id !== null) {
                scope.PUT('api/user-groups/' + scope.selected.id, scope.selected).then(function (res) {
                    if (res.success) {
                        alert(res.message)
                        scope.selected = null
                        scope.toggleModal(false)
                        scope.getUserGroup();
                    } else {
                        alert(res.message);
                    }
                });
            } else {
                scope.POST('api/user-groups', scope.selected).then(function (res) {
                    if (res.success) {
                        alert(res.message)
                        scope.selected.title = '';
                        scope.toggleModal(false)
                        scope.getUserGroup();
                    } else {
                        alert(res.message);
                    }
                });
            }
            
        },
        getUserGroup: function () {
            var scope = this
            scope.GET('api/user-groups?keyword=' + scope.filter.keyword).then(function (res) {
                scope.userGroups = res.rows
                console.log('aaaaa')
                console.log(scope.userGroups)
            });
        },
        getUsers: function () {
            var scope = this
            scope.GET('api/users?keyword=' + scope.filter.keyword).then(function (res) {
                scope.users = res.rows
                console.log('usersss')
                console.log(scope.users)

                res.rows.forEach(function (data) {
                    
                    scope.options_users.push({
                        id: data.id,
                        text: data.firstname
                    })
                
                })

            });
        },
        searchTimeOut() {  
            var scope = this
            if (scope.filter.timeout) {
                clearTimeout(scope.filter.timeout);
                scope.filter.timeout = null;
            }
            scope.filter.timeout = setTimeout(() => {
               scope.getUserGroup();
            }, 300);
        }
    },
    created() {

    },
    mounted() {
        var scope = this
        scope.getUserGroup();
        scope.getUsers();

        scope.$store.dispatch('changeHeader','User Groups')
    }
}
</script>

