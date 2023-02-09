<template>
    <div>
        <div id="retrospectives-page" v-if="module_loaded">
            <div style="margin-top:40px; margin-bottom:10px; display:flex; justify-content:space-between;">
                <div style="display:flex;">
                    <button @click="toggleModal(true)" class="bp-bordered-boxed"> <span class="ti-plus"></span></button>
                    <button @click="toggleFilter()" class="bp-bordered-boxed"> <span class="ti-filter"></span></button>

                    &nbsp;&nbsp;&nbsp;

                    <button @click="switchView('table_view')" class="bp-bordered-boxed" :class="{'active' : view == 'table_view' }"> <span class="ti-view-list-alt"></span></button>
                    <button @click="switchView('kanban_view')" class="bp-bordered-boxed" :class="{'active' : view == 'kanban_view' }"> <span class="ti-layout-grid3"></span></button>
                </div>
            </div>

            <div v-if="show_filter" style="background:#efefef; padding:10px; margin-bottom:10px;">
                <input v-model="filter.keyword" style="width:300px; margin-right:10px; padding:5px 10px;" type="text">
            </div>

            <div v-if="view == 'table_view'" class="table-view">
                <div class="datatable">
                    <div class="tr header">
                        <div class="th">Title</div>

                        <div class="th" style="max-width:100px;">Action</div>
                    </div>
                    <div v-for="row in items" :key="row.id" class="tr">
                        <div class="td">{{ row.title }}</div>
                        <div class="td" style="max-width:100px;">
                            <button @click="toggleModal(true,row)" style="cursor:pointer; margin:0px; background:#2a3042; color:#fff; border:none; font-size:12px; width:auto; padding:5px; display:inline-block;">Edit</button>
                            <button @click="toggleModal(true)" style="cursor:pointer; margin:0px; background:#f73830; color:#fff; border:none; font-size:12px; width:auto; padding:5px; display:inline-block;">Delete</button>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="view == 'kanban_view'" class="kandan-view" style="display:flex;">
                <div style="display:flex;">
                    <draggable style="display:flex;" class="dragArea list-group w-full"  :group="'boards'" :list="boards"  @change="sortModuleBoards()">
                        <div style="min-height:200px;  border:1px solid #ccc; margin:0px 10px; flex:1; min-width:200px;"  v-for="board,index in boards" :key="'board-' + index">
                            
                            <div style="background:#424c68; padding:3px 10px; color:#fff;">{{ board.title }}</div>
                            <draggable class="dragArea list-group w-full" :group="'element'" :list="board.retrospectives" @change="sortBoardTasks(board, $event)">
                                <div style="padding:3px 10px;  border:1px solid #ccc; border-left:none; border-right:none; border-top:none;" class="list-group-item" v-for="element in board.retrospectives" :key="element.id">
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

                        <div class="form-group">
                            <div class="form-label-group">
                                <label class="form-label" for="email-address">Description</label>
                            </div>
                            <div class="form-control-wrap">
                                <textarea rows="4" v-model="selected.description" type="text" class="form-control form-control-lg" required="" id="firstname" placeholder="Enter task description..."></textarea>
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
                                label="title" 
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
                                <label class="form-label" for="email-address">Sprint</label>
                            </div>
                            <div class="form-control-wrap">
                                <multiselect  v-model="selected_sprint" 
                                :options="options_sprint" 
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
                                deselect-label="Deselect" 
                                selectLabel="Select"
                                :searchable="true"
                                >
                                </multiselect>
                            </div>
                        </div>

                        <!-- <div class="form-group">
                            <div class="form-label-group">
                                <label class="form-label" for="email-address">Status</label>
                            </div>
                            <div class="form-control-wrap">
                                <multiselect  v-model="selected_status" 
                                :options="options_status" 
                                track-by="id" 
                                label="title" 
                                :allow-empty="false" 
                                deselect-label="Selected" 
                                selectLabel="Select"
                                :searchable="true"
                                >
                                </multiselect>
                            </div>
                        </div> -->

                        <div class="form-group">
                            <div class="form-label-group">
                                <label class="form-label" for="email-address">Retro Type</label>
                            </div>
                            <div class="form-control-wrap">
                                <multiselect  v-model="selected_retro_type" 
                                :options="options_retro_type" 
                                track-by="value" 
                                label="text" 
                                :allow-empty="false" 
                                deselect-label="Selected" 
                                selectLabel="Select"
                                :searchable="true"
                                >
                                </multiselect>
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
            view: 'table_view',
            show_filter: false,
            is_modal_open: false,
            retrospectives: [],
            selected: {
                id: null,
                title: '',
                description: '',
                milestone_id: null,
                sprint_id: null,
                project_id: null,
                status_id: null,
            },
            filter: {
                keyword: '',
                timeout: null,
            },

            selected_milestone: null,
            options_milestone: [],

            selected_project: null,
            options_project: [],

            selected_sprint: null,
            options_sprint: [],

            selected_status: null,
            options_status: [],

            selected_retro_type: null,
            options_retro_type: [
                {text: 'Continue', value: 'Continue'},
                {text: 'Stop', value: 'Stop'},
                {text: 'Start', value: 'Start'}
            ],

            module: {
                id: null,
                name: 'Retrospectives'
            },
            module_loaded: false,
            
        }
    },
    components: {

    },
    computed: {
        items: function () {
            var scope = this
            return scope.retrospectives.filter(item => (item.title.toLowerCase().includes(scope.filter.keyword) ));
        },
        boards: function() {
            let scope = this
            return scope.$store.getters.getModuleBoards(scope.module.id)
        },
        projects: function () {
            let scope =  this
            return scope.$store.getters.getProjects
        },
        milestones: function () {
            let scope =  this
            return scope.$store.getters.getMilestones
        },
        sprints: function () {
            let scope =  this
            return scope.$store.getters.getSprints
        },
        status: function () {
            let scope =  this
            return scope.$store.getters.getStatus
        }
    },
    methods: {
        toggleFilter: function () {
            this.show_filter = !this.show_filter
        },
        switchView: function (view) {
            this.view = view
        },
        toggleModal: function (is_open,row = null) {
            var scope = this

            scope.is_modal_open = is_open

            if (row != null) {
                scope.selected = row

                console.log('asdsadsad')
                console.log(row)

                if (row.milestone!= null){
                    scope.selected_milestone = 
                    {
                        id: row.milestone.id,
                        text: row.milestone.title
                    }
                }

                if (row.sprint!= null){
                    scope.selected_sprint = 
                    {
                        id: row.sprint.id,
                        text: row.sprint.title
                    }
                }

                if (row.project!= null){
                    scope.selected_project = 
                    {
                        id: row.project.id,
                        text: row.project.title
                    }
                }

                scope.selected_retro_type =  {
                            text: row.retro_type,
                            value: row.retro_type
                        }

            } else {
                scope.selected = {
                    id: null,
                    title: '',
                    description: '',
                }

                if (scope.options_milestone){
                   scope.selected_milestone = (scope.options_milestone.length < 1) ? null : {
                            id: scope.options_milestone[0].id,
                            text: scope.options_milestone[0].text
                        }
                }

                if (scope.options_sprint){
                   scope.selected_sprint = (scope.options_sprint.length < 1) ? null : {
                            id: scope.options_sprint[0].id,
                            text: scope.options_sprint[0].text
                        }
                }

                if (scope.options_project){
                   scope.selected_project = (scope.options_project.length < 1) ? null : {
                            id: scope.options_project[0].id,
                            text: scope.options_project[0].text
                        }
                }

                if (scope.options_status){
                   scope.selected_status = (scope.options_status.length < 1) ? null : {
                            id: scope.options_status[0].id,
                            text: scope.options_status[0].text
                        }
                }

                scope.selected_retro_type =  {
                            text: scope.options_retro_type[0].text,
                            value: scope.options_retro_type[0].value
                        }
            }
        },
        save: function () {
            var scope = this

            scope.selected.milestone_id = (scope.selected_milestone == null) ? null : scope.selected_milestone.id
            scope.selected.sprint_id = (scope.selected_sprint == null) ? null : scope.selected_sprint.id
            scope.selected.project_id = (scope.selected_project == null) ? null : scope.selected_project.id
            scope.selected.status_id = (scope.selected_status == null) ? null : scope.selected_status.id
            scope.selected.retro_type = (scope.selected_retro_type == null) ? null : scope.selected_retro_type.value

            if (scope.selected.id !== null) {
                scope.PUT('api/retrospectives/' + scope.selected.id, scope.selected).then(function (res) {
                    if (res.success) {
                        alert(res.message)
                        scope.selected = null
                        scope.toggleModal(false)
                        scope.getRetrospectives();
                    } else {
                        alert(res.message);
                    }
                });
            } else {
                scope.POST('api/retrospectives', scope.selected).then(function (res) {
                    if (res.success) {
                        alert(res.message)
                        scope.selected.title = '';
                        scope.selected.description = '';
                        scope.toggleModal(false)
                        scope.getRetrospectives();
                    } else {
                        alert(res.message);
                    }
                });
            }
            
        },
        getRetrospectives: function () {
            var scope = this
            scope.GET('api/retrospectives?keyword=' + scope.filter.keyword).then(function (res) {
                scope.retrospectives = res.rows
            });
        },
        // getMilestone: function () {
        //     var scope = this
        //     scope.GET('api/milestones?keyword=' + scope.filter.keyword).then(function (res) {

        //         res.rows.forEach(function (data) {
        //             scope.options_milestone.push({
        //                 id: data.id,
        //                 text: data.title,
        //             })
        //         })
        //     });

        // },

        // getSprint: function () {
        //     var scope = this
        //     scope.GET('api/sprints?keyword=' + scope.filter.keyword).then(function (res) {

        //         res.rows.forEach(function (data) {
        //             scope.options_sprint.push({
        //                 id: data.id,
        //                 text: data.title,
        //             })
        //         })
        //     });

        // },

        // getProject: function () {
        //     var scope = this
        //     scope.GET('api/projects?keyword=' + scope.filter.keyword).then(function (res) {

        //         res.rows.forEach(function (data) {
        //             scope.options_project.push({
        //                 id: data.id,
        //                 text: data.title,
        //             })
        //         })
        //     });
        // },

        // getStatus: function () {
        //     var scope = this
        //     scope.GET('api/status?keyword=' + scope.filter.keyword).then(function (res) {

        //         res.rows.forEach(function (data) {
        //             scope.options_status.push({
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
        },

        searchTimeOut() {  
            var scope = this
            if (scope.filter.timeout) {
                clearTimeout(scope.filter.timeout);
                scope.filter.timeout = null;
            }
            scope.filter.timeout = setTimeout(() => {
               scope.getRetrospectives();
            }, 300);
        }
    },
    created() {

    },
    mounted() {
        var scope = this
        scope.getRetrospectives()
        // scope.getMilestone()
        // scope.getSprint()
        // scope.getProject()
        // scope.getStatus()
        

        scope.$store.dispatch('changeHeader','Retrospectives')

        var module = scope.module
        scope.getModule(module);
        scope.$store.dispatch('fetchRetrospectives')
        scope.$store.dispatch('fetchBoards')

        scope.$store.dispatch('fetchProjects')
        scope.$store.dispatch('fetchMilestones')
        scope.$store.dispatch('fetchSprints')
        // scope.$store.dispatch('fetchStatus')

        setTimeout(function(){
            scope.options_project = scope.projects
            scope.options_milestone = scope.milestones
            scope.options_sprint = scope.sprints
            // scope.options_status = scope.status
        },1000);
    }
}
</script>

