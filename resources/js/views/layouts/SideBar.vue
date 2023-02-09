<template>
<div>
    <div class="bp-sidebar">
        <div class="bp-logo">SKELETON</div>
        <div class="bp-navigation">
            <perfect-scrollbar style="max-height:calc(100vh - 40px);">
            <ul style="padding-bottom:40px;">
                <li><router-link to="/">Dashboard</router-link></li>
                <li class="has-submenu" v-bind:class="{'open' : is_project_open }">
                    <a href="javascript:void(0);" @click="toggleProject()"><router-link to="/projects">Projects</router-link></a> 
                    <span v-if="is_project_open" class="has-submenu-icon ti-angle-up"></span>
                    <span v-else class="has-submenu-icon ti-angle-down"></span>
                    <perfect-scrollbar style="max-height:400px;">
                    <ul>
                        <li @click="flagProject(project.title)" :class="{'active' : ($route.name + '-' + project.slug) == 'ProjectTodos-' +  $route.params.project_slug }" v-for="project in projects" :key="'project-' + project.id">
                            <router-link :to="'/projects/' + project.slug + '/todos'">{{ project.title }}</router-link>
                        </li>
                        <li><a v-if="!project_filters.hide_load_more_button" href="javscript:void(0);" @click="viewMore()">View More</a></li>
                    </ul>
                    </perfect-scrollbar>
                </li>
               
                <li v-if="isAccesible('tasks')" :class="{'active' : $route.name == 'tasks' }"><router-link to="/tasks">Tasks</router-link></li>
                <li v-if="isAccesible('milestones')" :class="{'active' : $route.name == 'milestones' }"><router-link to="/milestones">Milestones</router-link></li>
                <li v-if="isAccesible('sprints')" :class="{'active' : $route.name == 'sprints' }"><router-link to="/sprint">Sprints</router-link></li>                
                <li v-if="isAccesible('retrospectives')" :class="{'active' : $route.name == 'retrospectives' }"><router-link to="/retrospectives">Retrospectives</router-link></li>
                
                <li class="has-submenu" v-bind:class="{'open' : is_settings_open }">
                    <a href="javascript:void(0);" @click="toggleSettings()">Settings</a> 
                    <span v-if="is_settings_open" class="has-submenu-icon ti-angle-up"></span>
                    <span v-else class="has-submenu-icon ti-angle-down"></span>
                    <ul>
                        <li v-if="isAccesible('modules')" :class="{'active' : $route.name == 'modules' }"><router-link to="/modules">Modules</router-link></li>
                        <li v-if="isAccesible('roles')" :class="{'active' : $route.name == 'roles' }"><router-link to="/roles">Roles</router-link></li>
                        <li v-if="isAccesible('efforts')" :class="{'active' : $route.name == 'efforts' }"><router-link to="/efforts">Efforts</router-link></li>
                        <li v-if="isAccesible('departments')" :class="{'active' : $route.name == 'departments' }"><router-link to="/departments">Departments</router-link></li>
                        <li v-if="isAccesible('itemtypes')" :class="{'active' : $route.name == 'itemtypes' }"><router-link to="/item-types">Item Types</router-link></li>
                        <li v-if="isAccesible('tags')" :class="{'active' : $route.name == 'tags' }"><router-link to="/tags">Tags</router-link></li>
                        <li v-if="isAccesible('priorities')" :class="{'active' : $route.name == 'priorities' }"><router-link to="/priorities">Priorities</router-link></li>
                        <li v-if="isAccesible('users')" :class="{'active' : $route.name == 'users' }"><router-link to="/users">Users</router-link></li>
                        <li v-if="isAccesible('usergroup')" :class="{'active' : $route.name == 'usergroup' }"><router-link to="/user-groups">User Groups</router-link></li>
                    </ul>
                </li>
            </ul>
            </perfect-scrollbar>
        </div>
    </div>
</div>
<!-- sidebar @e -->
</template>

<script>
export default {
        data: function () {
        return {
            is_settings_open: false,
            is_project_open: false,
            menus: [],
            projects: [],
            project_filters: {
                hide_load_more_button: false,
                page: 1,
                take: 2
            }
        }
    },
    computed: {
    },
    methods: {
        toggleSettings: function () {
            var scope = this
            scope.is_settings_open = !scope.is_settings_open
        },
        toggleProject: function () {
            var scope = this
            this.$router.push({ path: '/projects' })
            scope.is_project_open = !scope.is_project_open
        },
        getProjects: function () {
            var scope = this
            scope.GET('/api/projects?page=' + scope.project_filters.page + '&take=' + scope.project_filters.take).then(function (res) {
                
                res.rows.forEach(function (data) {
                    scope.projects.push(data)
                })

                if (res.rows.length < 1) {
                    scope.project_filters.hide_load_more_button = true
                }
            });
        },
        getMenus: function () {
            var scope = this
            scope.GET('/api/menus').then(function (res) {
                res.rows.forEach(function (data) {
                    scope.menus.push(data)
                })
            });
        },
        viewMore: function () {
            var scope = this
            scope.project_filters.page += 1;
            scope.getProjects();
        },
        flagProject: function (project_name) {
            var scope = this
            console.log(project_name)
            scope.$store.dispatch('changeHeader', project_name)
        },
        isAccesible: function (key) {
            var scope = this
            return scope.menus.includes(key)
        }
    },
    mounted() {
        var scope = this
        scope.getProjects();
        scope.getMenus();
    }
}
</script>

<style scoped>
.bp-sidebar {   position: fixed; top: 0px; left: 0px; width: 350px; height: 100vh; background: #2a3042; }
.bp-logo { height:45px; line-height:45px; padding-left:10px; padding-right:10px; text-align: center; color:#fff; font-weight:600; border-bottom:1px solid #202534; }

.bp-navigation ul { margin-top:10px; list-style: none; }
.bp-navigation ul li  { padding:0px 20px; line-height:45px; }
.bp-navigation ul li:hover  { background:#495064; cursor:pointer; }
.bp-navigation ul li a {  font-weight:600; display:inline-block; width:100%; text-decoration:none; color:#7a84a0; }
.bp-navigation ul li:hover a { color:#fff; }

.bp-sidebar .has-submenu { position:relative; }
.bp-sidebar .has-submenu.open { background:#191E2C; }

.bp-sidebar .has-submenu .has-submenu-icon { font-size:14px; color:#7a84a0; position:absolute; top:16px; right:17px; }
.bp-sidebar .has-submenu  ul { display:none; }
.bp-sidebar .has-submenu.open  ul { display:block; }
.bp-sidebar .has-submenu.open  ul li.active { background:#fff; border-radius:3px;  }
.bp-sidebar .has-submenu.open  ul li.active a { color:#000;  }

.bp-navigation ul li.active { background:#4267b2; }
.bp-navigation ul li.active a { color:#fff; }
</style>