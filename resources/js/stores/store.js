import { createStore } from 'vuex'

import auth  from './auth.js'
import task from './task.js'
import board  from './board.js'
import project  from './project.js'
import milestone  from './milestone.js'
import status  from './status.js'
import module  from './module.js'
import header  from './header.js'
import sprint  from './sprint.js'
import retrospective  from './retrospective.js'
import tags  from './tags.js'
import departments  from './department.js'
import users  from './user.js'
import efforts  from './effort.js'

export const store = createStore({
    //strict: true, // make sure everything us mutated, no direct editing of state
    name: 'store',
    modules: {
      auth: auth,
      task: task,
      project: project,
      milestone: milestone,
      module: module,
      board: board,
      header: header,
      sprint: sprint,
      retrospective: retrospective,
      status: status,
      tags: tags,
      departments: departments,
      users: users,
      efforts: efforts,
    }
  })