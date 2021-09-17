<template>
	<div class="container">
		<div class="row" style="margin-top: 10px;">
            <div style="width: 100%;"><img v-show="adgloading || posloading || mxloading || braloading || loading" style="margin-left: auto; margin-right: auto; height: 30px; width: 30px; display: block;" src="/img/loading_2.gif" alt="Loading"></div>
            <div v-if="errmsg != ''" class="alert-maba">{{ errmsg }}</div>
        </div>
		<!-- <div class="row justify-content-end" style="margin-top: 10px;">
			<button class="btn btn-info">Sync DB</button>
		</div> -->
		<div class="row" style="margin-top:10px;">
			<div class="col">
				<!-- <select name="branches" id="branches" class="form-control form-control-sm selectpicker" v-model="form.branch" data-live-search="true">
					<option value="" disabled selected>Select branch</option>
					<option value="all">All</option>
					<option v-for="br in branches" :value="br.br_id">{{br.br_name}}</option>
				</select> -->
				<v-select placeholder="Select branch" label="br_name" :options="branches" v-model="form.branch" @input="getPositions(0)"></v-select>
			</div>
			<div class="col" v-show="positions.length > 0">
				<!-- <select name="positions" id="positions" class="form-control form-control-sm" v-model="form.position">
					<option value="" disabled selected>Select position</option>
					<option value="all">All</option>
					<option v-for="pos in positions" :value="pos.pos_id">{{pos.pos_name}}</option>
				</select> -->
				<v-select placeholder="Select Posistion" label="pos_name" :options="positions" v-model="form.position"></v-select>
			</div>
			<div class="col">
				<!-- <select name="Groups" id="Groups" class="form-control form-control-sm"  v-model="form.group">
					<option value="" disabled selected>Select Domain Group</option>
					<option v-for="gr in groups" :value="gr.objectguid">{{gr.cn}}</option>
				</select> -->
				<v-select placeholder="Select AD Group" label="cn" :options="groups" v-model="form.group"></v-select>
			</div>
			
		</div>
		<div class="row" style="margin-top:10px;">
			<div class="col"><button class="btn btn-primary btn-sm" @click="setMatch">Add</button></div>
			<div class="col"><button class="btn btn-success btn-sm" @click="getMatrix">Search</button></div>
		</div>
		<div class="row" style="margin-top:10px;">
			<div class="col">
				<table class="table" style="font-size: 11px;">
					<tr>
						<th>Branches</th>
						<th>Posistions</th>
						<th>AD Groups</th>
						<th>Actions</th>
					</tr>
					<tr v-for="(m,index) in matrix">
						<td>
							<span v-show="index != editIndex">{{ m.br_name }}</span>
							<v-select v-show="index == editIndex" label="br_name" :options="branches" @input="getPositions(1)" v-model="editform.branch" :value="editform.branch">
							</v-select>
						</td>
						<td>
							<span v-show="index != editIndex">{{ m.pos_name }}</span>
							<v-select v-show="index == editIndex" label="pos_name" :options="editpositions" v-model="editform.position" :value="editform.position"></v-select>
						</td>
						<td>
							<span v-show="index != editIndex">{{ m.cn }}</span>
							<v-select v-show="index == editIndex" label="cn" :options="groups" v-model="editform.group" :value="editform.group"></v-select>
						</td>
						<td>
							<button v-show="index != editIndex" class="btn btn-warning btn-sm" @click="editMatch(index)">Edit</button>
							<button v-show="index == editIndex" class="btn btn-success btn-sm" @click="saveMatch">Save</button>
							<button v-show="index == editIndex" class="btn btn-info btn-sm" @click="cancelEdit">Cancel</button>
							<button class="btn btn-danger btn-sm" @click="deleteMatch(index)">Delete</button>
						</td>
					</tr>
				</table>
				<pagination :data="matrixpaginate" @pagination-change-page="getMatrix" :limit="5">
	                <span slot="prev-nav">&lt; Previous</span>
	                <span slot="next-nav">Next &gt;</span>
	            </pagination>
			</div>
		</div>
	</div>
