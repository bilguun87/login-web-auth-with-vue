<template>
	<div class="container">
		<div style="width: 100%;"><img v-show="loading" style="margin-left: auto; margin-right: auto; height: 30px; width: 30px; display: block;" src="/img/loading_2.gif" alt="Loading"></div>
		<div v-if="errmsg != ''" class="alert-maba">{{ errmsg }}</div>
		<div class="row" style="padding-top: 10px; padding-bottom: 10px;">
			<label for="typename" class="col-md-1 col-form-label text-md-right form-control-xs" style="font-size: 11px;"><b>Type Name:</b></label>
            <div class="col-md-2">
                <input id="typename" type="text" class="form-control form-control-sm" v-model="form.name" placeholder="Name of Type" style="font-size: 11px;">
            </div>
            <div class="col-md-2"><button class="btn btn-primary btn-sm" @click="getTypes">Search</button></div>
			<div class="col-md-2"><button class="btn btn-success btn-sm" @click="addType">Add</button></div>
		</div>
		<div class="row" style="padding-top: 10px; padding-bottom: 10px;">
			<table class="table" style="font-size: 12px; margin-left: auto; margin-right: auto;">
				<tr>
					<th>Name</th>
					<th colspan="2">Action</th>
				</tr>
				<tr v-for = "(type,index) in types">
					<td>
						<span v-if="editType.id != type.id">{{ type.name }}</span>
						<input class="form-control form-control-sm" type="text" v-bind:value="type.name" v-if="editType.id == type.id" v-on:input="setEditName">
					</td>
					<td>
						<button v-if="editType.id != type.id && !loading" class="btn btn-warning btn-sm" @click="editData" v-bind:value="index">Edit</button>
						<button v-if="editType.id == type.id" class="btn btn-success btn-sm" @click="saveEdited" v-bind:value="index">Save</button>
						<button v-if="editType.id == type.id" class="btn btn-secondary btn-sm" @click="cancelEdit" v-bind:value="index">Cancel</button>
					</td>
					<td>
						<button v-if="!loading" class="btn btn-danger btn-sm" @click="deleteType" v-bind:value="index">Delete</button>
					</td>
				</tr>
			</table>
			<pagination :data="typeresponse" @pagination-change-page="getTypes" :limit="5">
                <span slot="prev-nav">&lt; Previous</span>
                <span slot="next-nav">Next &gt;</span>
            </pagination>
		</div>
	</div>
	
</template>
<script>
	export default {
		data() {
			return {
				form: {

				},
				errmsg: '',
				loading: false,
				types: [],
				editType: {
					id: -1,
				},
				typeresponse: {},
			}
		},
		mounted() {
			this.getTypes();
		},
		methods: {
			cancelEdit: function(){
				this.editType.id = -1;
				this.editType.name = '';
			},
			setEditName: function(event){
				this.editType.name = event.target.value;
			},
			editData: function(event){
				//console.log(this.types[event.target.value]);
				this.editType.id = this.types[event.target.value].id;
				this.editType.name = this.types[event.target.value].name;
			},
			getTypes: function (page=1){
				let self = this;
				this.loading = true;
				this.errmsg = "";
				var url = '/api/backup/types/list/';
				page = typeof(page) == 'object'? 1: page;
				url = url + '?page=' + page;
				if (this.form.name !== undefined)
					url = url + '&name=' + this.form.name;
				axios.get(url).then((response) => {
					this.loading = false;
					this.typeresponse = response.data;
                    this.types = response.data.data;
                    //console.log(this.records);
                }).catch(function (error) {
                    self.loading = false;
                    self.errmsg = '';
                    console.log(error);
                    if (error.response.status == 401)
                        window.location.href = '/login';
                    else if (error.response.status == 500)
                        self.errmsg = error.response.data.message;
                    else if (error.response.status == 422){
                    	self.errmsg = error.response.data.message;
                    	console.log(error)
                    }
                    else
                        self.errmsg = error.response.data.message;
                    setTimeout(function(){self.errmsg = ''}, 5000);
                })
			},
			saveEdited: function(event){
				let self = this;
				this.loading = true;
				this.errmsg = "";
				axios.put('/api/backup/types/update/' + this.types[event.target.value].id, this.editType)
				.then((response) => {
					this.loading = false;
					//console.log(response.data.data);
					if (response.data.data == true){
						this.types[event.target.value].name = this.editType.name;
					}
					else
						console.log(response.data);
					this.cancelEdit();
				})
				.catch(function(error){
					self.loading = false;
					self.cancelEdit();
					//console.log(error);
					if (error.response.status == 401)
                        window.location.href = '/login';
                    else if (error.response.status == 422)
                        self.errmsg =error.response.data.message
                    else if (error.response.status == 500)
                        self.errmsg = error.response.data.message;
                    else
                		self.errmsg = error;
				})
			},
			deleteType: function(event){
				let self = this;
				this.loading = true;
				this.errmsg = "";
				axios.delete('/api/backup/types/delete/' + this.types[event.target.value].id)
				.then((response) => {
					this.loading = false;
					this.types.splice(event.target.value, 1);
				})
				.catch(function(error){
					self.loading = false;
					//self.cancelEdit();
					console.log(error);
					if (error.response.status == 401)
                        window.location.href = '/login';
                    else if (error.response.status == 422)
                        self.errmsg =error.response.data.message
                    else
                		self.errmsg = error;
				})
			},
			addType: function (){
				let self = this;
				this.loading = true;
				this.errmsg = "";
				var pagesize = 10;
				axios.post('/api/backup/types/add', this.form).then((response) => {
					this.types = response.data.data.concat(this.types);
					if (this.types.length > pagesize){
						var iluu = this.types.length - pagesize;
						for (var i = 0; i < iluu; i++)
							this.types.pop();		
					}
					this.loading = false;
				}).catch(function (error) {
					self.loading = false;
                    //self.clearErrmsg();
                    console.log(error);
                    if (error.response.status == 401)
                        window.location.href = '/login';
                    else if (error.response.status == 500)
                        self.errmsg = error.response.data.message;
                    else if (error.response.status == 422){
                    	self.errmsg = error.response.data.message;
                    	//console.log(error)
                    }
                    else if (error.response.status == 501)
                    	self.errmsg = error.response.data.errorInfo[2];
                    	//console.log(error.response.data.errorInfo[2]);
                    else
                        self.errmsg = error.response.data.message;
                        
                    setTimeout(function(){self.errmsg = ''}, 5000);
				})
			},
		}
	}
</script>