<template>
    <section class="w-dvw">
        <div class="container">
            <nav class="md:flex md:items-center p-5 md:justify-between fixed w-dvw bg-slate-100 z-10">
                <div>
                    <b class="text-sm">Task Management System</b>
                </div>
                <div>
                    <div class="grid grid-rows-3">
                        <div>
                            <b class="text-sm font-sans">Today: {{ data.currentDate }}</b>
                        </div>
                        <div>
                            <b class="text-sm font-sans">Good Day! Welcome {{ data.currentUser }}</b>
                        </div>
                        <div>
                            <button type="button" class="button-primary" @click="logout()">
                                Logout
                            </button>
                        </div>
                    </div>
                </div>
            </nav>
        </div>
    </section>
    <section>
        <div class="container pt-36">
            <div class="flex justify-start items-start p-3">
                <button type="button" class="button-primary" @click="openModal()">
                    Create New Record
                </button>
            </div>
        </div>
    </section>
    <section>
       <div class="flex w-full  p-3">
            <div class="w-full" >
                <table class="min-w-full divide-y divide-gray-200 border-solid " id="taskTable"  >
                    <thead class="text-center">
                        <tr>
                            <th data-index="title" scope="col" class="px-6 border-2  py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                            <th data-index="content" scope="col" class="px-6 border-2 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Content</th>
                            <th data-index="status_description" scope="col" class="px-6 border-2 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th data-index="attachment" scope="col" class="px-6  border-2 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Image</th>
                            <th data-index="createdDate" scope="col" class="px-6 border-2 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Created Date</th>
                            <th data-index="action" scope="col" class="px-6 border-2 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="table-body">
                    </tbody>
                </table>
            </div>
       </div>
    </section>
    <taskModal 
        :baseimage="baseimage"
        :data="data"
        :openmodal="isModalOpen"
        :details="payload"
        @isClosed="handleConfirm">
    </taskModal>
</template>
<script>
import taskModal from './modal/info.vue';
export default  {
    props: [
        "baseimage",
        "data",
        "storagepath"
    ],
    components: {
        taskModal,
    },
    data() {
        return {
            isModalOpen: false,
            payload: {
                id: null,
                title: null,
                content: null,
                status: null,
                parent_id: null,
                attachment: null,
                label: 'Submit',
            },
        }
    },
    methods: {
        loadTable(parentId = null) {
            let _this = this;
            let tableparam = [];
            $('#taskTable thead tr').clone(true).appendTo('#taskTable thead');
            $('#taskTable thead tr:eq(1) th').each( function (i) {
                let title = $(this).data('index');
                if (title !='action' && title !='attachment') {
                    $(this).html(
                        '<input type="text"  class="input-field text-center" \
                        data-index="'+$(this).data('index')+'" placeholder="Search"/>'
                    );
                } else {
                    $(this).html(
                        '<input type="text" class="input-field text-center" disabled\
                        data-index="'+$(this).data('index')+'"/>'
                    );
                }
            });

            let taskTable = $('#taskTable').DataTable({
                lengthMenu: [[10,20, 30, 50], [10,20, 30, 50]],
                scrollX : true,
                scrollCollapse : true,
                serverSide : true,
                processing : true,
                ordering : true,
                destroy : true,
                stateSave: false,
                deferLoading : 0,
                responsive: true,
                ajax: $.fn.dataTable.pipeline({
                    url: _this.data.taskUrl,
                    method: "POST",
                    pages: 5,
                    start: 0,
                    data:function(state) {
                        state._token = $('meta[name="csrf-token"]').attr('content');
                        state.parentId = parentId;
                        state.tableparam = tableparam;
                    }
                }),
                columns : [
                    { data : 'title' },
                    { data : 'content' },
                    { data : 'status_description' },
                    {
                       render:function(data, type, full, meta) {
                            let item = null;
                            let image = _this.storagepath+"/"+full["attachment"];
                            if (full["attachment"]) {
                                item =  "<img src="+image+"/>";
                            }

                            return item;
                       }
                    },
                    { data : 'createdDate' },
                    { 
                        render:function(data, type, full, meta) {
                            return " \
                                <button type='button' class='button-primary openDetail' \
                                    data-title='"+full['title']+"' \
                                    data-content='"+full['content']+"' \
                                    data-attachment='"+full['attachment']+"' \
                                    data-id='"+full['id']+"' \
                                    data-status='"+full['status_id']+"'> \
                                    Update \
                                </button> \
                                <button type='button' class='button-danger openDetail' \
                                    data-title='"+full['title']+"' \
                                    data-content='"+full['content']+"' \
                                    data-attachment='"+full['attachment']+"' \
                                    data-parent_id='"+full['id']+"' \
                                    data-status='"+full['status_id']+"'> \
                                    Create Task \
                                </button> \
                                <button type='button' class='button-warning subtask' \
                                data-parent_id='"+full['id']+"' "+(!full["withChild"] ? "hidden" : "")+"\> \
                                    Task \
                                </button>";
                        }
                    },
                ],
                dom:"<'row'<'sm md text-xs text-left mb-2'l><'md sm text-center'>" +
                    "<'row'<'md sm'tr>>" +
                    "<'row pt-2'<'md sm text-sm text-left'i><'md sm text-xs text-right'p>>"
            });


            $(taskTable.table().container()).on('keypress', 'thead input', function (e) {
                if (e.which == 13) {
                    e.preventDefault();
                    let col = $(this).data('index');
                    let search = this.value;
                    runUpdateTable(col, search);
                }
            });    

            $(taskTable.table().container()).on('change', 'thead input', function (e) {
                let col = $(this).data('index');
                let search = this.value;
                let i = $.map(tableparam, function(item, i) {
                    if (item.columnname == col)
                    return i;
                })[0];
                runUpdateTable(col, search);
            });

            function runUpdateTable (col, search) {
                let i = $.map(tableparam, function(item,i) {
                    if(item.column_name == col)
                        return i;
                })[0];
                if (typeof i === 'undefined') {
                    tableparam.push({column_name : col , column_value : search})
                } else {
                    tableparam[i].column_value = search;
                }
                taskTable.clearPipeline().draw();
            }

            taskTable.clearPipeline().draw();
        },
        openModal() {
           this.isModalOpen = true;
        },
        logout() {
            let _this = this;
            axios({
                method: 'POST',
                url: _this.data.logoutUrl,
            });
            location.href = _this.data.loginUrl;
        },
        handleConfirm() {
           this.isModalOpen = false;
           this.payload.parent_id = false;
        },
        openDetail() {
            let _this = this;
            $ (function() {
                $(document).on("click", ".openDetail", function() {
                    if (!$(this).attr("data-parent_id")) {
                        _this.payload.id = $(this).attr("data-id");
                        _this.payload.title = $(this).attr("data-title");
                        _this.payload.content = $(this).attr("data-content");
                        _this.payload.status = $(this).attr("data-status");
                        _this.payload.label = "Update";
                    } else {
                        _this.payload.label = "Extend";
                        _this.payload.parent_id = $(this).attr("data-parent_id");
                    }
                    _this.isModalOpen = true;
                });
            });
        },
        loadSubtask() {
            let _this = this;
            $ (function() {
                $(document).on("click", ".subtask", function() {
                    _this.loadTable($(this).attr("data-parent_id"));
                });
            });
        }
    },
    mounted() {
        this.loadTable();
        this.openDetail();
        this.loadSubtask();
    }
}
</script>
