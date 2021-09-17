<template>
	<div class="container">
		<div class="table-responsive-sm">
			<table class="table" style="font-size: 11px; width: 100%;">
				<tr>
					<th>Grantee</th>
					<th>Owner</th>
					<th>Table name</th>
					<th>Grantor</th>
					<th>Privilege</th>
					<th>Grantable</th>
					<th>Hierarchy</th>
				</tr>
				<tr v-for="perm in dbpermissions">
					<td>{{ perm.GRANTEE }}</td>
					<td>{{ perm.OWNER }}</td>
					<td>{{ perm.TABLE_NAME }}</td>
					<td>{{ perm.GRANTOR }}</td>
					<td><span :class="{'High':perm.PRIVILEGE=='DELETE','Medium':perm.PRIVILEGE=='UPDATE','Low':perm.PRIVILEGE=='INSERT'}">{{ perm.PRIVILEGE }}</span></td>
					<td>{{ perm.GRANTABLE }}</td>
					<td>{{ perm.HIERARCHY }}</td>
				</tr>
			</table>
		</div>
	</div>
</template>
<script>
export default {
	props: ['dbpermissions'],
	data() {
		return {

		}
	},
	mounted() {

	},

}
</script>