<template>
    <div class="container" style="padding: 20px;">
        <div class="row" style="margin-top: 10px;">
            <div style="width: 100%;"><img v-show="loading" style="margin-left: auto; margin-right: auto; height: 30px; width: 30px; display: block;" src="/img/loading_2.gif" alt="Loading"></div>
            <div v-if="errmsg != ''" class="alert-maba">{{ errmsg }}</div>
        </div>
        <div class="row">
            <div class="col-md-3" style="margin: 10px;">
                <div class="row">
                    <div class="input-group input-group-sm">
                        <div class="input-group-prepend"><span class="input-group-text"><i class="bi bi-search"></i></span></div>
                        <input type="text" class="form-control" placeholder="Search by user name" v-on:keyup.13="getUsers" v-model="searchName">
                    </div>
                </div>
                <div  v-if="edituser" style="margin-top: 10px;">
                    <div class="row justify-content-center">
                        <h5 style="width: 100%;">Edit section<span class="closeedit" @click="setEditNull">x</span></h5>
                        
                    </div>
                    <div class="row"><span class="bg-info text-white p-2">{{ edituser.name }}</span></div>
                    <div class="row" style="margin: 20px 0; overflow: hidden; overflow-y: scroll; max-height: 500px;">
                        <ul class="list-group list-group-sm list-group-flush">
                            <li class="list-group-item" v-for="(role, index) in roles" style="font-size: 1em">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" :id="'role'+index" v-model="edituser.roles" :value="role.name" :aria-describedby="'cb' + index">
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
                    <div class="row">
                        <div class="col"><button class="btn btn-success" @click="saveEdited">Save</button></div>
                        <div class="col"><button class="btn btn-danger" @click="deleteUser">Delete</button></div>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="row justify-content-end" style="border-left: 1px solid gray;">
                   <span style="display:block; padding: 2px; margin: 10px; border: 1px solid #3490dc;"><a href="/users/new" style="text-decoration: none;">&nbsp;Add user<i class="bi bi-plus"></i></a></span>
                </div>
                <div class="row" style="border-left: 1px solid gray;">
                    <table class="table" style="margin: 10px; font-size: .85em; width: 100%;">
                        <tr>
                            <th>User</th>
                            <th>Roles</th>
                            <th>Permissions Via Roles</th>
                            <!-- <th>Direct Permissions</th> -->
                        </tr>
                        <tr v-for="(user,index) in users">
                            <td><span class="edituser-button" @click="setEdit(index)">{{ user.name }}</span></td>
                            <td><span v-for="role in user.roles">{{ role }};&nbsp;</span></td>
                            <td style="word-wrap: break-word; max-width: 400px;"><span v-for="perm in user.permissionsviaroles">{{ perm.name }};&nbsp;</span></td>
                            <!-- <td><span v-for="perm in user.permissions">{{ perm }}</span></td> -->
                        </tr>
                    </table>
                    <pagination style="margin-left: 10px;" :data="userresponse" @pagination-change-page="getUsers" :limit="10">
                        <span slot="prev-nav">&lt; Previous</span>
                        <span slot="next-nav">Next &gt;</span>
                    </pagination>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    data() {
        return {
            form: { role_id: -1, perm_id: [], role_id: [] },
            role_id: "",
            users: [],
            roles: [],
            permissions: [],
            errmsg: '',
            loading: false,
            userresponse: {},
            edituser: null,
            editindex: null,
            searchName: "",
        }
    },
    mounted() {
        this.getUsers(1);
        this.getRoles();
    },
    methods: {
        valueCheck: function() {

        },
        /*setPerms: function(index) {
        	
        },*/
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
        getUsers: function(page = 1) {
            let self = this;
            this.loading = true;
            this.errmsg = "";
            var url = '/api/users/search';
            page = typeof(page) == 'object' ? 1 : page;
            url = url + '?page=' + page;
            if (this.searchName != "")
                url = url + '&name=' + this.searchName;
            axios.get(url).then((response) => {
                this.loading = false;
                this.userresponse = response.data; //Pagination-tei ued ashiglah ba ashiglahaar bol holbogdoh huvisagchiin neriig bichij ashiglana
                this.users = response.data.data;
                //console.log(this.records);
            }).catch(function(error) {
                //console.log(error.response)
                self.loading = false;
                if (error.response.status == 401)
                    window.location.href = '/login';
                else if (error.response.status == 500)
                    self.errmsg = error.response.data.message;
                else if (error.response.status == 422) //{
                    self.errmsg = error.response.data.message;
                    /*console.log(error)
                }*/ else if (error.response.status == 403) //{
                    self.errmsg = error.response.data.message
                    /*console.log(error.response.data)
                }*/
                else
                    self.errmsg = error.response.data.message;
                setTimeout(function() { self.errmsg = '' }, 5000);
            })
        },

        setEdit: function(index) {
            // console.log(this.users[index].roles)
            if (!this.edituser){
                this.edituser = {};
                this.edituser.id = this.users[index].id;
                this.edituser.name = this.users[index].name;
                this.edituser.roles = this.users[index].roles;
                this.editindex = index;
            }
            else
                if (this.edituser.id == this.users[index].id){
                    this.edituser = null;
                    this.editindex = null;
                }
                else{
                    this.edituser = {};
                    this.edituser.id = this.users[index].id;
                    this.edituser.name = this.users[index].name;
                    this.edituser.roles = this.users[index].roles;
                    this.editindex = index;
                }
        },
        setEditNull: function() {
            this.edituser = null;
            this.editindex = null;
        },
        saveEdited: function () {
            let self = this;
            this.loading = true;
            this.errmsg = "";
            var url = '/api/users/edit/' + this.edituser.id;
            axios.put(url, this.edituser).then((response) => {
                this.loading = false;
                // this.recordresponse = response.data; //Pagination-tei ued ashiglah ba ashiglahaar bol holbogdoh huvisagchiin neriig bichij ashiglana
                // this.permissions = response.data.data;
                // console.log(response.data);
                if (this.editindex !== null)
                    this.users[this.editindex] = response.data.data[0];
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
        deleteUser: function () {
            if (confirm("Are you sure")){
                let self = this;
                this.loading = true;
                this.errmsg = "";
                var url = '/api/users/delete/' + this.edituser.id;
                axios.delete(url).then((response) => {
                    this.loading = false;
                    // this.recordresponse = response.data; //Pagination-tei ued ashiglah ba ashiglahaar bol holbogdoh huvisagchiin neriig bichij ashiglana
                    // this.permissions = response.data.data;
                    // console.log(response.data);
                    alert(response.data.message);
                    if (this.editindex !== null)
                        this.users.splice([this.editindex], 1);
                    this.setEditNull();
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
