<template>
    <div>
       <modal id="form-milestone-modal" title="Add New Milestone">
        <template v-slot:body>
            <form v-on:submit.prevent="save()" action="/" class="form-validate is-alter" autocomplete="off" novalidate="novalidate">
                <div class="form-group">
                    <div class="form-group">
                        <div class="form-label-group">
                            <label class="form-label" for="email-address">Project</label>
                        </div>
                        <div class="form-control-wrap">
                            <multiselect  @select="onProjectChange($event,formdata.id)" v-model="formdata.project" 
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
                        <label class="form-label" for="email-address">Title</label>
                    </div>
                    <div class="form-control-wrap">
                        <input v-model="formdata.title" type="text" class="form-control form-control-lg" required="" id="email-address" placeholder="Enter milestone title...">
                    </div>
                </div>


                <div class="form-group">
                    <div class="form-label-group">
                        <label class="form-label" for="email-address">Milestone Start Date</label>
                    </div>
                    <div class="form-control-wrap">
                        <input v-model="formdata.milestone_start_date" type="date" class="form-control form-control-lg" required="" id="email-address">
                    </div>
                </div>

                <div class="form-group">
                    <div class="form-label-group">
                        <label class="form-label" for="email-address">Milestone Due Date</label>
                    </div>
                    <div class="form-control-wrap">
                        <input v-model="formdata.milestone_due_date" type="date" class="form-control form-control-lg" required="" id="email-address">
                    </div>
                </div>
                <div class="form-group">
                    <div class="form-label-group">
                        <label class="form-label" for="email-address">Milestone Completed Date</label>
                    </div>
                    <div class="form-control-wrap">
                        <input v-model="formdata.milestone_completed_date" type="date" class="form-control form-control-lg" required="" id="email-address">
                    </div>
                </div>

                <div class="form-group">
                    <div class="form-label-group">
                        <label class="form-label" for="email-address">Depends On</label>
                    </div>
                    <div class="form-control-wrap">
                        <multiselect  v-model="formdata.milestone" 
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

                <div class="form-group">
                    <div class="form-label-group">
                        <label class="form-label" for="email-address">Milestone Duration (Computed)</label>
                    </div>
                    <div class="form-control-wrap">
                        <input v-model="formdata.milestone_duration" type="text" class="form-control form-control-lg" required="" id="email-address" disabled>
                    </div>
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
            title: 'Add New Milestone',
            milestone_duration: 0
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
        other_milestones: function () {
            let scope =  this
            return scope.milestones.filter(function(item) {
                return (!scope.formdata.id || item.id != scope.formdata.id)
            });
        },
    },
     watch: {

    },
    methods: {
        onProjectChange(event, task_id) {
            var scope = this

            if (task_id == null){
                scope.GET('api/projects/' + event.id).then(function (res) {
                    scope.formdata.milestone_start_date = res.data.project_start_date
                    // scope.selected.task_due_date  =  moment(res.data.project_start_date).add(scope.selected.task_duration, 'days').format('YYYY-MM-DD')
                });
            }
        },
        
        save: function () {
            var scope = this

           var data = scope.CLONE_OBJECT(scope.formdata)
 
            data.depends_id = (data.milestone == null) ? null : data.milestone.id
            data.project_id = (data.project == null) ? null : data.project.id

            if (data.id !== null) {
                scope.PUT('api/milestones/' + data.id, data).then(function (res) {
                    if (res.success) {
                        alert(res.message)
                        //scope.$store.dispatch('fetchMilestones')
                        scope.$parent.refreshData()
                        scope.MODAL_TOGGLE('form-milestone-modal')
                    } else {
                        alert(res.message);
                        if (res.code == 500){
                            document.getElementsByTagName("input")[res.index].focus()
                        }
                    }
                });
            } else {
                scope.POST('api/milestones', data).then(function (res) {
                    if (res.success) {
                        alert(res.message)
                        //scope.$store.dispatch('fetchMilestones')
                        scope.$parent.refreshData()
                        scope.MODAL_TOGGLE('form-milestone-modal')
                    } else {
                        alert(res.message);
                        if (res.code == 500){
                            document.getElementsByTagName("input")[res.index].focus()
                        }
                    }
                });
            }
            
        },
    },
    created() {
    },
    mounted() {
        var scope = this
        scope.title = (scope.formdata.id) ? 'Update Milestone' : 'Add New Milestone'
    }
}

</script>
