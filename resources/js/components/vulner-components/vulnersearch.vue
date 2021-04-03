<template>
	<div class="container">
    	<div class="row" style="margin: auto; padding: 10px;">
    		
    		<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
    			<form v-on:submit.prevent="searchData">
    				<div class="form-group row" style="padding-right: 15px;">
                        <label for="vulname" class="col-md-1 col-form-label text-md-right form-control-xs" style="font-size: 11px;"><b>Vulner.Name:</b></label>
                        <div class="col-md-2">
                            <input id="vulname" type="text" class="form-control form-control-sm" v-model="form.vulname" placeholder="Name of VUlnerability" style="font-size: 11px;">
                        </div>
                        <label for="ipaddr" class="col-md-1 col-form-label text-md-right form-control-sm" style="font-size: 11px;"><b>IP address:</b></label>
                        <div class="col-md-2">
                            <input type="text" id="ipaddr" class="form-control form-control-sm" v-model="form.ipaddr" placeholder="comma seperated addresses" style="font-size: 11px;">
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
                        <label for="season" class="col-md-1 col-form-label text-md-right form-control-sm" style="font-size: 11px;"><b>Season:</b></label>
                        <div class="col-md-2">
                            <select id="season" class="form-control form-control-sm" v-on:change="valueCheck" v-model="form.season_id" style="font-size: 11px;">
                                <option selected value>All</option>
                                <option v-for="season in seasons" v-bind:value="season.id">
                                {{ season.name }}
                                </option>
                            </select>
                        </div>
                    </div>
                    <div style="width: 100%; text-align: center; padding-bottom: 1rem; font-size: 12px;">
                        <a v-if="!morefilter" role="button" data-toggle="collapse" aria-controls="moreFilter" href="#moreFilter" v-on:click="changeFilter">More Filter</a>
                        <a v-else role="button" data-toggle="collapse" aria-controls="moreFilter" href="#moreFilter" v-on:click="changeFilter">Less Filter</a>
                    </div>
                    <div id="moreFilter" class="form-group row collapse" style="padding-right: 15px;">
                        <label for="status" class="col-md-1 col-form-label text-md-right form-control-xs" style="font-size: 11px;"><b>Status:</b></label>
                        <div class="col-md-2">
                            <select id="status" class="form-control form-control-sm" v-on:change="valueCheck" v-model="form.status" style="font-size: 11px;">
                                <option selected value>All</option>
                                <option value="0">New</option>
                                <option value="1">New for host</option>
                                <option value="2">Detected Again</option>
                            </select>
                        </div>
                        <label for="port" class="col-md-1 col-form-label text-md-right form-control-sm" style="font-size: 11px;"><b>Port:</b></label>
                        <div class="col-md-2">
                            <input type="text" id="port" class="form-control form-control-sm" v-model="form.port" placeholder="Port number" style="font-size: 11px;">
                        </div>
                        <label for="protocol" class="col-md-1 col-form-label text-md-right form-control-sm" style="font-size: 11px;"><b>Protocol:</b></label>
                        <div class="col-md-2">
                            <input type="text" id="protocol" class="form-control form-control-sm" placeholder="tcp udp etc..." v-model="form.protocol" style="font-size: 11px;">
                        </div>
                        <label for="risk" class="col-md-1 col-form-label text-md-right form-control-xs" style="font-size: 11px;"><b>Risk:</b></label>
                        <div class="col-md-2">
                            <select id="risk" class="form-control form-control-sm" v-on:change="valueCheck" v-model="form.risk" style="font-size: 11px;">
                                <option selected value>All</option>
                                <option value="Critical">Critical</option>
                                <option value="High">High</option>
                                <option value="Medium">Medium</option>
                                <option value="Low">Low</option>
                            </select>
                        </div>
                    </div>
                    <div id="moreFilter" class="form-group row collapse" style="padding-right: 15px;">
                        <label for="fixed" class="col-md-1 col-form-label text-md-right form-control-xs" style="font-size: 11px;"><b>Fixed:</b></label>
                        <div class="col-md-2">
                            <select id="fixed" class="form-control form-control-sm" v-on:change="valueCheck" v-model="form.fix" style="font-size: 11px;">
                                <option selected value>All</option>
                                <option value="1">Fixed</option>
                                <option value="0">Not</option>
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary" v-if="!loading">Filter</button>

    			</form>
    		</div>
            <div style="width: 100%;"><img v-show="loading" style="margin-left: auto; margin-right: auto; height: 30px; width: 30px; display: block;" src="/img/loading_2.gif" alt="Loading">
            </div>
    	</div>
        <div class="row" v-if="searchresult.length">
            <div style="font-size: 11px; text-align: right; width: 100%;">
                Total: <b>{{ searchresponse.meta.total }}</b>. Displaying: <b>{{ searchresponse.meta.from }} - {{ searchresponse.meta.to }}</b>
            </div>
            <table class="table" style="font-size: 11px; width: 100%;">
                <tr>
                    <th>Risk</th>
                    <th>Host</th>
                    <th>Prot/Port</th>
                    <th>Name</th>
                    <th>Fix</th>
                    <th>Department</th>
                    <th>Season</th>
                    <th>First at</th>
                </tr>
                <tr v-for="result in searchresult">
                    <td>
                        <span :class="result.risk">
                        {{ result.risk }}
                        </span>
                    </td>
                    <td>{{ result.host }}</td>
                    <td>{{ result.protocol }}/{{ result.port }}</td>
                    <td>{{ result.name }}</td>
                    <td>
                        <span v-if="result.fix == 1" class="bg-success text-light" style="border-radius: 2px; padding: 2px; font-size: 12px; display: block; text-align: center;">Fixed</span>
                        <span v-else class="bg-danger text-light" style="border-radius: 2px; padding: 2px; font-size: 12px; display: block; text-align: center;">No</span>
                    </td>
                    <td style="min-width: 150px;">{{ result.group_name }}</td>
                    <td style="min-width: 70px">{{ result.season_year }}-{{ result.season_name }}</td>
                    <td style="min-width: 70px">{{ result.detected_year }}-{{ result.detected_sname }}</td>
                </tr>
            </table>
            <pagination :data="searchresponse" @pagination-change-page="searchData" :limit="5">
                <span slot="prev-nav">&lt; Previous</span>
                <span slot="next-nav">Next &gt;</span>
            </pagination>
        </div>
    </div>
