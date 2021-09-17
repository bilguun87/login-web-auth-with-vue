import Vue from "vue";
import Vuex from "vuex";
 
Vue.use(Vuex);
 
export default new Vuex.Store({
 state: {
 	userrights: {
 		permissions: ['none'],
 		roles: ['none']
 	}
 },
 getters: {},
 mutations: {
 	setPermissionsAndRoles(state, payload) {
 		//console.log(payload[0])
 		state.userrights.permissions = payload[0].permissions
 		state.userrights.roles = payload[0].roles
 		for (var i = 0; i < payload[0].permissionsviaroles.length; i++){
 			state.userrights.permissions.push(payload[0].permissionsviaroles[i].name)
 		}
 	}
 },
 actions: {}
});