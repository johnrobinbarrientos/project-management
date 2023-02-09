<template>
    <div>
        <div id="item-types-page">
            <div style="margin-top:40px; margin-bottom:10px; display:flex; justify-content:space-between;">
                <div style="display:flex;">
                    <button v-if="CAN_WRITE('ItemTypes')"  @click="toggleModal(true)" class="bp-bordered-boxed"> <span class="ti-plus"></span></button>
                    <button @click="toggleFilter()" class="bp-bordered-boxed"> <span class="ti-filter"></span></button>
                </div>
            </div>

            <div v-if="show_filter" style="background:#efefef; padding:10px; margin-bottom:10px;">
                <input v-model="filter.keyword" style="width:300px; margin-right:10px; padding:5px 10px;" type="text">
            </div>
     

            <div class="datatable">
                <div class="tr header">
                    <div class="th">Title</div>

                    <div class="th" style="max-width:100px;">Action</div>
                </div>
                <div v-for="row in items" :key="row.id" class="tr">
                    <div class="td">{{ row.title }}</div>
                    <div class="td" style="max-width:100px;">
                        <button v-if="CAN_EDIT('ItemTypes')" @click="toggleModal(true,row)" style="cursor:pointer; margin:0px; background:#2a3042; color:#fff; border:none; font-size:12px; width:auto; padding:5px; display:inline-block;">Edit</button>
                        <button v-if="CAN_DELETE('ItemTypes')" @click="toggleModal(true)" style="cursor:pointer; margin:0px; background:#f73830; color:#fff; border:none; font-size:12px; width:auto; padding:5px; display:inline-block;">Delete</button>
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
            itemTypes: [],
            selected: {
                id: null,
                title: '',
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
        items: function () {
            var scope = this
            return scope.itemTypes.filter(item => (item.title.toLowerCase().includes(scope.filter.keyword) ));
        }
    },
    methods: {
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
                scope.PUT('api/item-types/' + scope.selected.id, scope.selected).then(function (res) {
                    if (res.success) {
                        alert(res.message)
                        scope.selected = null
                        scope.toggleModal(false)
                        scope.getItemTypes();
                    } else {
                        alert(res.message);
                    }
                });
            } else {
                scope.POST('api/item-types', scope.selected).then(function (res) {
                    if (res.success) {
                        alert(res.message)
                        scope.selected.title = '';
                        scope.toggleModal(false)
                        scope.getItemTypes();
                    } else {
                        alert(res.message);
                    }
                });
            }
            
        },
        getItemTypes: function () {
            var scope = this
            scope.GET('api/item-types?keyword=' + scope.filter.keyword).then(function (res) {
                scope.itemTypes = res.rows
            });
        },
        searchTimeOut() {  
            var scope = this
            if (scope.filter.timeout) {
                clearTimeout(scope.filter.timeout);
                scope.filter.timeout = null;
            }
            scope.filter.timeout = setTimeout(() => {
               scope.getItemTypes();
            }, 300);
        }
    },
    created() {

    },
    mounted() {
        var scope = this
        scope.getItemTypes();

        scope.$store.dispatch('changeHeader','Item Types')

    }
}
</script>

