<template>
    <div>
       <modal id="form-project-modal" title="Add New Milestone">
        <template v-slot:body>
            <form v-on:submit.prevent="save()" action="/" class="form-validate is-alter" autocomplete="off" novalidate="novalidate">
                <div class="form-group">
                    <div class="form-label-group">
                        <label class="form-label" for="email-address">Title</label>
                    </div>
                    <div class="form-control-wrap">
                        <input v-model="formdata.title" type="text" class="form-control form-control-lg" required="" id="email-address" placeholder="Enter project title...">
                    </div>
                </div>
                <div class="form-group">
                    <div class="form-label-group">
                        <label class="form-label" for="email-address">Description</label>
                    </div>
                    <div class="form-control-wrap">
                        <textarea rows="4" v-model="formdata.description" type="text" class="form-control form-control-lg" required="" id="firstname" placeholder="Enter project description..."></textarea>
                    </div>
                </div>

                <div class="form-group">
                    <div class="form-label-group">
                        <label class="form-label" for="email-address">Users</label>
                    </div>
                    <div class="form-control-wrap">
                        <multiselect  v-model="formdata.project_users" 
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

                <div class="form-control-wrap">
                    <div class="form-check">
                        <input type="checkbox" v-model="formdata.is_active" true-value="1" false-value="0" class="form-check-input" id="is-active">
                        <label class="form-check-label" for="is-active">Is Active</label>
                    </div>
                </div>

                <div class="form-control-wrap">
                    <div class="form-check">
                        <input type="checkbox" v-model="formdata.sprints_yes" true-value="1" false-value="0" class="form-check-input" id="sprints">
                        <label class="form-check-label" for="sprints">Sprints</label>
                    </div>
                </div>

                <div class="form-control-wrap">
                    <div class="form-check">
                        <input type="checkbox" v-model="formdata.milestones_yes" true-value="1" false-value="0" class="form-check-input" id="milestone">
                        <label class="form-check-label" for="milestone">Milestone</label>
                    </div>
                </div>

                <div class="form-control-wrap">
                    <div class="form-check">
                        <input type="checkbox" v-model="formdata.ideas_yes" true-value="1" false-value="0" class="form-check-input" id="ideas">
                        <label class="form-check-label" for="ideas">Ideas</label>
                    </div>
                </div>

                <div class="form-control-wrap">
                    <div class="form-check">
                        <input type="checkbox" v-model="formdata.retrospectives_yes" true-value="1" false-value="0" class="form-check-input" id="retrospectives">
                        <label class="form-check-label" for="retrospectives">Retrospective</label>
                    </div>
                </div>

                <div class="form-control-wrap">
                    <div class="form-check">
                        <input type="checkbox" v-model="formdata.timesheet_yes" true-value="1" false-value="0" class="form-check-input" id="timesheet">
                        <label class="form-check-label" for="timesheet">Timesheet</label>
                    </div>
                </div>

                <div class="form-control-wrap">
                    <div class="form-check">
                        <input type="checkbox" v-model="formdata.chat_yes" true-value="1" false-value="0" class="form-check-input" id="chat">
                        <label class="form-check-label" for="chat">Chat</label>
                    </div>
                </div>

                <div class="form-group">
                    <div class="form-label-group">
                        <label class="form-label" for="email-address">Duration</label>
                    </div>
                    <div class="form-control-wrap">
                        <input v-model="formdata.duration" type="text" class="form-control form-control-lg" required="" id="duration">
                    </div>
                </div>

                <div class="form-group">
                    <div class="form-label-group">
                        <label class="form-label" for="email-address">Project Start Date</label>
                    </div>
                    <div class="form-control-wrap">
                        <input v-model="formdata.project_start_date" type="date" class="form-control form-control-lg" required="" id="email-address">
                    </div>
                </div>

                <div class="form-group">
                    <div class="form-label-group">
                        <label class="form-label" for="email-address">Project Due Date</label>
                        <span v-if="formdata.id">
                            <button @click="recalculate(formdata.id)" type="button"  class="bp-btn bp-btn-gray">Recalculate</button>
                        </span>
                    </div>
                    <div class="form-control-wrap">
                        <input v-model="formdata.project_due_date" type="date" class="form-control form-control-lg" required="" id="email-address">
                    </div>
                </div>

                <div class="form-group">
                    <div class="form-label-group">
                        <label class="form-label" for="email-address">Project Completed Date</label>
                    </div>
                    <div class="form-control-wrap">
                        <input v-model="formdata.project_completed_date" type="date" class="form-control form-control-lg" required="" id="email-address">
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
            title: 'Add New Project',
            users: []
        }
    },
    components: {
        modal
    },
    computed: {

    },
    watch: {

    },
    methods: {
        getUsers: function () {
            var scope = this
            scope.GET('/api/users').then(function (res) {
                scope.users = res.rows
            });
        },
        save: function () {
            var scope = this

            var data = scope.CLONE_OBJECT(scope.formdata)
 
            data.user_details =scope.formdata.project_users

            if (data.id !== null) {
                scope.PUT('api/projects/' + data.id, data).then(function (res) {
                    if (res.success) {
                        alert(res.message)
                        scope.MODAL_TOGGLE('form-project-modal')
                        scope.$parent.getProjects()
                    } else {
                        alert(res.message);
                    }
                });
            } else {
                scope.POST('api/projects', data).then(function (res) {
                    if (res.success) {
                        alert(res.message)
                        scope.MODAL_TOGGLE('form-project-modal')
                        scope.$parent.getProjects()
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
        scope.getUsers()
        scope.title = (scope.formdata.id) ? 'Update Project' : 'Add New Project'
    }
}

</script>
