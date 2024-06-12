<template>
    <section class="w-dvw block">
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
        <div class="flex justify-start items-start w-full mx-1 pt-40">
            <button type="button" class="button-primary" @click="openModal()">
                Create New Record
            </button>
        </div>
   
        <div class="p-3 text-center" style="width: 100%;">
            <table class="min-w-full divide-y text-center divide-gray-200 border-solid" id="taskTable" >
                <thead class="text-center">
                    <tr>
                        <th data-index="name" scope="col" class="px-6 border-2  py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                        <th data-index="name" scope="col" class="px-6 border-2 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Content</th>
                        <th data-index="name" scope="col" class="px-6 border-2 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th data-index="image" scope="col" class="px-6  border-2 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Image</th>
                        <th data-index="name" scope="col" class="px-6 border-2 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Created Date</th>
                        <th data-index="action" scope="col" class="px-6 border-2 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="table-body">
                </tbody>
            </table>
        </div>
        <taskModal 
            :baseimage="baseimage"
            :data="data"
            :openmodal="isModalOpen"
            :details="payload"
            @isClosed="handleConfirm">
        </taskModal>
    </section>
</template>
<script>
import taskModal from './modal/info.vue';

export default  {
    props: [
        "baseimage",
        "data",
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
            },
        }
    },
    methods: {
        loadTable() {
            let _this = this;
            let tableparam = [];
            $('#taskTable thead tr').clone(true).appendTo('#taskTable thead');
            $('#taskTable thead tr:eq(1) th').each( function (i) {
                let title = $(this).data('index');
                if (title !='action' && title !='image') {
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

            let userTable = $('#taskTable').DataTable({
                lengthMenu: [[10,20, 30, 50], [10,20, 30, 50]],
                scrollX : true,
                scrollCollapse : true,
                serverSide : true,
                processing : true,
                ordering : false,
                destroy : true,
                stateSave: false,
                deferLoading : 0,
                responsive: true,
                ajax: $.fn.dataTable.pipeline({
                    url: _this.data.taskUrl,
                    method: "POST",
                    pages: 5,
                    start: 0,
                    data:function(preparedstatement) {
                      preparedstatement._token = $('meta[name="csrf-token"]').attr('content');
                    }
                }),
                columns : [
                    { data : 'title' },
                    { data : 'content' },
                    { data : 'status_id' },
                    { data : 'attachment' },
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
                            <button type='button' class='button-danger'> \
                                Create Subtask \
                            </button> \
                            <button type='button' class='button-warning'> \
                                Subtask \
                            </button>";
                        }
                    },
                ],
                dom:"<'row'<'sm md text-xs text-left mb-2'l><'md sm text-center'>" +
                    "<'row'<'md sm'tr>>" +
                    "<'row pt-2'<'md sm text-sm text-left'i><'md sm text-xs text-right'p>>"
            });
            userTable.clearPipeline().draw();
        },
        openModal() {
           //this.loadTable();
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
        },
        openDetail() {
            let _this = this;
            $ (function() {
                $(document).on("click", ".openDetail", function() {
                    _this.payload.id = $(this).attr("data-id");
                    _this.payload.title = $(this).attr("data-title");
                    _this.payload.content = $(this).attr("data-content");
                    _this.payload.status = $(this).attr("data-status");
                    _this.isModalOpen = true;
                });
            });
        }
    },
    mounted() {
        this.loadTable();
        this.openDetail();
    }
}
</script>
