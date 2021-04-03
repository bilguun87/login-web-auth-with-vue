<template>
	<div class="container" v-if="!seasonLoad && !groupLoad">
		<div class="row">
			<div class="col-md-12" style="padding: 20px 0;">
				<ul class="nav nav-tabs">
					<li class="nav-item">
						<a class="nav-link active" data-toggle="tab" href="#allot" style="font-size: 13px;">Allot</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" data-toggle="tab" href="#departments-div" style="font-size: 13px;">Departments</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" data-toggle="tab" href="#seasons-div" style="font-size: 13px;" v-on:click = "checkCurrentSeasonExist">Seasons</a>
					</li>
				</ul>
				<div style="width: 100%;"><img v-show="loading" style="margin-left: auto; margin-right: auto; height: 30px; width: 30px; display: block;" src="/img/loading_2.gif" alt="Loading"></div>
				<div v-if="errmsg != ''" class="alert-maba">
					{{ errmsg }}
				</div>
				<div class="tab-content">
					<div id="allot" class="container tab-pane active"><br>
						<p style="width: 100%; text-align: center;">To Allot registered vulnerabilities to departments<p>
						<form v-on:submit.prevent="allotVulners">
							<div class="form-group row" style="padding-right: 15px;">
								<label for="season" class="col-md-1 col-form-label text-md-right form-control-sm" style="font-size: 11px;"><b>Season:</b></label>
								<div class="col-md-6">
		                            <select id="season" class="form-control form-control-sm" v-on:change="valueCheck" v-model="form.season_id" style="font-size: 11px;">
		                                <option selected value>All</option>
		                                <option v-for="season in seasons" v-bind:value="season.id">
		                                {{ season.name }}
		                                </option>
		                            </select>
		                        </div>
		                        <label for="group" class="col-md-1 col-form-label text-md-right form-control-sm" style="font-size: 11px;"><b>Group:</b></label>
		                        <div class="col-md-2">
		                            <select id="group" class="form-control form-control-sm" v-on:change="valueCheck" v-model="form.group_id" style="font-size: 11px;">
		                                <option selected="selected" value>All</option>
		                                <option v-for="group in groups" v-bind:value="group.id">
		                                {{ group.name }}
		                                </option>
		                            </select>
		                        </div>
		                        <div class="container" style="border: 1px solid #ced4da; border-radius: 5px; padding: 10px; margin: 10px 0;">
			                        <div class="row">
				                        <div class="col-md-6" style="max-width: 45%;">
				                        	<p style="text-align: center; border-bottom: 1px solid #ced4da"><b>Allot a Vulnerabilities</b></p>
				                        	<div class="form-check">
												<input class="form-check-input" type="radio" name="allotRadios" id="allotRadio1" value="ip" checked v-model="form.type">
												<label class="form-check-label" for="allotRadio1">By IP</label>
											</div>
											<div class="form-check">
												<input class="form-check-input" type="radio" name="allotRadios" id="allotRadio2" value="all" v-model="form.type">
												<label class="form-check-label" for="allotRadio2">Allot an all non allocated vulnerabilities to chosen department</label>
											</div>
											<div class="form-check" style="max-width: 45%;">
												<input class="form-check-input" type="radio" name="allotRadios" id="allotRadio3" value="name" v-model="form.type">
												<label class="form-check-label" for="allotRadio3">By name</label>
											</div>
											<div class="form-check">
												<input class="form-check-input" type="radio" name="allotRadios" id="allotRadio4" value="reg" v-model="form.type">
												<label class="form-check-label" for="allotRadio4">Allot an all vulnerabilities according to known hosts</label>
											</div>
				                        </div>
				                        <div class="col-md-6">
				                        	<p style="text-align: center; border-bottom: 1px solid #ced4da"><b>Allot an IP addresses</b></p>
				                        	<div class="form-check">
												<input class="form-check-input" type="radio" name="allotRadios" id="allotRadio5" value="toip" v-model="form.type">
												<label class="form-check-label" for="allotRadio5">Allot an IP's to chosen department</label>
											</div>
											<div class="form-check">
												<input class="form-check-input" type="radio" name="allotRadios" id="allotRadio6" value="allip" v-model="form.type">
												<label class="form-check-label" for="allotRadio6">Allot an all IP's to chosen department</label>
											</div>
				                        </div>
									</div>
								</div>									
							</div>
							<div class="form-group row" v-if = "form.type == 'ip' || form.type == 'toip'">
								<input type="text" id="ipaddr" class="form-control form-control-sm" v-model="form.ipaddr" placeholder="comma seperated addresses" style="font-size: 11px;">
							</div>
							<div class="form-group row" v-if = "form.type == 'name'">
								<input type="text" id="vulname" class="form-control form-control-sm" v-model="form.vulname" placeholder="vulnerabilities name" style="font-size: 11px;">
							</div>
							<div class="form-group row">
								<button type="submit" class="btn btn-primary" style="margin-top: 10px;" v-if="!loading">Allot</button>
							</div>
						</form>
						<div class="col-md-12">
							Result section
						</div>
					</div>
					<div id="departments-div" class="container tab-pane fade"><br>
						<p style="width: 100%; text-align: center;">Managing Departments<p>
						<form v-on:submit.prevent="createDep">
							<div class="form-group row" style="padding-right: 15px;">
								<label for="department" class="col-md-2 col-form-label text-md-right form-control-xs" style="font-size: 11px;"><b>Department Name:</b></label>
		                        <div class="col-md-8">
		                            <input id="department" type="text" class="form-control form-control-sm" v-model="formdep.name" placeholder="Deparment name" style="font-size: 11px;">
		                            
		                        </div>
		                        <button v-if="!loading" type="submit" class="btn btn-primary btn-sm">Add</button>
	                    	</div>
						</form>
						<div class="row" style="margin-top: 30px;">
							<div class="col-md-12">
								<table class="table" style="font-size: 12px; width: 80%; margin-left: auto; margin-right: auto;">
									<tr>
										<th>Department Name</th>
										<th></th>
										<th></th>
									</tr>
									<tr v-for="(dep, index) in groups">
										<td>
											<span v-if="editDep.id != dep.id">{{ dep.name }}</span>
											<input v-bind:id="index" class="form-control form-control-sm" type="text" v-bind:value="dep.name" v-if="editDep.id == dep.id" v-on:input="setEditName">
										</td>
										<td>
											<button v-if="editDep.id != dep.id && !loading" class="btn btn-warning btn-sm" @click="setEditData" v-bind:value="index">Change Name</button>
											<button v-if="editDep.id == dep.id" class="btn btn-success btn-sm" @click="saveEdited" v-bind:value="index">Save</button>
											<button v-if="editDep.id == dep.id" class="btn btn-secondary btn-sm" @click="cancelEdit" v-bind:value="index">Cancel</button>
										</td>
										<td><button v-if="!loading" class="btn btn-danger btn-sm" @click="deleteDepartment" v-bind:value="index">Delete</button></td>
									</tr>
								</table>
							</div>
						</div>
					</div>
					<div id="seasons-div" class="container tab-pane fade"><br>
						<p style="width: 100%; text-align: center;">Managing Seasons<p>
						<form v-on:submit.prevent="createSeason">
							<div class="form-group row">
								<div style="margin: 0 auto;">
									<span>Current season: {{ curSeason.year }}-{{curSeason.season_name}}</span>
									<button v-if="!exist && !loading" style="margin-left: 10px;" type="submit" class="btn btn-primary btn-sm">Add</button>
									<span  v-if="exist" style="margin-left: 10px; color: white; padding: 0 5px; border-radius: 2px;" class="bg-success">Exist</span>
								</div>
	                    	</div>
						</form>
						<!--div style="width: 100%;"><img v-show="loading" style="margin-left: auto; margin-right: auto; height: 30px; width: 30px; display: block;" src="/img/loading_2.gif" alt="Loading">
            			</div-->
						<div class="row" style="margin-top: 30px;">
							<div class="col-md-12">
								<table class="table" style="font-size: 12px; width: 80%; margin-left: auto; margin-right: auto;">
									<tr>
										<th>Season</th>
										<th></th>
									</tr>
									<tr v-for="(season, index) in seasons">
										<td>{{ season.name }}</td>
										<td style="text-align: right;"><button class="btn btn-danger btn-sm" v-bind:value="index" @click="deleteSeason">Delete</button></td>
									</tr>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>
