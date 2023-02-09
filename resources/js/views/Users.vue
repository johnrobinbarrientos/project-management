<template>
    <div>
        <div id="users-page">
            <div style="margin-top:40px; margin-bottom:10px; display:flex; justify-content:space-between;">
                <div style="display:flex;">
                    <button v-if="CAN_WRITE('Users')" @click="toggleModal(true)" class="bp-bordered-boxed"> <span class="ti-plus"></span></button>
                    <button @click="toggleFilter()" class="bp-bordered-boxed"> <span class="ti-filter"></span></button>
                </div>
            </div>

            <div v-if="show_filter" style="background:#efefef; padding:10px; margin-bottom:10px;">
                <input v-model="filter.keyword" style="width:300px; margin-right:10px; padding:5px 10px;" type="text">
            </div>

            <div class="table-view">
                <div class="bp-list">
                    <div style="background:#424c68; color:#fff !important;" class="tr">
                        <div @click="sortBy('email')"  class="td sortable">
                            Email
                            <template v-if="sort.key == 'email'">
                                <i v-if="sort.direction == 'asc'" class="direction fa-solid fa-arrow-up-short-wide"></i>
                                <i v-if="sort.direction == 'desc'" class="direction fa-solid fa-arrow-down-short-wide"></i>
                            </template>
                            <template v-else>
                                <i class="direction fa-solid fa-sort"></i>
                            </template>
                        </div>
                        <div @click="sortBy('firstname')"  class="td sortable">
                            First Name
                            <template v-if="sort.key == 'firstname'">
                                <i v-if="sort.direction == 'asc'" class="direction fa-solid fa-arrow-up-short-wide"></i>
                                <i v-if="sort.direction == 'desc'" class="direction fa-solid fa-arrow-down-short-wide"></i>
                            </template>
                            <template v-else>
                                <i class="direction fa-solid fa-sort"></i>
                            </template>
                        </div>
                        <div @click="sortBy('lastname')"  class="td sortable">
                            Last Name
                            <template v-if="sort.key == 'lastname'">
                                <i v-if="sort.direction == 'asc'" class="direction fa-solid fa-arrow-up-short-wide"></i>
                                <i v-if="sort.direction == 'desc'" class="direction fa-solid fa-arrow-down-short-wide"></i>
                            </template>
                            <template v-else>
                                <i class="direction fa-solid fa-sort"></i>
                            </template>
                        </div>
                        <div style="width:180px;" class="td">
                    
                        </div>
                    </div>
                    <div v-for="row in items" :key="row.id" class="tr">
                        <div class="td">{{ row.email }}</div>
                        <div class="td">{{ row.firstname }}</div>
                        <div class="td">{{ row.lastname }}</div>

                        <div style="width:180px;" class="td">
                            <button v-if="CAN_EDIT('Users')" @click="toggleModal(true,row)" class="bp-btn bp-btn-blue">Edit</button>
                            <button v-if="CAN_DELETE('Users')" @click="toggleModal(true)" class="bp-btn bp-btn-red">Delete</button>
                        </div>
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
                                <label class="form-label" for="email-address">Email</label>
                            </div>
                            <div class="form-control-wrap">
                                <input v-model="selected.email" type="text" class="form-control form-control-lg" required="" id="email-address" placeholder="Enter email addresss...">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-label-group">
                                <label class="form-label" for="email-address">First Name</label>
                            </div>
                            <div class="form-control-wrap">
                                <input v-model="selected.firstname" type="text" class="form-control form-control-lg" required="" id="firstname" placeholder="Enter first name...">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-label-group">
                                <label class="form-label" for="email-address">Last Name</label>
                            </div>
                            <div class="form-control-wrap">
                                <input v-model="selected.lastname" type="text" class="form-control form-control-lg" required="" id="lastname" placeholder="Enter last name...">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-label-group">
                                <label class="form-label" for="email-address">User Type</label>
                            </div>
                            <div class="form-control-wrap">
                                <multiselect  v-model="selected_user_type" 
                                :options="options_user_type" 
                                track-by="id" 
                                label="text" 
                                :allow-empty="false" 
                                deselect-label="Selected" 
                                selectLabel="Select"
                                :searchable="true"
                                >
                                </multiselect>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="form-label-group">
                                <label class="form-label" for="email-address">User Role</label>
                            </div>
                            <div class="form-control-wrap">
                                <multiselect  v-model="selected.user_role" 
                                :options="options_user_role" 
                                track-by="id" 
                                label="title" 
                                :allow-empty="false" 
                                deselect-label="Selected" 
                                selectLabel="Select"
                                :searchable="true"
                                >
                                </multiselect>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="form-label-group">
                                <label class="form-label" for="email-address">Time</label>
                            </div>
                            <div class="form-control-wrap">
                                <multiselect  v-model="selected_time" 
                                :options="options_time"
                                track-by="value" 
                                label="text" 
                                :allow-empty="false" 
                                deselect-label="Selected" 
                                selectLabel="Select"
                                :searchable="true"
                                >
                                </multiselect>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="form-label-group">
                                <label class="form-label" for="email-address">Password</label>
                            </div>
                            <div class="form-control-wrap">
                                <input v-model="selected.password_1" type="password" class="form-control form-control-lg" required="" id="lastname" placeholder="Enter last name...">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-label-group">
                                <label class="form-label" for="email-address">Re-type Password</label>
                            </div>
                            <div class="form-control-wrap">
                                <input v-model="selected.password_2" type="password" class="form-control form-control-lg" required="" id="lastname" placeholder="Enter last name...">
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
       
         
    </div>
