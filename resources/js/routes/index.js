import { createWebHistory, createRouter } from "vue-router";

import Home from '../views/Home'
import Users from '../views/Users'
import Projects from '../views/Projects'
import Milestones from '../views/Milestones'
import Modules from '../views/Modules'
import Roles from '../views/Roles'
import Tags from '../views/Tags'
import Tasks from '../views/Tasks'
import Priority from '../views/Priority'
import Efforts from '../views/Effort'
import Departments from '../views/Departments'
import ItemTypes from '../views/ItemTypes'
import Sprints from '../views/Sprints'
import Retrospective from '../views/Retrospective'
import UserGroup from '../views/UserGroup'
import UserTypes  from '../views/UserTypes'
import Authentication from '../views/Authentication'
import ForgotPassword from '../views/ForgotPassword'
import ResetPassword from '../views/ResetPassword'
import Verification from '../views/Verification'
import Register  from '../views/Register'


function checkAuth (to, from, next) {
    
    // check if route is protected
    if (to.meta.protected) {
      var token = localStorage.getItem(window.TOKEN_KEY)
      if (token && token !== null && token !== '' ) {
        // next() // allow to enter route
        checkRoles (to, from, next);
      } else {
        next('/auth?redirect=' + window.location.href) // go to '/auth';
      }
    } else {
      next()
    }
}

function checkRoles (to, from, next) {
    
     var roles = [];

    // check if route is protected
    if (to.meta.rolbased) {
        next() // allow to enter route
    } else {
      next()
    }
}

function checkGuess (to, from, next) {
    // check if route is protected
    var token = localStorage.getItem(window.TOKEN_KEY)
    if (token && token !== null && token !== '' ) {
    next('/')// allow to enter route
    } else {
    next() // go to '/auth';
    }
}


const routes =  [
    {
        path: '/',
        name: 'home',
        component: Home,
        beforeEnter: checkAuth,
        meta: { layout: 'with-side-bar', protected: true },
    },
    {
        path: '/users',
        name: 'users',
        component: Users,
        beforeEnter: checkAuth,
        meta: { layout: 'with-side-bar', protected: true },
    },
    {
        path: '/projects',
        name: 'projects',
        component: Projects,
        beforeEnter: checkAuth,
        meta: { layout: 'with-side-bar', protected: true },
    },
    {
        path: '/milestones',
        name: 'milestones',
        component: Milestones,
        beforeEnter: checkAuth,
        meta: { layout: 'with-side-bar', protected: true },
    },
    {
        path: '/modules',
        name: 'modules',
        component: Modules,
        beforeEnter: checkAuth,
        meta: { layout: 'with-side-bar', protected: true },
    },
    {
        path: '/roles',
        name: 'roles',
        component: Roles,
        beforeEnter: checkAuth,
        meta: { layout: 'with-side-bar', protected: true },
    },
    {
        path: '/tasks',
        name: 'tasks',
        component: Tasks,
        beforeEnter: checkAuth,
        meta: { layout: 'with-side-bar', protected: true },
    },
    {
        path: '/projects/:project_slug/todos',
        name: 'projecttodos',
        component: Tasks,
        beforeEnter: checkAuth,
        meta: { layout: 'with-side-bar', protected: true },
    },
    {
        path: '/tags',
        name: 'tags',
        component: Tags,
        beforeEnter: checkAuth,
        meta: { layout: 'with-side-bar', protected: true },
    },
    {
        path: '/priorities',
        name: 'priorities',
        component: Priority,
        beforeEnter: checkAuth,
        meta: { layout: 'with-side-bar', protected: true },
    },
    {
        path: '/efforts',
        name: 'efforts',
        component: Efforts,
        beforeEnter: checkAuth,
        meta: { layout: 'with-side-bar', protected: true },
    },
    {
        path: '/departments',
        name: 'departments',
        component: Departments,
        beforeEnter: checkAuth,
        meta: { layout: 'with-side-bar', protected: true },
    },
    {
        path: '/item-types',
        name: 'itemtypes',
        component: ItemTypes,
        beforeEnter: checkAuth,
        meta: { layout: 'with-side-bar', protected: true },
    },
    {
        path: '/retrospectives',
        name: 'retrospectives',
        component: Retrospective,
        beforeEnter: checkAuth,
        meta: { layout: 'with-side-bar', protected: true },
    },
    {
        path: '/sprint',
        name: 'sprints',
        component: Sprints,
        beforeEnter: checkAuth,
        meta: { layout: 'with-side-bar', protected: true },
    },
    {
        path: '/user-groups',
        name: 'usergroup',
        component: UserGroup,
        beforeEnter: checkAuth,
        meta: { layout: 'with-side-bar', protected: true },
    },
    {
        path: '/user-types',
        name: 'usertypes',
        component: UserTypes,
        beforeEnter: checkAuth,
        meta: { layout: 'with-side-bar', protected: true },
    },
    {
        path: '/register',
        name: 'register',
        component: Register,
        meta: { layout: 'no-side-bar', protected: false },
        beforeEnter: checkGuess,
    },
    {
        path: '/auth',
        name: 'authentication',
        component: Authentication,
        meta: { layout: 'no-side-bar', protected: false },
        beforeEnter: checkGuess,
    },
    {
        path: '/forgot-password',
        name: 'forgotpassword',
        component: ForgotPassword,
        meta: { layout: 'no-side-bar', protected: false },
        beforeEnter: checkGuess,
    },
    {
        path: '/reset-password/:token',
        name: 'resetpassword',
        component: ResetPassword,
        meta: { layout: 'no-side-bar', protected: false },
        beforeEnter: checkGuess,
    },
    {
        path: '/verify/:token',
        name: 'verification',
        component: Verification,
        meta: { layout: 'no-side-bar', protected: false },
        beforeEnter: checkGuess,
    },
]

const router = createRouter({
    history: createWebHistory(),
    routes,
  });
  
export default router;

