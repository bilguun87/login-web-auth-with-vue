<template>
	<div>
		<span style="font-size:30px;cursor:pointer" v-on:click="openNav">&#9776;</span>
	    <div id="mySidenav" class="sidenav" v-bind:style = "{width : width + 'px'}">
			<a href="javascript:void(0)" class="closebtn" v-on:click="closeNav">&times;</a>
			<a href="/vulners" class="sidelink">Dashboard</a>
			<a href="/vulners/search" class="sidelink">Search vulnerability</a>
			<a href="/vulners/upload" class="sidelink">Upload scanner result</a>
			<a href="/vulners/allot" class="sidelink">To allot a vulnerabilities into departments</a>
			<a href="/vulners/servers" class="sidelink">Known servers</a>
			<a href="/vulners/report" class="sidelink">Report</a>
		</div>
	</div>
</template>
<script>
	export default {
		data() {
			return {
				width: "0",
				currentPath: window.location.href,
			}
		},

        mounted() {
        	this.openNav(),
        	this.closeNav(),
        	this.setActiveSymptom()
        },

        methods: {
        	openNav: function() {
        		this.width = "250";
        	},
        	closeNav: function() {
        		this.width = "0";
        	},
        	setActiveSymptom: function() {
        		var submenus = document.getElementById("mySidenav").querySelectorAll(".sidelink");
        		//console.log (submenus);
        		submenus.forEach(link => {
        			//console.log(link.href);
        			//console.log(this.currentPath);
        			if (link.href == this.currentPath){
    					link.style.color = "red";
    				}
    				//console.log(link.href.includes(this.currentPath))
        		});
        	}
    	}
    }
</script>