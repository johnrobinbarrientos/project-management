export default {
    strict: true, // make sure everything us mutated, no direct editing of state
    name: 'tag',
    state: {
        tags: [],
    },
    getters: {
      getTags: state => {
        return state.tags
      }
    },
    mutations: {
        fetchTags (state, payload) {
            state.tags = payload.rows
        },
        addNewTag (state, payload) {
            state.tags.unshift({
                id: payload.id,
                title: payload.title,
                project_id: payload.project_id,
            })
        }
    },
    actions: {
        fetchTags ({ commit, state }, payload) {
            window.instance.GET('/api/tags').then(function (res) {
                commit('fetchTags', { rows: res.rows })
            });
        },
        addNewTag ({ commit, state }, payload) {
            window.instance.GET('/api/tags').then(function (res) {
                commit('addNewTag', payload)
            });
        },
    }
  }