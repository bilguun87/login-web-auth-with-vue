<template>
	<div class="container">
		<div class="table-responsive-sm">
			<table class="table" style="font-size: 11px; width: 100%;">
				<tr>
					<th>Username</th>
					<th v-for="conf in audconfs">{{ conf.polvalue }}</th>
				</tr>
				<tr v-for="user in useraudits">
					<td>{{ user.USERNAME }}</td>
					<td v-for="conf in audconfs"><span :class="{'High':user[conf.polvalue]=='Bad','normal':user[conf.polvalue]=='Good'}">{{ user[conf.polvalue] }}</span></td>
				</tr>
			</table>
		</div>
	</div>
</template>
<script>
export default {
	props: ['audconfs','useraudits'],
	data() {
		return {

		}
	},
	mounted() {

	},

}
</script>