<template>
   <section id="taskmodal" v-if="openmodal" class="modal">
        <div class="bg-slate-100 xs:w-3/6  sm:w-2/4 md:w-1/3  shadow-md drop-shadow-md">
            <div class="flex justify-end items-end mx-2">
                <button @click="closeModal()" class="text-gray-500 hover:text-gray-800 z-40">&times;</button>
            </div>
            <div class="flex justify-center items-center relative top-[-50px]">
                <img :src="baseimage + '/frontend.svg'" class="bg-slate-200 rounded-full w-28 h-28">
            </div>
            <div class="flex justify-center items-center">
                <b>Task Management System</b>
            </div>
            <form class="shadow-lg rounded-lg p-8 w-full">
                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2 py-1" for="username">
                        TITLE 
                    </label>
                    <input :class="(!errors['payload.title'] ? 'border-gray-300' : 'error-field')" class="input-field" id="name" name="name" type="text" placeholder="Enter your Title" v-model="payload.title">
                    <span class="error-text" v-if="errors['payload.title']">
                            {{ errors['payload.title'][0] }}
                     </span>

                    <label class="block text-gray-700 text-sm font-bold mb-2 py-1" for="username">
                        CONTENT 
                    </label>
                    <input :class="(!errors['payload.content'] ? 'border-gray-300' : 'error-field')" class="input-field" id="name" name="name" type="text" placeholder="Enter your Content" v-model="payload.content">
                    <span class="error-text" v-if="errors['payload.content']">
                            {{ errors['payload.content'][0] }}
                     </span>

                    <label class="block text-gray-700 text-sm font-bold mb-2 py-1" for="username">
                        STATUS 
                    </label>
                    <treeselect :class="(!errors['payload.status'] ? '' : 'treeselect-invalid')"  :multiple="false" :options="data.statusOption" placeholder="Please select status" v-model="payload.status"/>
                    <span class="error-text" v-if="errors['payload.status']">
                            {{ errors['payload.status'][0] }}
                     </span>
                </div>
                <button type="button" class="button-primary" @click="update()">
                    Update
                </button>
            </form>
        </div>
   </section>
</template>
<script>
import Treeselect from 'vue3-treeselect';
import 'vue3-treeselect/dist/vue3-treeselect.css';
export default  {
    props: [
        "baseimage",
        "data",
        "openmodal",
        "details"
    ],
    components: {
        Treeselect
    },
    data() {
        return  {
            payload: {
                id: null,
                title: null,
                content: null,
                status: null,
                parent_id: null,
            },
            errors: [],
            isOpenModal: false,
        }
    },
    methods: {
        update() {
            let _this = this;
            axios({
                method: 'POST',
                url: _this.data.taskUpdateUrl,
                data: {
                    payload: _this.payload
                },
            }).then(function (response) {
                if (response.status == 200) {
                   location.reload(); 
                }
            })
            .catch(function (errorHandler) {
                if (errorHandler.response.status == 422) {
                    _this.errors = errorHandler.response.data.errors;
                }
            });
        },
        closeModal() {
            this.clearFields();
            this.errors = [];
            this.$emit("isClosed", true);
        },
        clearFields() {
            this.payload.id = null;
            this.payload.title = null;
            this.payload.content = null;
            this.payload.status = null;
            this.payload.parent_id = null;
        },
        setDetails() {
            if (this.details) {
                this.payload.title = this.details.title;
            }
        }
    },
    watch: {
        'details.title': {
            handler:function(val) {
                if (val) {
                    this.payload.title = this.details.title;
                    this.payload.content = this.details.content;
                    this.payload.status = this.details.status;
                    this.payload.id = this.details.id;
                    this.payload.parent_id = this.details.parent_id;
                }
            }
        },
        deep: true
    }
}
</script>
