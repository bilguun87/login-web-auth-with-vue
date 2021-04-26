<template>
	<div class="container">
		
		<div class="row" style="padding-top: 20px; padding-bottom: 10px;">
			
			<label for="tapename" class="col-md-1 col-form-label text-md-right form-control-xs" style="font-size: 11px;"><b>Backup Name:</b></label>
            <div class="col-md-2">
                <input id="tapename" type="text" class="form-control form-control-sm" v-model="form.name" placeholder="Backup of Tape" style="font-size: 11px;">
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
                <input id="ogson" type="text" class="form-control form-control-sm" v-model="form.ogson" placeholder="Type name of Performer" style="font-size: 11px;">
            </div>
            <label for="awsan" class="col-md-1 col-form-label text-md-right form-control-xs" style="font-size: 11px;"><b>Receiver:</b></label>
            <div class="col-md-2">
                <input id="awsan" type="text" class="form-control form-control-sm" v-model="form.awsan" placeholder="Type name of Requestor" style="font-size: 11px;">
            </div>
		</div>
		<div class="row" style="padding-top: 10px; padding-bottom: 10px;">
			<label for="date" class="col-md-1 col-form-label text-md-right form-control-sm" style="font-size: 11px;"><b>Date:</b></label>
			<div class="col-md-2">
				<datepicker id="date" :format="'yyyy-MM-dd'" @selected="searchDate" @cleared="clearDate" :value="form.date" :clear-button="true" :monday-first="true" :highlighted="highlighted"></datepicker>
			</div>
			<label for="place" class="col-md-1 col-form-label text-md-right form-control-sm" style="font-size: 11px;"><b>Place:</b></label>
            <div class="col-md-2">
                <select id="place" class="form-control form-control-sm" v-on:change="valueCheck" v-model="form.place_id" style="font-size: 11px;">
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
		</div>
		<div class="row" style="padding-top: 10px; padding-bottom: 10px;">
			<div class="col-md-2"><button class="btn btn-primary btn-sm" @click="getRecords">Search</button></div>
			<div class="col-md-2"><button class="btn btn-success btn-sm" @click="addRecord">Add</button></div>
		</div>
		<div style="width: 100%;"><img v-show="loading" style="margin-left: auto; margin-right: auto; height: 30px; width: 30px; display: block;" src="/img/loading_2.gif" alt="Loading"></div>
		<div v-if="errmsg != ''" class="alert-maba">{{ errmsg }}</div>
		<div class="row" style="padding-top: 10px; padding-bottom: 10px;">
			<table class="table" style="font-size: 12px; margin-left: auto; margin-right: auto;">
				<tr>
					<th>Name</th>
					<th>Type</th>
					<th>From</th>
					<th>Receiver</th>
					<th>Date</th>
					<th>Place</th>
					<th colspan="3">Actions</th>
				</tr>
				<tr v-for = "(record,index) in records">
					<td>
						<span v-if="editRecord.id != record.id">{{ record.name }}</span>
						<input class="form-control form-control-sm" type="text" v-bind:value="record.name" v-if="editRecord.id == record.id" v-on:input="setEditName">
					</td>
					<td>
						<span v-if="editRecord.id != record.id">{{ record.type_name }}</span>
						<select class="form-control form-control-sm" v-model="editRecord.type_id" style="font-size: 11px;" v-if="editRecord.id == record.id">
                            <option v-for="type in types" v-bind:value="type.id" :selected="editRecord.type_id == type.id">
                            {{ type.name }}
                            </option>
                        </select>
					</td>
					<td>
						<span v-if="editRecord.id != record.id">{{ record.ogson }}</span>
						<input class="form-control form-control-sm" type="text" v-bind:value="record.ogson" v-if="editRecord.id == record.id" v-on:input="setEditOgson">
					</td>
					<td>
						<span v-if="editRecord.id != record.id">{{ record.awsan }}</span>
						<input class="form-control form-control-sm" type="text" v-bind:value="record.awsan" v-if="editRecord.id == record.id" v-on:input="setEditAwsan">
					</td>
					<td>
						<span v-if="editRecord.id != record.id">{{ record.date }}</span>
						<datepicker v-if="editRecord.id == record.id" :value="editRecord.date" :format="'yyyy-MM-dd'" @selected="setEditDate"></datepicker>
					</td>
					<td>
						<span v-if="editRecord.id != record.id">{{ record.place_name }}</span>
						<select class="form-control form-control-sm" v-model="editRecord.place_id" style="font-size: 11px;" v-if="editRecord.id == record.id">
                            <option v-for="place in places" v-bind:value="place.id" :selected="editRecord.place_id == place.id">
                            {{ place.name }}
                            </option>
                        </select>
					</td>
					<td>
						<button v-if="editRecord.id != record.id && !loading" class="btn btn-warning btn-sm" @click="editData" v-bind:value="index">Edit</button>
						<button v-if="editRecord.id == record.id" class="btn btn-success btn-sm" @click="saveEdited" v-bind:value="index">Save</button>
						<button v-if="editRecord.id == record.id" class="btn btn-secondary btn-sm" @click="cancelEdit" v-bind:value="index">Cancel</button>
					</td>
					<td>
						<button v-if="!loading" class="btn btn-primary btn-sm" @click="toggleMoveModal" :value="index">Move</button>
						<div class="move-modal" :ref="'modal'+index">
							<input type="text" class="form-control form-control-sm" placeholder="Type Requestor Name" style="margin-bottom: 3px;" v-model="move.requestor">
							<input type="text" class="form-control form-control-sm" placeholder="Type Performer Name" style="margin-bottom: 3px;" v-model="move.performer">
							<label :for="'mdate'+index" style="margin-bottom: 3px;">Select date of Move</label>
							<datepicker :id="'mdate'+index" :format="'yyyy-MM-dd'" @selected="setMoveDate" @cleared="clearDate" :value="move.date" :clear-button="true" :monday-first="true" :highlighted="highlighted" style="margin-bottom: 3px;"></datepicker>

							<select class="form-control form-control-sm" v-model="move.place_id" style="font-size: 11px; margin-bottom: 3px;">
			                    <option v-for="place in places" :value="place.id" :disabled = "record.place_id == place.id">
			                    {{ place.name }}
			                    </option>
			                </select>

							<textarea class="form-control form-control-sm" placeholder="Type Description" style="margin-bottom: 3px;" v-model="move.description"></textarea>
							<button class="btn btn-success btn-sm" :value="index" @click="saveMove">Save</button>
						</div>
					</td>
					<td>
						<button v-if="!loading" class="btn btn-danger btn-sm" @click="deleteRecord" v-bind:value="index">Delete</button>
					</td>
				</tr>
			</table>
			<pagination :data="recordresponse" @pagination-change-page="getRecords" :limit="5">
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
				form: {
					date: '',
					condition: 'is',
				},
				move: {
					date: '',
					record_id: -1,
				},
				errmsg: '',
				loading: false,
				records: [],
				editRecord: {
					id: -1,
				},
				types: [],
				places: [],
				recordresponse: {},
				highlighted: {
			    	customPredictor: function(date) {
					var today = new Date();
					if(date.getDate() == today.getDate() && date.getMonth() == today.getMonth())
						return true
			    	}
				}
			}
		},
		components: {
			Datepicker
		},
		mounted(){
			this.getRecords(1);
			this.getTypes();
			this.getPlaces();
		},
		methods: {
			valueCheck: function(event){
                if (event.target.value === ""){
                    console.log("Hooson");
                    if (event.target.id == "ttype")
                        delete this.form["type_id"];
                        //Vue.delete(this.form, 'group_id')
                    if (event.target.id == "place")
                        delete this.form["place_id"];
                }
                //console.log('testing');
            },
			deleteRecord: function(event){
				let self = this;
				this.loading = true;
				this.errmsg = "";
				this.hideModals();
				axios.put('/api/backup/delete/' + this.records[event.target.value].id)
				.then((response) => {
					this.loading = false;
					this.records.splice(event.target.value, 1);
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
			saveEdited: function(event){
				let self = this;
				this.loading = true;
				this.errmsg = "";
				axios.put('/api/backup/' + this.records[event.target.value].id, this.editRecord)
				.then((response) => {
					this.loading = false;
					//console.log(response.data.data);
					if (response.data.data == true){
						this.records[event.target.value].name = this.editRecord.name;
						this.records[event.target.value].ogson = this.editRecord.ogson;
						this.records[event.target.value].awsan = this.editRecord.awsan;
						this.records[event.target.value].date = this.editRecord.date;
						this.records[event.target.value].type_id = this.editRecord.type_id;
						this.records[event.target.value].place_id = this.editRecord.place_id;
						this.records[event.target.value].type_name = this.types.filter(obj => { return obj.id === this.editRecord.type_id })[0].name;
						this.records[event.target.value].place_name = this.places.filter(obj => { return obj.id === this.editRecord.place_id })[0].name;
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
			cancelEdit: function(){
				this.editRecord.id = -1;
				this.editRecord.name = '';
				this.editRecord.ogson = '';
				this.editRecord.awsan = '';
				this.editRecord.date = '';
				this.editRecord.type_id = -1;
				this.editRecord.place_id = -1;
			},
			setEditName: function(event){
				this.editRecord.name = event.target.value;
			},
			setEditOgson: function(event){
				this.editRecord.ogson = event.target.value;
			},
			setEditAwsan: function(event){
				this.editRecord.awsan = event.target.value;
			},
			setEditDate: function(date){
				this.editRecord.date = new Date(date).toISOString().slice(0,10);
				//console.log(date);
			},
			searchDate: function(date){
				this.form.date = new Date(date).toISOString().slice(0,10);
				//console.log(date);
			},
			setMoveDate: function(date){
				this.move.date = new Date(date).toISOString().slice(0,10);
				//console.log(date);
			},
			clearDate: function(){
				//console.log('cleared');
				this.form.date = "";
				this.move.date = "";
			},
			editData: function(event){
				//console.log(this.records[event.target.value]);
				this.hideModals();
				this.editRecord.id = this.records[event.target.value].id;
				this.editRecord.name = this.records[event.target.value].name;
				this.editRecord.ogson = this.records[event.target.value].ogson;
				this.editRecord.awsan = this.records[event.target.value].awsan;
				this.editRecord.type_id = this.records[event.target.value].type_id;
				this.editRecord.place_id = this.records[event.target.value].place_id;
				this.editRecord.date = this.records[event.target.value].date;
			},
			addRecord: function (){
				let self = this;
				this.loading = true;
				this.errmsg = "";
				var pagesize = 10;
				this.hideModals();
				axios.post('/api/backup/add', this.form).then((response) => {
					this.records = response.data.data.concat(this.records);
					if (this.records.length > pagesize){
						var iluu = this.records.length - pagesize;
						for (var i = 0; i < iluu; i++)
							this.records.pop();		
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
			getRecords: function (page=1){
				let self = this;
				this.loading = true;
				this.errmsg = "";
				this.hideModals();
				var url = '/api/backup/search/';
				page = typeof(page) == 'object'? 1: page;
				url = url + '?page=' + page;
				if (this.form.name !== undefined)
					url = url + '&nm=' + this.form.name;
				if (this.form.ogson !== undefined)
					url = url + '&og=' + this.form.ogson;
				if (this.form.awsan !== undefined)
					url = url + '&aw=' + this.form.awsan;
				if (this.form.type_id !== undefined)
					url = url + '&ti=' + this.form.type_id;
				if (this.form.place_id !== undefined)
					url = url + '&pi=' + this.form.place_id + '&condition=' + this.form.condition;
				if (this.form.date !== ''){
					url = url + '&date=' + this.form.date;
				}
				axios.get(url).then((response) => {
					this.loading = false;
					this.recordresponse = response.data;
                    this.records = response.data.data;
                    //console.log(this.records);
                }).catch(function (error) {
                    self.loading = false;
                    //self.clearErrmsg();
                   // console.log(error);
                    if (error.response.status == 401)
                        window.location.href = '/login';
                    else if (error.response.status == 500)
                        self.errmsg = error.response.data.message;
                    else if (error.response.status == 422){
                    	self.errmsg = error.response.data.message;
                    	console.log(error)
                    	/*for(const index in error.response.data.errors){
                    		//console.log(error.response.data.errors[index]);
                    		self.errmsg += error.response.data.errors[index] + " ";
                    		if (index == 'season')
                    			self.seasonValidationError = true;
                    		if (index == 'group')
                    			self.groupValidationError = true;
                    	}*/
                    }
                    else
                        self.errmsg = error.response.data.message;
                        //console.log("Error: " + error.response.data.message);
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
			toggleMoveModal: function(event){
				var modal = this.$refs['modal' + event.target.value][0];
				for (const [key, value] of Object.entries(this.$refs)) {
					if ('modal' + event.target.value != key && value.length > 0)
						value[0].style.display = 'none';
				}
				
				if (modal.style.display != 'block')
					modal.style.display = 'block';
				else
					modal.style.display = 'none';

				this.move.record_id = -1;
				this.move.date = '';
			},
			hideModals: function(){
				for (const [key, value] of Object.entries(this.$refs)) {
					if (value.length > 0)
						value[0].style.display = 'none';
				}
			},
			saveMove: function(event){
				let self = this;
				this.loading = true;
				this.errmsg = "";
				var pagesize = 10;
				this.move.record_id = this.records[event.target.value].id;
				axios.post('/api/backup/moves/add', this.move).then((response) => {
					/*if (response.data.data == true){
						this.records[event.target.value].place_id = this.move.place_id;
						this.records[event.target.value].ogson = this.move.requestor;
						this.records[event.target.value].awsan = this.move.performer;
					}
					else{
						console.log(response);
					}*/
					this.records[event.target.value].place_id = response.data.data[0].place_id;
					this.records[event.target.value].place_name = response.data.data[0].place_name;
					this.records[event.target.value].ogson = response.data.data[0].performer_name;
					this.records[event.target.value].awsan = response.data.data[0].requester_name;
					this.hideModals();
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
			}
		}
	}
</script>