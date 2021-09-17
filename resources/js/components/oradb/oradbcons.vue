<template>
	<div class="container">
		<div class="row" style="margin-top: 10px;">
            <div style="width: 100%;"><img v-show="loading" style="margin-left: auto; margin-right: auto; height: 30px; width: 30px; display: block;" src="/img/loading_2.gif" alt="Loading"></div>
            <div v-if="errmsg != ''" class="alert-maba">{{ errmsg }}</div>
        </div>
        <div class="row">
            <a href="/oradb">Back to Oracle DB check</a>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="col">
            	<div class="form-group form-group-sm">
                    <label for="name">Connection Name</label>
    	        	<input id="name" type="text" class="form-control" v-model="form.name" aria-description="nameDesc" ref="name">
                    <small id="nameDesc" class="form-text text-muted">Displayed by this name in Connection Lists</small>
    			</div>
            </div>
            <div class="col">
    			<div class="form-group form-group-sm">
                    <label for="constr">Connection String</label>
    				<input id="constr" type="text" class="form-control" v-model="form.constr" aria-description="constrDesc" ref="constr">
                    <small id="constrDesc" class="form-text text-muted">It will be encrypt and not displayed in Connection Lists (targethost/servic_ename)</small>
    			</div>
            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="col">
                <div class="form-group form-group-sm">
                    <label for="user">User Name</label>
                    <input id="user" type="text" class="form-control" v-model="form.user" aria-description="userDesc" ref="user">
                    <small id="userDesc" class="form-text text-muted">It will be encrypt and not displayed in Connection Lists</small>
                </div>
            </div>
            <div class="col">
                <div class="form-group form-group-sm">
                    <label for="desc">Short Description</label>
                    <input id="desc" type="text" class="form-control" v-model="form.desc" aria-description="descDesc" ref="desc">
                    <small id="descDesc" class="form-text text-muted">/Optional/ Description of connection</small>
                </div>
            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <button class="btn btn-success" @click="saveConnection">Save</button>
        </div>

        <div class="row" style="margin-top: 10px;">
            <table class="table">
                <tr>
                    <th>Name</th>
                    <th>Description</th>
                    <th>User</th>
                    <th colspan="2">Actions</th>
                </tr>
                <tr v-for="(con,index) in connections">
                    <td>{{ con.name }}</td>
                    <td>{{ con.desc }}</td>
                    <td>{{ con.user }}</td>
                    <td><button class="btn btn-warning" @click="editConstr(index)">Edit</button></td>
                    <td><button class="btn btn-danger" @click="deleteConstr(index)">Delete</button></td>
                </tr>
            </table>
        </div>
	</div>
</template>
<script>
export default {
    data() {
        return {
            errmsg: '',
            loading: false,
            editIndex: -1,
            connections: [],
            form: {
                mode: "new",
            },
        }
    },
    mounted() {
        this.getConnections()
    },
    methods: {
    	getConnections: function(){
            let self = this;
            this.loading = true;
            this.errmsg = "";
            var url = '/api/oradb/all';
            // page = typeof(page) == 'object'? 1: page;
            // url = url + '?page=' + page;
            axios.get(url).then((response) => {
                this.loading = false;
                this.connections = response.data.data;
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

        saveConnection: function(){
            let self = this;
            this.loading = true;
            this.errmsg = "";
            if (this.form.mode == "new"){
                var url = '/api/oradb/new';
                axios.post(url, this.form).then((response) => {
                    this.loading = false;
                    this.connections.push(...response.data.data);
                    this.form = {}
                }).catch(function(error) {
                    self.loading = false;
                    console.log(error)
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
            
            else if ((this.form.mode == "edit")){
                var url = '/api/oradb/edit/' + this.form.editid;
                axios.put(url, this.form).then((response) => {
                    this.loading = false;
                    this.connections[this.editIndex] = response.data.data[0];
                    this.form.mode = "new";
                }).catch(function(error) {
                    self.loading = false;
                    console.log(error)
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

        editConstr: function(index) {
            this.editIndex = index
            this.form.editid = this.connections[index].id
            this.form.name = this.connections[index].name
            this.$refs["name"].value = this.connections[index].name

            this.form.user = this.connections[index].user
            this.$refs["user"].value = this.connections[index].user
            
            this.form.desc = this.connections[index].desc
            this.$refs["desc"].value = this.connections[index].desc
            
            this.form.mode = "edit"
        },

        deleteConstr: function(index) {
            // console.log(index)
             let self = this;
            this.loading = true;
            this.errmsg = "";
            var url = '/api/oradb/delete/' + this.connections[index].id;
            axios.delete(url).then((response) => {
                this.loading = false;
                alert(this.connections[index].name + ' connection has deleted successfully')
                this.connections.splice(index, 1)
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