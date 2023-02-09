<template>
    <div>
        <div id="todos-page" v-if="module_loaded">
            <div class="page-actions">
                <div class="page-actions-inner">
                    <button v-if="CAN_WRITE('Tasks')" @click="toggleModal(true)" class="bp-bordered-boxed"> <span class="ti-plus"></span></button>
                    <button @click="toggleFilter()" class="bp-bordered-boxed"> <span class="ti-filter"></span></button>
                    &nbsp;&nbsp;&nbsp;

                    <button @click="switchView('table_view')" class="bp-bordered-boxed" :class="{'active' : view == 'table_view' }"> <span class="ti-view-list-alt"></span></button>
                    <button @click="switchView('kanban_view')" class="bp-bordered-boxed" :class="{'active' : view == 'kanban_view' }"> <span class="ti-layout-grid3"></span></button>
                    <button @click="switchView('gantt_view')" class="bp-bordered-boxed" :class="{'active' : view == 'gantt_view' }"> <span class="ti-bar-chart"></span></button>
                    &nbsp;&nbsp;&nbsp;

                    <button @click="toggleBulkEditModal(true)" class="bp-bordered-boxed" :class="{'disabled' : (selected_ids.length < 1) }"> <span class="ti-pencil"></span></button>
                    <button @click="toggleDuplicateTaskModal(true)" class="bp-bordered-boxed" :class="{'disabled' : (selected_ids.length < 1) }"> <span class="ti-layers"></span></button>
                    <button @click="deleteBulk()" class="bp-bordered-boxed" :class="{'disabled' : (selected_ids.length < 1) }"> <span class="ti-trash"></span></button>

                    <div v-if="!project_slug && view == 'kanban_view'" style="margin-left:10px; padding:20px 0px; ">
                        <select v-model="project_id" style="height:35px; padding:0px 10px;">
                            <option :value="null">All</option>
                            <option :value="project_item.id" v-for="project_item in projects" :key="'opt-project-' + project_item.id ">{{ project_item.title }}</option>
                        </select>
                    </div>
                </div>
            </div>

            <div v-if="show_filter" class="page-filter" style="display:flex;">
                <div style="max-width:320px;">
                    <input v-on:keyup.enter="search()" v-model="filter.keyword" type="text">

                    <div class="tag-keyword" v-for="k,index in filter.keywords" :key="'keyword-' + index">
                        {{ k }}
                        <i @click="removeSearchKeyword(k)" class="ti-close"></i>
                    </div>
                </div>
                <div>
                    <input v-model="filter.start_date" type="date">
                </div>
                <div>
                    <input v-model="filter.due_date" type="date">
                </div>
                <div>
                    <multiselect  
                        v-model="filter.project_ids"
                        :options="options_project" 
                        track-title="id" 
                        label="title" 
                        :multiple="true"
                        :taggable="true"
                        >
                    </multiselect>
                </div>
            </div>

            <div v-if="view == 'table_view'" class="table-view">
                <div class="datatable">
                    <div class="tr header">
                        <div class="th" style="max-width: 40px;"></div>
                        <div class="th">Title</div>
                        <div class="th">Description</div>
                        <div class="th">Task Start Date</div>
                        <div class="th">Task Due Date</div>
                        <div class="th">Completed Date</div>
                        
                        <div class="th">Milestone</div>
                        <div class="th">Project</div>
                        <div class="th">Assign</div>
                        <div class="th">Tags</div>
                        <div class="th">Sprint</div>
                        <div class="th">Task Time</div>
                        <div class="th" style="max-width:140px;">Action</div>
                    </div>
                    
                    
                    <div v-for="row in items" :key="row.id" class="tr">
                        <template v-if="!row.edit">
                                <div class="td" style="max-width: 40px;">
                                    <input @change="toggleSelectedItem(row.id)" type="checkbox">
                                </div>
                                <div class="td" @dblclick="inlineEditRow(row)">{{ row.title }}</div>
                                <div class="td" @dblclick="inlineEditRow(row)">{{ row.description }}</div>
                                <div class="td" @dblclick="inlineEditRow(row)">{{ row.task_start_date }}</div>
                                <div class="td" @dblclick="inlineEditRow(row)">{{ row.task_due_date }}</div>
                                <div class="td" @dblclick="inlineEditRow(row)">{{ row.task_completed_date }}</div>

                                <div class="td" @dblclick="inlineEditRow(row)" v-if="row.milestone == null">N/A</div>
                                <div class="td" @dblclick="inlineEditRow(row)" v-if="row.milestone != null">{{ row.milestone.title }}</div>

                                <div class="td" @dblclick="inlineEditRow(row)" v-if="row.project == null">N/A</div>
                                <div class="td" @dblclick="inlineEditRow(row)" v-if="row.project != null">{{ row.project.title }}</div>

         

                                <div class="td">
                                    <span v-if="row.task_users.length > 0">
                                        <span v-for="item in row.task_users" :key="item.id">
                                            <span class="bp-tag">
                                            {{ item.user.firstname }}</span>
                                        </span>
                                    </span>
                                    <span v-else></span>
                                </div>
                                <div  class="td">
                                    <div  v-if="row.tags && row.tags.length > 0">
                                        <span class="bp-tag" :style="{'background' : tag.background, 'color' : tag.color}" v-for="tag in row.tags" :key="tag.id">
                                            {{ tag.title }}
                                        </span>
                                    </div>
                                </div>

                                <div class="td" v-if="row.sprint != null">{{ row.sprint.title }}</div>
                                <div class="td">{{ row.task_duration }}</div>

                                <div class="td" style="max-width:140px;">
                                    <button v-if="CAN_EDIT('Tasks')" @click="toggleModalNew(row)" class="bp-btn bp-btn-blue">New Edit</button>
                                    <button v-if="CAN_EDIT('Tasks')" @click="toggleModal(true,row)" class="bp-btn bp-btn-blue">Edit</button>
                                    <button v-if="CAN_DELETE('Tasks')" @click="toggleModal(true)" class="bp-btn bp-btn-red">Delete</button>
                                </div>
                        </template>
                        <template v-else>
                            <div class="tr">
                                <div class="td" style="max-width: 40px;"></div>
                                <div class="td"><input v-model="row.title" type="text" placeholder="Enter title here.."></div>
                                <div class="td"><input v-model="row.description" type="text" placeholder="Enter description here.."></div>
                                <div class="td"><input v-model="row.task_start_date" type="date"></div>
                                <div class="td"><input v-model="row.task_due_date" type="date"></div>
                                <div class="td"><input v-model="row.task_completed_date" type="date"></div>
                                <div class="td">
                                    <div class="form-control-wrap">
                                        <multiselect  v-model="row.milestone" 
                                        :options="options_milestone" 
                                        track-by="id" 
                                        label="title" 
                                        :multiple="false"
                                        :taggable="true"
                                        @tag="createNewMilestone" 
                                        >
                                        </multiselect>
                                    </div>

                                </div>
                                <div class="td">
                                    <div class="form-control-wrap">
                                        <multiselect  v-model="row.project" 
                                        :options="options_project" 
                                        track-title="id" 
                                        label="title" 
                                        :multiple="false"
                                        :taggable="true"
                                        @tag="createNewProject" 
                                        >
                                        </multiselect>
                                    </div>
                                </div>
                                <div class="td">
                                    
                                </div>
                                <div  class="td">
                                    <multiselect  v-model="row.tags" 
                                        :options="options_tags" 
                                        track-by="id" 
                                        label="title" 
                                        :multiple="true"
                                        :taggable="true"
                                        @tag="inlineCreateNewTag"  
                                        >
                                    </multiselect>
                                </div>

                                <div class="td actions" style="max-width:100px;">
                                    <button @click="save(false,row)" class="bp-btn bp-btn-blue">Save</button>
                                </div>
                            </div>
                        </template>
                    </div>



                    <!-- 
                    <div v-if="!filter.is_loading" class="tr">
                        <div class="td" style="max-width: 40px;"></div>
                        <div class="td"><input v-model="new_row.title" type="text" placeholder="Enter title here.."></div>
                        <div class="td"><input v-model="new_row.description" type="text" placeholder="Enter description here.."></div>
                        <div class="td"><input v-model="new_row.task_start_date" type="date"></div>
                        <div class="td"><input v-model="new_row.task_due_date" type="date"></div>
                        <div class="td"><input v-model="new_row.task_completed_date" type="date"></div>
               
                        <div class="td">
                            <div class="form-control-wrap">
                                <multiselect  v-model="new_row.milestone" 
                                :options="options_milestone" 
                                track-by="id" 
                                label="title" 
                                :multiple="false"
                                :taggable="true"
                                @tag="createNewMilestone" 
                                
                                >
                                </multiselect>
                            </div>

                        </div>
                        <div class="td">
                            <div class="form-control-wrap">
                                <multiselect  v-model="new_row.project" 
                                :options="options_project" 
                                track-by="id" 
                                label="title" 
                                :multiple="false"
                                :taggable="true"
                                @tag="createNewProject" 
                                
                                >
                                </multiselect>
                            </div>
                        </div>
                        <div class="td">
                            ASSIGN
                        </div>
                        <div  class="td">
                            <multiselect  v-model="new_row.tags" 
                                :options="options_tags" 
                                track-by="id" 
                                label="title" 
                                :multiple="true"
                                :taggable="true"
                                @tag="inlineCreateNewTag"  
                                >
                            </multiselect>
                        </div>
                        <div  class="td">
                            <multiselect  v-model="new_row.sprint" 
                                :options="options_sprint" 
                                track-by="id" 
                                label="title" 
                                :multiple="true"
                                :taggable="true"
                                @tag="createNewSprint"  
                                >
                            </multiselect>
                        </div>
                        <div  class="td">
                            <input v-model="new_row.task_duration" type="number" step="1" class="form-control form-control-lg">
                        </div>

                        <div class="td" style="max-width:100px;">
                            <button @click="save(true)" class="bp-btn bp-btn-blue">Save</button>
                        </div>
                    </div>
                    -->

                    <div v-if="filter.is_loading" class="table-loader">
                        <svg version="1.1" id="L4" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                        viewBox="0 0 100 100" enable-background="new 0 0 0 0" xml:space="preserve">
                        <circle fill="#474747" stroke="none" cx="20" cy="50" r="6">
                            <animate
                            attributeName="opacity"
                            dur="1s"
                            values="0;1;0"
                            repeatCount="indefinite"
                            begin="0.1"/>    
                        </circle>
                        <circle fill="#474747" stroke="none" cx="50" cy="50" r="6">
                            <animate
                            attributeName="opacity"
                            dur="1s"
                            values="0;1;0"
                            repeatCount="indefinite" 
                            begin="0.2"/>       
                        </circle>
                        <circle fill="#474747" stroke="none" cx="80" cy="50" r="6">
                            <animate
                            attributeName="opacity"
                            dur="1s"
                            values="0;1;0"
                            repeatCount="indefinite" 
                            begin="0.3"/>     
                        </circle>
                        </svg>
                    </div>
                </div>
            </div>


            <div v-if="view == 'kanban_view'">
       
                <template v-if="project != null">
                    <div class="kanban-view">
                            <div class="kanban-view-inner">
                                <draggable class="kanban-view-row  dragArea list-group w-full"  :group="'project-boards'" :list="boards"  @change="sortModuleBoards()">
                                    <div class="kanban-row"  v-for="board,index in getProjectBoards(project.id)" :key="'board-' + index">
                                        <div class="kanban-row-title">{{ board.title }}</div>
                                        <draggable class="kanban-view-col dragArea list-group w-full" :group="'element'" :list="board.tasks" @change="sortBoardTasks(board, $event,project)">
                                            <div class="kanban-col-title list-group-item" v-for="element in board.tasks" :key="element.id">
                                                {{ element.title }}
                                            </div>
                                        </draggable>
                                    </div>
                                </draggable>

                                <div class="kanban-new-board" @click="toggleAddBoardModal(true,project)">
                                    <div style="width:100%;">
                                        <span class="ti-plus"></span>
                                        <p>Add Board</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                </template>
                <template v-else>
                    <div class="projects kanban-project-single" v-for="project_item in projects" :key="'kanban-project-' + project_item.id ">
                        <div class="title">{{ project_item.title }}</div>
            
                        <div class="kanban-view">
                            <div class="kanban-view-inner">
                                <draggable class="kanban-view-row  dragArea list-group w-full"  :group="'boards-' + project_item.id" :list="boards"  @change="sortModuleBoards()">
                                    <div class="kanban-row"  v-for="board,index in getProjectBoards(project_item.id)" :key="'board-' + index">
                                        <div class="kanban-row-title">{{ board.title }}</div>
                                        <draggable class="kanban-view-col dragArea list-group w-full" :group="'element'" :list="board.tasks" @change="sortBoardTasks(board, $event,project_item)">
                                            <div class="kanban-col-title list-group-item" v-for="element in board.tasks" :key="element.id">
                                                {{ element.title }}
                                            </div>
                                        </draggable>
                                    </div>
                                </draggable>

                                <div class="kanban-new-board" @click="toggleAddBoardModal(true,project_item)">
                                    <div style="width:100%;">
                                        <span class="ti-plus"></span>
                                        <p>Add Board</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                
            </div>

            <div v-if="view == 'gantt_view'" class="table-view">
                <Gantt :module_type="'tasks'"></Gantt> 
            </div>

        </div>

        <FormTaskModal :formdata="selected"></FormTaskModal>

        <div :class="{'open' : is_modal_open}" class="bp-modal">
            <div class="bp-modal-wrap">
                    <div @click="toggleModal(false)" class="bp-modal-overlay"></div>
                    <div class="bp-modal-content">
                        <div class="bp-modal-header">
                            <h4 v-if="selected.id != null">Update</h4>
                            <h4 v-else>New</h4>
                        </div>

                        <div class="bp-modal-body">
                            <form v-on:submit.prevent="save()" action="/" class="form-validate is-alter" autocomplete="off" novalidate="novalidate">
                                <div class="form-group">
                                    <div class="form-label-group">
                                        <label class="form-label" for="email-address">Title</label>
                                    </div>

                                    <div class="form-control-wrap">
                                        <input v-model="selected.title" type="text" class="form-control form-control-lg" required="" id="email-address" placeholder="Enter title...">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="form-label-group">
                                        <label class="form-label" for="email-address">Description</label>
                                    </div>
                                    <div class="form-control-wrap">
                                        <textarea rows="4" v-model="selected.description" type="text" class="form-control form-control-lg" required="" id="firstname" placeholder="Enter task description..."></textarea>
                                    </div>
                                </div>


                                <div class="form-group">
                                    <div class="form-label-group">
                                        <label class="form-label" for="email-address">User Groups</label>
                                    </div>
                                    <div class="form-control-wrap">
                                        <multiselect  @select="selectGroup" @remove="removeGroup" v-model="selected_user_groups" 
                                            :options="options_user_groups" 
                                            track-by="id" 
                                            label="text" 
                                            :allow-empty="true" 
                                            deselect-label="Unselect" 
                                            selectLabel="Select"
                                            :searchable="true"
                                            :multiple="true"
                                            >
                                        </multiselect>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="form-label-group">
                                        <label class="form-label" for="email-address">Users</label>
                                    </div>
                                    <div class="form-control-wrap">
                                        <multiselect  v-model="selected_users" 
                                            :options="options_users" 
                                            track-by="id" 
                                            label="text" 
                                            :allow-empty="true" 
                                            deselect-label="Unselect" 
                                            selectLabel="Select"
                                            :searchable="true"
                                            :multiple="true"
                                            >
                                        </multiselect>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="form-label-group">
                                        <label class="form-label" for="email-address">Task Start Date</label>
                                    </div>
                                    <div class="form-control-wrap">
                                        <input v-model="selected.task_start_date" type="date" class="form-control form-control-lg" required="" id="email-address">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="form-label-group">
                                        <label class="form-label" for="email-address">Task Due Date</label>
                                    </div>
                                    <div class="form-control-wrap">
                                        <input v-model="selected.task_due_date" type="date" class="form-control form-control-lg" required="" id="email-address">
                                    </div>
                                </div>


                                <div class="form-group">
                                    <div class="form-label-group">
                                        <label class="form-label" for="email-address">Task Duration</label>
                                    </div>
                                    <div class="form-control-wrap">
                                        <input @change="onDurationChange()" v-model="selected.task_duration" type="number" step="1" class="form-control form-control-lg" required="" id="email-address">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="form-label-group">
                                        <label class="form-label" for="task-project">Projects</label>
                                    </div>
                                    <div style="display:flex; justify-content:space-between;">
                                        <div style="width:calc(100% - 50px);">
                                            <div class="form-control-wrap">
                                                <multiselect @select="onProjectChange($event,selected.id)" v-model="selected_project" 
                                                :options="options_project" 
                                                track-by="id" 
                                                label="title" 
                                                :allow-empty="true" 
                                                deselect-label="Deselect" 
                                                selectLabel="Select"
                                                :searchable="true"
                                                >
                                                </multiselect>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="form-label-group">
                                        <label class="form-label" for="email-address">Note</label>
                                    </div>
                                    <div class="form-control-wrap">
                                        <textarea rows="4" v-model="selected.note" type="text" class="form-control form-control-lg" required="" id="firstname" placeholder="Enter task description..."></textarea>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="form-label-group">
                                        <label class="form-label" for="email-address">Sprint</label>
                                    </div>
                                    <div class="form-control-wrap">
                                        <multiselect  v-model="selected_sprint" 
                                        :options="options_sprint" 
                                        track-by="id" 
                                        label="title" 
                                        :multiple="false"
                                        :allow-empty="true" 
                                        deselect-label="Deselect" 
                                        :taggable="true"
                                        @tag="createNewSprint" 
                                        >
                                        </multiselect>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="form-label-group">
                                        <label class="form-label" for="email-address">Tags</label>
                                    </div>
                                    <div class="form-control-wrap">
                                        <multiselect  v-model="selected.tags" 
                                            :options="options_tags" 
                                            track-by="id" 
                                            label="title" 
                                            :multiple="true"
                                            :taggable="true"
                                            @tag="createNewTag"  
                                            >
                                        </multiselect>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="form-label-group">
                                        <label class="form-label" for="email-address">Milestone</label>
                                    </div>
                                    <div style="display:flex; justify-content:space-between;">
                                        <div style="width:calc(100% - 50px);">
                                            <div class="form-control-wrap">
                                                <multiselect  v-model="selected_milestone" 
                                                :options="options_milestone" 
                                                track-by="id" 
                                                label="title" 
                                                :multiple="false"
                                                :allow-empty="true" 
                                                deselect-label="Deselect" 
                                                :taggable="true"
                                                @tag="createNewMilestone" 
                                                >
                                                </multiselect>
                                            </div>
                                        </div>
                                        <div style="width:40px;">
                                            <button @click="toggleAddNewMilestone()" type="button" style="margin-top:0px; max-width:40px; background:#efefef; color:#333; text-align:center; padding:10px 0px;">
                                                <span v-if="!is_add_new_milestone_open" class="ti-plus"></span>
                                                <span v-if="is_add_new_milestone_open" class="ti-close"></span>
                                            </button>
                                        </div>
                                    </div>

                                    <div v-if="selected.id" class="form-group">
                                        <div class="form-label-group">
                                            <label class="form-label" for="email-address">Task Completed Date</label>
                                        </div>
                                        <div class="form-control-wrap">
                                            <input v-model="selected.task_completed_date" type="date" class="form-control form-control-lg" required="" id="email-address">
                                        </div>
                                    </div>

                                    <div v-if="is_add_new_milestone_open" style="background:#fafafa; border:1px solid #efefef; padding:10px;">
                                        <strong>New Milestone</strong>
                                        <div>
                                            <div class="form-label-group">
                                                <label class="form-label" for="email-address">Title</label>
                                            </div>
                                            <div class="form-control-wrap">
                                                <input v-model="milestone.title" type="text" class="form-control form-control-lg" required="" id="email-address" placeholder="Enter milestone title...">
                                            </div>
                                            <div style="display:flex; justify-content:space-between;">
                                                <div style="width:calc(100% - 50px);">
                                                    <div class="form-control-wrap">
                                                        <multiselect  v-model="selected_milestone" 
                                                        :options="options_milestone" 
                                                        track-by="id" 
                                                        label="title" 
                                                        :multiple="false"
                                                        :taggable="true"
                                                        @tag="createNewMilestone" 
                                                        >
                                                        </multiselect>
                                                    </div>
                                            <div class="form-group">
                                                <div class="form-label-group">
                                                    <label class="form-label" for="email-address">Start On</label>
                                                </div>
                                                <div class="form-control-wrap">
                                                    <input v-model="milestone.start_on" type="date" class="form-control form-control-lg" required="" id="email-address">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="form-label-group">
                                                    <label class="form-label" for="email-address">Finished On</label>
                                                </div>
                                                <div class="form-control-wrap">
                                                    <input v-model="milestone.finished_on" type="date" class="form-control form-control-lg" required="" id="email-address">
                                                </div>
                                            </div>
                                            <div style="text-align:right;">
                                                <button @click="saveMilestone()" type="button" class="bp-btn bp-btn-blue">Add Milestone</button>
                                                <button @click="toggleAddNewMilestone()" type="button"  class="bp-btn bp-btn-gray">Cancel</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                </div>
                                </div>
                            </form>
                        </div>
                    
                        <div class="bp-modal-footer bp-modal-form-actions">
                            <button @click="save()" type="button" class="bp-btn bp-btn-blue">Save</button>
                            <button @click="toggleModal(false)" type="button"  class="bp-btn bp-btn-gray">Cancel</button>
                            <button v-if="selected.id" @click="taskCompleted()" type="button" class="bp-btn bp-btn-blue">Completed</button>
                        </div>  
                    </div>
                    
                </div>
            </div>
        </div>

        <div :class="{'open' : is_bulk_edit_modal_open}" class="bp-modal">
            <div class="bp-modal-wrap">
                <div @click="toggleBulkEditModal(false)" class="bp-modal-overlay"></div>
                <div class="bp-modal-content">
                    <div class="bp-modal-header">
                        <h4>Update</h4>
                    </div>
                    <div class="bp-modal-body">
                        <form  action="/" class="form-validate is-alter" autocomplete="off" novalidate="novalidate">
                            <div class="form-group">

                                <div class="form-group">
                                    <div class="form-label-group">
                                        <label class="form-label" for="email-address">Task Start Date</label>
                                    </div>
                                    <div class="form-control-wrap">
                                        <input v-model="selected.task_start_date" type="date" class="form-control form-control-lg" required="" id="email-address">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="form-label-group">
                                        <label class="form-label" for="email-address">Task Due Date</label>
                                    </div>
                                    <div class="form-control-wrap">
                                        <input v-model="selected.task_due_date" type="date" class="form-control form-control-lg" required="" id="email-address">
                                    </div>
                                </div>
                                

                                <div class="form-group">
                                    <div class="form-label-group">
                                        <label class="form-label" for="email-address">Milestone</label>
                                    </div>
                                    <div class="form-control-wrap">
                                        <multiselect  v-model="selected_milestone" 
                                        :options="options_milestone" 
                                        track-by="id" 
                                        label="text" 
                                        :allow-empty="false" 
                                        deselect-label="Selected" 
                                        selectLabel="Select"
                                        :searchable="true"
                                        >
                                        </multiselect>
                                    </div>
                                </div>

                            <div class="form-group">
                                <div class="form-label-group">
                                    <label class="form-label" for="email-address">Parent Task</label>
                                </div>
                                <div class="form-control-wrap">
                                    <multiselect  v-model="selected_parent_task" 
                                    :options="options_parent_task" 
                                    track-by="id" 
                                    label="text" 
                                    :allow-empty="true" 
                                    deselect-label="Unselect" 
                                    selectLabel="Select"
                                    :searchable="true"
                                    >
                                    </multiselect>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="form-label-group">
                                    <label class="form-label" for="email-address">Department</label>
                                </div>
                                <div class="form-control-wrap">
                                    <multiselect  v-model="selected_department" 
                                    :options="options_department" 
                                    track-by="id" 
                                    label="text" 
                                    :allow-empty="true" 
                                    deselect-label="Unselect" 
                                    selectLabel="Select"
                                    :searchable="true"
                                    >
                                    </multiselect>
                                </div>
                            </div>


                            <div class="form-group">
                                <div class="form-label-group">
                                    <label class="form-label" for="email-address">Sprint</label>
                                </div>
                                <div class="form-control-wrap">
                                    <multiselect  v-model="selected_sprint" 
                                    :options="options_sprint" 
                                    track-by="id" 
                                    label="title" 
                                    :multiple="false"
                                    :taggable="true"
                                    @tag="createNewSprint" 
                                    >
                                    </multiselect>
                                </div>
                            </div>

                            </div>
                    
                        </form>
                    </div>
                    <div class="bp-modal-footer bp-modal-form-actions">
                        <button @click="bulkSave()" type="button" class="bp-btn bp-btn-blue">Save</button>
                        <button @click="toggleBulkEditModal(false)" type="button"  class="bp-btn bp-btn-gray">Cancel</button>
                    </div>
                </div>
                
            </div>
        </div>


        <div :class="{'open' : is_add_board_modal_open}" class="bp-modal">
            <div class="bp-modal-wrap">
                <div @click="toggleAddBoardModal(false)" class="bp-modal-overlay"></div>
                <div class="bp-modal-content">
                    <div style="padding-bottom:10px; border-bottom:1px solid #efefef; margin-bottom:20px;">
                        <h4>New Board</h4>
                    </div>
                    <div class="bp-modal-body">
                        <form v-on:submit.prevent="saveModuleBoard()" action="/" class="form-validate is-alter" autocomplete="off" novalidate="novalidate">
                            <div class="form-group">

                                <div class="form-group">
                                    <div class="form-label-group">
                                        <label class="form-label" for="email-address">Name</label>
                                    </div>
                                    <div class="form-control-wrap">
                                        <input v-model="board.title" type="text" class="form-control form-control-lg" required="">
                                    </div>
                                </div>

                            </div>
                    
                            <div class="bp-modal-form-actions">
                                <button type="submit" class="bp-btn bp-btn-blue">Save</button>
                                <button @click="toggleAddBoardModal(false)" type="button"  class="bp-btn bp-btn-gray">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div :class="{'open' : is_duplicate_task_modal_open}" class="bp-modal">
            <div class="bp-modal-wrap">
                <div @click="toggleDuplicateTaskModal(false)" class="bp-modal-overlay"></div>
                <div class="bp-modal-content">
                    <div style="padding-bottom:10px; border-bottom:1px solid #efefef; margin-bottom:20px;">
                        <h4>Duplicate Tasks</h4>
                    </div>
                    <div class="bp-modal-body">
                        <form action="/" class="form-validate is-alter" autocomplete="off" novalidate="novalidate">
                            <div class="form-group">
                                <div class="form-label-group">
                                    <label class="form-label" for="task-project">Project</label>
                                </div>
                                <div style="display:flex; justify-content:space-between;">
                                    <div style="width:calc(100% - 50px);">
                                        <div class="form-control-wrap">
                                            <multiselect v-model="selected_project" 
                                            :options="options_project" 
                                            track-by="id" 
                                            label="title" 
                                            :allow-empty="true" 
                                            deselect-label="Deselect" 
                                            selectLabel="Select"
                                            :searchable="true"
                                            >
                                            </multiselect>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    
                            <div class="bp-modal-form-actions">
                                <button type="button" @click="duplicateTasks()" class="bp-btn bp-btn-blue">Save</button>
                                <button @click="toggleDuplicateTaskModal(false)" type="button"  class="bp-btn bp-btn-gray">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
