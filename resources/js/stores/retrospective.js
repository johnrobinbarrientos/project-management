export default {
    strict: true, // make sure everything us mutated, no direct editing of state
    name: 'retrospectives',
    state: {
      Retrospectives: [],
    },
    getters: {
      getRetrospectives: state => {
        return state.Retrospectives
      },
      getModuleRetrospectives: state => (moduleId) => {

        if (state.Retrospectives.length < 1 || state.Retrospectives == undefined) {
          return []
        }

        return state.Retrospectives.filter(item => (item.module_id == moduleId));
      },
    },
    mutations: {
        fetchRetrospectives (state, payload) {
            state.Retrospectives = payload.rows
        }
    },
    actions: {
        fetchRetrospectives ({ commit, state }, payload) {
            window.instance.GET('/api/retrospectives').then(function (res) {
              // console.log('sulloodd')
              // console.log(res.rows)
                commit('fetchRetrospectives', { rows: res.rows })
            });
        },
        moveModuleRetrospectives ({ commit, state }, payload) {
            window.instance.PUT('/api/modules/' + payload.module_id + '/retrospectives/sort',payload).then(function (res) {
                commit('fetchRetrospectives', { rows: res.rows })
            });
        }
    }
  }