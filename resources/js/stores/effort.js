export default {
    strict: true, // make sure everything us mutated, no direct editing of state
    name: 'effort',
    state: {
        efforts: [],
    },
    getters: {
      getBoards2: state => {
        return state.efforts
      },
    },
    mutations: {
        fetchEfforts (state, payload) {
            state.boards = payload.rows
        }
    },
    actions: {
        fetchEfforts ({ commit, state }, payload) {
            window.instance.GET('/api/efforts').then(function (res) {
                commit('fetchEfforts', { rows: res.rows })
            });
        },
    }
  }