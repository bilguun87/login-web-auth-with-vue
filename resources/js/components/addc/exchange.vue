<template>
	<div class="container">
		<div class="row" style="margin-top: 10px;">
            <div style="width: 100%;"><img v-show="loading" style="margin-left: auto; margin-right: auto; height: 30px; width: 30px; display: block;" src="/img/loading_2.gif" alt="Loading"></div>
            <div v-if="errmsg != ''" class="alert-maba">{{ errmsg }}</div>
        </div>
		<div class="row" style="margin-top:10px;">
			<div class="col-md-3">
				<div class="form-check">
					<input class="form-check-input" type="radio" value="activesync" v-model="show">
					<label class="form-check-label" for="exampleRadios1">ActiveSync enabled</label>
				</div>
			</div>
			<div class="col-md-3">
				<div class="form-check">
					<input class="form-check-input" type="radio" value="owa" v-model="show">
					<label class="form-check-label" for="exampleRadios1">OWA enabled</label>
				</div>
			</div>
			<div class="col-md-3">
				<div class="form-check">
					<input class="form-check-input" type="radio" value="all" v-model="show">
					<label class="form-check-label" for="exampleRadios1">Both</label>
				</div>
			</div>
			<div class="col">
				<button class="btn btn-primary btn-sm" value="all" @click="getExchUsers">Show</button>
			</div>
		</div>
		<div class="row" style="margin-top: 10px;">
			<div class="col">
				<table class="table" style="font-size: 11px; width: 100%;">
					<tr>
						<th>DisplayName</th>
						<th>Name</th>
						<th>Account</th>
						<th>Mail Address</th>
						<th>OWA</th>
						<th>ActiveSync</th>
					</tr>
					<tr v-for="user in users">
						<td>{{ user.displayname }}</td>
						<td>{{ user.name }}</td>
						<td>{{ user.samaccountname }}</td>
						<td>{{ user.primarysmtpaddress }}</td>
						<td>
							<span v-if="user.owaenabled == 'True'"  class="Medium">
								Enabled
							</span>
							<span v-else class="Low">
								Disabled
							</span>
						</td>
						<td>
							<span v-if="user.activesyncenabled == 'True'"  class="Medium">
								Enabled
							</span>
							<span v-else class="Low">
								Disabled
							</span>
						</td>
					</tr>
				</table>
			</div>
		</div>
		<div class="row" style="margin-top: 10px;">
			<div class="col">
				<pagination :data="userresponse" @pagination-change-page="getExchUsers" :limit="5">
	                <span slot="prev-nav">&lt; Previous</span>
	                <span slot="next-nav">Next &gt;</span>
	            </pagination>
			</div>
		</div>
	</div>
</template>
<script>
export default{
	data() {
			return {
				errmsg: '',
				loading: false,
				users: [],
				userresponse: {},
				show: "all"
			}
		},

	mounted() {

	},

	methods: {
		getExchUsers: function(page=1){
			let self = this;
            this.loading = true;
            this.errmsg = "";
            var url = '/api/getActiveSyncOWAEnabledUsers';
            page = typeof(page) == 'object'? 1: page;
            url = url + '?page=' + page + '&show=' + this.show;
            axios.get(url).then((response) => {
                this.loading = false;
                this.userresponse = response.data
                this.users = response.data.data
                console.log(response.data);
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
	}
}
</script>