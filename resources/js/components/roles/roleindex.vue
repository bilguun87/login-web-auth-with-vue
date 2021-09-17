<template>
	<div class="container">
		<div class="row" style="margin-top: 10px;">
            <div style="width: 100%;"><img v-show="loading" style="margin-left: auto; margin-right: auto; height: 30px; width: 30px; display: block;" src="/img/loading_2.gif" alt="Loading"></div>
            <div v-if="errmsg != ''" class="alert-maba">{{ errmsg }}</div>
        </div>
        <div class="row">
        	<div class="col">
        		<div class="card">
                    <div class="card-header">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="rolemode" id="existingRole" value="modify" v-model="form.rolemode" @click="checkMode">
                            <label class="form-check-label" for="existingRole">Change Role Permissions</label>
                        </div>
                    </div>
                    <div class="card-body">
                        <select class="form-control form-control-sm" v-model="form.role" :disabled="form.rolemode == 'new'" @change="tickPermissions" aria-describedby="modifyroleHelp">
							<option disabled>Please select Role</option>
							<option v-for="(role,index) in roles" :value="index">{{ role.name }}</option>
						</select>
						<small id="modifyroleHelp" class="form-text text-muted">Choose role to change permissions</small>
						<div class="row" style="margin-top: 10px;">
				        	<div class="col">
				        		<button class="btn btn-success" :disabled="form.rolemode == 'new'" @click="saveRole">Change Permission</button>
				        	</div>
				        	<div class="col">
				        		<button class="btn btn-danger" :disabled="form.rolemode == 'new'" @click="deleteRole">Delete</button>
				        	</div>
				        </div>
                    </div>
                </div>
        	</div>
        	<div class="col">
        		<div class="card">
                    <div class="card-header">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="rolemode" id="createRole" value="new" v-model="form.rolemode" @click="checkMode">
                            <label class="form-check-label" for="createRole">Create role</label>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="col">
                            <input type="text" class="form-control form-control-sm" aria-describedby="rolenameHelp" placeholder="Role name" v-model="form.rolename" :disabled="form.rolemode == 'modify'">
                            <small id="rolenameHelp" class="form-text text-muted">Please enter Role name to create</small>
                            <button class="btn btn-info" style="margin-top:5px;" :disabled="form.rolemode == 'modify'"  @click="saveRole">Create</button>
                        </div>

                    </div>
                </div>
        	</div>
        </div>
        
        <div class="row" style="margin: 10px 0;">
        	<div class="col">
	        	<ul class="list-group list-group-sm list-group-flush">
	                <li class="list-group-item" v-for="(perm, index) in permissions" style="font-size: 1em">
	                    <input class="form-check-input" type="checkbox" :id="'perm'+index" v-model="form.perms" :value="perm.name">
	                    <label class="form-check-label" :for="'perm'+index">{{ perm.name }}</label>
	                </li>
	            </ul>
            </div>
        </div>
	</div>
