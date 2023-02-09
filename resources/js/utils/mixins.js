// Mixins are a flexible way to distribute reusable functionalities for Vue components.
import moment from 'moment'
export default {
    name: 'mixins',
    data () {
      return {
        msg: ''
      }
    },
    methods: {
        CHECK_VUE_INSTANCE: function () {
            console.log(this)
        },
        UNIQUE: function () {
            let key = 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function (c) {
                // eslint-disable-next-line one-var
                var r = Math.random() * 16 | 0, v = c === 'x' ? r : (r & 0x3 | 0x8)
                return v.toString(16)
            })
            return key
        },
        LOCALIZED_UTC_DATE: function (date,format) {
            var stillUtc = moment.utc(date).toDate();
            var formatted = moment(stillUtc).local().format(format)
            return  formatted
        },
        FORMAT_DATE: function (date,format = 'MM-DD-YYYY') {
            moment.updateLocale(moment.locale(), { invalidDate: "Not Set" })
            return moment(date).format(format);
        },
        ELLIPSIS: function (text,length) {
            // '<div>Hello</div>' ==> 'Hello'
            text = text.replace(/<\/?[^>]+(>|$)/g, "");
            if (text.length > length) {
                return text.substring(0, length) + '...';
            }
            return text;
        },
        RESPONSE: function (action,data) {
            var scope = this
            
            if (action === 'notify') {

            }

            if (data.status == 401) {
                localStorage.removeItem(window.TOKEN_KEY)
                this.$router.push({name: 'auth'})
            }
        },
        SET_COOKIE: function (cname, cvalue, exdays) {
            var d = new Date();
            d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
            var expires = "expires="+d.toUTCString();
            document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
        },
        GET_COOKIE: function (cname) {
            var name = cname + "=";
            var ca = document.cookie.split(';');
            for(var i = 0; i < ca.length; i++) {
                var c = ca[i];
                while (c.charAt(0) == ' ') {
                c = c.substring(1);
                }
                if (c.indexOf(name) == 0) {
                    return c.substring(name.length, c.length);
                }
            }
            return null;
        },
        ROUTE: function (data) {
            var scope = this
            scope.$router.push(data)
        },
        OBJECT_TO_QUERYSTRING :  function(obj) {
            var str = [];
            for (var p in obj)
              if (obj.hasOwnProperty(p)) {
                str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
              }
            return str.join("&");
        },
        SET_LOCALSTORAGE_OBJECT: function (key,obj) {
            localStorage.setItem(key, JSON.stringify(obj));
        },
        GET_LOCALSTORAGE_OBJECT: function (key) {

            if (localStorage.getItem(key) === null) {
                return '';
            }

            return JSON.parse(localStorage.getItem(key));
        },
        CHECK_OBJECT_KEY_VALUE: function (obj,key) {
            var value = (obj[key] && (obj[key] !== undefined || obj[key] !== null) ) ? obj[key] : false
        },
        MODAL_TOGGLE: function (id) {
            var modal = document.getElementById(id);
            if (modal.classList.contains("open")) {
                modal.classList.remove("open");
            } else {
                modal.classList.add("open");
            }
        },
        CLONE_OBJECT: function (obj) {
            return JSON.parse(JSON.stringify(obj));
        },
        SORT_OBJECT: function (obj,key,direction) {

            var sortKey = key
            var sortDirection = direction

            return obj.sort((a, b) => {
                let compare = 0;

                var keys = sortKey.split('.')

                if (keys.length == 1) {
                    var A = a[sortKey]
                    var B = b[sortKey]
                } else {
                    var k1 = keys[0]
                    var k2 = keys[1]

                    var A = (a[k1]) ? a[k1][k2]: 'zzzzz'
                    var B = (b[k1]) ? b[k1][k2]: 'zzzzz'
                }
                
                if (sortDirection == 'asc') {
                    if (A > B) {
                        compare = 1;
                    } else if (B > A) {
                        compare = -1;
                    }
                } else {
                    if (A < B) {
                        compare = 1;
                    } else if (B < A) {
                        compare = -1;
                    }
                }
                
                return compare;
            });
        },
        POST: function (URL,payload) {
            var scope = this
            // scope.credentials.error = null
            return scope.axios
            .post(URL, payload,{
                'headers': {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Authorization': 'Bearer ' + localStorage.getItem(window.TOKEN_KEY)
                }
            })
            .then(response => {
                var data = response.data
                return data
            })
            .catch(function (error) {

                var data = error.response.data
                var code = error.response.status
                data.code = code
                data.success = 0

                return data
            })
        },
        GET: function (URL,payload = {}) {
            var scope = this
            // scope.credentials.error = null
            return scope.axios
            .get(URL,{
                params: payload,
                'headers': {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Authorization': 'Bearer ' + localStorage.getItem(window.TOKEN_KEY)
                }
            })
            .then(response => {
                var data = response.data
                return data
            })
            .catch(function (error) {
                var data = error.response.data
                var code = error.response.status
                data.code = code
                data.success = 0

                return data
            })
        },
        DELETE: function (URL,payload = {}) {
            var scope = this
            // scope.credentials.error = null
            return scope.axios
            .delete(URL,{
                params: payload,
                'headers': {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Authorization': 'Bearer ' + localStorage.getItem(window.TOKEN_KEY)
                }
            })
            .then(response => {
                var data = response.data
                if (data.success) {
                    return data
                }
            })
            .catch(function (error) {
                var data = error.response.data
                var code = error.response.status
                data.code = code
                data.success = 0

                return data
            })
        },
        PUT: function (URL,payload) {
            var scope = this
            // scope.credentials.error = null
            return scope.axios
            .put(URL, payload,{
                'headers': {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Authorization': 'Bearer ' + localStorage.getItem(window.TOKEN_KEY)
                }
            })
            .then(response => {
                var data = response.data
                if (data.success) {
                    return data
                }
            })
            .catch(function (error) {
                var data = error.response.data
                var code = error.response.status
                data.code = code
                data.success = 0
                
                return data
            })
        }
    }
}