</template>



<script>
export default {
    props: ['user_id'],
    data: function () {
        return {
            show_filter: false,
            is_modal_open: false,
            rows: [],
            selected: {
                id: null,
                email: '',
                first_name: '',
                last_name: '',
                password_1: '',
                password_2: '',
                user_type_id: null,
                time: ''
            },
            filter: {
                keyword: '',
                timeout: null,
            },
            selected_user_type: null,
            options_user_type: [],

            selected_user_role: null,
            options_user_role: [],

            selected_time: null,
            options_time: [
                {text: 'minutes', value: 'minutes'},
                {text: 'hours', value: 'hours'},
                {text: 'days', value: 'days'},
                {text: 'weeks', value: 'weeks'}
            ],
            sort: { key: 'title', direction: 'asc' }
        }
    },
    components: {

    },
    computed: {
        items: function () {
            var scope = this
            var items = scope.rows.filter(item => (item.email.toLowerCase().includes(scope.filter.keyword) || item.firstname.toLowerCase().includes(scope.filter.keyword) || item.lastname.toLowerCase().includes(scope.filter.keyword)));
            var sortKey = scope.sort.key
            var sortDirection = scope.sort.direction

            return scope.SORT_OBJECT(items,sortKey,sortDirection)
        }
    },
    methods: {
        sortBy: function(key) {
            if (this.sort.key != key) { this.sort.direction == 'desc' } // to be converted back later to asc
            this.sort.key = key;
            this.sort.direction = (this.sort.direction == 'asc') ? 'desc' : 'asc';            
        },
        toggleModal: function (is_open,row = null) {
            var scope = this

            scope.is_modal_open = is_open
            if (row != null) {
                scope.selected = row
                
                if (row.user_type!= null){
                    scope.selected_user_type = 
                    {
                        id: row.user_type.id,
                        text: row.user_type.title
                    }  
                }
                else {
                        scope.selected_user_type = null
                    }

                scope.selected_time =  {
                            text: row.time,
                            value: row.time
                        }
                console.log(row)
                console.log('sulood')

            } else {
                scope.selected = {
                    id: null,
                    title: ''
                }

                if (scope.options_user_type){

                   scope.selected_user_type = (scope.options_user_type.length < 1) ? null : {
                            id: scope.options_user_type[0].id,
                            text: scope.options_user_type[0].text
                        }
                }

                scope.selected_time =  {
                            text: scope.options_time[0].text,
                            value: scope.options_time[0].value
                        }
            }
        },
        toggleFilter: function (show_filter) {
            this.show_filter = !this.show_filter
        },
        save: function () {
            var scope = this

            scope.selected.user_type_id = (scope.selected_user_type == null) ? null : scope.selected_user_type.id

            scope.selected.time = (scope.selected_time == null) ? null : scope.selected_time.value

            if (scope.selected.id !== null) {
                scope.PUT('api/users/' + scope.selected.id, scope.selected).then(function (res) {
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
                scope.POST('api/users', scope.selected).then(function (res) {
                    if (res.success) {
                        alert(res.message)
                        scope.selected.email = '';
                        scope.selected.first_name = '';
                        scope.selected.last_name = '';
                        scope.selected.password_1 = '';
                        scope.selected.password_2 = '';
                        scope.toggleModal(false)
                        scope.getRows();
                    } else {
                        alert(res.message);
                    }
                });
            }
            
        },
        getRows: function () {
            var scope = this
            scope.GET('api/users?keyword=' + scope.filter.keyword).then(function (res) {
                scope.rows = res.rows
                console.log('rooows')
                console.log(scope.rows)
            });
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
        getUserType: function () {
            var scope = this
            scope.GET('api/user_type?keyword=' + scope.filter.keyword).then(function (res) {

                res.rows.forEach(function (data) {
                    scope.options_user_type.push({
                        id: data.id,
                        text: data.title,
                    })
                })
            });
        },
        getUserRole: function () {
            var scope = this
            scope.options_user_role = []

            scope.GET('api/roles?keyword=' + scope.filter.keyword).then(function (res) {
                scope.options_user_role = res.rows
            });
        },
    },
    created() {

    },
    mounted() {
        var scope = this
        scope.getRows();
        scope.getUserType()
        scope.getUserRole()

        scope.$store.dispatch('changeHeader','Users')
    }
}
</script>
<style scoped>
</style>