<template>
    <div>
        <div id="todos-page" v-if="module_loaded">
            <div class="bp-actions">
                <div class="bp-actions-inner">
                    <button @click="switchView('table_view')" class="bp-bordered-boxed" :class="{'active' : view == 'table_view' }"> <i class="fa-solid fa-table-list"></i></button>
                    <button @click="switchView('kanban_view')" class="bp-bordered-boxed" :class="{'active' : view == 'kanban_view' }"> <i class="fa-solid fa-table-cells-large"></i></button>
                    <button @click="switchView('gantt_view')" class="bp-bordered-boxed" :class="{'active' : view == 'gantt_view' }"> <i class="fa-solid fa-chart-gantt"></i></button>
                    &nbsp;

                    <button v-if="CAN_WRITE('Tasks')" @click="toggleModalNew()" class="bp-bordered-boxed"> <i class="fa-solid fa-plus"></i></button>
                    <button @click="toggleFilter()" class="bp-bordered-boxed"> <i class="fa-solid fa-filter"></i></button>
                    &nbsp;

                    <button @click="toggleBulkEditModal(true)" class="bp-bordered-boxed" :class="{'disabled' : (selected_ids.length < 1) }"> <i class="fa-solid fa-pencil"></i></button>
                    <button @click="toggleDuplicateTaskModal(true)" class="bp-bordered-boxed" :class="{'disabled' : (selected_ids.length < 1) }"> <i class="fa-regular fa-clone"></i></button>
                    <button @click="deleteBulk()" class="bp-bordered-boxed" :class="{'disabled' : (selected_ids.length < 1) }"> <i class="fa-solid fa-trash"></i></button>
                    &nbsp;

                    

                    <div class="mini-column-filter-container">
                        <button @click="toggleSettings()" class="bp-bordered-boxed"> <i class="fa-solid fa-gear"></i></button>
                        <div class="mini-column-filter-inner" v-bind:class="{'open' : is_settings_open }">
                            <perfect-scrollbar>
                                <div class="col" v-for="c,index in columns" :key="'cfilter-' + index" style="">
                                    <input @change="TOGGLE_COLUMN('tasks',c)" type="checkbox" :checked="!c.hidden">
                                    <label>{{ c.label }}</label>
                                </div>
                            </perfect-scrollbar>
                        </div>
                    </div>

                    <div v-if="!project_slug && view == 'kanban_view'" style="margin-left:10px; padding:20px 0px; ">
                        <select v-model="project_id" style="height:35px; padding:0px 10px;">
                            <option :value="null">All</option>
                            <option :value="project_item.id" v-for="project_item in projects" :key="'opt-project-' + project_item.id ">{{ project_item.title }}</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="bp-body">
                <div v-if="show_filter" class="page-filter" style="display:flex;">
                    <div style="max-width:320px;">
                        <input v-on:keyup.enter="search()" v-model="filter.keyword" type="text">

                        <div class="tag-keyword" v-for="k,index in filter.keywords" :key="'keyword-' + index">
                            {{ k }}
                            <i @click="removeSearchKeyword(k)" class="ti-close"></i>
                        </div>
                    </div>
                    <div>
                        <input v-model="filter.start_date" type="date">
                    </div>
                    <div>
                        <input v-model="filter.due_date" type="date">
                    </div>
                    <div>
                        <multiselect  
                            v-model="filter.project_ids"
                            :options="options_project" 
                            track-title="id" 
                            label="title" 
                            :multiple="true"
                            :taggable="true"
                            >
                        </multiselect>
                    </div>
                </div>

                <div v-if="view == 'table_view'" class="table-view">
                    <div class="bp-list">
                        <div style="background:#000; color:#fff !important;" class="tr">
                            <div class="td" style="max-width: 40px;">#</div>
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
                            
                            
                            <div style="width:220px;" class="td">
                    
                            </div>
                        </div>
                        <div class="tr" v-for="row in items" :key="'items-' + row.id">
                            <div class="td"><input @change="toggleSelectedItem(row.id)" type="checkbox"></div>
                            <div v-if="!IS_COLUMN_HIDDEN('tasks','title')" class="td" style="font-weight:600;">{{ row.title }}</div>
                            <div v-if="!IS_COLUMN_HIDDEN('tasks','description')" class="td">{{ row.description }}</div>
                            <div v-if="!IS_COLUMN_HIDDEN('tasks','task_start_date')" class="td"><i>{{ FORMAT_DATE(row.task_start_date) || 'Not Set' }}</i></div>
                            <div v-if="!IS_COLUMN_HIDDEN('tasks','task_due_date')" class="td"><i>{{ FORMAT_DATE(row.task_due_date) || 'Not Set' }}</i></div>
                            <div v-if="!IS_COLUMN_HIDDEN('tasks','task_completed_date')" class="td"><i>{{ FORMAT_DATE(row.task_completed_date) || 'Not Set' }}</i></div>
                            <div class="td"><span v-if="row.milestone">{{ row.milestone.title }}</span></div>
                            <div class="td"><span v-if="row.project">{{ row.project.title }}</span></div>
                            <div class="td">
                                <span v-if="row.task_users.length > 0">
                                    <span v-for="item in row.task_users" :key="item.id" class="bp-tag">
                                        {{ item.user.firstname }}
                                    </span>
                                </span>
                            </div>
                            <div class="td">
                                <template v-if="row.tags && row.tags.length > 0">
                                    <span class="bp-tag" :style="{'background' : tag.background, 'color' : tag.color}" v-for="tag in row.tags" :key="tag.id">
                                        {{ tag.title }}
                                    </span>
                                </template>
                            </div>
                            <div class="td"><span v-if="row.sprint">{{ row.sprint.title }}</span></div>
                            <div class="td">{{ row.task_duration }}</div>
                            <div style="width:130px; text-align:right;" class="td">
                                <button v-if="CAN_EDIT('Tasks')" @click="toggleModalNew(row)" class="bp-btn bp-btn-blue">Edit</button>
                                <button v-if="CAN_DELETE('Tasks')" @click="toggleModal(true)" class="bp-btn bp-btn-red">Delete</button>
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


                <div v-if="view == 'kanban_view'">
        
                    <template v-if="project != null">
                        <div class="kanban-view">
                                <div class="kanban-view-inner">
                                    <draggable class="kanban-view-row  dragArea list-group w-full"  :group="'project-boards'" :list="boards"  @change="sortModuleBoards()">
                                        <div class="kanban-row"  v-for="board,index in getProjectBoards(project.id)" :key="'board-' + index">
                                            <div class="kanban-row-title">{{ board.title }}</div>
                                            <draggable class="kanban-view-col dragArea list-group w-full" :group="'element'" :list="board.tasks" @change="sortBoardTasks(board, $event,project)">
                                                <div class="kanban-col-title list-group-item" v-for="element in board.tasks" :key="element.id">
                                                    {{ element.title }}
                                                </div>
                                            </draggable>
                                        </div>
                                    </draggable>

                                    <div class="kanban-new-board" @click="toggleAddBoardModal(true,project)">
                                        <div style="width:100%;">
                                            <span class="ti-plus"></span>
                                            <p>Add Board</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    </template>
                    <template v-else>
                        <div class="projects kanban-project-single" v-for="project_item in projects" :key="'kanban-project-' + project_item.id ">
                            <div class="title">{{ project_item.title }}</div>
                
                            <div class="kanban-view">
                                <div class="kanban-view-inner">
                                    <draggable class="kanban-view-row  dragArea list-group w-full"  :group="'boards-' + project_item.id" :list="boards"  @change="sortModuleBoards()">
                                        <div class="kanban-row"  v-for="board,index in getProjectBoards(project_item.id)" :key="'board-' + index">
                                            <div class="kanban-row-title">{{ board.title }}</div>
                                            <draggable class="kanban-view-col dragArea list-group w-full" :group="'element'" :list="board.tasks" @change="sortBoardTasks(board, $event,project_item)">
                                                <div class="kanban-col-title list-group-item" v-for="element in board.tasks" :key="element.id">
                                                    {{ element.title }}
                                                </div>
                                            </draggable>
                                        </div>
                                    </draggable>

                                    <div class="kanban-new-board" @click="toggleAddBoardModal(true,project_item)">
                                        <div style="width:100%;">
                                            <span class="ti-plus"></span>
                                            <p>Add Board</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>

                    
                </div>

                <div v-if="view == 'gantt_view'" class="table-view">
                    <Gantt :module_type="'tasks'"></Gantt> 
                </div>
            </div>

        </div>

        <FormTaskModal :formdata="selected"></FormTaskModal>

  
        </div>

        <div :class="{'open' : is_bulk_edit_modal_open}" class="bp-modal">
            <div class="bp-modal-wrap">
                <div @click="toggleBulkEditModal(false)" class="bp-modal-overlay"></div>
                <div class="bp-modal-content">
                    <div class="bp-modal-header">
                        <h4>Update</h4>
                    </div>
                    <div class="bp-modal-body">
                        <form  action="/" class="form-validate is-alter" autocomplete="off" novalidate="novalidate">
                            <div class="form-group">

                                <div class="form-group">
                                    <div class="form-label-group">
                                        <label class="form-label" for="email-address">Task Start Date</label>
                                    </div>
                                    <div class="form-control-wrap">
                                        <input v-model="selected.task_start_date" type="date" class="form-control form-control-lg" required="" id="email-address">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="form-label-group">
                                        <label class="form-label" for="email-address">Task Due Date</label>
                                    </div>
                                    <div class="form-control-wrap">
                                        <input v-model="selected.task_due_date" type="date" class="form-control form-control-lg" required="" id="email-address">
                                    </div>
                                </div>
                                

                                <div class="form-group">
                                    <div class="form-label-group">
                                        <label class="form-label" for="email-address">Milestone</label>
                                    </div>
                                    <div class="form-control-wrap">
                                        <multiselect  v-model="selected_milestone" 
                                        :options="options_milestone" 
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
                                    <label class="form-label" for="email-address">Parent Task</label>
                                </div>
                                <div class="form-control-wrap">
                                    <multiselect  v-model="selected_parent_task" 
                                    :options="options_parent_task" 
                                    track-by="id" 
                                    label="text" 
                                    :allow-empty="true" 
                                    deselect-label="Unselect" 
                                    selectLabel="Select"
                                    :searchable="true"
                                    >
                                    </multiselect>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="form-label-group">
                                    <label class="form-label" for="email-address">Department</label>
                                </div>
                                <div class="form-control-wrap">
                                    <multiselect  v-model="selected_department" 
                                    :options="options_department" 
                                    track-by="id" 
                                    label="text" 
                                    :allow-empty="true" 
                                    deselect-label="Unselect" 
                                    selectLabel="Select"
                                    :searchable="true"
                                    >
                                    </multiselect>
                                </div>
                            </div>


                            <div class="form-group">
                                <div class="form-label-group">
                                    <label class="form-label" for="email-address">Sprint</label>
                                </div>
                                <div class="form-control-wrap">
                                    <multiselect  v-model="selected_sprint" 
                                    :options="options_sprint" 
                                    track-by="id" 
                                    label="title" 
                                    :multiple="false"
                                    :taggable="true"
                                    @tag="createNewSprint" 
                                    >
                                    </multiselect>
                                </div>
                            </div>

                            </div>
                    
                        </form>
                    </div>
                    <div class="bp-modal-footer bp-modal-form-actions">
                        <button @click="bulkSave()" type="button" class="bp-btn bp-btn-blue">Save</button>
                        <button @click="toggleBulkEditModal(false)" type="button"  class="bp-btn bp-btn-gray">Cancel</button>
                    </div>
                </div>
                
            </div>
        </div>


        <div :class="{'open' : is_add_board_modal_open}" class="bp-modal">
            <div class="bp-modal-wrap">
                <div @click="toggleAddBoardModal(false)" class="bp-modal-overlay"></div>
                <div class="bp-modal-content">
                    <div style="padding-bottom:10px; border-bottom:1px solid #efefef; margin-bottom:20px;">
                        <h4>New Board</h4>
                    </div>
                    <div class="bp-modal-body">
                        <form v-on:submit.prevent="saveModuleBoard()" action="/" class="form-validate is-alter" autocomplete="off" novalidate="novalidate">
                            <div class="form-group">

                                <div class="form-group">
                                    <div class="form-label-group">
                                        <label class="form-label" for="email-address">Name</label>
                                    </div>
                                    <div class="form-control-wrap">
                                        <input v-model="board.title" type="text" class="form-control form-control-lg" required="">
                                    </div>
                                </div>

                            </div>
                    
                            <div class="bp-modal-form-actions">
                                <button type="submit" class="bp-btn bp-btn-blue">Save</button>
                                <button @click="toggleAddBoardModal(false)" type="button"  class="bp-btn bp-btn-gray">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div :class="{'open' : is_duplicate_task_modal_open}" class="bp-modal">
            <div class="bp-modal-wrap">
                <div @click="toggleDuplicateTaskModal(false)" class="bp-modal-overlay"></div>
                <div class="bp-modal-content">
                    <div style="padding-bottom:10px; border-bottom:1px solid #efefef; margin-bottom:20px;">
                        <h4>Duplicate Tasks</h4>
                    </div>
                    <div class="bp-modal-body">
                        <form action="/" class="form-validate is-alter" autocomplete="off" novalidate="novalidate">
                            <div class="form-group">
                                <div class="form-label-group">
                                    <label class="form-label" for="task-project">Project</label>
                                </div>
                                <div style="display:flex; justify-content:space-between;">
                                    <div style="width:calc(100% - 50px);">
                                        <div class="form-control-wrap">
                                            <multiselect v-model="selected_project" 
                                            :options="options_project" 
                                            track-by="id" 
                                            label="title" 
                                            :allow-empty="true" 
                                            deselect-label="Deselect" 
                                            selectLabel="Select"
                                            :searchable="true"
                                            >
                                            </multiselect>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    
                            <div class="bp-modal-form-actions">
                                <button type="button" @click="duplicateTasks()" class="bp-btn bp-btn-blue">Save</button>
                                <button @click="toggleDuplicateTaskModal(false)" type="button"  class="bp-btn bp-btn-gray">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
