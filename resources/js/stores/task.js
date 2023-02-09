export default {
    strict: true, // make sure everything us mutated, no direct editing of state
    name: 'task',
    state: {
      tasks: [],
    },
    getters: {
      getTasks: state => {
        return state.tasks
      },
      getBoardTasks: state => (boardId) => {
        if (state.tasks.length < 1 || state.tasks == undefined) {
          return []
        }

        var tasks =  state.tasks.filter(item => (item.board_id == boardId))
        return tasks
        /*
        return tasks.sort(function(a, b) {
            return a.sort_index - b.sort_index;
        });
        */
      },
    },
    mutations: {
        fetchTasks (state, payload) {
            state.tasks = payload.rows
        },
        moveBoardTasks (state, payload) {
          var tasks = state.tasks
          var task = payload.task;
    
          for (let i = 0; i < tasks.length; i++) {
            var current = tasks[i]

            if (current.id == task.id) {
              state.tasks[i].board_id = payload.board_id
              state.tasks[i].sort_index = payload.sort_index
              break;
            }
          }
        },
        sortBoardTasks (state, payload) {
          var board_id = payload.board_id
          var tasks = state.tasks
          
          var count = payload.index
          for (let i = payload.index; i < tasks.length; i++) {
            var task = state.tasks[i]

            if (task.board_id != payload.board_id) {
              continue;
            }

            count++;
            state.tasks[i].sort_index = count
          }


        
        }

    },
    actions: {
        fetchTasks ({ commit, state }, payload) {
            window.instance.GET('/api/tasks').then(function (res) {
              // console.log('sulloodd')
              // console.log(res.rows)
                commit('fetchTasks', { rows: res.rows })
            });
        },
        moveBoardTasks ({ commit, state }, payload) {
          commit('moveBoardTasks', payload)
          window.instance.PUT('/api/boards/' + payload.board_id + '/tasks/sort',{tasks: payload.board.tasks, cloned_tasks_id: payload.cloned_tasks_id }).then(function (res) {
            // commit('sortBoardTasks', { rows: res.rows })
          });
        },
        sortBoardTasks ({ commit, state }, payload) {
           commit('sortBoardTasks', payload)

          window.instance.PUT('/api/boards/' + payload.board_id + '/tasks/sort',{tasks: payload.board.tasks}).then(function (res) {
            // commit('sortBoardTasks', { rows: res.rows })
          });
        },
        createTask({ commit, state }, payload) {
          return new Promise((resolve, reject) => {
              window.instance.POST('/api/tasks',payload).then(function (res) {
                  if (res.success) {
                      commit('createTask', res.data)
                      resolve(res);
                  } else {
                      reject(res);
                  }
                  
              });
          });
      },
    }
  }