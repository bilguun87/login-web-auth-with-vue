<template>
<div class="col-xl-12 col-lg-12 col-md-12 col-sm-6">  
    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 hover" style="float: left; margin-bottom: 20px;" v-for="menu in menus">
        <div :id="menu.id" class="block-menu" v-if="checkPermission(menu.required_perm)">
            <a :href="menu.url">
                <img class="menu-image" :src="'/img/' + menu.img" :alt="menu.name">
                <div class="overlay">
                    <div class="text">{{ menu.name }}</div>
                </div>
            </a>
        </div>
    </div>
</div>
</template>

<script>
    import { getPermissions } from "../mixins/getPermissions"
    export default {
        mixins: [getPermissions],
        data () {
            return {
                hoverId: 0,
                menus: []
            }
        },

        mounted() {
            this.loadMenu();
            /*this.getHoverElem(0);*/
            //console.log('Component menu mounted.')
        },

        methods: {
            loadMenu: function() {
                // load API
                axios.get ('/api/menu')
                .then ((response) => {
                    this.menus = response.data.data;
                })
                .catch( function (error) {
                    console.log(error);
                });
                // assign this categories
                // catch errors
            },

            checkPermission: function(required_perm) {
                //console.log(required_perm)
                var rightfull = false
                var userrights = this.$store.state.userrights.permissions;
                //console.log(userrights)
                for (var i=0; i < userrights.length; i++){
                    console.log(userrights[i])
                    if (userrights[i].includes(required_perm)){
                        rightfull = true
                        //break
                    }
                }
                return rightfull
            }
        }
    }
</script>