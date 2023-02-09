// Mixins are a flexible way to distribute reusable functionalities for Vue components.
import moment from 'moment'
export default {
    name: 'getters',
    data () {
      return {
        msg: ''
      }
    },
    methods: {
        GET_MILESTONES: function () {
            scope.$store.dispatch('fetchMilestones')
        },
        GET_PROJECTS: function () {
            scope.$store.dispatch('fetchProjects')
        },
        GET_PROJECTS: function () {
            scope.$store.dispatch('fetchProjects')
        },
    }
}