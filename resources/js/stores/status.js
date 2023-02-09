export default {
    strict: true, // make sure everything us mutated, no direct editing of state
    name: 'status',
    state: {
      status: [],
      status_loaded_pages: [],
      status_page: 1,
      status_is_loaded: false
    },
    getters: {
      getStatus: state => {
        return state.status
      },
    },
    mutations: {
      fetchStatus (state, payload) {
          if (state.status_is_loaded) {
            return
          }
          payload.rows.forEach(function (data) {
            state.status_is_loaded = true
            state.status.push(data)
          })
        }
    },
    actions: {
      fetchStatus ({ commit, state }, payload) {
            window.instance.GET('/api/status').then(function (res) {
                commit('fetchStatus', { rows: res.rows })
            });
        }
    }
  }