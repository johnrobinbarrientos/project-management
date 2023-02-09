export default {
    strict: true, // make sure everything us mutated, no direct editing of state
    name: 'board',
    state: {
      boards: [],
    },
    getters: {
      getBoards: state => {
        return state.boards
      },
      getModuleBoards: state => (moduleId) => {
        if (state.boards == undefined || state.boards.length < 1) {
          return []
        }

        return state.boards.filter(item => (item.module_id == moduleId));
      },
    },
    mutations: {
        fetchBoards (state, payload) {
            state.boards = payload.rows
        }
    },
    actions: {
        fetchBoards ({ commit, state }, payload) {
            window.instance.GET('/api/boards').then(function (res) {
                commit('fetchBoards', { rows: res.rows })
            });
        },
        moveModuleBoards ({ commit, state }, payload) {
            window.instance.PUT('api/modules/' + payload.module_id + '/boards/sort',payload).then(function (res) {
                commit('fetchBoards', { rows: res.rows })
            });
        }
    }
  }