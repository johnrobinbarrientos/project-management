// Mixins are a flexible way to distribute reusable functionalities for Vue components.
import moment from 'moment'
export default {
    name: 'middleware',
    data () {
      return {
        msg: ''
      }
    },
    computed: {
        auth: function() {
            return this.$store.getters.getUser;
        }
    },
    methods: {
      VALIDATE_MODULE_ROLES: function (module) {
        var scope = this
        console.log(module)
        console.log(scope.auth)
      }
    }
}