</template>

<style src="vue-multiselect/dist/vue-multiselect.css" scope>

</style>


<script>
import moment from 'moment'
import Gantt from './components/Gantt'
import FormTaskModal from './forms/FormTaskModal'
import { VueDraggableNext } from 'vue-draggable-next'

var attributes = { 
    id: null, title: '', description: '', task_start_date: '', task_due_date: '', task_completed_date: '', milestone_id: null, project_id: null,module_id: null, parent_task_id: null,
    dept_id: null, project: null, milestone: null, tags: [], task_duration: 0
}

export default {
    props: ['user_id'],
    data: function () {
        return {
            view: 'table_view',
            project_slug: null,
            project_id: null,
            show_filter: false,
            is_modal_open: false,
            is_bulk_edit_modal_open: false,
            is_add_board_modal_open: false,
            is_add_new_milestone_open:false,
            is_duplicate_task_modal_open:false,
            module_loaded: false,
            module: {
                id: null,
                name: 'Tasks'
            },
            tasks: [],
            selected: JSON.parse(JSON.stringify(attributes)),
            edited_row: JSON.parse(JSON.stringify(attributes)),
            new_row: JSON.parse(JSON.stringify(attributes)),
            milestone: {
                id: null,
                project_id: null,
                title: '',
                start_on: '',
                finished_on: '',
                depends_id: null
            },
            selected_ids: [],
            filter: {
                keyword: '',
                keywords: [],
                start_date: null,
                due_date: null,
                project_ids: [],
                timeout: null,
                page: 1, 
                take:40,
                enable_paginate: true,
                is_loading: true
            },

            selected_milestone: null,
            options_milestone: [],

            board: {
                id: null,
                title: '',
                project_id: null,
                module_id: null
            },

            clone: null,

            selected_parent_task: null,
            options_parent_task: [],

            selected_department: null,
            options_department: [],

            selected_sprint: null,
            options_sprint: [],

            selected_users: [],
            options_users: [],

            selected_tags: [],
            options_tags: [],

            selected_user_groups: [],
            options_user_groups: [],

            selected_project: null,
            options_project: [],

        }
    },
    components: {
        'Gantt': Gantt,
        'draggable': VueDraggableNext,
        FormTaskModal,
    },
    computed: {
        items: function () {
            var scope = this
            return scope.tasks.filter(function(item){

                var data = item.title.toLowerCase().includes(scope.filter.keyword);
                var is_start_date_included = true
                var is_due_date_included = true
                var is_project_included = true

                if (scope.filter.start_date) {
                    var start_d1 = Date.parse(item.task_start_date);
                    var start_d2 = Date.parse(scope.filter.start_date);
                    

                    is_start_date_included = (start_d1 >= start_d2)
                }

                if (scope.filter.due_date) {
                    var due_d1 = Date.parse(item.task_due_date);
                    var due_d2 = Date.parse(scope.filter.due_date);
                    
                    console.log('item.task_due_date',due_d1)
                    console.log('scope.filter.due_date',due_d2)

                    is_due_date_included = (due_d1 <= due_d2)
                }

                var project_ids = scope.filter.project_ids.map(function(el) { return el.id; });
                
                if (project_ids.length > 0) {
                    is_project_included = (project_ids.includes(item.project_id)) ? true : false
                }

                var keywords = scope.filter.keywords
                
                return (
                    ( 
                        (keywords.length < 1 )||
                        keywords.some(el => item.title.toLowerCase().includes(el)) || 
                        (item.project && keywords.some(el => item.project.title.toLowerCase().includes(el))) ||
                        (item.milestone &&  keywords.some(el => item.milestone.title.toLowerCase().includes(el))) || 
                        (item.milestone && keywords.some(el => item.description.toLowerCase().includes(el))) || 
                            (item.task_start_date && keywords.some(el => item.task_start_date.toLowerCase().includes(el))) || 
                            (item.task_due_date && keywords.some(el => item.task_due_date.toLowerCase().includes(el)))
                    ) 
                    && (is_start_date_included && is_due_date_included) && is_project_included)
            });
        },
        boards: function() {
            let scope = this
                        console.log('hooot')
            console.log(scope.$store.getters.getModuleBoards(scope.module.id))
            return scope.$store.getters.getModuleBoards(scope.module.id)
        },
        projects: function () {
            let scope =  this
            return scope.$store.getters.getProjects
        },
        project: function () {
            let scope = this
            if (scope.project_slug) {
                return scope.$store.getters.getProjectBySlug(scope.project_slug)
            }

            return scope.$store.getters.getProjectByID(scope.project_id)
        },
        milestones: function () {
            let scope =  this
            return scope.$store.getters.getMilestones
        },
        sprints: function () {
            let scope =  this
            return scope.$store.getters.getSprints
        },
        tags: function () {
            let scope =  this
            return scope.$store.getters.getTags
        },
        departments: function () {
            let scope =  this
            return scope.$store.getters.getDepartments
        }
    },
    watch: {

    },
    methods: {
        toggleModalNew: function (data = null) {
            var scope = this

            scope.selected = JSON.parse(JSON.stringify(attributes))

            if (data != null) {
                scope.selected = data
            }
            
            scope.MODAL_TOGGLE('form-task-modal')
        },
        search: function () {
            var scope = this
            var keyword = scope.filter.keyword

            if (keyword == '') {
                return
            }

            var keywords = keyword.split(",");

            for (let i = 0; i < keywords.length; i++) {
                var key = keywords[i]
                scope.filter.keywords.push(key)
            }

            scope.filter.keyword = ''
        },
        removeSearchKeyword: function (k) {
            var scope = this
            var index = scope.filter.keywords.findIndex(keyword => keyword == k);

            scope.filter.keywords.splice(index, 1);
            
        },
        onProjectChange(event, task_id) {
            var scope = this


            if (task_id == null){
                scope.GET('api/projects/' + event.id).then(function (res) {
                    scope.selected.task_start_date = res.data.project_start_date
                    scope.selected.task_due_date  =  moment(res.data.project_start_date).add(scope.selected.task_duration, 'days').format('YYYY-MM-DD')
                });
            }
        },

        onDurationChange(){
            var scope = this
            scope.selected.task_due_date  =  moment(scope.selected.task_start_date).add(scope.selected.task_duration, 'days').format('YYYY-MM-DD')
        },

        selectGroup ({id}) {
            var scope = this

            scope.GET('api/user-groups/' + id).then(function (res) {
                if (res.rows.users.length > 0){
                
                    res.rows.users.forEach(function (usergroup_data) {
                        if (scope.selected_users.find(x => (x.id == usergroup_data.user.id))) {
                        }else{
                            scope.selected_users.push({
                                id: usergroup_data.user.id,
                                text: usergroup_data.user.firstname,
                            })
                        }
                    
                    })
                }
            });

        },
        removeGroup ({id}) {
            var scope = this
            scope.found = null
            scope.GET('api/user-groups/' + id).then(function (res) {
                if (res.rows.users.length > 0){
                    res.rows.users.forEach(function (usergroup_data) {
                        if (scope.selected_users.find(x => (x.id == usergroup_data.user.id))) {
                            scope.found = scope.selected_users.find(x => (x.id == usergroup_data.user.id))
                            
                        var index = scope.selected_users.findIndex(user => user.id === scope.found.id);
                        scope.selected_users.splice(index, 1)
                        }
                    })
                }
            });

        },
        toggleModal: function (is_open,row = null) {
            var scope = this
            scope.is_modal_open = is_open

            if (!scope.is_modal_open) {
                return;
            }

            if (row != null) {
                
                scope.edited_row = row
                scope.selected = row
                scope.selected.id = row.id
                scope.selected.title = (row.name) ? row.name : row.title

                scope.selected.task_start_date = (row.start) ? row.start : row.task_start_date
                scope.selected.task_due_date = (row.start) ? row.end : row.task_due_date

                scope.selected.description = row.description
     
                scope.selected.task_completed_date = row.task_completed_date
                scope.selected.task_duration = row.task_duration
                scope.selected.note = row.note


                if (row.milestone!= null) {
                    scope.selected_milestone = 
                    {
                        id: row.milestone.id,
                        title: row.milestone.title
                    }
                }


                if (row.project!= null) {
                    scope.selected_project = 
                    {
                        id: row.project.id,
                        title: row.project.title
                    }
                }

                

                if (row.task!= null){
                    scope.selected_parent_task = 
                    {
                        id: row.task.id,
                        text: row.task.title
                    }
                }

                if (row.department!= null){
                    scope.selected_department = 
                    {
                        id: row.department.id,
                        text: row.department.title
                    }
                }

                if (row.sprint!= null){
                    scope.selected_sprint = 
                    {
                        id: row.sprint.id,
                        text: row.sprint.title
                    }
                }

                if (row.task_users!= null){
                    row.task_users.forEach(function (data) {
                    
                        scope.selected_users.push({
                            id: data.user.id,
                            text: data.user.firstname
                        })
                     })
                }

                if (row.tags!= null){
                    row.tags.forEach(function (data) {
                        scope.selected.tags.push({
                            id: data.tag.id,
                            text: data.tag.title
                        })
                     })
                }

                if (row.task_user_groups!= null){
                    row.task_user_groups.forEach(function (data) {
                    
                        scope.selected_user_groups.push({
                            id: data.group.id,
                            text: data.group.title
                        })
                     })
                }


            } else {

                scope.selected = JSON.parse(JSON.stringify(attributes))

                if (scope.options_milestone){

                    scope.selected_milestone= (scope.options_milestone.length < 1) ? null : {
                        id: scope.options_milestone[0].id,
                        title: scope.options_milestone[0].title,
                        // projectid: scope.options_milestone[0].projectid
                    }

                }

                if (scope.options_project){
                    scope.selected_project= (scope.options_project.length < 1) ? null : {
                        id: scope.options_project[0].id,
                        title: scope.options_project[0].title,
                    }
     
                    scope.selected.task_start_date = moment(scope.options_project[0].project_start_date).format('YYYY-MM-DD')
                    scope.selected.task_due_date = moment(scope.options_project[0].project_start_date).add(1, 'days').format('YYYY-MM-DD')

                }

                if (scope.options_parent_task){
                    scope.selected_parent_task = null
                }

                if (scope.options_department){
                    scope.selected_department= null
                }

                if (scope.options_sprint){
                    scope.selected_sprint= null
                }

                scope.selected_users = []
                scope.selected_user_groups = []

            }

        },
        taskCompleted: function (){
            var scope = this
            scope.selected.task_completed_date = moment().format('YYYY-MM-DD')
        },
        toggleSelectedItem: function(id) {
            var scope = this
            
            var index = scope.selected_ids.indexOf(id)
            if (index > -1) {
                scope.selected_ids.splice(index, 1);
            } else {
                scope.selected_ids.push(id)
            }
        },
        toggleBulkEditModal: function (is_open) {
            var scope = this

            if (scope.selected_ids.length < 1) {
                return;
            }

            scope.is_bulk_edit_modal_open = is_open
        },
        toggleAddBoardModal: function (is_open,project = null) {
            var scope = this
         
            scope.board.title = ''
            scope.board.project_id = (project != null) ? project.id : null 
            scope.is_add_board_modal_open = is_open
        },
        toggleDuplicateTaskModal: function (is_open,project = null) {
            var scope = this

            scope.is_duplicate_task_modal_open = is_open
        },
        toggleAddNewMilestone: function () {
            var scope = this

            scope.is_add_new_milestone_open = !scope.is_add_new_milestone_open
        },
        getProjectBoards: function (project_id) {
            var scope = this
            return scope.boards.filter(item => (item.project_id == project_id ));
        },
        deleteBulk: function()
        {
            var scope = this

            if (scope.selected_ids.length < 1) {
                return;
            }
            
            scope.DELETE('/api/tasks', {
                ids: scope.selected_ids
            }).then(function (response) {
                if (!response.success) {
                    alert(response.message)
                }

                alert(response.message)
                scope.selected_ids = []
                scope.getRows();
                
            });
        },
        inlineEditRow: function (row) {
            var scope = this
            row.edit = true
        },
        save: function (is_new = false, row = null) {
            var scope = this

            if (row != null) {
                scope.selected = row
                scope.selected.milestone_id = (row.milestone == null) ? null : row.milestone.id
                scope.selected.project_id = (row.project == null) ? null : row.project.id

            }

            if (!is_new) {

                if (!row) {
                    scope.selected.milestone_id = (scope.selected_milestone == null) ? null : scope.selected_milestone.id
                    scope.selected.project_id = (scope.selected_milestone == null) ? null : scope.selected_milestone.projectid
                }
                

                scope.selected.parent_task_id = (scope.selected_parent_task == null) ? null : scope.selected_parent_task.id

                scope.selected.dept_id = (scope.selected_department == null) ? null : scope.selected_department.id

                scope.selected.sprint_id = (scope.selected_sprint== null) ? null : scope.selected_sprint.id

                scope.selected.user_details = scope.selected_users
                //scope.selected.tags = scope.selected_tags

                scope.selected.user_group_details = scope.selected_user_groups


            } else {
                scope.selected = scope.new_row
                scope.selected.milestone_id = (scope.new_row.milestone == null) ? null : scope.new_row.milestone.id
                // scope.selected.tags = scope.selected_tags
                scope.selected.project_id = (scope.new_row.project == null) ? null : scope.new_row.project.id
            }

            
            if (scope.selected.id !== null) {
                scope.PUT('/api/tasks/' + scope.selected.id, scope.selected).then(function (res) {
                    if (res.success) {
                        alert(res.message)
                        scope.edited_row = res.data

                        scope.selected = JSON.parse(JSON.stringify(attributes))
                        scope.new_row = JSON.parse(JSON.stringify(attributes))
                        scope.toggleModal(false)

                        /*
                        if (!row) {
                            scope.getRows();
                        } else {
                            row.edit = false
                        }
                        */

                       
                        
                    } else {
                        alert(res.message);
                    }
                });
            } else {
                scope.POST('/api/tasks', scope.selected).then(function (res) {
                    if (res.success) {
                        alert(res.message)
                        scope.selected = JSON.parse(JSON.stringify(attributes));
                        scope.new_row = JSON.parse(JSON.stringify(attributes))
                        scope.toggleModal(false)
                        scope.getRows();
                    } else {
                        alert(res.message);
                    }
                });
            }
            
        },
        bulkSave: function () {
            var scope = this

            if (scope.selected_ids.length < 1) {
                return;
            }

            scope.selected.milestone_id = (scope.selected_milestone == null) ? null : scope.selected_milestone.id

            scope.PUT('/api/tasks', {fields: scope.selected, ids: scope.selected_ids}).then(function (res) {
                if (res.success) {
                    alert(res.message)
                    scope.selected = {
                        id: null,
                        title: '',
                        description: '',
                        task_start_date: '',
                        task_due_date: '',
                        task_completed_date: '',
                        milestone_id: null,
                        project_id: null,
                    }
                    
                    scope.toggleBulkEditModal(false)
                    scope.selected_ids = []
                    scope.getRows();
                } else {
                    alert(res.message);
                }
            });
        },
        saveModuleBoard: function () {
            var scope = this
            scope.board.module_id = scope.module.id 
            scope.POST('/api/modules/' + scope.module.id + '/boards',scope.board).then(function (res) {
                 if (res.success) {
                    alert(res.message)
                    scope.$store.dispatch('fetchBoards')
                    scope.toggleAddBoardModal(false)
                } else {
                    alert(res.message);
                }
            });
        },
        saveMilestone: function () {
            var scope = this

            scope.milestone.project_id = (scope.selected_project == null) ? null : scope.selected_project.id
            scope.milestone.depends_id = (scope.selected_depends_on == null) ? null : scope.selected_depends_on.id

            scope.POST('/api/milestones', scope.milestone).then(function (res) {
                if (res.success) {
                    scope.milestone.title = '';
                    scope.getMilestone();
                    scope.toggleAddNewMilestone();
                    
                } else {
                    alert(res.message);
                }
            });
            
            
        },
        getRows: function () {
            var scope = this
            var query_project = (scope.project_slug) ? '&project_slug=' + scope.project_slug : ''

            scope.GET('/api/tasks?keyword=' + scope.filter.keyword + '' + query_project  + '&page=' + scope.filter.page + '&take=' + scope.filter.take).then(function (res) {
    
                // scope.tasks = res.rows

                res.rows.forEach(function (data) {
                    scope.options_parent_task.push({
                        id: data.id,
                        text: data.title,
                    })

                    var tags = JSON.parse(JSON.stringify(data.tags))
                    data.tags = []
                    tags.forEach(function (data2) {
                        data.tags.push(data2.tag)
                    });

                    data['edit'] = false
                    scope.tasks.push(data)
                })

                if (res.rows.length < 1 )  {
                    scope.filter.enable_paginate = false
                }

                scope.filter.is_loading = false

            });

        },

        getDepartment: function () {
            var scope = this
            scope.GET('/api/departments').then(function (res) {

                res.rows.forEach(function (data) {
                    scope.options_department.push({
                        id: data.id,
                        text: data.title,
                    })
                })

            });
        },

        getSprint: function () {
            var scope = this
            scope.GET('/api/sprints').then(function (res) {
                res.rows.forEach(function (data) {
                    scope.options_sprint.push({
                        id: data.id,
                        text: data.title,
                    })
                })

            });
        },

        getModule: function (module) {
            var scope = this
            scope.GET('/api/modules/find?key=' + module.name ).then(function (res) {
                scope.module = res.data
                scope.module_loaded = true
            });
        },

        getUsers: function () {
            var scope = this
            scope.GET('/api/users?keyword=' + scope.filter.keyword).then(function (res) {
                scope.users = res.rows

                res.rows.forEach(function (data) {
                    
                    scope.options_users.push({
                        id: data.id,
                        text: data.firstname
                    })
                
                })

            });
        },

        getUserGroup: function () {
            var scope = this
            scope.GET('/api/user-groups?keyword=' + scope.filter.keyword).then(function (res) {

                res.rows.forEach(function (data) {
                    
                    scope.options_user_groups.push({
                        id: data.id,
                        text: data.title
                    })
                
                })
            });
        },
   
        getTags: function () {
            var scope = this
            scope.GET('/api/tags').then(function (res) {
                res.rows.forEach(function (data) {
                    scope.options_tags.push({
                        id: data.id,
                        title: data.title,
                    })
                });
            });
        },
        switchView: function (view) {
            this.view = view
        },
        toggleFilter: function (show_filter) {
            this.show_filter = !this.show_filter
        },
        duplicateTasks: function() {
            var scope = this
            var data = {project_id: scope.selected_project, task_ids: scope.selected_ids}

            scope.POST('/api/tasks/duplicate',data).then(function (res) {
                if (res.success) {
                    alert(res.message)
                    res.rows.forEach(function (data) {
                        scope.tasks.push(data)
                    })
                    scope.toggleDuplicateTaskModal(false);
                } else {
                    alert(res.message)
                }
            });   
        },
        searchTimeOut() {  
            var scope = this
            scope.filter.is_loading = true
            if (scope.filter.timeout) {
                clearTimeout(scope.filter.timeout);
                scope.filter.timeout = null;
            }
            scope.filter.timeout = setTimeout(() => {
               scope.getRows();
            }, 600);
        },
        sortBoardTasks: function (board,evt,project) {
            var scope = this
            
            var cloned_tasks_id = 0; // this will be the indicator to make the ID of this class treated as new (insert new record)

            if (evt.added) {
                scope.clone = (evt.added.element.project_id != project.id) ? evt.added.element : null
                cloned_tasks_id = (scope.clone) ? evt.added.element.id : 0
                //console.log('ADD BOARD',project.title)
                //console.log('ADD BOARD',board.title)
            }

            if (evt.removed && scope.clone != null) {
                var new_task = JSON.parse(JSON.stringify(scope.clone))

                board.tasks.push(new_task)
                
                // scope.clone = null
                // scope.clone.project_id = project.id

                //console.log('REMOVE BOARD',project.title)
                //console.log('REMOVE BOARD',board.title)
            }
            
            /*
            if (evt.added && scope.clone) {
                console.log('sss')
            } else if (evt.removed && scope.clone) {
                console.log('process sorting and adding')
                scope.clone = null
            }

            return
            */

          

            if (evt.added) {
                scope.$store.dispatch('moveBoardTasks', { board_id: board.id, board: board, task: evt.added.element, cloned_tasks_id: cloned_tasks_id, sort_index: evt.added.newIndex})
                cloned_tasks_id = 0
            } else if (evt.removed && scope.clone ) {
                scope.$store.dispatch('moveBoardTasks', { board_id: board.id, board: board, task: evt.removed.element, cloned_tasks_id: cloned_tasks_id, sort_index: evt.removed.newIndex})
                scope.clone = null
                cloned_tasks_id = 0
            } else if (evt.moved) {
                scope.$store.dispatch('sortBoardTasks', {board_id: board.id, board: board, task: evt.moved.element, index: evt.moved.newIndex, old_index: evt.moved.oldIndex})
            }

        },
        sortModuleBoards: function () {
            var scope = this
            scope.$store.dispatch('moveModuleBoards', {module_id: scope.module.id, boards: scope.boards})
        },
        
    },
    created() {

    },
    mounted() {
        var scope = this
        var module = scope.module

        scope.project_slug = (scope.$route.params.project_slug) ? scope.$route.params.project_slug : null
        
        scope.searchTimeOut()
        // scope.getMilestone()
        // scope.getDepartment()
        // scope.getSprint()
        scope.getUsers()
        scope.getUserGroup()
        // scope.getTags()
        
        scope.getModule(module);

        scope.$store.dispatch('fetchTasks')
        scope.$store.dispatch('fetchBoards')
        scope.$store.dispatch('changeHeader','Tasks')
        scope.$store.dispatch('fetchProjects')
        scope.$store.dispatch('fetchSprints')
        scope.$store.dispatch('fetchTags')
        scope.$store.dispatch('fetchDepartments')
        scope.$store.dispatch('fetchMilestones')

       
        
        window.onscroll = function() {
            const difference = document.documentElement.scrollHeight - window.innerHeight;
            const scrollposition = document.documentElement.scrollTop; 
            if (scope.filter.is_loading == false && scope.filter.enable_paginate && difference - scrollposition <= 2) {
                scope.filter.page += 1
                scope.searchTimeOut()
            }   
        }

        
    
        setTimeout(function(){
            scope.options_project = scope.projects
            scope.options_milestone = scope.milestones
            scope.options_sprint = scope.sprints
            scope.options_tags = scope.tags

            scope.MODAL_TOGGLE('form-task-modal')
        },1000);

    }
}
</script>

<style scoped>
.table-view { margin-bottom:50px; }
.badge-user { background-color: #e9ebee; border-color: #afb7c1; background: #50a5f1; color: #fff; padding-top: 4px; padding-bottom: 7px;padding-left: 4px;padding-right: 4px;border-radius: 100px;margin-right: 4px; }

.kanban-project-single { background:#fafafa;  margin-bottom:20px; }
.kanban-project-single .title { padding:5px 10px; background:#f1f1f1;  font-weight:600; }
.kanban-view { padding:10px; border:1px solid #efefef; }

.kanban-view { display:flex; }
.kanban-view-inner { display:flex; }
.kanban-view-row {display:flex; }
.kanban-row { min-height:200px;  border:1px solid #ccc; margin:0px 10px; flex:1; min-width:200px; }
.kanban-row-title { background:#424c68; padding:3px 10px; color:#fff; }
.kanban-col-title { padding:3px 10px;  border:1px solid #ccc; border-left:none; border-right:none; border-top:none; }
.kanban-new-board { cursor:pointer; background:#efefef; border:1px solid #ccc; display:flex; min-height:200px; align-items:center; text-align:center; margin:0px 10px; flex:1; min-width:200px; }


</style>

