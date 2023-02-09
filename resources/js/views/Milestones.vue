<template>
    <div>
        <div id="milestone-page">

            <div class="bp-actions">
                <div class="page-actions-inner">
                    <button v-if="CAN_WRITE('Milestones')" @click="toggleModalNew()" class="bp-bordered-boxed"> <span class="ti-plus"></span></button>
                    <button @click="toggleFilter()" class="bp-bordered-boxed"> <span class="ti-filter"></span></button>
                    &nbsp;

                    <button @click="switchView('table_view')" class="bp-bordered-boxed" :class="{'active' : view == 'table_view' }"> <span class="ti-view-list-alt"></span></button>
                    <button @click="switchView('kanban_view')" class="bp-bordered-boxed" :class="{'active' : view == 'kanban_view' }"> <span class="ti-layout-grid3"></span></button>
                    <button @click="switchView('gantt_view')" class="bp-bordered-boxed" :class="{'active' : view == 'gantt_view' }"> <span class="ti-bar-chart"></span></button>
                
                    &nbsp;&nbsp;&nbsp;
                    <button @click="toggleBulkEditModal(true)" class="bp-bordered-boxed" :class="{'disabled' : (selected_ids.length < 1) }"> <span class="ti-pencil"></span></button>
                    <button @click="toggleDuplicateModal(true)" class="bp-bordered-boxed" :class="{'disabled' : (selected_ids.length < 1) }"> <span class="ti-layers"></span></button>
                    <button @click="deleteBulk()" class="bp-bordered-boxed" :class="{'disabled' : (selected_ids.length < 1) }"> <span class="ti-trash"></span></button>

                    <div v-if="!project_slug && view == 'kanban_view'" style="margin-left:10px; padding:20px 0px; ">
                        <select v-model="project_id" style="height:35px; padding:0px 10px;">
                            <option :value="null">All</option>
                            <option :value="project_item.id" v-for="project_item in projects" :key="'opt-project-' + project_item.id ">{{ project_item.title }}</option>
                        </select>
                    </div>

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
                    <input v-on:keyup.enter="search()" v-model="filter.keyword" type="text">

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
                  
                        <div v-for="row in items" :key="row.id" class="tr">
                            <template v-if="!row.edit">
                                <div class="td" style="max-width: 40px;">
                                    <input @change="toggleSelectedItem(row.id)" type="checkbox">
                                </div>
                                <div v-if="!IS_COLUMN_HIDDEN('milestones','title')"  class="td" @dblclick="inlineEditRow(row)">{{ row.title }}</div>
                                <div v-if="!IS_COLUMN_HIDDEN('milestones','milestone_start_date')"  class="td" @dblclick="inlineEditRow(row)">{{ row.milestone_start_date }}</div>
                                <div v-if="!IS_COLUMN_HIDDEN('milestones','milestone_due_date')"  class="td" @dblclick="inlineEditRow(row)">{{ row.milestone_due_date }}</div>
                                <div v-if="!IS_COLUMN_HIDDEN('milestones','project.title')"  class="td" @dblclick="inlineEditRow(row)">{{ row.project.title }}</div>

                                <div v-if="!IS_COLUMN_HIDDEN('milestones','milestone.title')"  class="td" @dblclick="inlineEditRow(row)">
                                    <span v-if="!row.milestone">N/A</span>
                                    <span v-else>{{ row.milestone.title }}</span>
                                </div>
                            

                                <div v-if="!IS_COLUMN_HIDDEN('milestones','milestone_completed_date')"  class="td" @dblclick="inlineEditRow(row)">{{ row.milestone_completed_date }}</div>


                                <div class="td" style="max-width:140px; text-align:right;">
                                    <button v-if="CAN_EDIT('Milestones')" @click="toggleModalNew(row)" class="bp-btn bp-btn-blue">Edit</button>
                                    <button v-if="CAN_DELETE('Milestones')" @click="toggleModal(true)" class="bp-btn bp-btn-red">Delete</button>
                                </div>
                            </template>
                            <template v-else>
                               
                                <div class="td" style="max-width: 40px;"></div>
                                <div class="td"><input v-model="row.title" type="text" placeholder="Enter title here.."></div>
                                <div class="td"><input v-model="row.milestone_start_date" type="date"></div>
                                <div class="td"><input v-model="row.milestone_due_date" type="date"></div>
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
                                <div class="td">
                                    <div class="form-control-wrap">
                                        <multiselect  v-model="row.milestone" 
                                        :options="other_milestones" 
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
                                <div class="td"><input v-model="row.milestone_completed_date" type="date"></div>
                                <div class="td actions" style="max-width:100px; text-align:right;">
                                    <button @click="cancelRow(row)" class="bp-btn bp-btn-red">Cancel</button>
                                    <button @click="saveRow(row)" class="bp-btn bp-btn-blue">Save</button>
                                </div>
                               
                            </template>

                        </div>


                </div>

                <div v-if="view == 'kanban_view'">
        
                    <template v-if="project != null">
                        <div class="kanban-view">
                                <div class="kanban-view-inner">
                                    <draggable class="kanban-view-row  dragArea list-group w-full"  :group="'project-boards'" :list="boards"  @change="sortModuleBoards()">
                                        <div class="kanban-row"  v-for="board,index in getProjectBoards(project.id)" :key="'board-' + index">
                                            <div class="kanban-row-title">{{ board.title }}</div>
                                            <draggable class="kanban-view-col dragArea list-group w-full" :group="'element'" :list="board.milestones" @change="sortBoardTasks(board, $event,project)">
                                                <div class="kanban-col-title list-group-item" v-for="element in board.milestones" :key="element.id">
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
                                                <div class="kanban-col-title list-group-item" v-for="element in board.milestones" :key="element.id">
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

                <div v-if="view == 'gantt_view'" class="gantt-view">
                    <Gantt :module_type="'milestones'" :milestones="items"></Gantt>   
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
                                        <label class="form-label" for="email-address">Milestone Start Date</label>
                                    </div>
                                    <div class="form-control-wrap">
                                        <input v-model="selected.milestone_start_date" type="date" class="form-control form-control-lg" required="" id="email-address">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="form-label-group">
                                        <label class="form-label" for="email-address">Milestone Due Date</label>
                                    </div>
                                    <div class="form-control-wrap">
                                        <input v-model="selected.milestone_due_date" type="date" class="form-control form-control-lg" required="" id="email-address">
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
            <FormMilestoneModal :formdata="selected"></FormMilestoneModal>
        </div>
    </div>
