<template>
	<div class="container">
		<div style="width: 100%;"><img v-show="loading" style="margin-left: auto; margin-right: auto; height: 30px; width: 30px; display: block;" src="/img/loading_2.gif" alt="Loading"></div>
		<div v-if="errmsg != ''" class="alert-maba">{{ errmsg }}</div>
		<div class="row" style="padding-top: 10px; padding-bottom: 10px;">
			<label for="placename" class="col-md-1 col-form-label text-md-right form-control-xs" style="font-size: 11px;"><b>Place Name:</b></label>
            <div class="col-md-2">
                <input id="placename" type="text" class="form-control form-control-sm" v-model="form.name" placeholder="Name of Type" style="font-size: 11px;">
            </div>
            <div class="col-md-2"><button class="btn btn-primary btn-sm" @click="getPlaces">Search</button></div>
			<div class="col-md-2"><button class="btn btn-success btn-sm" @click="addPlace">Add</button></div>
		</div>
		<div class="row" style="padding-top: 10px; padding-bottom: 10px;">
			<table class="table" style="font-size: 12px; margin-left: auto; margin-right: auto;">
				<tr>
					<th>Name</th>
					<th colspan="2">Action</th>
				</tr>
				<tr v-for = "(place,index) in places">
					<td>
						<span v-if="editPlace.id != place.id">{{ place.name }}</span>
						<input class="form-control form-control-sm" type="text" v-bind:value="place.name" v-if="editPlace.id == place.id" v-on:input="setEditName">
					</td>
					<td>
						<button v-if="editPlace.id != place.id && !loading" class="btn btn-warning btn-sm" @click="editData" v-bind:value="index">Edit</button>
						<button v-if="editPlace.id == place.id" class="btn btn-success btn-sm" @click="saveEdited" v-bind:value="index">Save</button>
						<button v-if="editPlace.id == place.id" class="btn btn-secondary btn-sm" @click="cancelEdit" v-bind:value="index">Cancel</button>
					</td>
					<td>
						<button v-if="!loading" class="btn btn-danger btn-sm" @click="deletePlace" v-bind:value="index">Delete</button>
					</td>
				</tr>
			</table>
			<pagination :data="placeresponse" @pagination-change-page="getPlaces" :limit="5">
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
				places: [],
				editPlace: {
					id: -1,
				},
				placeresponse: {},
			}
		},
		mounted() {
			this.getPlaces();
		},
		methods: {
			cancelEdit: function(){
				this.editPlace.id = -1;
				this.editPlace.name = '';
			},
			setEditName: function(event){
				this.editPlace.name = event.target.value;
			},
			editData: function(event){
				//console.log(this.types[event.target.value]);
				this.editPlace.id = this.places[event.target.value].id;
				this.editPlace.name = this.places[event.target.value].name;
			},
			getPlaces: function (page=1){
				let self = this;
				this.loading = true;
				this.errmsg = "";
				var url = '/api/backup/places/list/';
				page = typeof(page) == 'object'? 1: page;
				url = url + '?page=' + page;
				if (this.form.name !== undefined)
					url = url + '&name=' + this.form.name;
				axios.get(url).then((response) => {
					this.loading = false;
					this.placeresponse = response.data;
                    this.places = response.data.data;
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
				axios.put('/api/backup/places/update/' + this.places[event.target.value].id, this.editPlace)
				.then((response) => {
					this.loading = false;
					//console.log(response.data.data);
					if (response.data.data == true){
						this.places[event.target.value].name = this.editPlace.name;
					}
					else
						console.log(response.data);
					this.cancelEdit();
				})
				.catch(function(error){
					self.loading = false;
					self.cancelEdit();
					console.log(error);
					if (error.response.status == 401)
                        window.location.href = '/login';
                    else if (error.response.status == 422)
                        self.errmsg =error.response.data.message
                    else
                		self.errmsg = error;
				})
			},
			deletePlace: function(event){
				let self = this;
				this.loading = true;
				this.errmsg = "";
				axios.delete('/api/backup/places/delete/' + this.places[event.target.value].id)
				.then((response) => {
					this.loading = false;
					this.places.splice(event.target.value, 1);
				})
				.catch(function(error){
					self.loading = false;
					//self.cancelEdit();
					//console.log(error);
					if (error.response.status == 401)
                        window.location.href = '/login';
                    else if (error.response.status == 422)
                        self.errmsg = error.response.data.message
                    else if (error.response.status == 500)
                        self.errmsg = error.response.data.message;
                    else
                		self.errmsg = error;
				})
			},
			addPlace: function (){
				let self = this;
				this.loading = true;
				this.errmsg = "";
				var pagesize = 10;
				axios.post('/api/backup/places/add', this.form).then((response) => {
					this.places = response.data.data.concat(this.places);
					if (this.places.length > pagesize){
						var iluu = this.places.length - pagesize;
						for (var i = 0; i < iluu; i++)
							this.places.pop();		
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