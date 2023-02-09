<template>
    <div>
       <modal id="form-task-modal" title="Add New Task">
        <template v-slot:body>
            <form v-on:submit.prevent="save()" action="/" class="form-validate is-alter" autocomplete="off" novalidate="novalidate">
                <div class="form-group">
                    <div class="form-label-group">
                        <label class="form-label" for="email-address">Title</label>
                    </div>

                    <div class="form-control-wrap">
                        <input v-model="formdata.title" type="text" class="form-control form-control-lg" required="" id="email-address" placeholder="Enter title...">
                    </div>
                </div>

                <div class="form-group">
                    <div class="form-label-group">
                        <label class="form-label" for="email-address">Description</label>
                    </div>
                    <div class="form-control-wrap">
                        <textarea rows="4" v-model="formdata.description" type="text" class="form-control form-control-lg" required="" id="firstname" placeholder="Enter task description..."></textarea>
                    </div>
                </div>


                <div class="form-group">
                    <div class="form-label-group">
                        <label class="form-label" for="email-address">User Groups</label>
                    </div>
                    <div class="form-control-wrap">
                        <multiselect   v-model="selected_user_groups" 
                            :options="user_groups" 
                            track-by="id" 
                            label="title" 
                            :allow-empty="true" 
                            deselect-label="Unselect" 
                            selectLabel="Select"
                            :searchable="true"
                            :multiple="true"
                            >
                        </multiselect>
                    </div>
                </div>

                <div class="form-group">
                    <div class="form-label-group">
                        <label class="form-label" for="email-address">Users</label>
                    </div>
                    <div class="form-control-wrap">
                        <multiselect  v-model="formdata.users" 
                            :options="users" 
                            track-by="id" 
                            label="firstname" 
                            :allow-empty="true" 
                            deselect-label="Unselect" 
                            selectLabel="Select"
                            :searchable="true"
                            :multiple="true"
                            >
                        </multiselect>
                    </div>
                </div>

                <div class="form-group">
                    <div class="form-label-group">
                        <label class="form-label" for="email-address">Task Start Date</label>
                    </div>
                    <div class="form-control-wrap">
                        <input v-model="formdata.task_start_date" type="date" class="form-control form-control-lg" required="" id="email-address">
                    </div>
                </div>
                <div class="form-group">
                    <div class="form-label-group">
                        <label class="form-label" for="email-address">Task Due Date</label>
                    </div>
                    <div class="form-control-wrap">
                        <input v-model="formdata.task_due_date" type="date" class="form-control form-control-lg" required="" id="email-address">
                    </div>
                </div>


                <div class="form-group">
                    <div class="form-label-group">
                        <label class="form-label" for="email-address">Task Duration</label>
                    </div>
                    <div class="form-control-wrap">
                        <input @change="onDurationChange()" v-model="formdata.task_duration" type="number" step="1" class="form-control form-control-lg" required="" id="email-address">
                    </div>
                </div>

             
                <div class="form-group">
                    <div class="form-label-group">
                        <label class="form-label" for="task-project">Projects</label>
                    </div>
                    <div class="form-control-wrap">
                        <multiselect @select="onProjectChange($event,formdata.id)" v-model="formdata.project" 
                            :options="projects" 
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
                        <label class="form-label" for="email-address">Note</label>
                    </div>
                    <div class="form-control-wrap">
                        <textarea rows="4" v-model="formdata.note" type="text" class="form-control form-control-lg" required="" id="firstname" placeholder="Enter task description..."></textarea>
                    </div>
                </div>
    
                <div class="form-group">
                    <div class="form-label-group">
                        <label class="form-label" for="email-address">Sprint</label>
                    </div>
                    <div class="form-control-wrap">
                        <multiselect  v-model="formdata.sprint" 
                        :options="sprints" 
                        track-by="id" 
                        label="title" 
                        :multiple="false"
                        :allow-empty="true" 
                        deselect-label="Deselect" 
                        :taggable="true"
                        @tag="createNewSprint" 
                        >
                        </multiselect>
                    </div>
                </div>
        
            

              
                <div class="form-group">
                    <div class="form-label-group">
                        <label class="form-label" for="email-address">Tags</label>
                    </div>
                    <div class="form-control-wrap">
                        <multiselect  v-model="formdata.tags" 
                            :options="tags" 
                            track-by="id" 
                            label="title" 
                            :multiple="true"
                            :taggable="true"
                            @tag="createNewTag"  
                            >
                        </multiselect>
                    </div>
                </div>
              
                
                <div class="form-group">
                
                    <div class="form-label-group">
                        <label class="form-label" for="email-address">Milestone</label>
                    </div>
                    <div class="form-control-wrap">
                        <multiselect  v-model="formdata.milestone" 
                        :options="milestones" 
                        track-by="id" 
                        label="title" 
                        :multiple="false"
                        :allow-empty="true" 
                        deselect-label="Deselect" 
                        :taggable="true"
                        @tag="createNewMilestone" 
                        >
                        </multiselect>
                    </div>
         

                    
                </div>

                <div v-if="formdata.id" class="form-group">
                    <div class="form-label-group">
                        <label class="form-label" for="email-address">Task Completed Date</label>
                    </div>
                    <div class="form-control-wrap">
                        <input v-model="formdata.task_completed_date" type="date" class="form-control form-control-lg" required="" id="email-address">
                    </div>
                </div>

                
            </form>
        </template>
        <template v-slot:footer>
            <button type="button" @click="save()" class="btn-bp btn-bp-black">Save</button>
        </template>
    </modal>
    </div>
