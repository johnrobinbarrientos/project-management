<template>
    <div>
        <div id="sprints-page" v-if="module_loaded">
            <div class="bp-actions">
                <div class="page-actions-inner">
                    <button v-if="CAN_WRITE('Sprints')" @click="toggleModal(true)" class="bp-bordered-boxed"> <span class="ti-plus"></span></button>
                    <button @click="toggleFilter()" class="bp-bordered-boxed"> <span class="ti-filter"></span></button>
                    &nbsp;

                    <button @click="switchView('table_view')" class="bp-bordered-boxed" :class="{'active' : view == 'table_view' }"> <span class="ti-view-list-alt"></span></button>
                    <button @click="switchView('kanban_view')" class="bp-bordered-boxed" :class="{'active' : view == 'kanban_view' }"> <span class="ti-layout-grid3"></span></button>
                    &nbsp;

                    <button @click="toggleBulkEditModal(true)" class="bp-bordered-boxed" :class="{'disabled' : (selected_ids.length < 1) }"> <span class="ti-pencil"></span></button>
                    <button @click="toggleDuplicateModal(true)" class="bp-bordered-boxed" :class="{'disabled' : (selected_ids.length < 1) }"> <span class="ti-layers"></span></button>
                    <button @click="deleteBulk()" class="bp-bordered-boxed" :class="{'disabled' : (selected_ids.length < 1) }"> <span class="ti-trash"></span></button>
                    &nbsp;
                    <div class="mini-column-filter-container">
                        <button @click="toggleSettings()" class="bp-bordered-boxed"> <i class="fa-solid fa-gear"></i></button>
                        <div class="mini-column-filter-inner" v-bind:class="{'open' : is_settings_open }">
                            <perfect-scrollbar>
                                <div class="col" v-for="c,index in columns" :key="'cfilter-' + index" style="">
                                    <input @change="TOGGLE_COLUMN('sprints',c)" type="checkbox" :checked="!c.hidden">
                                    <label>{{ c.label }}</label>
                                </div>
                            </perfect-scrollbar>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="show_filter" class="page-filter">
                <input v-model="filter.keyword" type="text">
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

                        <div v-for="row in items" :key="row.id" class="tr">
                            <template v-if="!row.edit">
                                    <div class="td" style="max-width: 40px;">
                                        <input @change="toggleSelectedItem(row.id)" type="checkbox">
                                    </div>
                                    <div v-if="!IS_COLUMN_HIDDEN('sprints','title')" class="td" @dblclick="inlineEditRow(row)">{{ row.title }}</div>
                                    <div v-if="!IS_COLUMN_HIDDEN('sprints','sprint_start_date')" class="td" @dblclick="inlineEditRow(row)">{{ row.sprint_start_date }}</div>
                                    <div v-if="!IS_COLUMN_HIDDEN('sprints','sprint_due_date')" class="td" @dblclick="inlineEditRow(row)">{{ row.sprint_due_date }}</div>
                                    <div v-if="!IS_COLUMN_HIDDEN('sprints','project.title')" class="td" @dblclick="inlineEditRow(row)">
                                        <span  v-if="row.project != null">{{ row.project.title }}</span>
                                        <span  v-else>N/A</span>
                                    </div>
                                    <div v-if="!IS_COLUMN_HIDDEN('sprints','sprint_duration')"  class="td" @dblclick="inlineEditRow(row)">{{ row.sprint_duration }}</div>
                                    <div v-if="!IS_COLUMN_HIDDEN('sprints','total_task_duration')"  class="td">
                                        <span v-if="row.total_task_duration > row.sprint_duration" style="color: red; font-weight: bold;">{{ row.total_task_duration }} - days</span>
                                        <span  v-else>{{ row.total_task_duration }} - days</span>
                                    </div>
                                    <div v-if="!IS_COLUMN_HIDDEN('sprints','sprint_completed_date')" class="td">{{ row.sprint_completed_date }}</div>

                                    <div style="width:180px; text-align:right;" class="td">
                                        <button v-if="CAN_EDIT('Sprints')" @click="toggleModal(true,row)" class="bp-btn bp-btn-blue">Edit</button>
                                        <button v-if="CAN_DELETE('Sprints')" @click="toggleModal(true)" class="bp-btn bp-btn-red">Delete</button>
                                    </div>
                            </template>
                            <template v-else>
                            
                                <div class="td" style="max-width: 40px;"></div>
                                <div class="td"><input v-model="row.title" type="text" placeholder="Enter title here.."></div>
                                <div class="td"><input v-model="row.sprint_start_date" type="date"></div>
                                <div class="td"><input v-model="row.sprint_due_date" type="date"></div>
                                <div class="td">
                                    <div class="form-control-wrap">
                                        <multiselect  v-model="row.project" 
                                        :options="options_project" 
                                        track-title="id" 
                                        label="title" 
                                        :multiple="false"
                                        :taggable="true"
                                        @tag="createNewProject" 
                                        >
                                        </multiselect>
                                    </div>
                                </div>
                        
                                <div class="td"><input v-model="row.sprint_duration" type="text"></div>
                                <div class="td"></div>
                                <div class="td"><input v-model="row.milestone_completed_date" type="date"></div>
                                <div class="td actions" style="max-width:100px; text-align:right;">
                                    <button @click="cancelRow(row)" class="bp-btn bp-btn-red">Cancel</button>
                                    <button @click="saveRow(row)" class="bp-btn bp-btn-blue">Save</button>
                                </div>
                            
                            </template>
                        </div>

                    </div>
    
                
                </div>

                <div v-if="view == 'kanban_view'">
        
                    <template v-if="project != null">
                        <div class="kanban-view">
                            <div class="kanban-view-inner">
                                <draggable class="kanban-view-row  dragArea list-group w-full"  :group="'project-boards'" :list="boards"  @change="sortModuleBoards()">
                                    <div class="kanban-row"  v-for="board,index in getProjectBoards(project.id)" :key="'board-' + index">
                                        <div class="kanban-row-title">{{ board.title }}</div>
                                        <draggable class="kanban-view-col dragArea list-group w-full" :group="'element'" :list="board.sprints" @change="sortBoardTasks(board, $event,project)">
                                            <div class="kanban-col-title list-group-item" v-for="element in board.sprints" :key="element.id">
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
                                                <div class="kanban-col-title list-group-item" v-for="element in board.sprints" :key="element.id">
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
                                        <label class="form-label" for="email-address">Sprint Start Date</label>
                                    </div>
                                    <div class="form-control-wrap">
                                        <input v-model="selected.sprint_start_date" type="date" class="form-control form-control-lg" required="" id="email-address">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="form-label-group">
                                        <label class="form-label" for="email-address">Sprint Due Date</label>
                                    </div>
                                    <div class="form-control-wrap">
                                        <input v-model="selected.sprint_due_date" type="date" class="form-control form-control-lg" required="" id="email-address">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="form-label-group">
                                        <label class="form-label" for="email-address">Project</label>
                                    </div>
                                    <div class="form-control-wrap">
                                        <multiselect  v-model="selected_project" 
                                        :options="options_project" 
                                        track-by="id" 
                                        label="title" 
                                        :allow-empty="true" 
                                        deselect-label="Unselect" 
                                        selectLabel="Select"
                                        :searchable="true"
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


        <div :class="{'open' : is_duplicate_modal_open}" class="bp-modal">
            <div class="bp-modal-wrap">
                <div @click="toggleDuplicateModal(false)" class="bp-modal-overlay"></div>
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
                                <button type="button" @click="duplicate()" class="bp-btn bp-btn-blue">Save</button>
                                <button @click="toggleDuplicateModal(false)" type="button"  class="bp-btn bp-btn-gray">Cancel</button>
                            </div>
                        </form>
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
                            <div class="form-group">
                                <div class="form-label-group">
                                    <label class="form-label" for="email-address">Project</label>
                                </div>
                                <div class="form-control-wrap">
                                    <multiselect  @select="onProjectChange($event,selected.id)" v-model="selected_project" 
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
                            <div class="form-label-group">
                                <label class="form-label" for="email-address">Title</label>
                            </div>
                            <div class="form-control-wrap">
                                <input v-model="selected.title" type="text" class="form-control form-control-lg" required="" id="email-address" placeholder="Enter title...">
                            </div>
                            
                            <div class="form-group">
                                <div class="form-label-group">
                                    <label class="form-label" for="email-address">Description</label>
                                </div>
                                <div class="form-control-wrap">
                                    <textarea rows="4" v-model="selected.description" type="text" class="form-control form-control-lg" required="" id="firstname" placeholder="Enter description..."></textarea>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="form-label-group">
                                    <label class="form-label" for="email-address">Sprint Duration</label>
                                </div>
                                <div class="form-control-wrap">
                                    <input v-model="selected.sprint_duration" type="number" step="1" class="form-control form-control-lg" required="" id="email-address">
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="form-label-group">
                                    <label class="form-label" for="email-address">Sprint Start Date</label>
                                </div>
                                <div class="form-control-wrap">
                                    <input v-model="selected.sprint_start_date" type="date" class="form-control form-control-lg" required="" id="email-address">
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="form-label-group">
                                    <label class="form-label" for="email-address">Sprint Due Date</label>
                                </div>
                                <div class="form-control-wrap">
                                    <input v-model="selected.sprint_due_date" type="date" class="form-control form-control-lg" required="" id="email-address">
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="form-label-group">
                                    <label class="form-label" for="email-address">Depends On</label>
                                </div>
                                <div class="form-control-wrap">
                                    <multiselect  v-model="selected_depends_on" 
                                    :options="options_depends_on" 
                                    track-by="id" 
                                    label="text" 
                                    :allow-empty="true" 
                                    deselect-label="Deselect" 
                                    selectLabel="Select"
                                    :searchable="true"
                                    >
                                    </multiselect>
                                </div>
                            </div>
                        </div> 
                    </form> 
                    </div>              
                    <div class="bp-modal-footer bp-modal-form-actions">
                        <button @click="save()"  type="button" class="bp-btn bp-btn-blue">Save</button>
                        <button @click="toggleModal(false)" type="button"  class="bp-btn bp-btn-gray">Cancel</button>
                        <button v-if="selected.id != null && selected.sprint_completed_date == null" @click="tagComplete('complete')" type="button"  class="bp-btn bp-btn-gray">Set Complete</button>
                        <button v-if="selected.id != null && selected.sprint_completed_date != null" @click="tagComplete('uncomplete')" type="button"  class="bp-btn bp-btn-gray">Set Uncomplete</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</template>


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

