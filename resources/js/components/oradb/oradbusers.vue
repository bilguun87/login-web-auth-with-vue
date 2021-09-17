<template>
	<div class="container">
		<div class="table-responsive-sm">
			<table class="table" style="font-size: 11px; width: 100%;">
				<tr>
					<th>Username</th>
					<th>Account Status</th>
					<th>Created</th>
					<th>Profile</th>
					<th></th>
					<th></th>
				</tr>
				<tr v-for="user in openusers">
					<td>{{ user.USERNAME }}</td>
					<td>{{ user.ACCOUNT_STATUS }}</td>
					<td>{{ user.CREATED }}</td>
					<td>{{ user.PROFILE }}</td>
					<td></td>
					<td></td>
				</tr>
			</table>
		</div>
	</div>
</template>
<script>
export default {
	props: ['openusers'],
	data() {
		return {

		}
	},
	mounted() {

	},

}
</script>