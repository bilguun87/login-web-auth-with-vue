<template>
	<div class="container">
		
		<div class="row" style="padding-top: 10px; padding-bottom: 10px;">
			<label for="records" class="col-md-1 col-form-label text-md-right form-control-sm" style="font-size: 11px;"><b>Backup Name:</b></label>
			<div id="records" class="col-md-2" style="position: relative;">
            	<div class="row" style="padding: 0 15px;">
            	<input id="recordname" type="text" class="form-control form-control-sm" placeholder="Name of Type" style="font-size: 11px; float: left; margin-right: 0;" @input="typing = true" v-model="recname" ref="recordname" @click="checkRecords">
            	</div>
            	<div class="row">
            		<div class="custom-select-container" ref="custom-container">
		            	<ul class="custom-select-ul">
		            		<li v-for="(record,index) in records" style="font-size: 11px; padding: 3px 10px; display: block;" @click="setRecordId" :value="index">
		            			{{ record.name }}
		            		</li>
		            	</ul>
	            	</div>
                </div>
            </div>
            <label for="ttype" class="col-md-1 col-form-label text-md-right form-control-sm" style="font-size: 11px;"><b>Type:</b></label>
            <div class="col-md-2">
                <select id="ttype" class="form-control form-control-sm" v-on:change="valueCheck" v-model="form.type_id" style="font-size: 11px;">
                    <option selected="selected" value>All</option>
                    <option v-for="type in types" v-bind:value="type.id">
                    {{ type.name }}
                    </option>
                </select>
            </div>
			<label for="ogson" class="col-md-1 col-form-label text-md-right form-control-xs" style="font-size: 11px;"><b>Giver:</b></label>
            <div class="col-md-2">
                <input id="ogson" type="text" class="form-control form-control-sm" v-model="form.performer" placeholder="Type name of Performer" style="font-size: 11px;">
            </div>
            <label for="awsan" class="col-md-1 col-form-label text-md-right form-control-xs" style="font-size: 11px;"><b>Receiver:</b></label>
            <div class="col-md-2">
                <input id="awsan" type="text" class="form-control form-control-sm" v-model="form.requestor" placeholder="Type name of Requestor" style="font-size: 11px;">
            </div>
		</div>
		<div class="row">
            <label for="date" class="col-md-1 col-form-label text-md-right form-control-sm" style="font-size: 11px;"><b>Date:</b></label>
            <div class="col-md-2">
				<datepicker id="date" :format="'yyyy-MM-dd'" @selected="searchDate" @cleared="clearDate" :value="form.date" :clear-button="true" :monday-first="true" :highlighted="highlighted" placeholder="Select date"></datepicker>
			</div>
			<label for="place" class="col-md-1 col-form-label text-md-right form-control-sm" style="font-size: 11px;"><b>Place:</b></label>
            <div class="col-md-2">
                <select id="ttype" class="form-control form-control-sm" v-on:change="valueCheck" v-model="form.place_id" style="font-size: 11px;">
                    <option selected="selected" value>All</option>
                    <option v-for="place in places" v-bind:value="place.id">
                    {{ place.name }}
                    </option>
                </select>
            </div>
            <select class="form-control form-control-sm col-md-1" v-if="form.place_id > 0" v-model="form.condition">
            	<option value="is" selected>=</option>
            	<option value="not">!=</option>
            </select>
            <label for="desc" class="col-md-1 col-form-label text-md-right form-control-xs" style="font-size: 11px;"><b>Description:</b></label>
            <div class="col-md-4">
                <input id="desc" type="text" class="form-control form-control-sm" v-model="form.description" placeholder="Request description" style="font-size: 11px;">
            </div>
		</div>
		<div class="row" style="padding-top: 10px; padding-bottom: 10px;">
			<div class="col-md-2"><button class="btn btn-primary btn-sm" @click="getMoves">Search</button></div>
			<div class="col-md-2"><button class="btn btn-success btn-sm" @click="addMove">Add</button></div>
		</div>
		<div style="width: 100%;"><img v-show="loading" style="margin-left: auto; margin-right: auto; height: 30px; width: 30px; display: block;" src="/img/loading_2.gif" alt="Loading"></div>
		<div v-if="errmsg != ''" class="alert-maba">{{ errmsg }}</div>
		<div class="row" style="padding-top: 10px; padding-bottom: 10px;">
			<table class="table" style="font-size: 12px; margin-left: auto; margin-right: auto;">
				<tr>
					<th>Backup Name</th>
					<th>Place</th>
					<th>Date</th>
					<th>Requestor Name</th>
					<th>Performer Name</th>
					<th>Description</th>
					<th>Action</th>
				</tr>
				<tr v-for = "(move,index) in moves">
					<td>{{ move.record_name }}</td>
					<td>
						<span v-if="editMove.id != move.id">{{ move.place_name }}</span>
						<select class="form-control form-control-sm" v-model="editMove.place_id" style="font-size: 11px;" v-if="editMove.id == move.id">
                            <option v-for="place in places" :value="place.id" :selected="editMove.place_id == place.id">
                            {{ place.name }}
                            </option>
                        </select>
					</td>
					<td>
						<span v-if="editMove.id != move.id">{{ move.move_start_date }}</span>
						<datepicker v-if="editMove.id == move.id" :value="editMove.date" :format="'yyyy-MM-dd'" @selected="setEditDate"></datepicker>
					</td>
					<td>
						<span v-if="editMove.id != move.id">{{ move.requester_name }}</span>
						<input class="form-control form-control-sm" type="text" v-bind:value="move.requester_name" v-if="editMove.id == move.id" v-on:input="setEditRequester">
					</td>
					<td>
						<span v-if="editMove.id != move.id">{{ move.performer_name }}</span>
						<input class="form-control form-control-sm" type="text" v-bind:value="move.performer_name" v-if="editMove.id == move.id" v-on:input="setEditPerformer">
					</td>
					<td style="width: 250px;">
						<span v-if="editMove.id != move.id">{{ move.description }}</span>
						<input class="form-control form-control-sm" type="text" :value="move.description" v-if="editMove.id == move.id" v-on:input="setEditDesc">
					</td>
					<td>
						<button v-if="editMove.id != move.id && !loading" class="btn btn-warning btn-sm" @click="editData" v-bind:value="index">Edit</button>
						<button v-if="editMove.id == move.id" class="btn btn-success btn-sm" @click="saveEdited" v-bind:value="index">Save</button>
						<button v-if="editMove.id == move.id" class="btn btn-secondary btn-sm" @click="cancelEdit" v-bind:value="index">Cancel</button>
					</td>
					<!--td>
						<button v-if="!loading" class="btn btn-danger btn-sm" @click="deletePlace" v-bind:value="index">Delete</button>
					</td-->
				</tr>
			</table>
			<pagination :data="moveresponse" @pagination-change-page="getMoves" :limit="5">
                <span slot="prev-nav">&lt; Previous</span>
                <span slot="next-nav">Next &gt;</span>
            </pagination>
		</div>
	</div>
	
