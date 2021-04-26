<template>
	<div class="container">
        <div v-if="errmsg != ''" class="alert-maba">{{ errmsg }}</div>
        <div class="row" style="margin: auto; padding: 10px;">
            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                <form v-on:submit.prevent="uploadData" enctype="multipart/form-data">
                    <div class="form-group row" style="padding-right: 15px;">
                        <label for="season" class="col-md-1 col-form-label text-md-right form-control-sm" style="font-size: 11px;"><b>Season:</b></label>
                        <div class="col-md-2">
                            <select id="season" class="form-control-sm form-control" refs="season" v-on:change="valueCheck" v-model="season" style="font-size: 11px;">
                                <option disabled="disabled" value>Choose Season</option>
                                <option v-for="season in seasons" v-bind:value="season.id">
                                {{ season.name }}
                                </option>
                            </select>
                        </div>
                        <label for="files" class="col-md-1 col-form-label text-md-right form-control-sm" style="font-size: 11px;"><b>CVS File:</b></label>
                        <div class="col-md-5">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="file" multiple ref="files" v-on:change="handleFileUpload" accept=".csv">
                                <label class="custom-file-label">Choose file</label>
                            </div>
                        </div>
                        <label for="beggining" class="col-md-1 col-form-label text-md-right form-control-sm" style="font-size: 11px;padding-top: 0; padding-bottom: 0; padding-left: 30; padding-right: 0; min-width: 150px;"><b>The beggining of the season:</b></label>
                        <div class="col-md-2" style="padding-left: 0; max-width: 80px;">
                            <toggle-button id="beggining" v-model="beggining" :labels="{checked: 'Yes', unchecked: 'No'}"/>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary" v-if="!loading">Upload</button>
                </form>
                <div style="padding: 10px 0;">
                    <button class="btn btn-warning" v-if="compare" v-on:click="checkFixed">Check Fixed</button>
                </div>
            </div>
            <div style="width: 100%;"><img v-show="loading" style="margin-left: auto; margin-right: auto; height: 30px; width: 30px; display: block;" src="/img/loading_2.gif" alt="Loading">
            </div>
            <div style="width: 100%; padding: 10px;">
                <div>
                    <span style="font-size: 11px;"><b>Stored Vulnerabilities:</b> {{ storedVulners }} </span>&nbsp;
                    <span style="font-size: 11px;"><b>Number of File:</b> {{ numberOfFiles }} </span>&nbsp;
                    <span style="font-size: 11px;"><b>Total file size:</b> {{ (totalFileSize/1024/1024).toFixed(3) }} MB</span>&nbsp;
                    <span v-if="uploaded > 0" style="font-size: 11px;"><b>Uploaded vulners:</b> {{ uploaded }}</span>
                    <span v-if="fixed > 0" class="bg-success" style="font-size: 11px; padding: 2px; border-radius: 2px; color: white;"><b>Fixed vulners:</b> {{ fixed }}</span>
                </div>
                <div v-if="numberOfFiles > 0" style="border: 1px solid gray; min-width: 200px; padding: 20px; overflow: hidden; overflow-y: scroll; max-height: 500px;">
                    <ul class="file-ul">
                        <li v-for="(file, index) in files">
                            <b>{{ file.name }}</b> - size: {{ (file.size/1024/1024).toFixed(3) }}MB - Last modified: {{ file.lastModifiedDate }} <span class="close" @click="removItem(index)">x</span>
                        </li>
                    </ul>
                </div>
                <div v-if="uploadedFiles.length > 0" style="margin-top: 10px;">
                    Uploaded Files:
                    <ul>
                        <li v-for="ufile in uploadedFiles" style="font-size: 12px;">
                            {{ ufile.name }}
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
	import {ToggleButton} from 'vue-js-toggle-button'
	export default {
        //props: ['subpage'],
		data() {
			return {
                errmsg: '',
                beggining: true,
                files: [],
                uploadedFiles : [],
                season: '',
                seasons: [],
                loading: false,
                storedVulners: 0,
                numberOfFiles: 0,
                totalFileSize: 0,
                uploaded: 0,
                compare: false,
                fixed: 0,
			}
		},
		components: {
			ToggleButton,
		},
        mounted() {
            this.getSeasons(),
            //console.log('loaded'),
            $('[data-toggle="tooltip"]').tooltip(this.tooltipOptions);
            //console.log(this.subpage);
        },        
        methods: {
            checkFixed: function(){
                this.compare = false;
                this.loading = true;
                axios.get('/api/checkfixed?sid=' + this.season)
                .then((response) => {
                    //console.log(response.data);
                    this.compare = true;
                    this.loading = false;
                    if (response.data.data.executed == true)
                        this.fixed = response.data.data.fixed
                })
                .catch( function (error){
                    self = this;
                    self.compare = true;
                    self.loading = false;
                    //console.log(error.response);
                    if (error.response.status == 401)
                        window.location.href = '/login';
                    else if (error.response.status == 500)
                        alert("Error: " + error.response.data.message);
                    else if (error.response.status == 422)
                        alert("Incomplete data, make shure the choose season");
                        //alert("Error: " + error.response.data.message);
                    else
                        alert("Error: " + error.response.data.message);
                })
            },
            removItem: function(index){
                //console.log(index);
                var sizes = 0
                this.files.splice(index, 1);
                this.numberOfFiles =  this.files.length;
                for (var i = 0; i < this.numberOfFiles; i++){
                    sizes += this.files[i].size;
                }
                this.totalFileSize = sizes;
            },
            /*
            *handleFileUpload нь файл давхардсан үгүйг шалгаад давхардаагүй бол оруулна, мөн оруулсан файлуудын тус бүрийн хэмжээ болон нийт файлын хэмжээг авч хувьсагчид өгнө.
            */
            handleFileUpload: function(){
                var selectedFiles = this.$refs.files.files;
                var exists = false;
                var sizes = 0;
                //console.log(selectedFiles);
                for (var i=0; i < selectedFiles.length; i++){
                    //var file = selectedFiles[i];
                    //console.log(selectedFiles[i]);
                    for (var j=0; j < this.numberOfFiles; j++){
                        if (this.files[j].name == selectedFiles[i].name &&
                            this.files[j].size == selectedFiles[i].size)
                            exists = true;
                        else
                            exists = false;
                    }
                    if (!exists)
                        this.files.push(selectedFiles[i]);
                }
                this.numberOfFiles = this.files.length;
                for (var i = 0; i < this.numberOfFiles; i++){
                    sizes += this.files[i].size;
                }
                this.totalFileSize = sizes;
                //console.log(this.files);
            },
            valueCheck: function(event){
                if (event.target.value === ""){
                    this.season = "";
                }
                else {
                    this.checkCompare();
                }
            },
            /*
            *Сонгосон улиралд засагдсан үгүйг шалгах боломж байгаа эсэхийг тогтооно. Засагдсан эсэхийг шалгах боломж байвал Check Fixed гэсэн товчлуур гарна
            */
            checkCompare: function (){
                this.loading = true;
                axios.get("/api/checkcompareable?sid=" + this.season)
                .then((response) => {
                    //console.log(response.data);
                    this.compare = response.data.data;
                    this.loading = false;
                })
                .catch( function (error){
                    self = this;
                    self.loading = false;
                    console.log(error.response);
                    if (error.response.status == 401)
                        window.location.href = '/login';
                    else if (error.response.status == 500)
                        alert("Error: " + error.response.data.message);
                    else if (error.response.status == 422)
                        alert("Incomplete data, make shure the choose season");
                        //alert("Error: " + error.response.data.message);
                    else
                        alert("Error: " + error.response.data.message);
                })
            },
            getSeasons: function(){
                axios.get('/api/getseasons')
                .then((response) => {
                    this.seasons = response.data.data;
                    /*for (var i = 0; i < response.data.data.length; i++){
                        //console.log(response.data.data[i].id);
                        this.seasons.push({
                            value: response.data.data[i].id,
                            text: response.data.data[i].name
                        });
                    }*/
                })
                .catch( function (error) {console.log(error)})
            },
            uploadData: function(){
                this.loading = true;
                let self = this;
                let formData = new FormData();
                for (var i=0; i < this.files.length; i++){
                    let file = this.files[i];
                    formData.append('files[' + i + ']', file);
                }
                formData.append('season', this.season);
                formData.append('beggining', this.beggining);
                axios.post ('/api/vulnerupload', formData)
                .then ((response) => {
                    //console.log(response.data);
                    this.errmsg = '';
                    this.uploaded = this.uploaded + response.data.rows;
                    this.loading = false;
                    this.uploadedFiles.push(...this.files);
                    this.files = [];
                    this.numberOfFiles = 0;
                    this.totalFileSize = 0;
                    this.checkCompare();
                })
                .catch( function (error) {
                    self.loading = false;
                    self.errmsg = '';
                    //console.log(error.response);
                    if (error.response.status == 401)
                        window.location.href = '/login';
                    else if (error.response.status == 500)
                        self.errmsg = error.response.data.message;
                    else if (error.response.status == 422){
                        self.errmsg = "Incomplete data, make shure choose the season and/or file";
                        for(const index in error.response.data.errors)
                            self.errmsg += error.response.data.errors[index] + " ";
                    }
                    else
                        self.errmsg = "Error: " + error;

                    setTimeout(function(){self.errmsg = ''}, 5000);
                })
            },
    	}
    }
</script>