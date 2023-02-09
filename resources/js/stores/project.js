export default {
    strict: true, // make sure everything us mutated, no direct editing of state
    name: 'projects',
    state: {
      projects: [],
      project_loaded_pages: [],
      project_page: 1,
      project_is_loaded: false
    },
    getters: {
      getProjects: state => {
        return state.projects
      },
      getProjectBySlug: state => (slug) => {
        var projects = state.projects.filter(item => (item.slug == slug));
        return (projects.length > 0) ? projects[0] : null
      },
      getProjectByID: state => (project_id) => {
        var projects = state.projects.filter(item => (item.id == project_id));
        return (projects.length > 0) ? projects[0] : null
      },
      getModuleProjects: state => (moduleId) => {

        if (state.projects.length < 1 || state.projects == undefined) {
          return []
        }

        return state.projects.filter(item => (item.module_id == moduleId));
      },
    },
    mutations: {
        fetchProjects (state, payload) {
          payload.rows.forEach(function (data) {
            state.projects.push(data)
          })
        },
        createNewProject (state, payload) {
          state.sprints.push(payload)
        }
    },
    actions: {
        fetchProjects ({ commit, state }, payload) {
            if (state.project_is_loaded) {
              return
            }
            window.instance.GET('/api/projects').then(function (res) {
                state.project_is_loaded = true
                commit('fetchProjects', { rows: res.rows })
            });
        },
        createNewProject ({ commit, state }, payload) {
          window.instance.POST('/api/projects',payload).then(function (res) {
            commit('createNewProject', res.data )
          });
        },
        moveModuleProjects ({ commit, state }, payload) {
            window.instance.PUT('/api/modules/' + payload.module_id + '/Projects/sort',payload).then(function (res) {
                commit('fetchProjects', { rows: res.rows })
            });
        }
    }
  }