<template>
    <div>
        <div id="projects-page">

            <div class="page-actions">
                <div class="page-actions-inner">
                    <button v-if="CAN_WRITE('Milestones')" @click="toggleModal(true)" class="bp-bordered-boxed"> <span class="ti-plus"></span></button>
                    <button @click="toggleFilter()" class="bp-bordered-boxed"> <span class="ti-filter"></span></button>
                    &nbsp;&nbsp;&nbsp;

                    <button @click="switchView('table_view')" class="bp-bordered-boxed" :class="{'active' : view == 'table_view' }"> <span class="ti-view-list-alt"></span></button>
                    <button @click="switchView('kanban_view')" class="bp-bordered-boxed" :class="{'active' : view == 'kanban_view' }"> <span class="ti-layout-grid3"></span></button>
                    <button @click="switchView('gantt_view')" class="bp-bordered-boxed" :class="{'active' : view == 'gantt_view' }"> <span class="ti-bar-chart"></span></button>
                
                    <div v-if="!project_slug && view == 'kanban_view'" style="margin-left:10px; padding:20px 0px; ">
                        <select v-model="project_id" style="height:35px; padding:0px 10px;">
                            <option :value="null">All</option>
                            <option :value="project_item.id" v-for="project_item in projects" :key="'opt-project-' + project_item.id ">{{ project_item.title }}</option>
                        </select>
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


            <div v-if="view == 'table_view'" class="table-view">
                <div class="datatable">
                    <div class="tr header">
                        <div class="th">Title</div>
                        <div class="th">Milestone Start Date</div>
                        <div class="th">Milestone Due Date </div>
                        <div class="th">Project</div>
                        <div class="th">Depends On</div>
                        <div class="th">Milestone Completed Date </div>
                        <div class="th" style="max-width:100px;">Action</div>
                    </div>
                    <div v-for="row in items" :key="row.id" class="tr">
                        <div class="td">{{ row.title }}</div>
                        <div class="td">{{ row.milestone_start_date }}</div>
                        <div class="td">{{ row.milestone_due_date }}</div>
                        <div class="td">{{ row.project.title }}</div>
                        <div class="td">
                            <template v-if="row.milestone != null">
                                {{ row.milestone.title }}
                            </template>
                            <template v-else>
                                None
                            </template>
                        </div>
                        <div class="td">{{ row.milestone_completed_date }}</div>
                        <div class="td" style="max-width:100px;">
                            <button v-if="CAN_EDIT('Milestones')" @click="toggleModal(true,row)" class="bp-btn bp-btn-blue">Edit</button>
                            <button v-if="CAN_DELETE('Milestones')" @click="toggleModal(true)" class="bp-btn bp-btn-red">Delete</button>
                        </div>
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
                                    <input v-model="selected.title" type="text" class="form-control form-control-lg" required="" id="email-address" placeholder="Enter milestone title...">
                                </div>
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
                                    <label class="form-label" for="email-address">Milestone Completed Date</label>
                                </div>
                                <div class="form-control-wrap">
                                    <input v-model="selected.milestone_completed_date" type="date" class="form-control form-control-lg" required="" id="email-address">
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

                            <div class="form-group">
                                <div class="form-label-group">
                                    <label class="form-label" for="email-address">Milestone Duration (Computed)</label>
                                </div>
                                <div class="form-control-wrap">
                                    <input v-model="milestone_duration" type="text" class="form-control form-control-lg" required="" id="email-address" disabled>
                                </div>
                            </div>

                            </div>
                        </form>
                        </div>
                        <div class="bp-modal-form-actions">
                            <button type="button" @click="save()" class="bp-btn bp-btn-blue">Save</button>
                            <button @click="toggleModal(false); getMilestone()" type="button"  class="bp-btn bp-btn-gray">Cancel</button>
                            <button @click="tagCompleteDate()" type="button"  class="bp-btn bp-btn-gray">Mark Complete</button>
                            <!-- <button v-if="selected.id != null && selected.milestone_completed_date == null" @click="tagComplete('complete')" type="button"  class="bp-btn bp-btn-gray">Set Complete</button>
                            <button v-if="selected.id != null && selected.milestone_completed_date != null" @click="tagComplete('uncomplete')" type="button"  class="bp-btn bp-btn-gray">Set Uncomplete</button> -->
                        </div>
                    
                    </div>
                </div>
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

export default {
    props: ['user_id'],
    data: function () {
        return {
            project_slug: null,
            project_id: null,
            view: 'table_view',
            show_filter: false,
            is_modal_open: false,
            milestones: [],
            selected: {
                id: null,
                project_id: null,
                title: '',
                milestone_start_date: '',
                milestone_due_date: '',
                milestone_completed_date: '',
                depends_id: null
            },
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

            milestone_duration: 0
        }
    },
    components: {
        'Gantt': Gantt,
    },
    computed: {
        items: function () {
            var scope = this
       
            return scope.milestones.filter(function(item) { 
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
        onProjectChange(event, task_id) {
            var scope = this

            if (task_id == null){
                scope.GET('api/projects/' + event.id).then(function (res) {
                    console.log('dasdsad')
                    console.log(res.data.project_start_date)
                    scope.selected.milestone_start_date = res.data.project_start_date
                    // scope.selected.task_due_date  =  moment(res.data.project_start_date).add(scope.selected.task_duration, 'days').format('YYYY-MM-DD')
                });
            }
        },
        getProjectBoards: function (project_id) {
            var scope = this
            return scope.boards.filter(item => (item.project_id == project_id ));
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
            this.view = view
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
        save: function () {
            var scope = this

            scope.selected.project_id = (scope.selected_project == null) ? null : scope.selected_project.id

            scope.selected.depends_id = (scope.selected_depends_on == null) ? null : scope.selected_depends_on.id

            if (scope.selected.id !== null) {
                scope.PUT('api/milestones/' + scope.selected.id, scope.selected).then(function (res) {
                    if (res.success) {
                        alert(res.message)
                        scope.selected = null
                        scope.toggleModal(false)
                        scope.getMilestone();
                    } else {
                        alert(res.message);
                    }
                });
            } else {
                scope.POST('api/milestones', scope.selected).then(function (res) {
                    if (res.success) {
                        alert(res.message)
                        scope.selected.title = '';
                        scope.toggleModal(false)
                        scope.getMilestone();
                    } else {
                        alert(res.message);
                    }
                });
            }
            
        },

        getMilestoneDuration: function (id) {
            var scope = this
            console.log('total task time')
            console.log(id)

            scope.GET('api/milestones/duration/' + id).then(function (res) {
                scope.milestone_duration = res.rows
                console.log('tesssst')
                console.log(res.rows)

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
               scope.getMilestone();
            }, 300);
        }
    },
    created() {

    },
    mounted() {
        var scope = this
        scope.getMilestone()
        // scope.getProjects()
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