</template>

<style src="vue-multiselect/dist/vue-multiselect.css" scope>

</style>


<script>
import moment from 'moment'
import Gantt from './components/Gantt'
import FormTaskModal from './forms/FormTaskModal'
import { VueDraggableNext } from 'vue-draggable-next'

var attributes = { 
    id: null, title: '', description: '', task_start_date: '', task_due_date: '', task_completed_date: '', milestone_id: null, project_id: null,module_id: null, parent_task_id: null,
    dept_id: null, project: null, milestone: null, tags: [], task_duration: 0
}

export default {
    props: ['user_id'],
    data: function () {
        return {
            view: 'table_view',
            project_slug: null,
            project_id: null,
            show_filter: false,
            is_modal_open: false,
            is_bulk_edit_modal_open: false,
            is_add_board_modal_open: false,
            is_add_new_milestone_open:false,
            is_duplicate_task_modal_open:false,
            module_loaded: false,
            module: {
                id: null,
                name: 'Tasks'
            },
            tasks: [],
            selected: JSON.parse(JSON.stringify(attributes)),
            edited_row: JSON.parse(JSON.stringify(attributes)),
            new_row: JSON.parse(JSON.stringify(attributes)),
            milestone: {
                id: null,
                project_id: null,
                title: '',
                start_on: '',
                finished_on: '',
                depends_id: null
            },
            selected_ids: [],
            filter: {
                keyword: '',
                keywords: [],
                start_date: null,
                due_date: null,
                project_ids: [],
                timeout: null,
                page: 1, 
                take:40,
                enable_paginate: true,
                is_loading: true
            },

            selected_milestone: null,
            options_milestone: [],

            board: {
                id: null,
                title: '',
                project_id: null,
                module_id: null
            },

            clone: null,

            selected_parent_task: null,
            options_parent_task: [],

            selected_department: null,
            options_department: [],

            selected_sprint: null,
            options_sprint: [],

            selected_users: [],
            options_users: [],

            selected_tags: [],
            options_tags: [],

            selected_user_groups: [],
            options_user_groups: [],

            selected_project: null,
            options_project: [],
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
                    column: 'task_start_date',
                    label: 'Task Start Date',
                    filter: true,
                    hidden: false
                },
                {
                    column: 'task_due_date',
                    label: 'Task Due Date',
                    filter: true,
                    hidden: false
                },
                {
                    column: 'task_completed_date',
                    label: 'Complete Date',
                    filter: true,
                    hidden: false
                },
                {
                    column: 'milestone.title',
                    label: 'Milestone',
                    filter: true,
                    hidden: false
                },
                {
                    column: 'project.title',
                    label: 'Project',
                    filter: true,
                    hidden: false
                },
                {
                    column: 'task_users',
                    label: 'Assign',
                    filter: false,
                    hidden: false
                },
                {
                    column: 'tags',
                    label: 'Tags',
                    filter: false,
                    hidden: false
                },
                {
                    column: 'sprint.title',
                    label: 'Sprint',
                    filter: false,
                    hidden: false
                },
                {
                    column: 'task_duration',
                    label: 'Task Time',
                    filter: true,
                    hidden: false
                },
            ]
        }
    },
    components: {
        'Gantt': Gantt,
        'draggable': VueDraggableNext,
        FormTaskModal,
    },
    computed: {
        items: function () {
            var scope = this
            var items =  scope.tasks.filter(function(item){

                var data = item.title.toLowerCase().includes(scope.filter.keyword);
                var is_start_date_included = true
                var is_due_date_included = true
                var is_project_included = true

                if (scope.filter.start_date) {
                    var start_d1 = Date.parse(item.task_start_date);
                    var start_d2 = Date.parse(scope.filter.start_date);
                    

                    is_start_date_included = (start_d1 >= start_d2)
                }

                if (scope.filter.due_date) {
                    var due_d1 = Date.parse(item.task_due_date);
                    var due_d2 = Date.parse(scope.filter.due_date);
                    
                    console.log('item.task_due_date',due_d1)
                    console.log('scope.filter.due_date',due_d2)

                    is_due_date_included = (due_d1 <= due_d2)
                }

                var project_ids = scope.filter.project_ids.map(function(el) { return el.id; });
                
                if (project_ids.length > 0) {
                    is_project_included = (project_ids.includes(item.project_id)) ? true : false
                }

                var keywords = scope.filter.keywords
                
                return (
                    ( 
                        (keywords.length < 1 )||
                        keywords.some(el => item.title.toLowerCase().includes(el)) || 
                        (item.project && keywords.some(el => item.project.title.toLowerCase().includes(el))) ||
                        (item.milestone &&  keywords.some(el => item.milestone.title.toLowerCase().includes(el))) || 
                        (item.milestone && keywords.some(el => item.description.toLowerCase().includes(el))) || 
                            (item.task_start_date && keywords.some(el => item.task_start_date.toLowerCase().includes(el))) || 
                            (item.task_due_date && keywords.some(el => item.task_due_date.toLowerCase().includes(el)))
                    ) 
                    && (is_start_date_included && is_due_date_included) && is_project_included)
            });

            var sortKey = scope.sort.key
            var sortDirection = scope.sort.direction
           
            return items.sort((a, b) => {
                let compare = 0;

                var keys = sortKey.split('.')

                if (keys.length == 1) {
                    var A = a[sortKey]
                    var B = b[sortKey]
                } else {
                    var k1 = keys[0]
                    var k2 = keys[1]

                    var A = (a[k1]) ? a[k1][k2]: 'zzzzz'
                    var B = (b[k1]) ? b[k1][k2]: 'zzzzz'
                }
                
                if (sortDirection == 'asc') {
                    if (A > B) {
                        compare = 1;
                    } else if (B > A) {
                        compare = -1;
                    }
                } else {
                    if (A < B) {
                        compare = 1;
                    } else if (B < A) {
                        compare = -1;
                    }
                }
                
                return compare;
            });

           
        },
        boards: function() {
            let scope = this
                        console.log('hooot')
            console.log(scope.$store.getters.getModuleBoards(scope.module.id))
            return scope.$store.getters.getModuleBoards(scope.module.id)
        },
        projects: function () {
            let scope =  this
            return scope.$store.getters.getProjects
        },
        project: function () {
            let scope = this
            if (scope.project_slug) {
                return scope.$store.getters.getProjectBySlug(scope.project_slug)
            }

            return scope.$store.getters.getProjectByID(scope.project_id)
        },
        milestones: function () {
            let scope =  this
            return scope.$store.getters.getMilestones
        },
        sprints: function () {
            let scope =  this
            return scope.$store.getters.getSprints
        },
        tags: function () {
            let scope =  this
            return scope.$store.getters.getTags
        },
        departments: function () {
            let scope =  this
            return scope.$store.getters.getDepartments
        }
    },
    watch: {

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
        toggleModalNew: function (data = null) {
            var scope = this

            scope.selected = JSON.parse(JSON.stringify(attributes))

            if (data != null) {
                scope.selected = data
            }
            
            scope.MODAL_TOGGLE('form-task-modal')
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

        onDurationChange(){
            var scope = this
            scope.selected.task_due_date  =  moment(scope.selected.task_start_date).add(scope.selected.task_duration, 'days').format('YYYY-MM-DD')
        },

        selectGroup ({id}) {
            var scope = this

            scope.GET('api/user-groups/' + id).then(function (res) {
                if (res.rows.users.length > 0){
                
                    res.rows.users.forEach(function (usergroup_data) {
                        if (scope.selected_users.find(x => (x.id == usergroup_data.user.id))) {
                        }else{
                            scope.selected_users.push({
                                id: usergroup_data.user.id,
                                text: usergroup_data.user.firstname,
                            })
                        }
                    
                    })
                }
            });

        },
        removeGroup ({id}) {
            var scope = this
            scope.found = null
            scope.GET('api/user-groups/' + id).then(function (res) {
                if (res.rows.users.length > 0){
                    res.rows.users.forEach(function (usergroup_data) {
                        if (scope.selected_users.find(x => (x.id == usergroup_data.user.id))) {
                            scope.found = scope.selected_users.find(x => (x.id == usergroup_data.user.id))
                            
                        var index = scope.selected_users.findIndex(user => user.id === scope.found.id);
                        scope.selected_users.splice(index, 1)
                        }
                    })
                }
            });

        },
        taskCompleted: function (){
            var scope = this
            scope.selected.task_completed_date = moment().format('YYYY-MM-DD')
        },
        toggleSelectedItem: function(id) {
            var scope = this
            
            var index = scope.selected_ids.indexOf(id)
            if (index > -1) {
                scope.selected_ids.splice(index, 1);
            } else {
                scope.selected_ids.push(id)
            }
        },
        toggleBulkEditModal: function (is_open) {
            var scope = this

            if (scope.selected_ids.length < 1) {
                return;
            }

            scope.is_bulk_edit_modal_open = is_open
        },
        toggleAddBoardModal: function (is_open,project = null) {
            var scope = this
         
            scope.board.title = ''
            scope.board.project_id = (project != null) ? project.id : null 
            scope.is_add_board_modal_open = is_open
        },
        toggleDuplicateTaskModal: function (is_open,project = null) {
            var scope = this

            scope.is_duplicate_task_modal_open = is_open
        },
        toggleAddNewMilestone: function () {
            var scope = this

            scope.is_add_new_milestone_open = !scope.is_add_new_milestone_open
        },
        getProjectBoards: function (project_id) {
            var scope = this
            return scope.boards.filter(item => (item.project_id == project_id ));
        },
        deleteBulk: function()
        {
            var scope = this

            if (scope.selected_ids.length < 1) {
                return;
            }
            
            scope.DELETE('/api/tasks', {
                ids: scope.selected_ids
            }).then(function (response) {
                if (!response.success) {
                    alert(response.message)
                }

                alert(response.message)
                scope.selected_ids = []
                scope.getRows();
                
            });
        },
        inlineEditRow: function (row) {
            var scope = this
            row.edit = true
        },
        bulkSave: function () {
            var scope = this

            if (scope.selected_ids.length < 1) {
                return;
            }

            scope.selected.milestone_id = (scope.selected_milestone == null) ? null : scope.selected_milestone.id

            scope.PUT('/api/tasks', {fields: scope.selected, ids: scope.selected_ids}).then(function (res) {
                if (res.success) {
                    alert(res.message)
                    scope.selected = {
                        id: null,
                        title: '',
                        description: '',
                        task_start_date: '',
                        task_due_date: '',
                        task_completed_date: '',
                        milestone_id: null,
                        project_id: null,
                    }
                    
                    scope.toggleBulkEditModal(false)
                    scope.selected_ids = []
                    scope.getRows();
                } else {
                    alert(res.message);
                }
            });
        },
        saveModuleBoard: function () {
            var scope = this
            scope.board.module_id = scope.module.id 
            scope.POST('/api/modules/' + scope.module.id + '/boards',scope.board).then(function (res) {
                 if (res.success) {
                    alert(res.message)
                    scope.$store.dispatch('fetchBoards')
                    scope.toggleAddBoardModal(false)
                } else {
                    alert(res.message);
                }
            });
        },
        getRows: function () {
            var scope = this
            var query_project = (scope.project_slug) ? '&project_slug=' + scope.project_slug : ''

            scope.GET('/api/tasks?keyword=' + scope.filter.keyword + '' + query_project  + '&page=' + scope.filter.page + '&take=' + scope.filter.take).then(function (res) {
    
                // scope.tasks = res.rows

                res.rows.forEach(function (data) {
                    scope.options_parent_task.push({
                        id: data.id,
                        text: data.title,
                    })

                    var tags = JSON.parse(JSON.stringify(data.tags))
                    data.tags = []
                    tags.forEach(function (data2) {
                        data.tags.push(data2.tag)
                    });

                    data['edit'] = false
                    scope.tasks.push(data)
                })

                if (res.rows.length < 1 )  {
                    scope.filter.enable_paginate = false
                }

                scope.filter.is_loading = false

            });

        },

        getDepartment: function () {
            var scope = this
            scope.GET('/api/departments').then(function (res) {

                res.rows.forEach(function (data) {
                    scope.options_department.push({
                        id: data.id,
                        text: data.title,
                    })
                })

            });
        },

        switchView: function (view) {
            this.view = view
        },
        toggleFilter: function (show_filter) {
            this.show_filter = !this.show_filter
        },
        duplicateTasks: function() {
            var scope = this
            var data = {project_id: scope.selected_project, task_ids: scope.selected_ids}

            scope.POST('/api/tasks/duplicate',data).then(function (res) {
                if (res.success) {
                    alert(res.message)
                    res.rows.forEach(function (data) {
                        scope.tasks.push(data)
                    })
                    scope.toggleDuplicateTaskModal(false);
                } else {
                    alert(res.message)
                }
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
               scope.getRows();
            }, 600);
        },
        sortBoardTasks: function (board,evt,project) {
            var scope = this
            
            var cloned_tasks_id = 0; // this will be the indicator to make the ID of this class treated as new (insert new record)

            if (evt.added) {
                scope.clone = (evt.added.element.project_id != project.id) ? evt.added.element : null
                cloned_tasks_id = (scope.clone) ? evt.added.element.id : 0
                //console.log('ADD BOARD',project.title)
                //console.log('ADD BOARD',board.title)
            }

            if (evt.removed && scope.clone != null) {
                var new_task = JSON.parse(JSON.stringify(scope.clone))

                board.tasks.push(new_task)
                
                // scope.clone = null
                // scope.clone.project_id = project.id

                //console.log('REMOVE BOARD',project.title)
                //console.log('REMOVE BOARD',board.title)
            }
            
            /*
            if (evt.added && scope.clone) {
                console.log('sss')
            } else if (evt.removed && scope.clone) {
                console.log('process sorting and adding')
                scope.clone = null
            }

            return
            */

          

            if (evt.added) {
                scope.$store.dispatch('moveBoardTasks', { board_id: board.id, board: board, task: evt.added.element, cloned_tasks_id: cloned_tasks_id, sort_index: evt.added.newIndex})
                cloned_tasks_id = 0
            } else if (evt.removed && scope.clone ) {
                scope.$store.dispatch('moveBoardTasks', { board_id: board.id, board: board, task: evt.removed.element, cloned_tasks_id: cloned_tasks_id, sort_index: evt.removed.newIndex})
                scope.clone = null
                cloned_tasks_id = 0
            } else if (evt.moved) {
                scope.$store.dispatch('sortBoardTasks', {board_id: board.id, board: board, task: evt.moved.element, index: evt.moved.newIndex, old_index: evt.moved.oldIndex})
            }

        },
        sortModuleBoards: function () {
            var scope = this
            scope.$store.dispatch('moveModuleBoards', {module_id: scope.module.id, boards: scope.boards})
        },
        getModule: function (module) {
            var scope = this
            scope.GET('/api/modules/find?key=' + module.name ).then(function (res) {
                scope.module = res.data
                scope.module_loaded = true
            });
        },
        
    },
    created() {

    },
    mounted() {
        var scope = this
        var module = scope.module

        scope.project_slug = (scope.$route.params.project_slug) ? scope.$route.params.project_slug : null
        
        scope.searchTimeOut()
        // scope.getMilestone()
        // scope.getDepartment()
        // scope.getSprint()
        //scope.getUsers()
        //scope.getUserGroup()
        // scope.getTags()
        
        scope.getModule(module);

        scope.$store.dispatch('fetchTasks')
        scope.$store.dispatch('fetchBoards')
        scope.$store.dispatch('changeHeader','Tasks')
        scope.$store.dispatch('fetchProjects')
        scope.$store.dispatch('fetchSprints')
        scope.$store.dispatch('fetchTags')
        scope.$store.dispatch('fetchDepartments')
        scope.$store.dispatch('fetchMilestones')

        scope.column = scope.INITIALIZE_MODULE_COLUMN('tasks',scope.columns)

        window.onscroll = function() {
            const difference = document.documentElement.scrollHeight - window.innerHeight;
            const scrollposition = document.documentElement.scrollTop; 
            if (scope.filter.is_loading == false && scope.filter.enable_paginate && difference - scrollposition <= 2) {
                scope.filter.page += 1
                scope.searchTimeOut()
            }   
        }

        
        setTimeout(function(){
            scope.options_project = scope.projects
            scope.options_milestone = scope.milestones
            scope.options_sprint = scope.sprints
            scope.options_tags = scope.tags
        },1000);

    }
}
</script>

<style scoped>
.table-view { margin-bottom:50px; }
.badge-user { background-color: #e9ebee; border-color: #afb7c1; background: #50a5f1; color: #fff; padding-top: 4px; padding-bottom: 7px;padding-left: 4px;padding-right: 4px;border-radius: 100px;margin-right: 4px; }

.kanban-project-single { background:#fafafa;  margin-bottom:20px; }
.kanban-project-single .title { padding:5px 10px; background:#f1f1f1;  font-weight:600; }
.kanban-view { padding:10px; border:1px solid #efefef; }

.kanban-view { display:flex; }
.kanban-view-inner { display:flex; }
.kanban-view-row {display:flex; }
.kanban-row { min-height:200px;  border:1px solid #ccc; margin:0px 10px; flex:1; min-width:200px; }
.kanban-row-title { background:#424c68; padding:3px 10px; color:#fff; }
.kanban-col-title { padding:3px 10px;  border:1px solid #ccc; border-left:none; border-right:none; border-top:none; }
.kanban-new-board { cursor:pointer; background:#efefef; border:1px solid #ccc; display:flex; min-height:200px; align-items:center; text-align:center; margin:0px 10px; flex:1; min-width:200px; }


</style>

