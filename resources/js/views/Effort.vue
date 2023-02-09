<template>
    <div>
        <div id="efforts-page">

            <div class="page-actions">
                <div class="page-actions-inner">
                    <button v-if="CAN_WRITE('Efforts')"  @click="toggleModal(true)" class="bp-bordered-boxed"> <span class="ti-plus"></span></button>
                    <button @click="toggleFilter()" class="bp-bordered-boxed"> <span class="ti-filter"></span></button>
                </div>
            </div>

            <div v-if="show_filter" class="page-filter">
                <input v-model="filter.keyword" type="text">
            </div>

            <div class="table-view">
                <div class="bp-list">
                    <div style="background:#424c68; color:#fff !important;" class="tr">
                        <div @click="sortBy('title')"  class="td sortable">
                            Title
                            <template v-if="sort.key == 'title'">
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
                        <div class="td">{{ row.title }}</div>
                        <div style="width:180px;" class="td">
                            <button v-if="CAN_EDIT('Efforts')" @click="toggleModal(true,row)" class="bp-btn bp-btn-blue">Edit</button>
                            <button v-if="CAN_DELETE('Efforts')" @click="toggleModal(true)" class="bp-btn bp-btn-red">Delete</button>
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
                                <label class="form-label" for="email-address">Title</label>
                            </div>
                            <div class="form-control-wrap">
                                <input v-model="selected.title" type="text" class="form-control form-control-lg" required="" id="email-address" placeholder="Enter title...">
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

<style scoped>
    
</style>

<script>
export default {
    props: ['user_id'],
    data: function () {
        return {
            show_filter: false,
            is_modal_open: false,
            efforts: [],
            selected: {
                id: null,
                title: '',
            },
            filter: {
                keyword: '',
                timeout: null,
            },
            sort: { key: 'title', direction: 'asc' }
        }
    },
    components: {

    },
    computed: {
        items: function () {
            var scope = this
            var items =  scope.efforts.filter(item => (item.title.toLowerCase().includes(scope.filter.keyword) ));
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
        toggleFilter: function () {
            this.show_filter = !this.show_filter
        },
        toggleModal: function (is_open,row = null) {
            this.is_modal_open = is_open
            if (row != null) {
                this.selected = row
            } else {
                this.selected = {
                    id: null,
                    title: '',
                    description: '',
                }
            }
        },
        save: function () {
            var scope = this

            if (scope.selected.id !== null) {
                scope.PUT('api/efforts/' + scope.selected.id, scope.selected).then(function (res) {
                    if (res.success) {
                        alert(res.message)
                        scope.selected = null
                        scope.toggleModal(false)
                        scope.getEffort();
                    } else {
                        alert(res.message);
                    }
                });
            } else {
                scope.POST('api/efforts', scope.selected).then(function (res) {
                    if (res.success) {
                        alert(res.message)
                        scope.selected.title = '';
                        scope.toggleModal(false)
                        scope.getEffort();
                    } else {
                        alert(res.message);
                    }
                });
            }
            
        },
        getEffort: function () {
            var scope = this
            scope.GET('api/efforts?keyword=' + scope.filter.keyword).then(function (res) {
                scope.efforts = res.rows
            });
        },
        searchTimeOut() {  
            var scope = this
            if (scope.filter.timeout) {
                clearTimeout(scope.filter.timeout);
                scope.filter.timeout = null;
            }
            scope.filter.timeout = setTimeout(() => {
               scope.getEffort();
            }, 300);
        }
    },
    created() {

    },
    mounted() {
        var scope = this
        scope.getEffort();

        scope.$store.dispatch('changeHeader','Efforts')

    }
}
</script>