</template>
<script>
	import Datepicker from 'vuejs-datepicker';
	export default {
		data() {
			return {
				typing: false,
				recname: '',
				form: {
					place_id: -1,
					condition: 'is',
				},
				errmsg: '',
				loading: false,
				places: [],
				types: [],
				moves: [],
				records: [],
				moveresponse: {},
				editMove: {
					id: -1,
				},
				highlighted: {
			    	customPredictor: function(date) {
					var today = new Date();
					if(date.getDate() == today.getDate() && date.getMonth() == today.getMonth())
						return true
			    	}
				}
			}
		},
		mounted() {
			this.getPlaces();
			this.getTypes();
			this.getMoves();
		},
		components: {Datepicker},
		watch: {
			recname: _.debounce(function() {
				this.typing = false;
			}, 1000),
			typing: function(value) {
				if (!value) {
					this.searchRecord();
				}
			}
		},
		methods: {
			clearDate: function(){
				//console.log('cleared');
				this.form.date = "";
			},
			searchDate: function(date){
				this.form.date = new Date(date).toISOString().slice(0,10);
				//console.log(date);
			},
			valueCheck: function(event){
                if (event.target.value === ""){
                    //console.log("Hooson");
                    if (event.target.id == "ttype")
                        delete this.form["type_id"];
                        //Vue.delete(this.form, 'group_id')
                    if (event.target.id == "place")
                        delete this.form["place_id"];
                }
                //console.log('testing');
            },
			cancelEdit: function(){
				this.editMove.id = -1;
				delete this.editMove.record_id;
				delete this.editMove.place_id;
				delete this.editMove.requester_name;
				delete this.editMove.performer_name;
				delete this.editMove.date;
			},
			setEditRequester: function(event){
				this.editMove.requester_name = event.target.value;
			},
			setEditPerformer: function(event){
				this.editMove.performer_name = event.target.value;
			},
			setEditDesc: function(event){
				this.editMove.description = event.target.value;
			},
			editData: function(event){
				this.editMove.id = this.moves[event.target.value].id;
				this.editMove.record_id = this.moves[event.target.value].record_id;
				this.editMove.place_id = this.moves[event.target.value].place_id;
				this.editMove.requester_name = this.moves[event.target.value].requester_name;
				this.editMove.performer_name = this.moves[event.target.value].performer_name;
				this.editMove.date = this.moves[event.target.value].move_start_date;
				this.editMove.description = this.moves[event.target.value].description
			},
			setEditDate: function(date){
				this.editMove.date = new Date(date).toISOString().slice(0,10);
			},
			getPlaces: function (){
				let self = this;
				this.loading = true;
				this.errmsg = "";
				axios.get('/api/backup/places').then((response) => {
					this.loading = false;
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
			getTypes: function (){
				let self = this;
				this.loading = true;
				this.errmsg = "";
				axios.get('/api/backup/types').then((response) => {
					this.loading = false;
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
			searchRecord: function (){
				if (this.recname.length == 0){
					this.records = [];
					return;
				}
				let self = this;
				this.loading = true;
				this.errmsg = "";
				var url = '/api/backup/recbyname?name=' + this.recname;
				axios.get(url).then((response) => {
					this.loading = false;
                    this.records = response.data.data;
                    if(this.records.length > 0)
                    	this.$refs['custom-container'].style.display = 'block'
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
			getMoves: function (page=1){
				let self = this;
				this.loading = true;
				this.errmsg = "";
				var url = '/api/backup/moves/list/';
				page = typeof(page) == 'object'? 1: page;
				url = url + '?page=' + page;
				if (this.form.record_id !== undefined)
					url = url + '&record_id=' + this.form.record_id;
				if (this.form.place_id > 0)
					url = url + '&place_id=' + this.form.place_id + '&condition=' + this.form.condition;
				if (this.form.type_id !== undefined)
					url = url + '&type_id=' + this.form.type_id;
				if (this.form.performer !== undefined)
					url = url + '&performer=' + this.form.performer;
				if (this.form.requestor !== undefined)
					url = url + '&requestor=' + this.form.requestor;
				if (this.form.date !== undefined)
					url = url + '&date=' + this.form.date;
				axios.get(url).then((response) => {
					this.loading = false;
					this.moveresponse = response.data;
                    this.moves = response.data.data;
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
				axios.put('/api/backup/moves/update/' + this.moves[event.target.value].id, this.editMove)
				.then((response) => {
					this.loading = false;
					//console.log(response.data.data);
					if (response.data.data == true){
						this.moves[event.target.value].requester_name = this.editMove.requester_name;
						this.moves[event.target.value].performer_name = this.editMove.performer_name;
						this.moves[event.target.value].description = this.editMove.description;
						this.moves[event.target.value].move_start_date = this.editMove.date;
						this.moves[event.target.value].place_id = this.editMove.place_id;
						this.moves[event.target.value].place_name = this.places.filter(obj => { return obj.id === this.editMove.place_id })[0].name;
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
			/*deletePlace: function(event){
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
			},*/
			setRecordId: function(event){
				this.form.record_id = this.records[event.target.value].id;
				this.$refs['recordname'].value = this.records[event.target.value].name;
				this.$refs['custom-container'].style.display = 'none';
				//console.log(event.target.value);
			},
			checkRecords: function(){
				var container = this.$refs['custom-container'];
				if (this.records.length > 0 && container.style.display != 'block') {
					container.style.display = 'block';
				}
				else
					container.style.display = 'none';
			},
			addMove: function (){
				let self = this;
				this.loading = true;
				this.errmsg = "";
				var pagesize = 10;
				axios.post('/api/backup/moves/add', this.form).then((response) => {
					this.moves = response.data.data.concat(this.moves);
					if (this.moves.length > pagesize){
						var iluu = this.moves.length - pagesize;
						for (var i = 0; i < iluu; i++)
							this.moves.pop();		
					}
					/**/
					
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