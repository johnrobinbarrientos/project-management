<template>
    <div>
        <div id="modules-page">
            <div class="page-actions">
                <div class="page-actions-inner">
                    <button v-if="CAN_WRITE('Roles')" @click="toggleModal(true)" class="bp-bordered-boxed"> <span class="ti-plus"></span></button>
                    <button @click="toggleFilter()" class="bp-bordered-boxed"> <span class="ti-filter"></span></button>
                </div>
            </div>

            <div v-if="show_filter" class="page-filter">
                <input v-model="filter.keyword" type="text">
            </div>

            <table class="bp-table">
               <thead>
                   <tr>
                       <th>Name</th>
                       <th width="100">Action</th>
                   </tr>
               </thead>
               <tbody>
                   <tr v-for="row in rows" :key="row.id">
                       <td>{{ row.title }}</td>
                       <td class="actions">
                           <button v-if="CAN_EDIT('Roles')" @click="toggleModal(true,row)" class="btn-dark">Edit</button>
                           <button v-if="CAN_EDIT('Roles')" @click="toggleModal(true)" class="btn-red">Delete</button>
                       </td>
                   </tr>
               </tbody>
            </table>
        </div>

        
        <div :class="{'open' : is_modal_open}" class="bp-modal">
            <div class="bp-modal-wrap">
                <div @click="toggleModal(false)" class="bp-modal-overlay"></div>
                <div class="bp-modal-content">
                    <div class="bp-modal-header">
                        <h4 v-if="selected.id != null">Update</h4>
                        <h4 v-else>New</h4>
                    </div>
                    <div class="bp-modal-body">
                        <form v-on:submit.prevent="save()" action="/" class="form-validate is-alter" autocomplete="off" novalidate="novalidate">
                            <div class="form-group">
                                <div class="form-label-group">
                                    <label class="form-label" for="email-address">Title</label>
                                </div>
                                <div class="form-control-wrap">
                                    <input v-model="selected.title" type="text" class="form-control form-control-lg" required="" id="email-address" placeholder="Enter role title...">
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="bp-modal-form-actions">
                        <button @click="save()" type="button" class="bp-btn bp-btn-blue">Save</button>
                        <button @click="toggleModal(false)" type="button"  class="bp-btn bp-btn-gray">Cancel</button>
                    </div>
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
                title: ''
            },
            filter: {
                keyword: '',
                timeout: null,
            }
        }
    },
    components: {

    },
    computed: {

    },
    methods: {
        toggleModal: function (is_open,row = null) {
            this.is_modal_open = is_open
            if (row != null) {
                this.selected = row
            } else {
                this.selected = {
                    id: null,
                    title: ''
                }
            }
        },
        toggleFilter: function (show_filter) {
            this.show_filter = !this.show_filter
        },
        save: function () {
            var scope = this

            if (scope.selected.id !== null) {
                scope.PUT('api/roles/' + scope.selected.id, scope.selected).then(function (res) {
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
                scope.POST('api/roles', scope.selected).then(function (res) {
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
        getRows: function () {
            var scope = this
            scope.GET('api/roles').then(function (res) {
                scope.rows = res.rows
            });
        }
    },
    created() {

    },
    mounted() {
        var scope = this
        scope.getRows();
        // scope.VALIDATE_MODULE_ROLES('milestones');
        scope.$store.dispatch('changeHeader','Roles')

    }
}
</script>
<style scoped>
</style>