<script>
	export default {
		data() {
			return {
				editDep: {id: -1, name: ""},
				formdep: {},
				form: {
					type: "ip",
				},
				groups: [],
                seasons: [],
                loading: false,
                curSeason: {},
                exist: false,
                seasonLoad: true,
                groupLoad: true,
                errmsg: "",
			}
		},
		mounted() {
			this.getGroups();
            this.getSeasons();
            this.getCurrentSeason();
            //this.checkCurrentSeasonExist();
		},
		methods: {
			allotVulners: function(){
				let self = this;
				this.loading = true;
				this.errmsg = '';
				axios.post('/api/allotvulners', this.form)
				.then((response) => {
					this.loading = false;
					console.log(response.data);
				})
				.catch( function (error) {
                	self.loading = false;
                	if (error.response.status == 401)
                        window.location.href = '/login';
                    else if (error.response.status == 422)
                        self.errmsg = error.response.data.messages;
                    else
                		self.errmsg = error;
                	//console.log(error);
                })
			},
			cancelEdit: function(){
				this.editDep.id = -1;
			},
			createSeason: function(){
				let self = this;
				this.loading = true;
				this.errmsg = '';
				axios.post('/api/createSeason', this.curSeason)
				.then((response) => {
					this.loading = false;
					console.log(response.data.data);
					this.seasons.unshift({'name': this.curSeason.year+'-'+this.curSeason.season_name, 'id': response.data.data.id});
					this.checkCurrentSeasonExist();
				})
				.catch( function (error) {
                	self.loading = false;
                	if (error.response.status == 401)
                        window.location.href = '/login';
                    else if (error.response.status == 422)
                        self.errmsg = error.response.data;
                    else
                		self.errmsg = error;
                	//console.log(error);
                })
			},
			deleteDepartment: function(event){
				let self = this;
				this.loading = true;
				this.errmsg = '';
				axios.delete('/api/deleteDepartment/' + this.groups[event.target.value].id)
				.then((response) => {
					//console.log(response.data);
					this.groups.splice(event.target.value, 1);
					this.loading = false;
				})
				.catch(function(error){
					self.loading = false
					//console.log(error.response.data.message);
					if (error.response.status == 401)
                        window.location.href = '/login';
                    else if (error.response.status == 422)
                        self.errmsg = 'error.response.data.message';
					else if (error.response.data.message.includes('SQLSTATE[23000]'))
						self.errmsg = 'Can not delete: There are a registered vulnerabilities allocated this Department';
					else
						self.errmsg = error;
					//alert(error);
				})
			},
			deleteSeason: function(event){
				let self = this;
				this.loading = true;
				this.errmsg = '';
				axios.delete('/api/deleteSeason/' + this.seasons[event.target.value].id)
				.then((response) => {
					//console.log(response.data);
					this.seasons.splice(event.target.value, 1);
					this.exist = false;
					this.checkCurrentSeasonExist();
					this.loading = false;
				})
				.catch(function(error){
					self.loading = false
					if (error.response.status == 401)
                        window.location.href = '/login';
                    else if (error.response.status == 422)
                        self.errmsg = error.response.data;
                    else if (error.response.data.message.includes('SQLSTATE[23000]'))
						self.errmsg = 'Can not delete: There are a registered vulnerabilities allocated this Season';
                    else
						self.errmsg = error;
				})
			},
			checkCurrentSeasonExist: function(){
				//console.log(this.seasons);
				for (var i = 0; i < this.seasons.length; i++){
					//console.log(this.seasons[i]);
					if (this.seasons[i].name == this.curSeason.year + "-" + this.curSeason.season_name){
						this.exist = true;
						break;
					}
				}
			},
			getCurrentSeason: function(){
				var year = new Date().getFullYear();
				var month = new Date().getMonth();
				if (month < 3){
					this.curSeason.year = new Date().getFullYear();
					this.curSeason.season = 1;
					this.curSeason.season_name = "I";
				}
				else if (3 <= month < 6){
					this.curSeason.year = new Date().getFullYear();
					this.curSeason.season = 2;
					this.curSeason.season_name = "II";
				}
				else if (6 <= month < 9){
					this.curSeason.year = new Date().getFullYear();
					this.curSeason.season = 3;
					this.curSeason.season_name = "III";
				}
				else{
					this.curSeason.year = new Date().getFullYear();
					this.curSeason.season = 4;
					this.curSeason.season_name = "IV";
				}
			},
			setEditName: function(event){
				this.editDep.name = event.target.value;
				//console.log('editing');
			},
			saveEdited: function(event){
				let self = this;
				this.loading = true;
				this.errmsg = "";
				axios.put('/api/updateDepartment/' + this.groups[event.target.value].id, this.editDep)
				.then((response) => {
					this.loading = false;
					//console.log(response.data.data);
					if (response.data.data == true)
						this.groups[event.target.value].name = this.editDep.name;
					else
						console.log(response.data.message);
					this.editDep.id = -1;
					this.editDep.name = "";	
				})
				.catch(function(error){
					self.loading = false;
					if (error.response.status == 401)
                        window.location.href = '/login';
                    else if (error.response.status == 422)
                        self.errmsg = "Department Name can not be empty";
                    else
                		self.errmsg = error;
				})
				
			},
			setEditData: function(event){
				this.editDep.id = this.groups[event.target.value].id;
				this.editDep.name = this.groups[event.target.value].name;
			},
			createDep: function(){
				let self = this;
				this.loading = true;
				this.errmsg = "";
				axios.post('/api/createDepartment', this.formdep)
				.then((response) => {
					this.loading = false;
					//console.log(response.data.data);
					this.groups.unshift({'name': this.formdep.name, 'id': response.data.data.id});
				})
				.catch( function (error) {
                	self.loading = false;
                	if (error.response.status == 401)
                        window.location.href = '/login';
                    else if (error.response.status == 422){
                    	//self.errors = error.response.data.errors;
                    	self.errmsg = "Department Name must be given";
                        //alert(error.response.data.message);
                    }
                    else
                		self.errmsg = error;
                	//console.log(error);
                })
			},
			valueCheck: function(event){
                if (event.target.value === ""){
                    //console.log("Hooson");
                    if (event.target.id == "group")
                        delete this.form["group_id"];
                        //Vue.delete(this.form, 'group_id')
                    if (event.target.id == "season")
                        delete this.form["season_id"];
                }
                //console.log('testing');
            },
			getGroups: function(){
				let self = this;
                axios.get('/api/getgroups')
                .then((response) => {
                    this.groups = response.data.data;
                    this.groupLoad = false;
                })
                .catch( function (error) {
                	self.groupLoad = false;
                	if (error.response.status == 401)
                        window.location.href = '/login';
                    else if (error.response.status == 422)
                        self.errmsg = error.response.data;
                    else
                		self.errmsg = error;
                	//console.log(error);
                })
            },
            getSeasons: function(){
            	let self = this;
                axios.get('/api/getseasons')
                .then((response) => {
                    this.seasons = response.data.data;
                    this.seasonLoad = false;
                })
                .catch( function (error) {
                	self.seasonLoad = false;
                	if (error.response.status == 401)
                        window.location.href = '/login';
                    else if (error.response.status == 422)
                        self.errmsg = error.response.data;
                    else
                		self.errmsg = error;
                	//console.log(error);
                })
            },
		},
	}
</script>