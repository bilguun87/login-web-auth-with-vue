<template>
	<div class="container">
		<div class="row" style="margin-top: 10px;">
            <div style="width: 100%;"><img v-show="loading || braloading || posloading" style="margin-left: auto; margin-right: auto; height: 30px; width: 30px; display: block;" src="/img/loading_2.gif" alt="Loading"></div>
            <div v-if="errmsg != ''" class="alert-maba">{{ errmsg }}</div>
        </div>

        <div class="row" style="margin-top:10px;">
        	<div class="col" v-if="branches.length > 0">
        		<v-select placeholder="Select branch" label="br_name" :options="branches" v-model="form.branch" @input="getPositions"></v-select>
        	</div>
        	<div class="col" v-if="positions.length > 0">
        		<v-select placeholder="Select position" label="pos_name" :options="positions" v-model="form.position"></v-select>
        	</div>
        	<div class="col">
        		<div class="col"><button class="btn btn-success btn-sm" @click="getAdUsers(1)">Get Users</button></div>
        	</div>
        </div>
        <div class="row" style="margin-top:10px;">
        	<table class="table" style="font-size: 11px; width: 100%;">
        		<tr>
        			<th>Branch Name</th>
        			<th>Position Name</th>
        			<th>Name</th>
        			<th>Domain</th>
        			<th>Active Groups</th>
        			<th>Necessary Groups</th>
        			<th>Unnecessary Groups</th>
        		</tr>
        		<tr v-for="(usr,index) in adusers">
        			<td>{{ usr.br_name }}</td>
        			<td>{{ usr.pos_name }}</td>
        			<td>{{ usr.mname.substring(0,1) }}.{{ usr.lname }}</td>
        			<td>{{ usr.domain_user }}</td>
        			<td><span v-for="gr in usr.activegroups">{{ gr[0] }}<br></span></td>
        			<td v-if = "usr.matches">
        				<span class="bg-info text-light" style="border-radius: 2px; padding: 2px; font-size: 12px; display: block; text-align: center;">
        					{{ usr.matches.dutuuNumber }}
        				</span>
        				<span v-for="gr in usr.matches.dutuu">
        					{{ gr.cn }}<br>
        				</span>
        			</td>
        			<td v-else></td>
        			<td v-if = "usr.matches">
        				<span class="bg-info text-light" style="border-radius: 2px; padding: 2px; font-size: 12px; display: block; text-align: center;">
        					{{ usr.matches.iluuNumber }}
        				</span>
        				<span v-for="gr in usr.matches.iluu">
        					{{ gr.cn }}<br>
        				</span>
        			</td>
        			<td v-else></td>
        		</tr>
        	</table>
        </div>
        <div class="row">
        	<pagination :data="aduserresponse" @pagination-change-page="getAdUsers" :limit="5">
                <span slot="prev-nav">&lt; Previous</span>
                <span slot="next-nav">Next &gt;</span>
            </pagination>
        </div>
	</div>
</template>
<script>
import 'vue-select/dist/vue-select.css';
export default{
	data() {
			return {
				// loadeddbusers: false,
				// loadingdbusers: false,
				// loadedgroupsbyuser: false,
				// loadediluugroups: false,
				// loadeddutuugroups: false,
				errmsg: '',
				posloading: false,
				braloading: false,
            	loading: false,
            	// form: {},
            	adusers: [],
            	aduserresponse: {},
            	branches: [],
            	positions: [],
            	form: {},
			}
		},
	
	mounted() {
		this.getBranches()
		this.getPositions()
	},

	methods: {
		getBranches: function () {
			let self = this;
            this.braloading = true;
            this.errmsg = "";
            var url = '/api/getbranches';
            // page = typeof(page) == 'object'? 1: page;
            // url = url + '?page=' + page;
            axios.get(url).then((response) => {
                this.braloading = false;
                this.branches = response.data.data;
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
		getPositions: function () {
			let self = this;
            this.posloading = true;
            this.errmsg = "";
        	let br_id = typeof this.form.branch !== "undefined"? this.form.branch.br_id : null
        	
            var url = '/api/getpositions';
            if (br_id !== null)
            	url = url + '?br_id=' + br_id
            // console.log(url)
            // page = typeof(page) == 'object'? 1: page;
            // url = url + '?page=' + page;
            axios.get(url).then((response) => {
            	// console.log(br)
                this.posloading = false;
                this.positions = response.data.data;
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
        getAdUsers: function(page=1){
            let self = this;
            this.loading = true;
            this.errmsg = "";
            var url = '/api/getusers';
            page = typeof(page) == 'object'? 1: page;
            url = url + '?page=' + page;
            // console.log(this.form.branch);
            if (typeof this.form.branch !== 'undefined' && this.form.branch != null)
            	url = url + '&br_id=' + this.form.branch.br_id
            if (typeof this.form.position !== 'undefined' && this.form.position != null)
            	url = url + '&pos_id=' + this.form.position.pos_id
            axios.get(url).then((response) => {
                this.loading = false;
                this.aduserresponse = response.data;
                this.adusers = response.data.data;
                // this.searchbranches = response.data.branches
                // this.setSearch(response.data.branches, 'branches')
                // this.setSearch(response.data.positions, 'positions')
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

        /*setSearch: function(br_object, whichone){
        	if (whichone == 'branches'){
        		this.searchbranches = []
	        	for(const key in br_object){
	        		this.searchbranches.push({'br_id': key, 'br_name': br_object[key]});
	        	}
	        }
	        else{
	        	this.searchpositions = []
	        	for(const key in br_object){
	        		this.searchpositions.push({'pos_id': key, 'pos_name': br_object[key]});
	        	}
	        }
        },*/
	}
}
</script>