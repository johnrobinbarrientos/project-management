<template>
    <div>
        <div id="projects-page" v-if="module_loaded">

            <div class="bp-actions">
                <div class="page-actions-inner">
                    <button v-if="CAN_WRITE('Projects')" @click="toggleModalNew()" class="bp-bordered-boxed"> <span class="ti-plus"></span></button>
                    <button @click="toggleFilter()" class="bp-bordered-boxed"> <span class="ti-filter"></span></button>
                    &nbsp;

                    <button @click="switchView('table_view')" class="bp-bordered-boxed" :class="{'active' : view == 'table_view' }"> <span class="ti-view-list-alt"></span></button>
                    <button @click="switchView('kanban_view')" class="bp-bordered-boxed" :class="{'active' : view == 'kanban_view' }"> <span class="ti-layout-grid3"></span></button>
                    &nbsp;

                    <button @click="toggleBulkEditModal(true)" class="bp-bordered-boxed" :class="{'disabled' : (selected_ids.length < 1) }"> <span class="ti-pencil"></span></button>
                    <button @click="deleteBulk()" class="bp-bordered-boxed" :class="{'disabled' : (selected_ids.length < 1) }"> <span class="ti-trash"></span></button>
                    &nbsp;

                    <div class="mini-column-filter-container">
                        <button @click="toggleSettings()" class="bp-bordered-boxed"> <i class="fa-solid fa-gear"></i></button>
                        <div class="mini-column-filter-inner" v-bind:class="{'open' : is_settings_open }">
                            <perfect-scrollbar>
                                <div class="col" v-for="c,index in columns" :key="'cfilter-' + index" style="">
                                    <input @change="TOGGLE_COLUMN('milestones',c)" type="checkbox" :checked="!c.hidden">
                                    <label>{{ c.label }}</label>
                                </div>
                            </perfect-scrollbar>
                        </div>
                    </div>
                </div>
            </div>

 
            <div v-if="show_filter" class="page-filter">
                <div style="max-width:320px;">
                    <input  v-on:keyup.enter="search()" v-model="filter.keyword" type="text">
                    <div class="tag-keyword" v-for="k,index in filter.keywords" :key="'keyword-' + index">
                        {{ k }}
                        <i @click="removeSearchKeyword(k)" class="ti-close"></i>
                    </div>
                </div>
            </div>

            
            <div class="bp-body">
                <div v-if="view == 'table_view'" class="table-view">

                    <div class="bp-list">
                        <div style="background:#000; color:#fff !important;" class="tr">
                            <div class="td">#</div>
                            <template v-for="c,index in columns"  :key="'col-' + index">
                                <template v-if="!c.hidden">
                                    <template v-if="c.filter">
                                        <div @click="sortBy(c.column)" class="td sortable">
                                            {{ c.label }}
                                            <template v-if="sort.key == c.column">
                                                <i v-if="sort.direction == 'asc'" class="direction fa-solid fa-arrow-up-short-wide"></i>
                                                <i v-if="sort.direction == 'desc'" class="direction fa-solid fa-arrow-down-short-wide"></i>
                                            </template>
                                            <template v-else>
                                                <i class="direction fa-solid fa-sort"></i>
                                            </template>
                                        </div>
                                    </template>
                                    <template v-else>
                                        <div class="td ">{{ c.label }}</div>
                                    </template>
                                </template>
                            </template>
                            <div style="width:180px;" class="td">
                    
                            </div>
                        </div>
                        <div class="tr" v-for="row in items" :key="row.id">
                            <div class="td" style="max-width: 40px;">#</div>
                            <div v-if="!IS_COLUMN_HIDDEN('projects','title')" class="td">{{ row.title }}</div>
                            <div v-if="!IS_COLUMN_HIDDEN('projects','description')" class="td">{{ row.description }}</div>
                            <div v-if="!IS_COLUMN_HIDDEN('projects','is_active')" class="td">
                                <span v-if="row.is_active === 1">Yes</span>
                                <span v-else>No</span>
                            </div>
                            <div v-if="!IS_COLUMN_HIDDEN('projects','sprints_yes')" class="td">
                                <span v-if="row.sprints_yes === 1">Yes</span>
                                <span v-else>No</span>
                            </div>
                            <div v-if="!IS_COLUMN_HIDDEN('projects','milestones_yes')" class="td">
                                <span v-if="row.milestones_yes === 1">Yes</span>
                                <span v-else>No</span>
                            </div>
                            <div v-if="!IS_COLUMN_HIDDEN('projects','ideas_yes')" class="td">
                                <span v-if="row.ideas_yes === 1">Yes</span>
                                <span v-else>No</span>
                            </div>
                            <div v-if="!IS_COLUMN_HIDDEN('projects','retrospectives_yes')" class="td">
                                <span v-if="row.retrospectives_yes === 1">Yes</span>
                                <span v-else>No</span>
                            </div>
                            <div v-if="!IS_COLUMN_HIDDEN('projects','timesheet_yes')" class="td">
                                <span v-if="row.timesheet_yes === 1">Yes</span>
                                <span v-else>No</span>
                            </div>
                            <div v-if="!IS_COLUMN_HIDDEN('projects','chat_yes')" class="td">
                                <span v-if="row.chat_yes === 1">Yes</span>
                                <span v-else>No</span>
                            </div>
                            <div v-if="!IS_COLUMN_HIDDEN('projects','project_start_date')" class="td"><i>{{ FORMAT_DATE(row.project_start_date) || 'Not Set' }}</i></div>
                            <div v-if="!IS_COLUMN_HIDDEN('projects','project_due_date')" class="td"><i>{{ FORMAT_DATE(row.project_due_date) || 'Not Set' }}</i></div>
                            <div v-if="!IS_COLUMN_HIDDEN('projects','project_completed_date')" class="td"><i>{{ FORMAT_DATE(row.project_completed_date) || 'Not Set' }}</i></div>
                            <div style="text-align:right;" class="td">
                                <button @click="viewTasks(row)" class="bp-btn bp-btn-blue">Tasks</button>
                                <button v-if="CAN_EDIT('Projects')"  @click="toggleModalNew(row)" class="bp-btn bp-btn-blue">Edit</button>
                                <button v-if="CAN_DELETE('Projects')" class="bp-btn bp-btn-red">Delete</button>
                            </div>
                        </div>

                    </div>

                </div>


                <div v-if="view == 'kanban_view'" class="kandan-view" style="display:flex;">
                    <div style="display:flex;">
                        <draggable style="display:flex;" class="dragArea list-group w-full"  :group="'boards'" :list="boards"  @change="sortModuleBoards()">
                            <div style="min-height:200px;  border:1px solid #ccc; margin:0px 10px; flex:1; min-width:200px;"  v-for="board,index in boards" :key="'board-' + index">
                                
                                <div style="background:#424c68; padding:3px 10px; color:#fff;">{{ board.title }}</div>
                                <draggable class="dragArea list-group w-full" :group="'element'" :list="board.projects" @change="sortBoardTasks(board, $event)">
                                    <div style="padding:3px 10px;  border:1px solid #ccc; border-left:none; border-right:none; border-top:none;" class="list-group-item" v-for="element in board.projects" :key="element.id">
                                        {{ element.title }}
                                    </div>
                                </draggable>
                            </div>
                        </draggable>


                        <div @click="toggleAddBoardModal(true)" style="cursor:pointer; background:#efefef; border:1px solid #ccc; display:flex; min-height:200px; align-items:center; text-align:center; margin:0px 10px; flex:1; min-width:200px;" >
                                
                            <div style="width:100%;">
                                <span class="ti-plus"></span>
                                <p>Add Board</p>
                            </div>
                        </div>
                    </div>
                    <div v-if="filter.is_loading" class="table-loader">
                        <svg version="1.1" id="L4" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                        viewBox="0 0 100 100" enable-background="new 0 0 0 0" xml:space="preserve">
                        <circle fill="#474747" stroke="none" cx="20" cy="50" r="6">
                            <animate
                            attributeName="opacity"
                            dur="1s"
                            values="0;1;0"
                            repeatCount="indefinite"
                            begin="0.1"/>    
                        </circle>
                        <circle fill="#474747" stroke="none" cx="50" cy="50" r="6">
                            <animate
                            attributeName="opacity"
                            dur="1s"
                            values="0;1;0"
                            repeatCount="indefinite" 
                            begin="0.2"/>       
                        </circle>
                        <circle fill="#474747" stroke="none" cx="80" cy="50" r="6">
                            <animate
                            attributeName="opacity"
                            dur="1s"
                            values="0;1;0"
                            repeatCount="indefinite" 
                            begin="0.3"/>     
                        </circle>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <FormProjectModal :formdata="selected"></FormProjectModal>
   
        <div :class="{'open' : is_bulK_edit_modal_open}" class="bp-modal">
            <div class="bp-modal-wrap">
                <div @click="toggleBulkEditModal(false)" class="bp-modal-overlay"></div>
                <div class="bp-modal-content">
                    <div style="padding-bottom:10px; border-bottom:1px solid #efefef; margin-bottom:20px;">
                        <h4>Edit Projects</h4>
                    </div>
                    <form v-on:submit.prevent="bulkSave()" action="/" class="form-validate is-alter" autocomplete="off" novalidate="novalidate">
               
                        <div class="form-control-wrap">
                            <div class="form-check">
                                <input type="checkbox" v-model="selected.is_active" true-value="1" false-value="0" class="form-check-input" id="is-active">
                                <label class="form-check-label" for="is-active">Is Active</label>
                            </div>
                        </div>

                        <div class="form-control-wrap">
                            <div class="form-check">
                                <input type="checkbox" v-model="selected.sprints_yes" true-value="1" false-value="0" class="form-check-input" id="sprints">
                                <label class="form-check-label" for="sprints">Sprints</label>
                            </div>
                        </div>

                        <div class="form-control-wrap">
                            <div class="form-check">
                                <input type="checkbox" v-model="selected.milestones_yes" true-value="1" false-value="0" class="form-check-input" id="milestone">
                                <label class="form-check-label" for="milestone">Milestone</label>
                            </div>
                        </div>

                        <div class="form-control-wrap">
                            <div class="form-check">
                                <input type="checkbox" v-model="selected.ideas_yes" true-value="1" false-value="0" class="form-check-input" id="ideas">
                                <label class="form-check-label" for="ideas">Ideas</label>
                            </div>
                        </div>

                        <div class="form-control-wrap">
                            <div class="form-check">
                                <input type="checkbox" v-model="selected.retrospectives_yes" true-value="1" false-value="0" class="form-check-input" id="retrospectives">
                                <label class="form-check-label" for="retrospectives">Retrospective</label>
                            </div>
                        </div>

                        <div class="form-control-wrap">
                            <div class="form-check">
                                <input type="checkbox" v-model="selected.timesheet_yes" true-value="1" false-value="0" class="form-check-input" id="timesheet">
                                <label class="form-check-label" for="timesheet">Timesheet</label>
                            </div>
                        </div>

                        <div class="form-control-wrap">
                            <div class="form-check">
                                <input type="checkbox" v-model="selected.chat_yes" true-value="1" false-value="0" class="form-check-input" id="chat">
                                <label class="form-check-label" for="chat">Chat</label>
                            </div>
                        </div>


            
                        <div class="bp-modal-form-actions">
                            <button type="submit" class="bp-btn bp-btn-blue">Save</button>
                            <button @click="toggleBulkEditModal(false)" type="button"  class="bp-btn bp-btn-gray">Cancel</button>
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
import moment from 'moment'
import FormProjectModal from './forms/FormProjectModal.vue'

