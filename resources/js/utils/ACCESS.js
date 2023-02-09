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
        CAN_WRITE: function (name) {
            var scope = this
            var check = scope.$store.getters.userCanWriteOn(name)
            return (check !== undefined) ? check : false
        },
        CAN_EDIT: function (name) {
            var scope = this
            var check = scope.$store.getters.userCanEditOn(name)
            return (check !== undefined) ? check : false
        },
        CAN_READ: function (name) {
            var scope = this
            var check = scope.$store.getters.userCanReadOn(name)
            return (check !== undefined) ? check : false
        },
        CAN_DELETE: function (name) {
            var scope = this
            var check = scope.$store.getters.userCanDeleteOn(name)
            return (check !== undefined) ? check : false
        },
    }
}