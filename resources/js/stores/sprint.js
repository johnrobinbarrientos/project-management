export default {
    strict: true, // make sure everything us mutated, no direct editing of state
    name: 'sprints',
    state: {
      sprints: [],
      sprint_loaded_pages: [],
      sprint_page: 1,
      sprint_is_loaded: false
    },
    getters: {
      getSprints: state => {
        return state.sprints
      },
      getModuleSprints: state => (moduleId) => {

        if (state.sprints.length < 1 || state.sprints == undefined) {
          return []
        }

        return state.sprints.filter(item => (item.module_id == moduleId));
      },
    },
    mutations: {
        fetchSprints (state, payload) {
            payload.rows.forEach(function (data) {
              state.sprints.push(data)
            })
        },
        createNewSprint (state, payload) {
          state.sprints.push(payload)
        }
    },
    actions: {
        fetchSprints ({ commit, state }, payload) {
            if (state.sprint_is_loaded) {
              return
            }
            window.instance.GET('/api/sprints').then(function (res) {
              state.sprint_is_loaded = true
              commit('fetchSprints', { rows: res.rows })
            });
        },
        createNewSprint ({ commit, state }, payload) {
          window.instance.POST('/api/sprints',payload).then(function (res) {
            commit('createNewSprint', res.data )
          });
        },
        moveModuleSprints ({ commit, state }, payload) {
            window.instance.PUT('/api/modules/' + payload.module_id + '/Sprints/sort',payload).then(function (res) {
                commit('fetchSprints', { rows: res.rows })
            });
        }
    }
  }