</template>
<script>
import 'vue-select/dist/vue-select.css';
export default{
	data() {
			return {
				editIndex:-1,
				errmsg: '',
				loading: false,
				mxloading: false,
            	braloading: false,
            	posloading: false,
            	adgloading: false,
            	form: {},
            	editform: {
            		branch: {br_id: null,br_name: null},
            		position: {pos_id: null,pos_name: null},
            		group: {objectguid: null,cn: null},
            	},
            	ldap: null,
            	groups: [],
            	branches: [],
            	positions: [],
            	editpositions: [],
            	matrixpaginate: {},
            	matrix: [],
			}
		},
	
	mounted() {
		this.getBranches()
		this.getMatrix()
		this.getADGroups()
		this.getPositions(0)
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
		getPositions: function (editornot = 0) {
			
			let self = this;
            this.posloading = true;
            this.errmsg = "";
            let br_id = null;
            console.log(editornot);
        	// let br_id = typeof(br) !== "object"? br : this.form.branch.br_id
        	if (editornot == 0)
        		br_id = (typeof this.form.branch !== "undefined" && this.form.branch != null)? this.form.branch.br_id : null
        	else
        		br_id = this.editform.branch != null?this.editform.branch.br_id:null

            var url = '/api/getpositions';
            if (br_id !== null)
            	url = url + "?br_id=" + br_id;
            // console.log(url)
            // page = typeof(page) == 'object'? 1: page;
            // url = url + '?page=' + page;
            axios.get(url).then((response) => {
            	// console.log(br)
                this.posloading = false;
                if (editornot !== 0)
                	this.editpositions = response.data.data;
                else
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
		getADGroups: function () {
			let self = this;
            this.adgloading = true;
            this.errmsg = "";
            var url = '/api/getadgroups';
            // page = typeof(page) == 'object'? 1: page;
            // url = url + '?page=' + page;
            axios.get(url).then((response) => {
                this.adgloading = false;
                this.groups = response.data.data;
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
		getMatrix: function (page=1) {
			let self = this;
            this.mxloading = true;
            this.errmsg = "";
            page = typeof(page) == 'object'? 1: page;
            var url = '/api/getmatrix?page=' + page;
            if (this.form.branch !== undefined && this.form.branch != null)
				url = url + '&br_id=' + this.form.branch.br_id;
			if (this.form.position !== undefined && this.form.position != null)
				url = url + '&pos_id=' + this.form.position.pos_id;
			if (this.form.group !== undefined && this.form.group != null)
				url = url + '&objectguid=' + this.form.group.objectguid;
            // page = typeof(page) == 'object'? 1: page;
            // url = url + '?page=' + page;
            axios.get(url, this.form).then((response) => {
                this.mxloading = false;
                this.matrixpaginate = response.data;
                this.matrix = response.data.data;
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
		setMatch: function () {
			let self = this;
            this.loading = true;
            this.errmsg = "";
            var url = '/api/addmatrix';
            // page = typeof(page) == 'object'? 1: page;
            // url = url + '?page=' + page;
            axios.post(url, this.form).then((response) => {
                this.loading = false;
                this.matrix = response.data.data.concat(this.matrix);
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
		saveMatch: function () {
			let self = this;
            this.loading = true;
            this.errmsg = "";
            var url = '/api/savematrix/' + this.matrix[this.editIndex].id;
            // page = typeof(page) == 'object'? 1: page;
            // url = url + '?page=' + page;
            axios.put(url, this.editform).then((response) => {
                this.loading = false;
                // this.matrix[editIndex] = response.data.data;
                if (response.data.data == true){
	                this.matrix[this.editIndex].br_id = this.editform.branch != null?this.editform.branch.br_id:null
	                this.matrix[this.editIndex].br_name = this.editform.branch != null?this.editform.branch.br_name:"All"
	                this.matrix[this.editIndex].pos_id = this.editform.position != null?this.editform.position.pos_id:null
	                this.matrix[this.editIndex].pos_name = this.editform.position != null?this.editform.position.pos_name:"All"
	                this.matrix[this.editIndex].objectguid = this.editform.group.objectguid
	                this.matrix[this.editIndex].cn = this.editform.group.cn
            	}
            	else
            		console.log(response.data)
                this.cancelEdit()
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
		cancelEdit: function () {
			this.editIndex = -1
			this.editform = {
					branch: 	{br_id: null,br_name: null},
            		position: 	{pos_id: null,pos_name: null},
            		group: 		{objectguid: null,cn: null},
				}
			this.editpositions = []
		},
		editMatch: function (index) {
			this.editIndex = index;
			
			this.editform.branch.br_id = this.matrix[index].br_id;
			this.editform.branch.br_name = this.matrix[index].br_name;
			this.getPositions(1)

			this.editform.position.pos_id = this.matrix[index].pos_id;
			this.editform.position.pos_name = (this.matrix[index].pos_name == "All")?"":this.matrix[index].pos_name;
			
			this.editform.group.objectguid = this.matrix[index].objectguid;
			this.editform.group.cn = this.matrix[index].cn;

		},
		deleteMatch: function (index){
			let self = this;
            this.loading = true;
            this.errmsg = "";
            var url = '/api/deletematrix/' + this.matrix[index].id;
            // page = typeof(page) == 'object'? 1: page;
            // url = url + '?page=' + page;
            axios.delete(url).then((response) => {
                this.loading = false;
                this.matrix.splice(index, 1);
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
	},
}
</script>