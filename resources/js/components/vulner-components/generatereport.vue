<template>
	<div class="container">
		<div style="width: 100%;"><img v-show="loading" style="margin-left: auto; margin-right: auto; height: 30px; width: 30px; display: block;" src="/img/loading_2.gif" alt="Loading"></div>
		<div v-if="errmsg != ''" class="alert-maba">{{ errmsg }}</div>
		<div class="row" style="margin-top: 10px;">
			<div class="col-md-12">
				<div class="form-group row" style="padding-right: 15px;">
					<label for="season" class="col-md-1 col-form-label text-md-right form-control-sm" style="font-size: 11px;"><b>Season:</b></label>
                    <div class="col-md-2">
                        <select id="season" ref="seasonselect" class="form-control form-control-sm" v-bind:class="{'danger-select': seasonValidationError}" v-on:change="valueCheck" v-model="form.season" style="font-size: 11px;">
                            <option selected value>All</option>
                            <option v-for="season in seasons" v-bind:value="season.id">
                            {{ season.name }}
                            </option>
                        </select>
                    </div>

                    <label for="ipaddr" class="col-md-1 col-form-label text-md-right form-control-sm" style="font-size: 11px;"><b>IP address:</b></label>
                    <div class="col-md-5">
                        <input type="text" id="ipaddr" class="form-control form-control-sm" v-model="form.ip" placeholder="comma seperated addresses" style="font-size: 11px;">
                    </div>

                    <label for="date" class="col-md-1 col-form-label text-md-right form-control-xs" style="font-size: 11px;"><b>Date:</b></label>
                    <div class="col-md-2">
                        <datepicker v-model="form.date"></datepicker>
                    </div>
				</div>
				<div class="form-group row">
					<div class="col-md-12 d-flex justify-content-end">
						<button class="btn btn-light d-flex justify-content-end" @click="clearDate">Clear Date</button>
					</div>
				</div>
				<div class="form-group row">
					<div class="col-md-6" v-bind:class="{'danger-group': groupValidationError}">
						<ul class="no-bullets">
							<li style="font-size: 14px;">
								<div class="form-check">
									<input type="checkbox" class="form-check-input" id="group_null" v-model="form.group_null" value="1">
									<label class="form-check-label" for="group_null">Not allocated to any Department</label>
								</div>
							</li>
							<li v-for="(group,index) in groups" style="font-size: 14px;">
								<div class="form-check">
									<input type="checkbox" class="form-check-input" :id="index" v-model="form.group" :value="group.id">
									<label class="form-check-label" :for="index">{{ group.name }}</label>
								</div>
							</li>
						</ul>
					</div>
					<div class="col-md-6">
						<!--Shine huuchin emzeg baidluudiig bagtaah eseh-->
						<div class="form-group row" style="margin-bottom: 0;">
							<div class="form-check col-md-3">
		                    	<input type="checkbox" class="form-check-input" id="new" v-model="form.status" value="0">
								<label class="form-check-label" for="new">New</label>
							</div>
							<div class="form-check col-md-3">
								<input type="checkbox" class="form-check-input" id="hostnew" v-model="form.status" value="1">
								<label class="form-check-label" for="hostnew">New for Host</label>
							</div>
							<div class="form-check col-md-3">
								<input type="checkbox" class="form-check-input" id="old" v-model="form.status" value="2">
								<label class="form-check-label" for="old">Detected again</label>
							</div>
                    	</div>
                    	<!--Zasagdsan zasagdaaguig bagtaah eseh-->

                    	<div class="form-group row" style="margin-bottom: 0;">
							<div class="form-check col-md-3">
								<input type="checkbox" class="form-check-input" id="fixed" v-model="form.fix" value="1">
								<label class="form-check-label" for="fixed">Fixed</label>
							</div>
							<div class="form-check col-md-3">
								<input type="checkbox" class="form-check-input" id="notfixed" v-model="form.fix" value="0">
								<label class="form-check-label" for="notfixed">Not Fixed</label>
							</div>
                    	</div>
                    	<!--Emzeg baidliin tuvshin-->
                    	<div class="form-group row" style="margin-bottom: 0;">
                    		<!--div class="col-md-12"-->
                			<div class="form-check col-md-3">
		                    	<input type="checkbox" class="form-check-input" id="crit" v-model="form.risk" value="critical" :checked="true">
								<label class="form-check-label" for="crit">Critical</label>
							</div>
							<div class="form-check col-md-3">
								<input type="checkbox" class="form-check-input" id="high" v-model="form.risk" value="high">
								<label class="form-check-label" for="high">High</label>
							</div>
							<div class="form-check col-md-3">
								<input type="checkbox" class="form-check-input" id="med" v-model="form.risk" value="medium">
								<label class="form-check-label" for="med">Medium</label>
							</div>
							<div class="form-check col-md-3">
								<input type="checkbox" class="form-check-input" id="low" v-model="form.risk" value="low">
								<label class="form-check-label" for="low">Low</label>
							</div>
							<!--/div-->
						</div>
					</div>
				</div>
				<div class="form-group row">
					<div class="col-md-10">
						<button class="btn btn-primary" @click="generateReport" v-if = "!loading">Generate Report</button>
					</div>
				</div>
			</div>
		</div>
		<div ref="represult">
			<reportresult :reportdata="reportdata" v-if="Object.keys(this.reportdata).length"/>
		</div>
		<go-top :size="50"></go-top>
	</div>
