<style scoped>
h1{
    font-size: 30px;
    font-weight: 300;
    margin: 0 0 5px 0;
}
.displayed{
    font-size: 16px;
    font-weight: 300;
    font-style: italic;
    margin: 0 0 15px 0;   
}
.no-results{
    padding: 40px 0;
}
.search-bar, .progress-wrapper, .alert{
    margin: 15px 0;
}
.progress{
    margin: 0;
}
input.file-input{
    position: absolute;
    left: -9999px;
}
input.file-input + label {
    margin: 0;
    padding: 30px;
    cursor: pointer;
    background: rgba(0, 0, 0, 0.1);
    border: 1px dashed rgba(0, 0, 0, 0.3);
    text-align: center;
    font-style: italic;
    font-weight: 400;
    background: #E0E0E0;
}
input.file-input + label:hover,
input.file-input + label.disabled {
    background: #ff9070;
    color: white;
}
.cell-preview{
    min-width: 150px;
}
</style>

<template>

    <div class="medias-manager">

        <div class="row">
            <div class="col-md-3">

                <h1>Medias</h1>
                <div class="displayed">
                    <span v-if="loaded && pagination.total > 0">
                        Showing {{ (pagination.current_page - 1) * pagination.per_page + 1 }}
                        to {{ Math.min(pagination.current_page * pagination.per_page, pagination.total) }} / {{ pagination.total }}
                    </span>
                    <span v-else-if="loaded && pagination.total == 0">
                        No media found
                    </span>
                    <span v-else>
                        Loading...
                    </span>
                </div>

                <input type="file" multiple
                    accept="image/jpeg,image/png,application/pdf,video/mp4,video/m4v"
                    :id="'file-input-' + id" 
                    :disabled="uploadStatus == 'uploading'"
                    class="file-input">
                <label :for="'file-input-' + id" class="table filedrop" :class="{'disabled': uploadStatus == 'uploading'}">
                    <div class="cell">
                        Drag & drop / browse files...
                    </div>
                </label>

                <div class="progress-wrapper" v-if="uploadStatus == 'uploading'">
                    <div class="progress-cell">
                        <div class="progress">
                            <div class="progress-bar" role="progressbar" :style="'width:' + (100 * uploadProgress / uploadTotal) + '%'">
                                <span class="sr-only">{{100 * uploadProgress / uploadTotal}}%</span>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="button" class="btn btn-warning btn-block" @click="cancel()" v-if="uploadStatus == 'uploading'">
                    <i class="fa fa-close"></i> Cancel
                </button>

                <div v-if="uploadError" class="alert alert-danger text-center">
                    Erreur {{ uploadError.response.status }} : {{ uploadError.response.statusText }}
                </div>

                <div v-if="uploadStatus == 'done'" class="alert alert-success text-center">
                    Files uploaded successfully
                </div>

                <div class="search-bar">
                    <input-text
                        v-model="search.text"
                        name="search"
                        placeholder="Search..."
                        :autofocus="true">
                    </input-text>
                </div>
                
            </div>
            <div class="col-md-9">

                <panel>
                    <div slot="body">

                        <div class="text-right">
                            <!--pagination :pagination="pagination" @change="loadPage"></pagination-->
                        </div>

                        <div v-if="pagination.total === 0" class="no-results text-center">
                            No media found, but you should hit the "browse" button pretty soon!
                        </div>

                        <div class="table-responsive" v-show="pagination.total > 0">
                            <table class="table table-condensed table-striped">
                                <thead>
                                    <tr>
                                        <th>Preview</th>
                                        <th>Details</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, idx) in pagination.data">
                                        <td class="cell-preview">
                                            <div class="preview">
                                                <div class="image"
                                                    v-if="item.mime.indexOf('image') !== -1"
                                                    :style="'background-image:url(' + item.filepath + '?' + item.decache + ')'">
                                                </div>
                                                <video controls v-else-if="item.mime.indexOf('video') !== -1">
                                                    <source :src="assetsBaseUrl + item.filepath + '?' + item.decache" :type="item.mime" />
                                                </video>
                                                <div class="text-wrapper" v-else>
                                                    <div class="text">
                                                        <div>{{ item.mime }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>    
                                            <div>
                                                <span class="lab">Title :</span>
                                                <span class="text-info">{{ item.title }}</span>
                                            </div>
                                            <div>
                                                <span class="lab">File :</span>
                                                <span class="text-info">{{ item.filename }}</span>
                                            </div>
                                            <div>
                                                <span class="lab">Type :</span>
                                                <span class="text-info">{{ item.mime }}</span>
                                            </div>
                                            <div>
                                                <span class="lab">Size :</span>
                                                <span class="text-info">
                                                    <span v-if="item.size < 1000000">{{ item.size / 1000 }} ko</span>
                                                    <span v-else>{{ item.size / 1000000 }} Mo</span>
                                                </span>
                                            </div>
                                            <div v-if="item.mime.indexOf('image') !== -1">
                                                <span class="lab">Dimensions :</span>
                                                <span class="text-info">{{ item.width }} x {{ item.height }} px</span>
                                            </div>
                                        </td>

                                        <td class="actions text-right">

                                            <div class="btn-group">

                                                <button type="button" @click="download(item)" class="btn btn-default">
                                                    <i class="fa fa-download"></i>
                                                </button>

                                                <button type="button" @click="openUpdate(item)" class="btn btn-default">
                                                    <i class="fa fa-edit"></i>
                                                </button>

                                                <button type="button" @click="openDestroy(item)" class="btn btn-danger">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                                
                                            </div>
                                            
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="text-right">
                            <pagination :pagination="pagination" @change="loadPage"></pagination>
                        </div>

                    </div>
                </panel>

            </div>
        </div>

        <div class="modal fade destroy-dialog"tabindex="-1" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    
                    <div class="modal-header">
                        <button type="button " class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">Delete file</h4>
                    </div>
                    
                    <div class="modal-body">
                        Are you sure ?

                        <div v-if="destroyError" class="alert alert-danger">
                            {{ destroyError }}
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal" v-if="destroyStatus == 'none'">
                            <i class="fa fa-close"></i> Cancel
                        </button>
                        <button type="button" 
                            class="btn btn-danger" 
                            @click="destroy()"
                            :disabled="destroyStatus === 'loading'">

                            <span v-if="destroyStatus === null">
                                Yes, delete !
                            </span>
                            <span v-if="destroyStatus === 'loading'">
                                <i class="fa fa-spinner fa-spin"></i> Deleting file...
                            </span>

                        </button>
                    </div>

                </div>
            </div>
        </div>

        <media-updater 
            ref="mediaUpdater"
            @updated="read()">
        </media-updater>
















        <!--div class="row">

            <div class="col-sm-6">
                <input type="file" multiple
                    accept="image/jpeg,image/png,application/pdf,video/mp4,video/m4v"
                    :id="'file-input-' + id" 
                    :disabled="uploadStatus == 'uploading'"
                    class="file-input">
                <label :for="'file-input-' + id" class="table filedrop" :class="{'disabled': uploadStatus == 'uploading'}">
                    <div class="cell">
                        <strong>Charger de nouveaux fichiers</strong>
                        <p>Vous pouvez glisser-déposer vos fichiers dans cette zone <br>(ou cliquer ici pour en sélectionner)</p>
                    </div>
                </label>
            </div>

            <div class="col-sm-6">

                <div class="progress-wrapper" v-if="uploadStatus == 'uploading'">
                    <div class="progress-cell">
                        <div class="progress">
                            <div class="progress-bar" role="progressbar" :style="'width:' + (100 * uploadProgress / uploadTotal) + '%'">
                                <span class="sr-only">{{100 * uploadProgress / uploadTotal}}%</span>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="button" class="btn btn-warning btn-block" @click="cancel()" v-if="uploadStatus == 'uploading'">
                    <i class="fa fa-close"></i> Annuler le chargement
                </button>

                <div v-if="uploadError" class="alert alert-danger">
                    Erreur {{ uploadError.response.status }} : {{ uploadError.response.statusText }}
                </div>

                <div v-if="uploadStatus == 'done'" class="alert alert-success">
                    <strong>Vos fichiers sont chargés !</strong>
                </div>

                <button type="button" class="btn btn-danger btn-block" @click="deleteDialog.modal('show')" v-if="hasSelection()">
                    <i class="fa fa-trash"></i> Supprimer les fichiers sélectionnés
                </button>

            </div>
        </div>

        <div class="text-right">
            <nav aria-label="Page navigation" v-show="pagination.total > pagination.per_page">
                <ul class="pagination">
                    <li v-if="pagination.current_page == 1" class="disabled">
                        <span>
                            <span aria-hidden="true">&laquo;</span>
                        </span>
                    </li>
                    <li v-else>
                        <a @click="loadPage(page - 1)" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                    <li v-for="p in pagination.last_page" :class="{'active': p === pagination.current_page}">
                        <a @click="loadPage(p)" v-if="p !== pagination.current_page">{{ p }}</a>
                        <span v-else>{{ p }}</span>
                    </li>
                    <li v-if="pagination.current_page == pagination.last_page" class="disabled">
                        <span>
                            <span aria-hidden="true">&raquo;</span>
                        </span>
                    </li>
                    <li v-else>
                        <a @click="loadPage(page + 1)" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>
                        <input-checkbox
                            name="select-all"
                            v-model="selectAll"
                            label=""
                            @input="toggleAll()">
                        </input-checkbox>
                    </th>
                    <th>Aperçu</th>
                    <th>Infos</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(item, idx) in items">
                    <td>
                        <input-checkbox
                            :name="item.id + '-selected'"
                            label=""
                            v-model="item.selected"
                            @input="toggle(item)">
                        </input-checkbox>
                    </td>
                    <td class="cell-preview">
                        <div class="preview">
                            <div class="image"
                                v-if="item.mime.indexOf('image') !== -1"
                                :style="'background-image:url(' + assetsBaseUrl + item.filepath + '?' + item.decache + ')'">
                            </div>
                            <video controls v-else-if="item.mime.indexOf('video') !== -1">
                                <source :src="assetsBaseUrl + item.filepath + '?' + item.decache" :type="item.mime" />
                            </video>
                            <div class="text-wrapper" v-else>
                                <div class="text">
                                    <div>{{ item.mime }}</div>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>    
                        <div>
                            <span class="lab">Titre :</span>
                            <span class="text-info">{{ item.title }}</span>
                        </div>
                        <div>
                            <span class="lab">Fichier :</span>
                            <span class="text-info">{{ item.filename }}</span>
                        </div>
                        <div>
                            <span class="lab">Type :</span>
                            <span class="text-info">{{ item.mime }}</span>
                        </div>
                        <div>
                            <span class="lab">Poids :</span>
                            <span class="text-info">
                                <span v-if="item.size < 1000000">{{ item.size / 1000 | number(1) }} ko</span>
                                <span v-else>{{ item.size / 1000000 | number(1) }} Mo</span>
                            </span>
                        </div>
                        <div v-if="item.mime.indexOf('image') !== -1">
                            <span class="lab">Taille :</span>
                            <span class="text-info">{{ item.width }} x {{ item.height }} px</span>
                        </div>
                    </td>
                    <td class="text-right">
                        <button 
                            type="button"
                            class="btn btn-default"
                            @click="download(item)">
                            <i class="fa fa-download"></i> Récupérer
                        </button>
                        <button 
                            type="button"
                            class="btn btn-default"
                            @click="openEditor(item)">
                            <i class="fa fa-edit"></i> Modifier
                        </button>
                        <button 
                            type="button"
                            class="btn btn-danger"
                            @click="destroy(item)">
                            <i class="fa fa-trash"></i> Supprimer
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="text-center">
            <nav aria-label="Page navigation" v-show="pagination.total > pagination.per_page">
                <ul class="pagination">
                    <li v-if="pagination.current_page == 1" class="disabled">
                        <span>
                            <span aria-hidden="true">&laquo;</span>
                        </span>
                    </li>
                    <li v-else>
                        <a @click="loadPage(page - 1)" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                    <li v-for="p in pagination.last_page" :class="{'active': p === pagination.current_page}">
                        <a @click="loadPage(p)" v-if="p !== pagination.current_page">{{ p }}</a>
                        <span v-else>{{ p }}</span>
                    </li>
                    <li v-if="pagination.current_page == pagination.last_page" class="disabled">
                        <span>
                            <span aria-hidden="true">&raquo;</span>
                        </span>
                    </li>
                    <li v-else>
                        <a @click="loadPage(page + 1)" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div-->

    </div>
</template>

<script>

    import Pagination from 'vue-crud/src/widgets/Pagination.vue'
    import MediaUpdater from './MediaUpdater.vue';
    //const MediaUpdater = () => import('./MediaUpdater.vue');

    export default {

        components:{
            mediaUpdater: MediaUpdater
        },

        data(){
            return{
                id: Math.floor(Math.random()*(9999-1000+1)+1000),
                files: null,
                search: {
                    text: '',
                },
                
                pagination: {},
                page: 1,

                selectAll: false,
                selected: [],

                loaded: false,
                //readStatus: 'none',

                uploadSource: null,
                uploadStatus: 'none',
                uploadProgress: 0,
                uploadTotal: 0,
                uploadError: null,

                destroyDialog: null,
                destroyStatus: 'none',
                destroyError: null
            };
        },

        watch: {
            search: {
                handler(){
                    this.page = 1;
                    this.read();
                },
                deep: true
            },
        },

        mounted() {

            var self = this;

            // -------------------------------------------------------
            //  Init dialogs
            // -------------------------------------------------------
            this.destroyDialog = $(this.$el).find('.destroy-dialog');

            this.destroyDialog.on('shown.bs.modal', function (e) {
                $(this).find('input').first().focus();
            });
            
            // -------------------------------------------------------
            //  Init browse button
            // -------------------------------------------------------
            $(this.$el).on('change', '.file-input', function(e){
                self.files = e.target.files;
                self.upload();
            }).on('click', '.file-input', function(e){
                this.value = null;
            });

            // -------------------------------------------------------
            //  Init cancel token
            // -------------------------------------------------------
            var CancelToken = axios.CancelToken;
            this.uploadSource = CancelToken.source();

            // -------------------------------------------------------
            //  read items
            // -------------------------------------------------------
            this.read();

            // -------------------------------------------------------
            //  Prevent from openning file in browser on drop
            // -------------------------------------------------------
            window.addEventListener("dragover", function(e){
                e.preventDefault();
            }, false);

            window.addEventListener("drop", function(e){
                e.preventDefault();
            }, false);

            // -------------------------------------------------------
            //  Init drag and drop
            // -------------------------------------------------------
            $(this.$el).find('.filedrop').on('drop', function(e){
                e.preventDefault();
                e.stopPropagation();
                //console.log('files dropped', e.originalEvent.dataTransfer.files);
                self.files = e.originalEvent.dataTransfer.files;
                self.upload();
            });
        },

        methods: {

            read: function()
            {
                var url = '/api/media?page=' + this.page;
                url += '&text=' + this.search.text;
                this.loaded = false;

                axios.get(url).then(response => {

                    this.pagination = response.data;
                    this.loaded = true;

                }).catch(response => {

                    this.loaded = true;

                });
            },

            loadPage(p){
                this.page = p;
                this.read();
            },

            upload: function()
            {
                var self = this;

                this.uploadStatus = 'uploading';
                this.uploadProgress = 0;
                this.uploadTotal = 0;
                this.uploadError = null;

                var formData = new FormData();
                for(var i=0; i<this.files.length; ++i){
                    formData.append('file-' + i, this.files[i], this.files[i].name);
                }

                var config = {
                    onUploadProgress: function(e) {
                        console.log('upload progress', e);
                        if (e.lengthComputable) {
                            self.uploadProgress = e.loaded;
                            self.uploadTotal = e.total;
                        }
                    },
                    //cancelToken: this.uploadSource
                };

                axios.post('/api/media', formData, config).then(response => {
                    
                    this.uploadStatus = 'done';
                    this.read();
                    
                }).catch(response => {
                    
                    this.uploadStatus = 'none';
                    this.uploadError = response.data;
                });
            },

            cancel: function()
            {
                if(this.uploadSource){
                    console.log('upload cancel');
                    this.uploadSource.cancel();
                }else{
                    console.log('Cannot cancel', this.uploadSource);
                }
            },

            openDestroy(item){
                
                this.destroyItem = item;
                this.destroyError = null;
                this.destroyStatus = null;
                this.destroyDialog.modal('show');
            },

            destroy() {

                this.destroyStatus = 'loading';
                
                axios.delete('/api/media/' + this.destroyItem.id).then(response => {

                    this.read();
                    this.destroyStatus = 'success';
                    this.destroyDialog.modal('hide');

                }).catch(response => {

                    this.destroyStatus = 'error';
                    this.destroyError = response.data;

                });

            },

            download(item){
                window.location.href = '/api/media/' + item.id + '/download';
            },

            openUpdate(item){
                this.$refs.mediaUpdater.openUpdateDialog(item);
            },
        }
    }
</script>