</template>
<script>
	/*import sidemenu from "./sidemenu"*/
	export default {
		data() {
			return {
                searchresponse: {},
                groups: [],
                seasons: [],
				searchresult:[],
				form: {},
                loading: false,
                morefilter: false,
                tooltipOptions: {
                    animation: true,
                    delay: { "show": 500, "hide": 100 },
                },
                /*csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),*/
			}
		},
		/*components: {
			sidemenu
		},*/
        mounted() {
            this.getGroups(),
            this.getSeasons(),
            $('[data-toggle="tooltip"]').tooltip(this.tooltipOptions);
        },
        
        methods: {
            changeFilter: function(){
                this.morefilter = this.morefilter ? false : true;
            },
            valueCheck: function(event){
                if (event.target.value === ""){
                    console.log("Hooson");
                    if (event.target.id == "group")
                        delete this.form["group_id"];
                        //Vue.delete(this.form, 'group_id')
                    if (event.target.id == "season")
                        delete this.form["season_id"];
                    if (event.target.id == "status")
                        delete this.form["status"];
                    if (event.target.id == "risk")
                        delete this.form["risk"];
                    if (event.target.id == "fixed")
                        delete this.form["fix"];
                }
                //console.log('testing');
            },
            getGroups: function(){
                axios.get('/api/getgroups')
                .then((response) => {
                    this.groups = response.data.data;
                })
                .catch( function (error) {console.log(error)})
            },
            getSeasons: function(){
                axios.get('/api/getseasons')
                .then((response) => {
                    this.seasons = response.data.data;
                })
                .catch( function (error) {console.log(error)})
            },
        	searchData: function(page=1) {
                this.form.page=page;
                this.loading = true;
                let self = this;
                axios.post ('/api/vulnersearch', this.form)
                .then ((response) => {
                    if (response.data.data){
                        this.searchresponse = response.data;
                        this.searchresult = response.data.data;
                        console.log(response.data);
                    }
                    this.loading = false;
                })
                .catch( function (error) {
                    self.loading = false;
                    if (error.response.status == 401)
                        window.location.href = '/login';
                    else if (error.response.status == 500)
                        alert("Error: " + error.response.data.message);
                    else
                        alert("Error: " + error.response.data.message);
                        //console.log("Error: " + error.response.data.message);
                })

            }
    	}
    }
</script>