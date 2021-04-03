<template>
	<div class="container" v-if="!groupLoad">
		<div class="row" style="margin-top: 10px;">
			<div style="width: 100%;"><img v-show="loading" style="margin-left: auto; margin-right: auto; height: 30px; width: 30px; display: block;" src="/img/loading_2.gif" alt="Loading"></div>
			<div v-if="errmsg != ''" class="alert-maba">{{ errmsg }}</div>
			<div class="col-md-12">
				<!--form-->
				<div class="form-group row">
					<div class="col-md-6">
						<input type="text" id="ipaddr" class="form-control form-control-sm" v-model="form.ipaddr" placeholder="comma seperated IP addresses" style="font-size: 11px;">
					</div>

					<div class="col-md-6">
						<input type="text" id="desc" class="form-control form-control-sm" v-model="form.desc" placeholder="Description" style="font-size: 11px;">
					</div>
				</div>
				<div class="form-group row">
					<div class="col-md-12">
						<select id="group" class="form-control form-control-sm" v-on:change="valueCheck" v-model="form.group_id" style="font-size: 11px;">
                            <option selected="selected" value>All</option>
                            <option v-for="group in groups" v-bind:value="group.id">
                            {{ group.name }}
                            </option>
                        </select>
					</div>	
				</div>
				<div class="form-group row">
					<div class="col-md-1">
						<button class="btn btn-primary" @click="addHost">Add</button>
					</div>
					<div class="col-md-1">
						<button class="btn btn-warning" @click="getHosts">Search</button>
					</div>
				</div>
				<!--/form-->
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<table class="table" style="font-size: 12px; width: 80%; margin-left: auto; margin-right: auto;">
					<tr>
						<th>IP Address</th>
						<th>Description</th>
						<th>Department</th>
						<th></th>
						<th></th>
					</tr>
					<tr v-for="(host, index) in hosts">
						<td>
							<span v-if="editHost.id != host.id">{{ host.ip }}</span>
							<input class="form-control form-control-sm" type="text" v-bind:value="host.ip" v-if="editHost.id == host.id" v-on:input="setEditIP">
						</td>
						<td>
							<span v-if="editHost.id != host.id">{{ host.desc }}</span>
							<input class="form-control form-control-sm" type="text" v-bind:value="host.desc" v-if="editHost.id == host.id" v-on:input="setEditDesc">
						</td>
						<td>
							<span v-if="editHost.id != host.id">{{ host.group_name }}</span>
							<select class="form-control form-control-sm" v-model="editHost.group_id" style="font-size: 11px;" v-if="editHost.id == host.id">
                                <option v-for="group in groups" v-bind:value="group.id" :selected="editHost.group_id == group.id">
                                {{ group.name }}
                                </option>
                            </select>
						</td>
						<td>
							<button v-if="editHost.id != host.id && !loading" class="btn btn-warning btn-sm" @click="editData" v-bind:value="index">Edit</button>
							
							<button v-if="editHost.id == host.id" class="btn btn-success btn-sm" @click="saveEdited" v-bind:value="index">Save</button>
							
							<button v-if="editHost.id == host.id" class="btn btn-secondary btn-sm" @click="cancelEdit" v-bind:value="index">Cancel</button>
						</td>
						<td>
							<button v-if="!loading" class="btn btn-danger btn-sm" @click="deleteHost" v-bind:value="index">Delete</button>
						</td>
					</tr>
				</table>
	            <pagination :data="hostresponse" @pagination-change-page="getHosts" :limit="5">
	                <span slot="prev-nav">&lt; Previous</span>
	                <span slot="next-nav">Next &gt;</span>
	            </pagination>
			</div>
		</div>
	</div>
