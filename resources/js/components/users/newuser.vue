<template>
    <div class="container" style="padding-bottom: 20px;">
    	<div class="row" style="margin-top: 10px;">
            <div style="width: 100%;"><img v-show="loading" style="margin-left: auto; margin-right: auto; height: 30px; width: 30px; display: block;" src="/img/loading_2.gif" alt="Loading"></div>
            <div v-if="errmsg != ''" class="alert-maba">{{ errmsg }}</div>
        </div>
        <div class="row">
            <a href="/users">Back to User List</a>
        </div>
        <div class="row" style="padding:10px;">
            <div class="col">
                <input type="text" class="form-control form-control-sm" aria-describedby="usernameHelp" placeholder="Enter user account name" v-model="form.username">
                <small id="usernameHelp" class="form-text text-muted">Please enter user's domain account name without domain</small>
            </div>
            <div class="col">
            	<button class="btn btn-success btn-sm" :disabled="form.username.length < 4" @click="addUser">Save</button>
            </div>
        </div>
        <div class="row">
        	<div class="col">
                <div class="card">
                    <div class="card-header">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="permmode" id="existingRole" value="exist" v-model="form.rolemode">
                            <label class="form-check-label" for="existingRole">Use existing role</label>
                        </div>
                    </div>
                    <div class="card-body" style="overflow: hidden; overflow-y: scroll; max-height: 500px;">
                        <ul class="list-group list-group-sm list-group-flush">
                            <li class="list-group-item" v-for="(role, index) in roles" style="font-size: 1em">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" :id="'role'+index" v-model="form.roles" :value="role.name" :aria-describedby="'cb' + index" :disabled="form.rolemode == 'new'">
                                    <label class="form-check-label" :for="'role'+index">{{ role.name }}</label>
                                    <small :id="'cb' + index" class="form-text text-muted">Includes following permissions:
                                        <ul class="list-inline">
                                            <li class="list-inline-item" v-for="rp in role.perms"><small class="form-text text-muted" style="font-size:1em">{{ rp.name }}</small></li>
                                        </ul>
                                    </small>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
        	</div>
        	<div class="col">
                <div class="card">
                    <div class="card-header">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="permmode" id="createRole" value="new" v-model="form.rolemode" v-if="">
                            <label class="form-check-label" for="createRole">Create role and use</label>
                        </div>
                    </div>
                    <div class="card-body" style="overflow: hidden; overflow-y: scroll; max-height: 500px;">
                        <div class="col">
                            <input type="text" class="form-control form-control-sm" aria-describedby="rolenameHelp" placeholder="Role name" v-model="form.rolename">
                            <small id="rolenameHelp" class="form-text text-muted">Please enter Role name to create</small>
                        </div>
                        <ul class="list-group list-group-sm list-group-flush">
                            <li class="list-group-item" v-for="(perm, index) in permissions" style="font-size: 1em">
                                <input class="form-check-input" type="checkbox" :id="'perm'+index" v-model="form.perms" :value="perm.name" :disabled="form.rolemode == 'exist'">
                                <label class="form-check-label" :for="'perm'+index">{{ perm.name }}</label>
                            </li>
                        </ul>
                    </div>
                </div>
        	</div>
        </div>
        <div v-if="recentusers.length > 0">
            <div class="row">Added Users</div>
            <div class="row">
                <table class="table">
                    <tr>
                        <th>User</th>
                        <th>Role</th>
                        <th>Permission</th>
                    </tr>
                    <tr v-for="user in recentusers">
                        <td>{{ user.name }}</td>
                        <td><span v-for="role in user.roles">{{ role }}</span></td>
                        <td><span v-for="perm in user.permissionsviaroles">{{ perm.name }}</span></td>
                    </tr>
                </table>
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
                username: "",
                rolemode: "exist",
				roles: [],
				perms: []
			},
            recentusers: []
		}
	},
	mounted() {
		this.getRoles();
        this.getPermissions();
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
        addUser: function() {
        	let self = this;
            this.loading = true;
            this.errmsg = "";
            axios.post('/api/users/add', this.form).then((response) => {
                this.loading = false;
                //this.recordresponse = response.data; //Pagination-tei ued ashiglah ba ashiglahaar bol holbogdoh huvisagchiin neriig bichij ashiglana
                this.recentusers.push(...response.data.data);
                if (this.form.rolemode == "new"){
                    this.getRoles();
                }
                alert('User "' + response.data.data[0].name + '" has added successfully');
                //this.permissions = response.data.data;
                //console.log(this.records);*/
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
	}

}

</script>
