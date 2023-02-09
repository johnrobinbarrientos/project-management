<template>
    <div>
        <div id="modules-page">
           
            <div class="page-actions">
                <div class="page-actions-inner">
                    <button v-if="CAN_WRITE('Tags')" @click="toggleModal(true)" class="bp-bordered-boxed"> <span class="ti-plus"></span></button>
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
                        <div @click="sortBy('project.title')"  class="td sortable">
                            Project
                            <template v-if="sort.key == 'project.title'">
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
                        <div class="td">{{ row.project.title }}</div>
                        <div style="width:180px;" class="td">
                            <button v-if="CAN_EDIT('Tags')" @click="toggleModal(true,row)" class="bp-btn bp-btn-blue">Edit</button>
                            <button v-if="CAN_DELETE('Tags')" @click="toggleModal(true)" class="bp-btn bp-btn-red">Delete</button>
                        </div>
                    </div>
                </div>
            </div>

    
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
                                    <label class="form-label" for="email-address">Project</label>
                                </div>
                                <div class="form-control-wrap">
                                    <multiselect  v-model="selected_project" 
                                    :options="options_project" 
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
                                    <label class="form-label" for="email-address">Title</label>
                                </div>
                                <div class="form-control-wrap">
                                    <input v-model="selected.title" type="text" class="form-control form-control-lg" required="" id="email-address" placeholder="Enter role title...">
                                </div>
                            </div>
                            <div class="form-group">
                                <div style="display:flex;">
                                    <div style="margin-right:50px;">
                                        <div class="form-label-group">
                                            <label style="font-weight:600; " class="form-label" for="email-address">Backgound Color</label>
                                        </div>
                                        <div style="margin-top:5px;" class="form-control-wrap">
                                            <ColorPicker v-if="is_modal_open" theme="light" :color="selected.background" @changeColor="changeBackgroundColor"/>
                                        </div>
                                    </div>
                                    <div  style="margin-right:10px;">
                                        <div  class="form-label-group">
                                            <label style="font-weight:600;"  class="form-label" for="email-address">Text Color</label>
                                        </div>
                                        <div style="margin-top:5px;" class="form-control-wrap">
                                            <ColorPicker v-if="is_modal_open"  theme="light" :color="selected.color" @changeColor="changeTextColor"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="bp-modal-footer bp-modal-form-actions">
                        <button @click="save()" type="button" class="bp-btn bp-btn-blue">Save</button>
                        <button @click="toggleModal(false)" type="button"  class="bp-btn bp-btn-gray">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
       
         
    </div>
</template>



<script>
import { ColorPicker } from 'vue-color-kit'


export default {
    props: ['user_id'],
    data: function () {
        return {
            show_filter: false,
            is_modal_open: false,
            rows: [],
            selected: {
                id: null,
                title: '',
                background: '#5b75aa',
                color: '#333333',
            },
            filter: {
                keyword: '',
                timeout: null,
            },
            selected_project: null,
            options_project: [],
            sort: { key: 'title', direction: 'asc' }
        }
    },
    components: {
        ColorPicker
    },
    computed: {
        items: function () {
            var scope = this
            var items =  scope.rows.filter(item => (item.title.toLowerCase().includes(scope.filter.keyword) ));
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

                scope.selected_project = 
                {
                    id: row.project.id,
                    text: row.project.title,
                }

            } else {
                scope.selected = {
                    id: null,
                    title: '',
                    background: '#5b75aa',
                    color: '#333333',
                }

                if (scope.options_project){
                   scope.selected_project = (scope.options_project.length < 1) ? null : {
                        id: scope.options_project[0].id,
                        text: scope.options_project[0].text
                    }

                    scope.selected_depends_on = null
                }
            }
        },
        toggleFilter: function (show_filter) {
            this.show_filter = !this.show_filter
        },
        changeBackgroundColor(color) {
            const { r, g, b, a } = color.rgba
            //this.color = `rgba(${r}, ${g}, ${b}, ${a})`
            this.selected.background = color.hex
        },
        changeTextColor(color) {
            const { r, g, b, a } = color.rgba
            //this.color = `rgba(${r}, ${g}, ${b}, ${a})`
            this.selected.color = color.hex
        },
        save: function () {
            var scope = this

            scope.selected.project_id = (scope.selected_project == null) ? null : scope.selected_project.id


            if (scope.selected.id !== null) {
                scope.PUT('api/tags/' + scope.selected.id, scope.selected).then(function (res) {
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
                scope.POST('api/tags', scope.selected).then(function (res) {
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
            scope.GET('api/tags').then(function (res) {
                scope.rows = res.rows
            });
        },
        getProjects: function () {
            var scope = this
            scope.GET('api/projects').then(function (res) {

                res.rows.forEach(function (data) {
                    scope.options_project.push({
                        id: data.id,
                        text: data.title,
                    })
                })

            });
        },
    },
    created() {

    },
    mounted() {
        var scope = this
        scope.getRows();
        scope.getProjects();

        scope.$store.dispatch('changeHeader','Tags')
    }
}
</script>
<style scoped>
</style>