</div>
</template>

<style scope>

.projects-page .table-view .datatable {
  border: 1px solid black;
  table-layout: fixed;
  width: 200px;
}

.projects-page .table-view .datatable .tr .td {
  border: 1px solid black;
  width: 100px;
  overflow: hidden;
}

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
import Gantt from './components/Gantt'
import moment from 'moment'
import FormMilestoneModal from './forms/FormMilestoneModal'
var attributes = { id: null, project_id: null, title: '', milestone_start_date: '', milestone_due_date: '', milestone_completed_date: '',  depends_id: null  }

export default {
    props: ['user_id'],
    data: function () {
        return {
            project_slug: null,
            project_id: null,
            view: 'table_view',
            show_filter: false,
            is_modal_open: false,
            // milestones: [],
            selected: JSON.parse(JSON.stringify(attributes)),
            filter: {
                keyword: '',
                timeout: null,
                keywords: []
            },

            module: {
                id: 4,
                name: 'Milestones'
            },

            selected_project: null,
            options_project: [],

            selected_depends_on: null,
            options_depends_on: [],

            milestone_duration: 0,

            milestones: [],
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
                    column: 'milestone_start_date',
                    label: 'Milestone Start Date',
                    filter: true,
                    hidden: false
                },
                {
                    column: 'milestone_due_date',
                    label: 'Task Due Date',
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
                    column: 'milestone.title',
                    label: 'Milestone',
                    filter: true,
                    hidden: false
                },
                
                {
                    column: 'milestone_completed_date',
                    label: 'Milestone Completed Date',
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
        'Gantt': Gantt,
        FormMilestoneModal,
    },
    computed: {
        items: function () {
            var scope = this
       
            var items = scope.milestones.filter(function(item) { 
                var keywords = scope.filter.keywords
                return ( 
                    (keywords.length < 1 )||
                    keywords.some(el => item.title.toLowerCase().includes(el)) || 
                    (item.project && keywords.some(el => item.project.title.toLowerCase().includes(el))) ||
                    (item.milestone &&  keywords.some(el => item.milestone.title.toLowerCase().includes(el))) || 
                        (item.milestone_start_date && keywords.some(el => item.milestone_start_date.toLowerCase().includes(el))) || 
                        (item.milestone_due_date && keywords.some(el => item.milestone_due_date.toLowerCase().includes(el)))
                ) 
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
        projects: function () {
            let scope =  this
            return scope.$store.getters.getProjects
        },
        boards: function() {
            let scope = this
            return scope.$store.getters.getModuleBoards(scope.module.id)
        },
        project: function () {
            let scope = this
            if (scope.project_slug) {
                return scope.$store.getters.getProjectBySlug(scope.project_slug)
            }

            return scope.$store.getters.getProjectByID(scope.project_id)
        },
        other_milestones: function () {
            let scope =  this
            return scope.milestones
        },
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
            
            scope.DELETE('/api/milestones', {
                ids: scope.selected_ids
            }).then(function (response) {
                if (!response.success) {
                    alert(response.message)
                }

                alert(response.message)
                scope.selected_ids = []
                // scope.getSprint();
                
            });
        },
        duplicate: function() {
            var scope = this
            var data = {project_id: scope.selected_project, milestone_ids: scope.selected_ids}

            scope.POST('/api/milestones/duplicate',data).then(function (res) {
                if (res.success) {
                    alert(res.message)
                    res.rows.forEach(function (data) {
                        scope.milestones.push(data)
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

            scope.PUT('/api/milestones', {fields: scope.selected, ids: scope.selected_ids}).then(function (res) {
                if (res.success) {
                    alert(res.message)
                    scope.selected = {
                        id: null,
                        title: '',
                        description: '',
                        milestone_start_date: '',
                        milestone_due_date: '',
                        project_id: null,
                    }
                    
                    scope.toggleBulkEditModal(false)
                    scope.selected_ids = []
                    scope.view = 'table_view'
                    
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
        console.log('kkkkk')
        console.log(row)

            row.edit = false

            scope.PUT('/api/milestones/' + row.id, row).then(function (res) {
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
        refreshData: function () {
            this.$store.dispatch('fetchMilestones')
        },
        getProjectBoards: function (project_id) {
            var scope = this
            return scope.boards.filter(item => (item.project_id == project_id ));
        },
        toggleModalNew: function (data = null) {
            var scope = this

            scope.selected = JSON.parse(JSON.stringify(attributes))
            scope.selected.milestone_duration =  0
            
            if (data != null) {
                scope.selected = data
                scope.getMilestoneDuration(data.id)
                scope.selected.milestone_duration = scope.milestone_duration
            }

            
            scope.MODAL_TOGGLE('form-milestone-modal')
        },
        toggleModal: function (is_open,row = null) {
            var scope = this
            scope.is_modal_open = is_open

            if (row != null) {

                scope.selected.id = row.id
                
                scope.selected.title = (row.name) ? row.name : row.title
                scope.selected.milestone_start_date = (row.start) ? row.start : row.milestone_start_date
                scope.selected.milestone_due_date = (row.start) ? row.end : row.milestone_due_date
                scope.selected.milestone_completed_date = row.milestone_completed_date

                if (row.project) {
                    scope.selected_project =      {
                        id: row.project.id,
                        text: row.project.title
                    }
                }
                

                if (row.milestone!= null){
                    scope.selected_depends_on = 
                    {
                        id: row.milestone.id,
                        text: row.milestone.title
                    }
                }

                scope.getMilestoneDuration(row.id)
                scope.removeSelfMilestone(row.id)


            } else {
                scope.selected = {
                    id: null,
                    title: '',
                    project_id: null,
                    milestone_start_date: '',
                    milestone_due_date: '',
                }

                scope.milestone_duration = 0

                // if (scope.options_project){

                //    scope.selected_project = (scope.options_project.length < 1) ? null : {
                //             id: scope.options_project[0].id,
                //             text: scope.options_project[0].text
                //         }

                //     scope.selected_depends_on = null
                // }

                if (scope.options_project){
                    scope.selected_project= (scope.options_project.length < 1) ? null : {
                        id: scope.options_project[0].id,
                        title: scope.options_project[0].title,
                    }
                    console.log('hooooyy')
                    console.log(scope.options_project[0].project_start_date)
                    scope.selected.milestone_start_date = moment(scope.options_project[0].project_start_date).format('YYYY-MM-DD')
                    // scope.selected.task_due_date = moment(scope.options_project[0].project_start_date).add(1, 'days').format('YYYY-MM-DD')

                }
            }

        },
        tagCompleteDate: function () {
            var scope = this
            scope.selected.milestone_completed_date = moment().format('YYYY-MM-DD')
        },
        tagComplete: function (status) {
            var scope = this

            if (status == 'complete'){
                scope.PUT('api/milestones/' + scope.selected.id + '/complete').then(function (res) {
                    if (res.success) {
                        alert(res.message)
                        scope.selected = null
                        scope.toggleModal(false)
                        scope.getMilestone();
                    } else {
                        alert(res.message);
                    }
                });
            }else{
                scope.PUT('api/milestones/' + scope.selected.id + '/uncomplete').then(function (res) {
                    if (res.success) {
                        alert(res.message)
                        scope.selected = null
                        scope.toggleModal(false)
                        scope.getMilestone();
                    } else {
                        alert(res.message);
                    }
                });
            }

            
        },
        switchView: function (view) {
            var scope = this
            this.view = view

            if (view == 'table_view') {
                scope.getMilestone()
            }
        },
        toggleFilter: function (show_filter) {
            this.show_filter = !this.show_filter
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
        getMilestoneDuration: function (id) {
            var scope = this

            scope.GET('api/milestones/duration/' + id).then(function (res) {
                scope.milestone_duration = res.rows
            });
        },
        getMilestone: function () {
            var scope = this
            scope.options_depends_on = []
            
            scope.GET('api/milestones?keyword=' + scope.filter.keyword).then(function (res) {
                scope.milestones = res.rows

                console.log('tesssst')
                console.log(scope.milestones)

                res.rows.forEach(function (data) {
                    scope.options_depends_on.push({
                        id: data.id,
                        text: data.title,
                    })
                })
            });

        },



        sortBoardTasks: function (board,evt,project) {
            var scope = this
            
            var cloned_tasks_id = 0;

            if (evt.added) {
                scope.clone = (evt.added.element.project_id != project.id) ? evt.added.element : null
                cloned_tasks_id = (scope.clone) ? evt.added.element.id : 0

            }

            if (evt.removed && scope.clone != null) {
                var new_task = JSON.parse(JSON.stringify(scope.clone))

                board.tasks.push(new_task)
            }
            

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
               scope.getMilestone();
            }, 300);
        }
    },
    created() {

    },
    mounted() {
        var scope = this
        scope.getMilestone()

        scope.project_slug = (scope.$route.params.project_slug) ? scope.$route.params.project_slug : null

        scope.$store.dispatch('changeHeader','Milestones')

        scope.$store.dispatch('fetchMilestones')
        scope.$store.dispatch('fetchProjects')
        scope.$store.dispatch('fetchBoards')

        setTimeout(function(){
            scope.options_project = scope.projects
        },1000);
    }
}
</script>

