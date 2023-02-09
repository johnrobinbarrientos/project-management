
export default {
    strict: true, // make sure everything us mutated, no direct editing of state
    name: 'auth',
    state: {
      user: null,
    },
    getters: {
      getUser: state => {
        return state.user
      },
      getUserType: state => {
        return (state.user) ? state.user.type : 'Guest'
      },
    },
    mutations: {
        authenticate (state, payload) {
            state.user = payload
        },
        setUser (state, payload) {
          state.user = payload
        }
    },
    actions: {
        authenticate ({ commit, state }, payload) {
            commit('authenticate', payload)
        }
    }
  }