</template>
<script>
	export default {
		data() {
			return {
				groupLoad: true,
				loading: false,
				form: {},
				editHost: {
					id: -1,
					desc: "",
					group_id: -1
				},
				hostresponse: {},
				groups: [],
				hosts: [],
				errmsg: "",
			}
		},
		mounted() {
			this.getGroups();
			this.getHosts();
		},
		methods: {
			valideIPv4:  function(ip){
				//let ipformat = 
				if (/^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/.test(ip)){
					return true;
				}
				//alert("You have entered an invalid IP address!")
				return false;
			},
			checkIPAddresses: function(strIP){
				let checkedIP = "";
				let arrayIP;
				strIP = strIP.replace(/\s/g, "");
				arrayIP = strIP.split(',');
				for (var i = 0; i < arrayIP.length; i++){
					if (this.valideIPv4(arrayIP[i]))
						checkedIP = checkedIP + arrayIP[i] + ","
				}
				checkedIP = checkedIP != "" ? checkedIP.slice(0, -1): "";
				return checkedIP;
			},
			addHost: function(){
				let self = this;
				let ipaddr = this.checkIPAddresses(this.form.ipaddr);
				this.form.ipaddr = ipaddr;
				this.loading = true;
				this.errmsg = "";
				axios.post('/api/createhost', this.form)
				.then((response) => {
					//console.log(response.data.data);
					//this.hosts.unshift(response.data.data);

					this.hosts = response.data.data.concat(this.hosts);
					for (var i = 0; i < response.data.data.length; i++)
						this.hosts.pop();
					this.form = {};
					this.loading = false;
					//this.hosts.unshift({
					//	'id': response.data.data.id,
					//	'ip': this.form.ip
					//});
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
			setEditIP: function(event){
				this.editHost.ip = event.target.value;
				console.log(event.target.value);
			},
			setEditDesc: function(event){
				this.editHost.desc = event.target.value;
			},
			editData: function(event){
				this.editHost.id = this.hosts[event.target.value].id;
				this.editHost.ip = this.hosts[event.target.value].ip;
				this.editHost.desc = this.hosts[event.target.value].desc;
				this.editHost.group_id = this.hosts[event.target.value].group_id;
			},
			saveEdited: function(event){
				let self = this;
				this.loading = true;
				this.errmsg = "";
				axios.put('/api/updatehost/' + this.hosts[event.target.value].id, this.editHost)
				.then((response) => {
					this.loading = false;
					//console.log(response.data.data);
					if (response.data.data == true){
						this.hosts[event.target.value].ip = this.editHost.ip;
						this.hosts[event.target.value].desc = this.editHost.desc;
						this.hosts[event.target.value].group_id = this.editHost.group_id;
						this.hosts[event.target.value].group_name = this.groups.filter(obj => { return obj.id === this.editHost.group_id })[0].name;
					}
					else
						console.log(response.data);
					this.cancelEdit();
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
				//this.cancelEdit();
			},
			cancelEdit: function(){
				this.editHost.id = -1;
				this.editHost.ip = "";
				this.editHost.desc = "";
				this.editHost.group_id = -1;
			},
			deleteHost: function(event){
				let self = this;
				this.loading = true;
				this.errmsg = "";
				axios.delete('/api/deletehost/' + this.hosts[event.target.value].id)
				.then((response) => {
					this.loading = false;
					this.hosts.splice(event.target.value, 1);
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
			valueCheck: function(event){
                if (event.target.value === ""){
                    //console.log("Hooson");
                    if (event.target.id == "group")
                        delete this.form["group_id"];
                }
                //console.log('testing');
            },
            getHosts: function(page=1){
            	let self = this;
            	let url = "/api/gethosts"
            	//console.log(typeof(page));
            	page = typeof(page) == 'object'? 1: page;
            	//this.form.page=page;
				url = url + "?page=" + page;
				//console.log(this.form.group_id);
				if (this.form.group_id !== undefined)
					url = url + "&group_id=" + this.form.group_id;
				if (this.form.desc !== undefined)
					url = url + "&desc=" + this.form.desc;
				if (this.form.ipaddr !== undefined)
					url = url + "&ipaddr=" + this.form.ipaddr;
            	this.loading = true;
                axios.get(url)
                .then((response) => {
                	this.hostresponse = response.data;
                    this.hosts = response.data.data;
                    this.loading = false;
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
		}
	}
</script>