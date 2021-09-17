<template>
	<div class="container">
		<div class="row" style="margin-top: 10px;">
			<div style="width: 100%;"><img v-show="loading" style="margin-left: auto; margin-right: auto; height: 30px; width: 30px; display: block;" src="/img/loading_2.gif" alt="Loading"></div>
			<div v-if="errmsg != ''" class="alert-maba">{{ errmsg }}</div>
		</div>
	</div>
</template>
<script>
	import { getPermissions } from "../../mixins/getPermissions"
	export default {
		mixins: [getPermissions],
		data () {
			return {
				form: {role_id: -1, perm_id: []},
				role_id: "",
				users : [],
				roles: [],
				permissions: [],
				errmsg: '',
				loading: false,
				userresponse: {}
			}
		},
		mounted() {
			console.log(this.$store.state.userrights)
		},
		methods: {
			
		}
	}
</script>