</template>
<script>
	import {ToggleButton} from 'vue-js-toggle-button'
	import Datepicker from 'vuejs-datepicker';
	import reportresult from './reportresult.vue';
	import GoTop from '@inotom/vue-go-top';
	export default{
		data() {
			return {
				errmsg: '',
				loading: false,
				seasons: [],
				groups: [],
				form: {
					status: [0, 1, 2],
					fix: [0, 1],
					risk: ['critical', 'high', 'medium', 'low'],
					group: [],
					group_null: false,
				},
				reportdata: {},
				seasonValidationError: false,
				groupValidationError: false,
			}
		},
		components: {
			ToggleButton,Datepicker,reportresult,GoTop,
		},
		mounted() {
			this.getSeasons();
			this.getGroups();
		},
		methods: {
			generateReport: function(){
				self = this;
				this.loading = true;
				let reportpos = this.$refs.represult.offsetTop;
				axios.post('/api/genreport', this.form)
				.then((response) => {
					this.loading = false;
					this.reportdata = response.data.data;
					this.clearErrmsg();
					window.scrollTo(0, reportpos);
					//console.log(Object.keys(this.reportdata).length);
					//console.log(this.reportdata);
				})
				.catch( function (error) {
                    self.loading = false;
                    self.clearErrmsg();
                    console.log(error);
                    if (error.response.status == 401)
                        window.location.href = '/login';
                    else if (error.response.status == 500)
                        self.errmsg = error.response.data.message;
                    else if (error.response.status == 422){
                    	
                    	for(const index in error.response.data.errors){
                    		//console.log(error.response.data.errors[index]);
                    		self.errmsg += error.response.data.errors[index] + " ";
                    		if (index == 'season')
                    			self.seasonValidationError = true;
                    		if (index == 'group')
                    			self.groupValidationError = true;
                    	}
                    	
                    }
                    else
                        self.errmsg = error.response.data.message;
                        //console.log("Error: " + error.response.data.message);
                    setTimeout(function(){self.errmsg = ''}, 5000);
                })
			},

			clearErrmsg: function(){
				this.errmsg = '';
				this.groupValidationError = false;
				this.seasonValidationError = false;
			},

			clearDate: function(){
				this.form.date = "";
			},
			valueCheck: function(event){
                if (event.target.value === ""){
                    if (event.target.id == "season")
                        delete this.form["season_id"];
                }
            },
			getSeasons: function(){
                axios.get('/api/getseasons')
                .then((response) => {
                    this.seasons = response.data.data;
                })
                .catch( function (error) {console.log(error)})
            },
            getGroups: function(){
                axios.get('/api/getgroups')
                .then((response) => {
                    this.groups = response.data.data;
                })
                .catch( function (error) {console.log(error)})
            },
		}
	}
</script>