export default {
    strict: true, // make sure everything us mutated, no direct editing of state
    name: 'modules',
    state: {
      modules: [],
      module_user_settings: [],
    },
    getters: {
      getModules: state => {
        return state.modules
      },
      getModuleUserSettings: state => {
        return state.module_user_settings
      },
      userCanWriteOn: state => (moduleName) => {
        if (!state.module_user_settings[moduleName]) {
          return false
        }

        return state.module_user_settings[moduleName].WRITE
      },
      userCanReadOn: state => (moduleName) => {
        if (!state.module_user_settings[moduleName]) {
          return false
        }

        return state.module_user_settings[moduleName].READ
      },
      userCanEditOn: state => (moduleName) => {
        if (!state.module_user_settings[moduleName]) {
          return false
        }

        return state.module_user_settings[moduleName].EDIT
      },
      userCanDeleteOn: state => (moduleName) => {
        if (!state.module_user_settings[moduleName]) {
          return false
        }
        
        return state.module_user_settings[moduleName].DELETE
      },
    },
    mutations: {
        fetchModules (state, payload) {
          payload.rows.forEach(function (data) {
            state.modules.push(data)
          })
        },
        fetchModuleUserSettings (state, payload) {
          state.module_user_settings =  payload.rows
        },
    },
    actions: {
        fetchModules ({ commit, state }, payload) {
            window.instance.GET('/api/modules').then(function (res) {
                commit('fetchModules', { rows: res.rows })
            });
        },
        fetchModuleUserSettings ({ commit, state }, payload) {
          window.instance.GET('/api/modules/user-settings').then(function (res) {
              commit('fetchModuleUserSettings', { rows: res.rows })
          });
      }
    }
  }