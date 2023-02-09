export default {
    strict: true, // make sure everything us mutated, no direct editing of state
    name: 'milestones',
    state: {
      milestones: [],
      milestone_loaded_pages: [],
      milestone_page: 1,
      milestone_is_loaded: false
    },
    getters: {
      getMilestones: state => {
        return state.milestones
      },
    },
    mutations: {
      fetchMilestones (state, payload) {
          if (state.milestone_is_loaded) {
            return
          }
          payload.rows.forEach(function (data) {
            state.milestone_is_loaded = true
            state.milestones.push(data)
          })
        },
        createNewMilestone (state, payload) {
          state.milestones.push(payload)
        }
    },
    actions: {
      fetchMilestones ({ commit, state }, payload) {
        window.instance.GET('/api/milestones').then(function (res) {
          commit('fetchMilestones', { rows: res.rows })
        });
      },
      createNewMilestone ({ commit, state }, payload) {
        window.instance.POST('/api/milestones',payload).then(function (res) {
          commit('createNewMilestone', res.data )
        });
      }
    }
  }