</template>



<script>
import moment from 'moment'
import modal from '../components/Modal.vue'

export default {
    props: ['formdata'],
    data: function () {
        return {
            title: 'Add New Task',
            user_groups: [],
            users: []
        }
    },
    components: {
        modal
    },
    computed: {
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
        getUserGroup: function () {
            var scope = this
            scope.GET('/api/user-groups').then(function (res) {
                scope.user_groups = res.rows
                // title
            });
        },
        getUsers: function () {
            var scope = this
            scope.GET('/api/users').then(function (res) {
                scope.users = res.rows

                scope.users = res.rows
                // firstname

            });
        },
        onProjectChange(event, task_id) {
            var scope = this


            if (task_id == null){
                scope.GET('api/projects/' + event.id).then(function (res) {
                    scope.selected.task_start_date = res.data.project_start_date
                    scope.selected.task_due_date  =  moment(res.data.project_start_date).add(scope.selected.task_duration, 'days').format('YYYY-MM-DD')
                });
            }
        },
        onDurationChange(){
            var scope = this
            scope.formdata.task_due_date  =  moment(scope.formdata.task_start_date).add(scope.formdata.task_duration, 'days').format('YYYY-MM-DD')
        },
        createNewMilestone: function (val) {
            var scope = this
            var project_id = (scope.selected_project) ? scope.selected_project.id : null

            
            scope.POST('/api/milestones?quick=yes',{title: val, project_id: project_id}).then(function (res) {
                if (res.success) {
                    scope.$store.dispatch('fetchMilestones')
                    scope.formdata.milestone = JSON.parse(JSON.stringify(res.data))
                } else {
                    alert(res.message);
                }
            });    
        },
        createNewSprint: function (val) {
            var scope = this
            var project_id = (scope.selected_project) ? scope.selected_project.id : null

            scope.POST('/api/sprints?quick=yes',{title: val, project_id: project_id}).then(function (res) {
                if (res.success) {
                    scope.$store.dispatch('fetchSprints') // change this to pushing to state in vuex to optimize
                    scope.formdata.sprint = JSON.parse(JSON.stringify(res.data))
                } else {
                    alert(res.message);
                }
            });    
        },
        inlineCreateNewTag: function (val) {
            var scope = this
            var project_id = (scope.new_row.project) ? scope.new_row.project.id : null 
            scope.saveNewTag(val,project_id,true)
        },
        createNewTag: function (val) {
            var scope = this
            var project_id = (scope.formdata.project) ? scope.formdata.project.id : null 
            scope.saveNewTag(val,project_id,false)
        },
        saveNewTag: function (title,project_id,is_inline = false) {
            var scope = this
            var data = {title: title, project_id: project_id}
            scope.POST('/api/tags?quick=yes',data).then(function (res) {
                if (res.success) {
                    var tag  = { id: res.data.id,  title: res.data.title, project_id: project_id }
                    scope.$store.dispatch('addNewTag',tag)
    
                    setTimeout(function(){
                        scope.formdata.tags.push(tag)
                    },300)
                
                } else {
                    alert(res.message);
                }
            }); 
        },
        save: function () {
            var scope = this

            var data = scope.CLONE_OBJECT(scope.formdata)
 
            data.milestone_id = (data.milestone == null) ? null : data.milestone.id
            data.project_id = (data.project == null) ? null : data.project.id
            data.sprint_id = (data.sprint == null) ? null : data.sprint.id



            
            if (scope.formdata.id !== null) {
                scope.PUT('/api/tasks/' + data.id, data).then(function (res) {
                    if (res.success) {
                        alert(res.message)
                        scope.edited_row = res.data

                        scope.selected = JSON.parse(JSON.stringify(attributes))
                        scope.new_row = JSON.parse(JSON.stringify(attributes))
                        scope.toggleModal(false)

                        scope.MODAL_TOGGLE('form-task-modal')
                       
                        
                    } else {
                        alert(res.message);
                    }
                });
            } else {
                scope.POST('/api/tasks',data).then(function (res) {
                    if (res.success) {
                        alert(res.message)
                        scope.selected = JSON.parse(JSON.stringify(attributes));
                        scope.new_row = JSON.parse(JSON.stringify(attributes))
                        scope.toggleModal(false)
                        scope.getRows();

                        scope.MODAL_TOGGLE('form-task-modal')
                    } else {
                        alert(res.message);
                    }
                });
            }
            
        },

    },
    created() {
    },
    mounted() {
        var scope = this
        scope.getUserGroup();
        scope.getUsers()

        scope.title = (scope.formdata.id) ? 'Update Task' : 'Add New Task'
    }
}

</script>
