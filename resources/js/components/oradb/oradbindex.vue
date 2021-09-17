<template>
	<div class="container">
		<div class="row" style="margin-top: 10px;">
            <div style="width: 100%;"><img v-show="loading" style="margin-left: auto; margin-right: auto; height: 30px; width: 30px; display: block;" src="/img/loading_2.gif" alt="Loading"></div>
            <div v-if="errmsg != ''" class="alert-maba">{{ errmsg }}</div>
        </div>
        <div class="row">
            <div style="padding: 0 15px; margin: auto;">
                <span style="padding: 8px;">Oracle DB check</span>
            </div>
            <div style="padding: 0 15px;" class="align-self-end">
                <span style="display:block; padding: 2px; border: 1px solid #3490dc; max-width: 160px;"><a href="/oradb/cons" style="text-decoration: none;">&nbsp;Add/Edit Connection<i class="bi bi-plus"></i></a></span>
            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
        	<div class="col-md-10">
	        	<select class="form-control form-control-sm" v-model="form.id" aria-describedby="conHelp">
					<option disabled>Please select Role</option>
					<option v-for="(con,index) in connections" :value="con.id">{{ con.name }}</option>
				</select>
				<small id="conHelp" class="form-text text-muted">Choose DB connection</small>
			</div>
			<div class="col-md-2">
				<input type="password" class="form-control form-control-sm" v-model="form.password">
			</div>
        </div>

        <div class="row" style="margin-top: 10px;" v-if="profnames.length == 0">
            <div class="col-md-3">
                <span class="spantobtn" style="background-color: rgb(52, 144, 220); color: white;" @click="getProfiles">Get Profiles</span>
            </div>
        </div>

        <div class="row" style="margin-top: 10px;" v-else>
            <div class="col-md-3">
                <select class="form-control form-control-sm" v-model="form.profile" aria-description="profileHelp">
                    <option v-for="(pro,index) in profnames" :value="pro.PROFILE">{{ pro.PROFILE }}</option>
                </select>
                <small id="profileHelp" class="form-text text-muted">Choose Profile</small>
            </div>
        </div>
        <div class="row" style="margin-top: 10px;" ref="checks" v-if="profnames.length > 0">
            <div class="col-md-3">
                <span class="spantobtn-blue" @click="getOpenUsers">List Open Users</span>
            </div>
            <div class="col-md-3">
                <span class="spantobtn-blue" @click="getPermissions">List Permissions</span>
            </div>
            <div class="col-md-3">
                <span class="spantobtn-blue" @click="getAuditConfigs">List Audit Configs</span>
            </div>
        </div>
        <div ref="oradbusers" class="row" style="margin-top: 10px;" v-if="openusers.length">
            <oradbusers :openusers="openusers" />
            <pagination :data="openuserspage" @pagination-change-page="getOpenUsers" :limit="5">
                <span slot="prev-nav">&lt; Previous</span>
                <span slot="next-nav">Next &gt;</span>
            </pagination>
        </div>
        <div ref="dbpermissions" class="row" style="margin-top: 10px;" v-if="dbperms.length">
            <oradbperms :dbpermissions="dbperms" />
            <pagination :data="dbpermspage" @pagination-change-page="getPermissions" :limit="5">
                <span slot="prev-nav">&lt; Previous</span>
                <span slot="next-nav">Next &gt;</span>
            </pagination>
        </div>
        <div ref="audconfs" class="row" style="margin-top: 10px;" v-if="useraudits.length">
            <oradbauds :audconfs="audconfs" :useraudits="useraudits"/>
            <pagination :data="audconfspage" @pagination-change-page="getAuditConfigs" :limit="5">
                <span slot="prev-nav">&lt; Previous</span>
                <span slot="next-nav">Next &gt;</span>
            </pagination>
        </div>
	</div>
</template>
<script>
import oradbusers from './oradbusers.vue';
import oradbperms from './oradbperms.vue';
import oradbauds from './oradbauds.vue';
export default {
    data() {
        return {
            errmsg: '',
            loading: false,
            connections: [],
            form: {},
            checkform: {},
            profiles: [],
            profnames: [],
            openusers: [],
            openuserspage: null,
            dbperms: [],
            dbpermspage: null,
            audconfs: [],
            useraudits: [],
            audconfspage: null,
        }
    },
    components: {oradbusers, oradbperms, oradbauds},
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

        getProfiles: function(){
            let self = this;
            this.loading = true;
            this.errmsg = "";
            var url = '/api/oradb/profiles';
            // page = typeof(page) == 'object'? 1: page;
            // url = url + '?page=' + page;
            axios.post(url, this.form).then((response) => {
                this.loading = false;
                this.profnames = response.data.data

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
        },

        getOpenUsers: function(page=1){
            this.setActive(0)
            let self = this;
            this.loading = true;
            this.errmsg = "";
            var url = '/api/oradb/openusers';
            this.form.page = typeof(page) == 'object'? 1: page;
            axios.post(url, this.form).then((response) => {
                this.loading = false;
                this.openusers = response.data.data
                this.openuserspage = response.data
                this.dbperms = []
                this.dbpermspage = null
                this.audconfs = []
                this.useraudits = []
                this.audconfspage = null
                // console.log(this.openusers);
            }).catch(function(error) {
                self.loading = false;
                console.log(error)
                if(error.response == "undefined")
                    self.errmsg = error
                else if (error.response.status == 401)
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

        getPermissions: function(page=1){
            this.setActive(1)
            let self = this;
            this.loading = true;
            this.errmsg = "";
            var url = '/api/oradb/permissions';
            this.form.page = typeof(page) == 'object'? 1: page;
            axios.post(url, this.form).then((response) => {
                this.loading = false;
                this.dbperms = response.data.data
                this.dbpermspage = response.data
                this.openusers = []
                this.openuserspage = null
                this.audconfs = []
                this.useraudits = []
                this.audconfspage = null
                // console.log(this.dbpermissions);
            }).catch(function(error) {
                self.loading = false;
                console.log(error)
                if(error.response == "undefined")
                    self.errmsg = error
                else if (error.response.status == 401)
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

        getAuditConfigs: function(page=1){
            this.setActive(2)
            let self = this;
            this.loading = true;
            this.errmsg = "";
            var url = '/api/oradb/audconfs';
            this.form.page = typeof(page) == 'object'? 1: page;
            axios.post(url, this.form).then((response) => {
                this.loading = false;
                this.audconfs = response.data.audconfs
                this.useraudits = response.data.data
                this.audconfspage = response.data
                this.openusers = []
                this.openuserspage = null
                this.dbperms = []
                this.dbpermspage = null
                // console.log(this.dbpermissions);
            }).catch(function(error) {
                self.loading = false;
                console.log(error)
                if(error.response == "undefined")
                    self.errmsg = error
                else if (error.response.status == 401)
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

        setActive: function(index){
            // console.log("Hello")
            for (var i=0; i < this.$refs["checks"].children.length; i++){
                if (i == index)
                    this.$refs["checks"].children[i].children[0].classList.add("spantobtn-blue-active")
                else
                    this.$refs["checks"].children[i].children[0].classList.remove("spantobtn-blue-active")
            }
        },
    }
}
</script>