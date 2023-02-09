<template>
    <div>
        <div id="projects-page" v-if="module_loaded">

            <div class="page-actions">
                <div class="page-actions-inner">
                    <button v-if="CAN_WRITE('Projects')" @click="toggleModal(true)" class="bp-bordered-boxed"> <span class="ti-plus"></span></button>
                    <button @click="toggleFilter()" class="bp-bordered-boxed"> <span class="ti-filter"></span></button>
                    &nbsp;&nbsp;&nbsp;

                    <button @click="switchView('table_view')" class="bp-bordered-boxed" :class="{'active' : view == 'table_view' }"> <span class="ti-view-list-alt"></span></button>
                    <button @click="switchView('kanban_view')" class="bp-bordered-boxed" :class="{'active' : view == 'kanban_view' }"> <span class="ti-layout-grid3"></span></button>

                    &nbsp;&nbsp;&nbsp;
                    <button @click="toggleBulkEditModal(true)" class="bp-bordered-boxed" :class="{'disabled' : (selected_ids.length < 1) }"> <span class="ti-pencil"></span></button>
                    <button @click="deleteBulk()" class="bp-bordered-boxed" :class="{'disabled' : (selected_ids.length < 1) }"> <span class="ti-trash"></span></button>
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

            
            <div v-if="view == 'table_view'" class="table-view">
                <div class="datatable">
                        <div class="tr header">
                            <div style="max-width:35px;" class="th" ></div>
                            <div class="th">Title</div>
                            <div class="th">Description</div>
                            <div class="th">Is Active</div>
                            <div class="th">Sprint</div>
                            <div class="th">Milestones</div>
                            <div class="th">Idea</div>
                            <div class="th">Retrospectives</div>
                            <div class="th">Timesheet</div>
                            <div class="th">Chat</div>
                            <div class="th">Project Start Date</div>
                            <div class="th">Project Due Date</div>
                            <div class="th">Completed Date</div>

                            <div class="th" style="max-width:100px;">Action</div>
                        </div>
                        
                        <div v-for="row in items" :key="row.id" class="tr">
                            <div style="max-width:35px;" class="td"><input @change="toggleSelectedItem(row.id)" type="checkbox"></div>
                            <div class="td">{{ row.title }}</div>
                            <div class="td">{{ row.description }}</div>
                            <div class="td" v-if="row.is_active === 1">Yes</div>
                            <div class="td" v-else>No</div>
                            <div class="td" v-if="row.sprints_yes === 1">Yes</div>
                            <div class="td" v-else>No</div>
                            <div class="td" v-if="row.milestones_yes === 1">Yes</div>
                            <div class="td" v-else>No</div>
                            <div class="td" v-if="row.ideas_yes === 1">Yes</div>
                            <div class="td" v-else>No</div>
                            <div class="td" v-if="row.retrospectives_yes === 1">Yes</div>
                            <div class="td" v-else>No</div>
                            <div class="td" v-if="row.timesheet_yes === 1">Yes</div>
                            <div class="td" v-else>No</div>
                            <div class="td" v-if="row.chat_yes === 1">Yes</div>
                            <div class="td" v-else>No</div>
                            <div class="td">{{ row.project_start_date }}</div>
                            <div class="td">{{ row.project_due_date }}</div>
                            <div class="td">{{ row.project_completed_date }}</div>
                            <div class="td" style="max-width:180px;">
                                <button @click="viewTasks(row)" style="cursor:pointer; margin:0px; background:#2a3042; color:#fff; border:none; font-size:12px; width:auto; padding:5px; display:inline-block; margin-right:4px;">Tasks</button>
                                <button v-if="CAN_EDIT('Projects')"  @click="toggleModal(true,row)" style="cursor:pointer; margin:0px; background:#2a3042; color:#fff; border:none; font-size:12px; width:auto; padding:5px; display:inline-block; margin-right:4px;">Edit</button>
                                <button v-if="CAN_DELETE('Projects')" style="cursor:pointer; margin:0px; background:#f73830; color:#fff; border:none; font-size:12px; width:auto; padding:5px; display:inline-block; margin-right:4px;">Delete</button>
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
                                <input v-model="selected.title" type="text" class="form-control form-control-lg" required="" id="email-address" placeholder="Enter project title...">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-label-group">
                                <label class="form-label" for="email-address">Description</label>
                            </div>
                            <div class="form-control-wrap">
                                <textarea rows="4" v-model="selected.description" type="text" class="form-control form-control-lg" required="" id="firstname" placeholder="Enter project description..."></textarea>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="form-label-group">
                                <label class="form-label" for="email-address">Users</label>
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
                        </div>

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

                        <div class="form-group">
                            <div class="form-label-group">
                                <label class="form-label" for="email-address">Duration</label>
                            </div>
                            <div class="form-control-wrap">
                                <input v-model="selected.duration" type="text" class="form-control form-control-lg" required="" id="duration">
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="form-label-group">
                                <label class="form-label" for="email-address">Project Start Date</label>
                            </div>
                            <div class="form-control-wrap">
                                <input v-model="selected.project_start_date" type="date" class="form-control form-control-lg" required="" id="email-address">
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="form-label-group">
                                <label class="form-label" for="email-address">Project Due Date</label>
                                <span v-if="selected.id">
                                    <button @click="recalculate(selected.id)" type="button"  class="bp-btn bp-btn-gray">Recalculate</button>
                                </span>
                            </div>
                            <div class="form-control-wrap">
                                <input v-model="selected.project_due_date" type="date" class="form-control form-control-lg" required="" id="email-address">
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="form-label-group">
                                <label class="form-label" for="email-address">Project Completed Date</label>
                            </div>
                            <div class="form-control-wrap">
                                <input v-model="selected.project_completed_date" type="date" class="form-control form-control-lg" required="" id="email-address">
                            </div>
                        </div>
            
                        <div class="bp-modal-form-actions">
                            <button type="submit" class="bp-btn bp-btn-blue">Save</button>
                            <button @click="toggleModal(false)" type="button"  class="bp-btn bp-btn-gray">Cancel</button>
                            <button @click="tagComplete()" type="button"  class="bp-btn bp-btn-gray">Mark Complete</button>
                            <!-- <button v-if="selected.id != null && selected.project_completed_date == null" @click="tagComplete('complete')" type="button"  class="bp-btn bp-btn-gray">Mark Complete</button>
                            <button v-if="selected.id != null && selected.project_completed_date != null" @click="tagComplete('uncomplete')" type="button"  class="bp-btn bp-btn-gray">Set Uncomplete</button> -->
                        </div>
                    </form>
                </div>
            </div>
        </div>



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
            selected: {
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
                duration: 60,
                project_start_date: '',
                project_due_date: '',
                project_completed_date: '',
            },
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

        }
    },
    components: {

    },
    computed: {
        items: function () {
            var scope = this

            return scope.projects.filter(function(item){
                var keywords = scope.filter.keywords
                return ( 
                    (keywords.length < 1 )||
                    keywords.some(el => item.title.toLowerCase().includes(el)) || 
                    ( item.description && keywords.some(el => item.description.toLowerCase().includes(el))) ||  
                    (item.project_start_date && keywords.some(el => item.project_start_date.toLowerCase().includes(el))) || 
                    (item.project_due_date && keywords.some(el => item.project_due_date.toLowerCase().includes(el)))
                ) 
            });
            // return scope.projects.filter(item => (item.title.toLowerCase().includes(scope.filter.keyword) ));
        },
        boards: function() {
            let scope = this
            return scope.$store.getters.getModuleBoards(scope.module.id)
        }
    },
    methods: {
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
        toggleModal: function (is_open,row = null) {
            var scope = this

            scope.is_modal_open = is_open
            
            if (row != null) {
                scope.selected = row

                if (row.project_users!= null){
                    row.project_users.forEach(function (data) {
                    
                        scope.selected_users.push({
                            id: data.user.id,
                            text: data.user.firstname
                        })
                     })
                }

            } else {
                scope.selected = {
                    id: null,
                    title: '',
                    description: '',
                    duration: 60,
                    project_start_date: moment().format('YYYY-MM-DD'),
                    project_due_date: moment().add(60, 'days').format('YYYY-MM-DD')
                }

                scope.selected_users = []
            }
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