var attributes = { 
    id: null,title: '',description: '',is_active: 0,sprints_yes: 0, milestones_yes: 0,ideas_yes: 0,
    retrospectives_yes: 0,timesheet_yes: 0,chat_yes: 0,duration: 60, project_start_date: '',project_due_date: '', project_completed_date: ''
}

export default {
    props: ['user_id'],
    data: function () {
        return {
            view: 'table_view',
            show_filter: false,
            is_modal_open: false,
            is_bulK_edit_modal_open: false,
            // rows: [],
            projects: [],
            selected: JSON.parse(JSON.stringify(attributes)),
            selected_ids: [],
            filter: {
                keyword: '',
                keywords: [],
                timeout: null,
                page: 1, 
                take:40,
                enable_paginate: true,
                is_loading: true
            },

            selected_users: [],
            options_users: [],

            module: {
                id: null,
                name: 'Projects'
            },
            module_loaded: false,
            sort: { key: 'title', direction: 'asc' },
            is_settings_open: false,
            columns: [
                {
                    column: 'title',
                    label: 'Title',
                    filter: true,
                    hidden: false
                },
                {
                    column: 'description',
                    label: 'Description',
                    filter: true,
                    hidden: false
                },
                {
                    column: 'is_active',
                    label: 'Active',
                    filter: true,
                    hidden: false
                },
                {
                    column: 'sprints_yes',
                    label: 'Sprint',
                    filter: true,
                    hidden: false
                },
                {
                    column: 'milestones_yes',
                    label: 'Milestone',
                    filter: true,
                    hidden: false
                },
                {
                    column: 'ideas_yes',
                    label: 'Ideas',
                    filter: true,
                    hidden: false
                },
                {
                    column: 'retrospectives_yes',
                    label: 'Retrospectives',
                    filter: true,
                    hidden: false
                },
                {
                    column: 'timesheet_yes',
                    label: 'Timesheet',
                    filter: false,
                    hidden: false
                },
                {
                    column: 'chat_yes',
                    label: 'Chat',
                    filter: false,
                    hidden: false
                },
                {
                    column: 'project_start_date',
                    label: 'Start Date',
                    filter: false,
                    hidden: false
                },
                {
                    column: 'project_due_date',
                    label: 'Due Date',
                    filter: true,
                    hidden: false
                },
                {
                    column: 'project_completed_date',
                    label: 'Completed',
                    filter: true,
                    hidden: false
                },
            ]
        }
    },
    components: {
        FormProjectModal
    },
    computed: {
        items: function () {
            var scope = this

            var items = scope.projects.filter(function(item){
                var keywords = scope.filter.keywords
                return ( 
                    (keywords.length < 1 )||
                    keywords.some(el => item.title.toLowerCase().includes(el)) || 
                    ( item.description && keywords.some(el => item.description.toLowerCase().includes(el))) ||  
                    (item.project_start_date && keywords.some(el => item.project_start_date.toLowerCase().includes(el))) || 
                    (item.project_due_date && keywords.some(el => item.project_due_date.toLowerCase().includes(el)))
                ) 
            });

            var sortKey = scope.sort.key
            var sortDirection = scope.sort.direction

            return scope.SORT_OBJECT(items,sortKey,sortDirection)
            // return scope.projects.filter(item => (item.title.toLowerCase().includes(scope.filter.keyword) ));
        },
        boards: function() {
            let scope = this
            return scope.$store.getters.getModuleBoards(scope.module.id)
        }
    },
    methods: {
        sortBy: function(key) {
            if (this.sort.key != key) { this.sort.direction == 'desc' } // to be converted back later to asc
            this.sort.key = key;
            this.sort.direction = (this.sort.direction == 'asc') ? 'desc' : 'asc';
        },
        toggleSettings: function () {
            this.is_settings_open = !this.is_settings_open
        },
        search: function () {
            var scope = this
            var keyword = scope.filter.keyword

            if (keyword == '') {
                return
            }

            var keywords = keyword.split(",");

            for (let i = 0; i < keywords.length; i++) {
                var key = keywords[i]
                scope.filter.keywords.push(key)
            }

            scope.filter.keyword = ''
        },
        removeSearchKeyword: function (k) {
            var scope = this
            var index = scope.filter.keywords.findIndex(keyword => keyword == k);

            scope.filter.keywords.splice(index, 1);
            
        },
        toggleModalNew: function (data = null) {
            var scope = this

            scope.selected = JSON.parse(JSON.stringify(attributes))
            
            if (data != null) {
                scope.selected = data
                scope.selected.milestone_duration = scope.milestone_duration
            }

            
            scope.MODAL_TOGGLE('form-project-modal')
        },
        switchView: function (view) {
            this.view = view
        },
        toggleBulkEditModal: function (is_open) {
            var scope = this

            if (scope.selected_ids.length < 1) {
                return;
            }

            scope.is_bulK_edit_modal_open = is_open
        },
        toggleFilter: function (show_filter) {
            this.show_filter = !this.show_filter
        },
        tagComplete: function () {
            var scope = this
            scope.selected.project_completed_date = moment().format('YYYY-MM-DD')
        },

        recalculate: function (project_id) {
            var scope = this
                    scope.GET('api/projects/task-durations/' + project_id).then(function (res) {
                    console.log(res.rows)
                    scope.selected.project_due_date = moment(scope.selected.project_start_date).add(res.rows, 'days').format('YYYY-MM-DD')
            });
            console.log(project_id)
            // scope.selected.project_completed_date = moment().format('YYYY-MM-DD')
        },
        
        // tagComplete: function (status) {
        //     var scope = this

        //     if (status == 'complete'){
        //         scope.PUT('api/projects/' + scope.selected.id + '/complete').then(function (res) {
        //             if (res.success) {
        //                 alert(res.message)
        //                 scope.selected = null
        //                 scope.toggleModal(false)
        //                 scope.getProjects();
        //             } else {
        //                 alert(res.message);
        //             }
        //         });
        //     }else{
        //         scope.PUT('api/projects/' + scope.selected.id + '/uncomplete').then(function (res) {
        //             if (res.success) {
        //                 alert(res.message)
        //                 scope.selected = null
        //                 scope.toggleModal(false)
        //                 scope.getProjects();
        //             } else {
        //                 alert(res.message);
        //             }
        //         });
        //     }   
        // },

        toggleSelectedItem: function(id) {
            var scope = this
            
            var index = scope.selected_ids.indexOf(id)
            if (index > -1) {
                scope.selected_ids.splice(index, 1);
            } else {
                scope.selected_ids.push(id)
            }
        },
        viewTasks: function (row) {
            this.$router.push({ path: '/projects/' + row.id + '/todos' })
        },
        save: function () {
            var scope = this

            scope.selected.user_details = scope.selected_users

            if (scope.selected.id !== null) {
                scope.PUT('api/projects/' + scope.selected.id, scope.selected).then(function (res) {
                    if (res.success) {
                        alert(res.message)
                        scope.selected = null
                        scope.toggleModal(false)
                        scope.getProjects();
                    } else {
                        alert(res.message);
                    }
                });
            } else {
                scope.POST('api/projects', scope.selected).then(function (res) {
                    if (res.success) {
                        alert(res.message)
                        scope.selected.title = '';
                        scope.selected.description = '';
                        scope.toggleModal(false)
                        scope.getProjects();
                    } else {
                        alert(res.message);
                    }
                });
            }
            
        },
         bulkSave: function () {
            var scope = this

            if (scope.selected_ids.length < 1) {
                return;
            }

            scope.selected.milestone_id = (scope.selected_milestone == null) ? null : scope.selected_milestone.id

            scope.PUT('api/projects', {fields: scope.selected, ids: scope.selected_ids}).then(function (res) {
                if (res.success) {
                    alert(res.message)
                    scope.selected = {
                        id: null,
                        title: '',
                        description: '',
                        is_active: 0,
                        sprints_yes: 0,
                        milestones_yes: 0,
                        ideas_yes: 0,
                        retrospectives_yes: 0,
                        timesheet_yes: 0,
                        chat_yes: 0,
                    }
                            
                    scope.toggleBulkEditModal(false)
                    scope.selected_ids = []
                    scope.getProjects();
                } else {
                    alert(res.message);
                }
            });
        },
        deleteBulk: function()
        {
            var scope = this

            if (scope.selected_ids.length < 1) {
                return;
            }

            scope.DELETE('api/projects', {
                ids: scope.selected_ids
            }).then(function (response) {
                console.log(response)
                if (!response.success) {
                    alert(response.message)
                }

                alert(response.message)
                scope.selected_ids = []
                scope.getProjects();
                
            });
        },
        getProjects: function () {
            var scope = this
            scope.GET('api/projects?keyword=' + scope.filter.keyword + '&page=' + scope.filter.page + '&take=' + scope.filter.take).then(function (res) {
               scope.projects = res.rows

                if (res.rows.length < 1 )  {
                    scope.filter.enable_paginate = false
                }

                scope.filter.is_loading = false

            });
        },
        getUsers: function () {
            var scope = this
            scope.GET('api/users?keyword=' + scope.filter.keyword).then(function (res) {
                scope.users = res.rows

                res.rows.forEach(function (data) {
                    
                    scope.options_users.push({
                        id: data.id,
                        text: data.firstname
                    })
                
                })

            });
        },
        getModule: function (module) {
            var scope = this
            scope.GET('api/modules/find?key=' + module.name ).then(function (res) {
                scope.module = res.data
                console.log('modullleee')
                console.log(res.data)
                scope.module_loaded = true
            });
        },
        searchTimeOut() {  
            var scope = this
            scope.filter.is_loading = true
            if (scope.filter.timeout) {
                clearTimeout(scope.filter.timeout);
                scope.filter.timeout = null;
            }
            scope.filter.timeout = setTimeout(() => {
               scope.getProjects();
            }, 300);
        },
        sortBoardTasks: function (board,evt) {
            var scope = this

            if (evt.added) {
                scope.$store.dispatch('moveBoardTasks', {board_id: board.id, board: board, task: evt.added.element, sort_index: evt.added.newIndex})
            } else if (evt.moved) {
                console.log(evt)
                scope.$store.dispatch('sortBoardTasks', {board_id: board.id, board: board, task: evt.moved.element, index: evt.moved.newIndex, old_index: evt.moved.oldIndex})
            }
        },
        sortModuleBoards: function () {
            var scope = this
            scope.$store.dispatch('moveModuleBoards', {module_id: scope.module.id, boards: scope.boards})
        }
    },
    created() {

    },
    mounted() {
        var scope = this
        scope.searchTimeOut()
        // scope.VALIDATE_MODULE_ROLES('milestones');

        window.onscroll = function() {
            const difference = document.documentElement.scrollHeight - window.innerHeight;
            const scrollposition = document.documentElement.scrollTop; 
            if (scope.filter.is_loading == false && scope.filter.enable_paginate && difference - scrollposition <= 2) {
                scope.filter.page += 1
                scope.searchTimeOut()
            }   
        }
        var module = scope.module

        scope.getProjects();
        scope.getUsers()
        // scope.VALIDATE_MODULE_ROLES('milestones');
        scope.getModule(module);

        scope.$store.dispatch('fetchProjects')
        scope.$store.dispatch('fetchBoards')

        scope.$store.dispatch('changeHeader','Projects')
    }
}
</script>