<script>
import moment from 'moment'

var attributes = { 
    id: null, title: '', description: '', sprint_start_date: '', sprint_due_date: '', sprint_completed_date: '', milestone_id: null, project_id: null,module_id: null,
}

export default {
    props: ['user_id'],
    data: function () {
        return {
            project_slug: null,
            project_id: null,
            view: 'table_view',
            show_filter: false,
            is_modal_open: false,
            sprints: [],
            selected: {
                id: null,
                project_id: null,
                title: '',
                description: '',
                sprint_start_date: '',
                sprint_due_date: '',
                sprint_completed_date: '',
                sprint_duration: 0
            },
            filter: {
                keyword: '',
                timeout: null,
            },
            selected_project: null,
            options_project: [],

            selected_depends_on: null,
            options_depends_on: [],

            module: {
                id: 5,
                name: 'Sprints'
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
                    column: 'sprint_start_date',
                    label: 'Start Date',
                    filter: true,
                    hidden: false
                },
                {
                    column: 'sprint_due_date',
                    label: 'Due Date',
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
                    column: 'sprint_duration',
                    label: 'Duration',
                    filter: true,
                    hidden: false
                },
                {
                    column: 'total_task_duration',
                    label: 'Task Time',
                    filter: true,
                    hidden: false
                },
                {
                    column: 'sprint_completed_date',
                    label: 'Completed Date',
                    filter: true,
                    hidden: false
                },
            ],
            selected_ids: [],
            is_bulk_edit_modal_open: false,
            selected: JSON.parse(JSON.stringify(attributes)),

            is_duplicate_modal_open:false,


        }
    },
    components: {
    },
    computed: {
        items: function () {
            var scope = this
            var items =  scope.sprints.filter(item => (item.title.toLowerCase().includes(scope.filter.keyword) ));

            var sortKey = scope.sort.key
            var sortDirection = scope.sort.direction

            return scope.SORT_OBJECT(items,sortKey,sortDirection)
        },
        projects: function () {
            let scope =  this
            return scope.$store.getters.getProjects
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
        deleteBulk: function()
        {
            var scope = this

            if (scope.selected_ids.length < 1) {
                return;
            }
            
            scope.DELETE('/api/sprints', {
                ids: scope.selected_ids
            }).then(function (response) {
                if (!response.success) {
                    alert(response.message)
                }

                alert(response.message)
                scope.selected_ids = []
                scope.getSprint();
                
            });
        },
        duplicate: function() {
            var scope = this
            var data = {project_id: scope.selected_project, sprint_ids: scope.selected_ids}

            scope.POST('/api/sprints/duplicate',data).then(function (res) {
                if (res.success) {
                    alert(res.message)
                    res.rows.forEach(function (data) {
                        scope.sprints.push(data)
                    })
                    scope.toggleDuplicateModal(false);
                } else {
                    alert(res.message)
                }
            });   
        },
        toggleDuplicateModal: function (is_open) {
            var scope = this

            scope.is_duplicate_modal_open = is_open
        },
        bulkSave: function () {
            var scope = this

            if (scope.selected_ids.length < 1) {
                return;
            }

            scope.selected.project_id = (scope.selected_project == null) ? null : scope.selected_project.id

            scope.PUT('/api/sprints', {fields: scope.selected, ids: scope.selected_ids}).then(function (res) {
                if (res.success) {
                    alert(res.message)
                    scope.selected = {
                        id: null,
                        title: '',
                        description: '',
                        sprint_start_date: '',
                        sprint_due_date: '',
                        milestone_id: null,
                        project_id: null,
                    }
                    
                    scope.toggleBulkEditModal(false)
                    scope.selected_ids = []
                    scope.getSprint();
                } else {
                    alert(res.message);
                }
            });
        },
        toggleBulkEditModal: function (is_open) {
            var scope = this

            if (scope.selected_ids.length < 1) {
                return;
            }

            scope.is_bulk_edit_modal_open = is_open
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
        saveRow: function (row) {
        var scope = this

            row.edit = false

            scope.PUT('/api/sprints/' + row.id, row).then(function (res) {
                if (res.success) {
                    alert(res.message)
                    
                } else {
                    alert(res.message);
                }
            });
        },
        cancelRow: function (row) {
            row.edit = false
        },
        inlineEditRow: function (row) {
            row.edit = true
        },
        getProjectBoards: function (project_id) {
            var scope = this
            return scope.boards.filter(item => (item.project_id == project_id ));
        },
        onProjectChange(event, task_id) {
            var scope = this
            console.log(event.id)
            console.log('task id')
            console.log(task_id)

            if (task_id == null){
                scope.GET('api/projects/' + event.id).then(function (res) {
                    console.log('dasdsad')
                    console.log(res.data.project_start_date)
                    scope.selected.sprint_start_date = res.data.project_start_date
                    // scope.selected.task_due_date  =  moment(res.data.project_start_date).add(scope.selected.task_duration, 'days').format('YYYY-MM-DD')
                });
            }
        },
        toggleFilter: function () {
            this.show_filter = !this.show_filter
        },
        tagComplete: function (status) {
            var scope = this

            if (status == 'complete'){
                scope.PUT('api/sprints/' + scope.selected.id + '/complete').then(function (res) {
                    if (res.success) {
                        alert(res.message)
                        scope.selected = null
                        scope.toggleModal(false)
                        scope.getSprint();
                    } else {
                        alert(res.message);
                    }
                });
            }else{
                scope.PUT('api/sprints/' + scope.selected.id + '/uncomplete').then(function (res) {
                    if (res.success) {
                        alert(res.message)
                        scope.selected = null
                        scope.toggleModal(false)
                        scope.getSprint();
                    } else {
                        alert(res.message);
                    }
                });
            }

            
        },
        toggleModal: function (is_open,row = null) {
            var scope = this
            scope.is_modal_open = is_open

            if (row != null) {

                scope.selected.id = row.id
                scope.selected.title = row.title
                scope.selected.description = row.description
                scope.selected.sprint_start_date = row.sprint_start_date
                scope.selected.sprint_due_date = row.sprint_due_date
                scope.selected.sprint_duration = row.sprint_duration
                scope.selected.sprint_completed_date = row.sprint_completed_date


                scope.selected_project = 
                {
                    id: row.project.id,
                    text: row.project.title
                }

                if (row.sprint!= null){
                    scope.selected_depends_on = 
                    {
                        id: row.sprint.id,
                        text: row.sprint.title
                    }
                }

                scope.removeSelfMilestone(row.id)


            } else {
                scope.selected = {
                    id: null,
                    title: '',
                    project_id: null,
                    sprint_start_date: '',
                    sprint_due_date: '',
                    sprint_duration: 0,
                }

                // if (scope.options_project){

                //    scope.selected_project = (scope.options_project.length < 1) ? null : {
                //             id: scope.options_project[0].id,
                //             text: scope.options_project[0].text
                //         }

                // }

                if (scope.options_project){
                    scope.selected_project= (scope.options_project.length < 1) ? null : {
                        id: scope.options_project[0].id,
                        title: scope.options_project[0].title,
                    }
                    console.log('hooooyy')
                    console.log(scope.options_project[0].project_start_date)
                    scope.selected.sprint_start_date = moment(scope.options_project[0].project_start_date).format('YYYY-MM-DD')
                    // scope.selected.task_due_date = moment(scope.options_project[0].project_start_date).add(1, 'days').format('YYYY-MM-DD')

                }

                scope.selected_depends_on = null
            }

        },

        save: function () {
            var scope = this

            scope.selected.project_id = (scope.selected_project == null) ? null : scope.selected_project.id

            scope.selected.depends_id = (scope.selected_depends_on == null) ? null : scope.selected_depends_on.id

            if (scope.selected.id !== null) {
                scope.PUT('api/sprints/' + scope.selected.id, scope.selected).then(function (res) {
                    if (res.success) {
                        alert(res.message)
                        scope.selected = null
                        scope.toggleModal(false)
                        scope.getSprint();
                    } else {
                        alert(res.message);
                    }
                });
            } else {
                scope.POST('api/sprints', scope.selected).then(function (res) {
                    if (res.success) {
                        alert(res.message)
                        scope.selected.title = '';
                        scope.selected.description = '';
                        scope.toggleModal(false)
                        scope.getSprint();
                    } else {
                        alert(res.message);
                    }
                });
            }
            
        },

        switchView: function (view) {
            this.view = view
        },

        removeSelfMilestone: function (id) {
            var scope = this
            var index = 0

            scope.options_depends_on.forEach(function (data) {

                if (data.id == id){
                    scope.options_depends_on.splice(index, 1)
                }
                index ++
            })

        },
        
        getSprint: function () {
            var scope = this
            scope.options_depends_on = []

            scope.GET('api/sprints?keyword=' + scope.filter.keyword).then(function (res) {
                scope.sprints = res.rows

                console.log('spriintsss')
                console.log(scope.sprints)

                res.rows.forEach(function (data) {
                    scope.options_depends_on.push({
                        id: data.id,
                        text: data.title,
                    })
                })
            
            });
        },

        // getProjects: function () {
        //     var scope = this
        //     scope.GET('api/projects').then(function (res) {

        //         res.rows.forEach(function (data) {
        //             scope.options_project.push({
        //                 id: data.id,
        //                 text: data.title,
        //             })
        //         })

        //     });
        // },

        getModule: function (module) {
            var scope = this
            scope.GET('api/modules/find?key=' + module.name ).then(function (res) {
                scope.module = res.data
                console.log('modullleee')
                console.log(res.data)
                scope.module_loaded = true
            });
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

        searchTimeOut() {  
            var scope = this
            if (scope.filter.timeout) {
                clearTimeout(scope.filter.timeout);
                scope.filter.timeout = null;
            }
            scope.filter.timeout = setTimeout(() => {
               scope.getSprint();
            }, 300);
        }
    },
    created() {

    },
    mounted() {
        var scope = this
        scope.getSprint()
        // scope.getProjects()
        scope.project_slug = (scope.$route.params.project_slug) ? scope.$route.params.project_slug : null

        var module = scope.module

        scope.getModule(module);

        scope.$store.dispatch('fetchSprints')
        scope.$store.dispatch('fetchBoards')

        scope.$store.dispatch('changeHeader','Sprints')

        scope.$store.dispatch('fetchProjects')

        setTimeout(function(){
            scope.options_project = scope.projects
        },1000);
    }
}
</script>

