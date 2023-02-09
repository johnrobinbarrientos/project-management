export default {
    strict: true, // make sure everything us mutated, no direct editing of state
    name: 'departments',
    state: {
        departments: [],
    },
    getters: {
      getDepartments: state => {
        return state.departments
      }
    },
    mutations: {
        fetchDepartments (state, payload) {
            state.departments = payload.rows
        }
    },
    actions: {
        fetchDepartments ({ commit, state }, payload) {
            window.instance.GET('/api/departments').then(function (res) {
                commit('fetchDepartments', { rows: res.rows })
            });
        }
    }
  }