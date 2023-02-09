export default {
    strict: true, // make sure everything us mutated, no direct editing of state
    name: 'users',
    state: {
        users: [],
    },
    getters: {
      getUsers: state => {
        return state.users
      }
    },
    mutations: {
        fetchUsers (state, payload) {
            state.users = payload.rows
        }
    },
    actions: {
        fetchUsers ({ commit, state }, payload) {
            window.instance.GET('/api/users').then(function (res) {
                commit('fetchUsers', { rows: res.rows })
            });
        }
    }
  }