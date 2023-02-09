export default {
    strict: true, // make sure everything us mutated, no direct editing of state
    name: 'header',
    state: {
      header: '',
    },
    getters: {
      getHeader: state => {
        return state.header
      }
    },
    mutations: {
        mutateHeader (state, payload) {
            state.header = payload
        }
    },
    actions: {
        changeHeader ({ commit, state }, payload) {
            commit('mutateHeader', payload)
        },

    }
  }