export const getPermissions = {
	created() {
		//console.log('Greating Global Mixin')
        axios.get('/api/getpermissions')
        .then((response) => {
            console.log('Mixins start')
            console.log(response.data.data);
            console.log('Mixins end')
            this.$store.commit("setPermissionsAndRoles", response.data.data);
        })
        .catch(function (error) {
            self.loading = false;
            if (error.response.status == 401)
                window.location.href = '/login';
                //console.log('Not authenticated');
            else if (error.response.status == 500)
                console.log("Error: " + error.response.data.message);
            else
            	console.log("Error on getPermissions");
                //alert("Error: " + error.response.data.message);
                //console.log("Error: " + error.response.data.message);
        })
	}
}