<template>
    <div>
        <div v-if="ready">
            <svg id="gantt"></svg>
        </div>
    </div>
</template>

<style scoped>
    
</style>

<script>
import Gantt from 'frappe-gantt';
import moment from 'moment'

export default {
    props: ['user_id','module_type','milestones'],
    data: function () {
        return {
            prerequiste: {
                moduleReady: false,
            },
            form: {
                chart_start: '',
                chart_end: '',
                dependent_chart_start: '',
                dependent_id: null
            },
            frappeGantt: [],
            send_request: true, // this will be used as flagging to prevnt multiple
        }
    },
    components: {

    },
    watch: {
        ready: function (val) {
            var scope = this
            if (val) {
                setTimeout(function(){
                    var gantt = new Gantt("#gantt", scope.frappeGantt, {
                        on_date_change: function(task, start, end) {
                            if (!scope.send_request) {
                                console.log('BLOCKED ',task.id)
                                return
                            }
                            scope.send_request = false
                            console.log('test')
                            console.log(task)
                            scope.dateChange(task, start, end)
                        },
                        on_click: function (task) {
                            scope.$parent.toggleModal(true,task)
                        }
                    });
                },400)
            }
        },
        
    },
    computed: {
        ready: function () {
            var scope = this

            if (scope.prerequiste.moduleReady) {
                return true
            }

            return false
        },
        /*
        milestones: function () {
            let scope =  this
            return scope.$store.getters.getMilestones
        },
        */
    },
    methods: {

        dateChange: function (task, start, end) {
            var scope = this

            scope.form.chart_start= moment(start).format('YYYY-MM-DD')
            scope.form.chart_end= moment(end).format('YYYY-MM-DD')
            scope.form.dependent_chart_start = moment(end).add(1, 'days').format('YYYY-MM-DD')
            scope.form.dependent_id = task.dependent_id
            

            if (scope.module_type == 'milestones'){
                scope.prerequiste.moduleReady = false
                scope.PUT('api/milestones-dependencies/' + parseInt(task.id), scope.form).then(function (res) {
                    scope.getMilestones()
                    
                    scope.send_request = true
                    scope.prerequiste.moduleReady = true
                })

            }else if(scope.module_type == 'tasks') {
                    scope.prerequiste.moduleReady = false
                    scope.PUT('api/tasks-dependencies/' + parseInt(task.id), scope.form).then(function (res) {
                    scope.getTasks()

                    scope.send_request = true
                    scope.prerequiste.moduleReady = true
                })
            }
        },

        getMilestones: function () {
            var scope = this
            scope.GET('api/milestones-chart').then(function (res) {
                scope.appendMilestoneData(res.rows)
            });
        },
        appendMilestoneData: function (rows) {
            var scope = this
            scope.frappeGantt = []
            var count = 0;
            console.log('LENGHT ===> ',rows.length)

            if (rows.length < 1) {
                scope.prerequiste.moduleReady = true
                console.log('1 we ready')
            }

            rows.forEach(function (data) {
                var result = []
                count++;
                if(data.milestone != null){
                    result.push(data.milestone.id + '')
                }

                var row = data

                row['id'] = data.id + '';
                row['name'] = data.title;
                row['start'] = data.milestone_start_date;
                row['end'] = data.milestone_due_date;
                row['progress'] = 100;
                row['dependencies'] = result;
                row['dependent_id'] = data.depends_id;
                row['custom_class'] = 'bar-task';


                scope.frappeGantt.push(row)
                
                if (count == rows.length) {
                    scope.prerequiste.moduleReady = true
                    console.log('2 we ready')
                }
            
            })
        },
        getTasks: function () {
            var scope = this

            scope.GET('api/tasks-chart').then(function (res) {
                
                scope.frappeGantt = []

                res.rows.forEach(function (data) {
                    var result = []
                    if(data.task != null){
                        result.push(data.task.id + '')
                    }

                    var task = data

                    task['id'] = data.id + '';
                    task['name'] = data.title;
                    task['start'] = data.task_start_date;
                    task['end'] = data.task_due_date;
                    task['progress'] = 100;
                    task['dependencies'] = result;
                    task['dependent_id'] = data.depends_id;
                    task['custom_class'] = 'bar-task';

                    scope.frappeGantt.push(task)
                
                })

            });

            scope.prerequiste.moduleReady = true

        },
    },

    mounted() {
        var scope = this

        if (scope.module_type == 'milestones') {
            scope.appendMilestoneData(scope.milestones);
        }else if(scope.module_type == 'tasks'){
            scope.getTasks();
        }
        

    }
}
</script>

