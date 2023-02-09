// Mixins are a flexible way to distribute reusable functionalities for Vue components.
import moment from 'moment'
export default {
    name: 'global',
    data () {
      return {
        msg: ''
      }
    },
    methods: {
        TOGGLE_COLUMN: function (module,c) {
            var scope = this
            c.hidden = !c.hidden

            var key = module + '_hidden_columns'
            var hidden_columns = scope.GET_LOCALSTORAGE_OBJECT(key);
            
            hidden_columns = (!Array.isArray(hidden_columns)) ? [] : hidden_columns

            if (c.hidden) {
                hidden_columns.push(c.column)
            } else {
                var index = hidden_columns.indexOf(c.column)
                hidden_columns.splice(index,1)
            }

            scope.SET_LOCALSTORAGE_OBJECT(key,hidden_columns)
        },
        IS_COLUMN_HIDDEN: function (module,column) {
            var scope = this
            var key = module + '_hidden_columns'

            var hidden_columns = scope.GET_LOCALSTORAGE_OBJECT(key);
            hidden_columns = (!Array.isArray(hidden_columns)) ? [] : hidden_columns

            var hidden = (hidden_columns.includes(column)) ? true : false

            return hidden
        },
        INITIALIZE_MODULE_COLUMN: function (module,columns) {
            var scope = this
            var key = module + '_hidden_columns'

            var hidden_columns = scope.GET_LOCALSTORAGE_OBJECT(key);
            hidden_columns = (!Array.isArray(hidden_columns)) ? [] : hidden_columns

            columns.forEach(function (data) {
                var hidden = (hidden_columns.includes(data.column)) ? true : false
                data.hidden = hidden
            });

            return columns
        }
    }
}