</template>
<script>
export default {
	data() {
		return {
			errmsg: '',
            loading: false,
			roles: [],
			permissions: [],
			form: {
				rolemode: "modify",
				perms: [],
				role: "",
			},
		}
	},
	mounted() {
		this.getRoles()
		this.getPermissions()
	},
	methods: {
		getRoles: function() {
            let self = this;
            this.loading = true;
            this.errmsg = "";
            var url = '/api/users/roles';
            axios.get(url).then((response) => {
                this.loading = false;
                //this.recordresponse = response.data; //Pagination-tei ued ashiglah ba ashiglahaar bol holbogdoh huvisagchiin neriig bichij ashiglana
                this.roles = response.data.data;
                //console.log(this.records);
            }).catch(function(error) {
                self.loading = false;
                if (error.response.status == 401)
                    window.location.href = '/login';
                else if (error.response.status == 500)
                    self.errmsg = error.response.data.message;
                else if (error.response.status == 422) {
                    self.errmsg = error.response.data.message;
                    console.log(error)
                } else
                    self.errmsg = error.response.data.message;
                setTimeout(function() { self.errmsg = '' }, 5000);
            })
        },
        getPermissions: function() {
            let self = this;
            this.loading = true;
            this.errmsg = "";
            var url = '/api/users/permissions';
            axios.get(url).then((response) => {
                this.loading = false;
                //this.recordresponse = response.data; //Pagination-tei ued ashiglah ba ashiglahaar bol holbogdoh huvisagchiin neriig bichij ashiglana
                this.permissions = response.data.data;
                //console.log(this.records);
            }).catch(function(error) {
                self.loading = false;
                console.log (error.response)
                if (error.response.status == 401)
                    window.location.href = '/login';
                else if (error.response.status == 500)
                    self.errmsg = error.response.data.message;
                else if (error.response.status == 422) {
                    self.errmsg = error.response.data.message;
                    console.log(error)
                } else
                    self.errmsg = error.response.data.message;
                setTimeout(function() { self.errmsg = '' }, 5000);
            })
        },
        checkMode: function (event) {
        	if (event.target.value == "new")
        		this.form.perms = []
        	else {
        		// console.log("Role:" + this.form.role)
        		if (this.form.role !== ""){
        			// console.log(this.roles[this.form.role])
        			for(let i=0; i < this.roles[this.form.role].perms.length; i++)
        				this.form.perms.push(this.roles[this.form.role].perms[i].name)
        		}
        	}
        },

        tickPermissions: function(event){
        	// console.log(this.roles[event.target.value])
        	this.form.perms = [];
        	for(let i=0; i < this.roles[event.target.value].perms.length; i++)
        		this.form.perms.push(this.roles[event.target.value].perms[i].name)
        },

        saveRole: function(){
        	let self = this;
        	let roleIndex = this.form.role;
            this.loading = true;
            this.errmsg = "";
            var url = '/api/roles/save';
            if (this.form.rolemode == "modify" && this.form.role !== "")
            	this.form.role = this.roles[roleIndex].name;
            axios.post(url, this.form).then((response) => {
                this.loading = false;
                //this.recordresponse = response.data; //Pagination-tei ued ashiglah ba ashiglahaar bol holbogdoh huvisagchiin neriig bichij ashiglana
                this.roles[roleIndex] = response.data.data[0];
                this.form.role = roleIndex;
                // console.log(this.form.rolemode)
                if (this.form.rolemode == "new"){
                	this.roles.push(response.data.data[0])
                	alert(response.data.data[0].name + ' role has created successfully')
                }
                else{
                	alert(this.roles[roleIndex].name + ' role has changed')
                }
                //console.log(this.records);
            }).catch(function(error) {
                self.loading = false;
                if (error.response.status == 401)
                    window.location.href = '/login';
                else if (error.response.status == 500)
                    self.errmsg = error.response.data.message;
                else if (error.response.status == 422) {
                    self.errmsg = error.response.data.message;
                    console.log(error)
                } else
                    self.errmsg = error.response.data.message;
                setTimeout(function() { self.errmsg = '' }, 5000);
            })
        },

        deleteRole: function(){
        	if(confirm('Are you sure?!!')){
	        	let self = this;
	        	let roleIndex = this.form.role;
	            this.loading = true;
	            this.errmsg = "";
	            var url = '/api/roles/delete/' ;
	            if (this.form.role !== "")
	            	url = url + this.roles[roleIndex].name;
	            axios.delete(url).then((response) => {
	                this.loading = false;
	                //this.recordresponse = response.data; //Pagination-tei ued ashiglah ba ashiglahaar bol holbogdoh huvisagchiin neriig bichij ashiglana
	                this.roles.splice(roleIndex, 1);
	                this.form.perms = [];
	                alert(response.data.message);
	            }).catch(function(error) {
	                self.loading = false;
	                if (error.response.status == 401)
	                    window.location.href = '/login';
	                else if (error.response.status == 500)
	                    self.errmsg = error.response.data.message;
	                else if (error.response.status == 422) {
	                    self.errmsg = error.response.data.message;
	                    console.log(error)
	                } else
	                    self.errmsg = error.response.data.message;
	                setTimeout(function() { self.errmsg = '' }, 5000);
	            })
	        }
        },
	}
}
</script>