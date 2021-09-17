<template>
	<div class="container">
		<div class="row" style="margin-top: 10px;">
            <div style="width: 100%;"><img v-show="loading" style="margin-left: auto; margin-right: auto; height: 30px; width: 30px; display: block;" src="/img/loading_2.gif" alt="Loading"></div>
            <div v-if="errmsg != ''" class="alert-maba">{{ errmsg }}</div>
        </div>
		<div class="row" style="margin-top: 10px;">
			<button class="btn btn-primary" @click="openModal">Add link</button>
		</div>
		<div class="row" style="margin-top: 10px;">
			<ul id="link-list" style="list-style-type: none; padding-left: 0; width: 70%;" v-if="links.length > 0">
				<li v-for="(link, index) in links">
					<div style="display: flex; position: relative; min-width: 410px; padding: 5px;">
						<div style="display: inline-block;">
							<img style="width: 50px; height: 50px; object-fit: cover;" :src="link.icon" alt="Icon">
						</div>
						<div style="display: 5px; width: 80%; padding: 5px 15px;">
							<a :href="link.link" target="_blank" style="font-size: 15px;">{{ link.name }}</a><br>
							<span style="padding: 5px;">{{ link.description }}</span>
						</div>
						<div style="float: right;">
							<button class="btn btn-sm btn-warning" @click="editShow(index)">Edit</button>
							<button class="btn btn-sm btn-danger" @click="deleteLink(index)">Delete</button>
						</div>
					</div>
				</li>
			</ul>
		</div>
		<!-- Modal -->
		<div class="row">
			<div tabindex="-1" ref="modal" class="cmodal">
				<div class="cmodal-body">
					<div class="row" style="margin-left: 0; margin-right:0;">
						<div class="col">
							<h5>Add link</h5>
						</div>
						<div class="col-sm-1">
							<span @click="closeModal" style="cursor: pointer; display: block; text-align: right;">X</span>
						</div>
					</div>

					<div class="row" style="margin-left: 0; margin-right:0;">
						<div class="col">
							<div class="form-group form-group-sm">
								<label for="linkname">Name of link</label>
								<input type="text" class="form-control" id="linkname" aria-describedby="linknameHelp" placeholder="Enter name of link" v-model="form.name">
								<small id="linknameHelp" class="form-text text-muted">Give a name for link</small>
							</div>
							<div class="form-group form-group-sm">
								<label for="link">Link</label>
								<input type="text" class="form-control" id="link" aria-describedby="linkHelp" placeholder="Link" v-model="form.link">
								<small id="linkHelp" class="form-text text-muted">Link/Url</small>
							</div>
							<div class="form-group form-group-sm">
								<label for="linkdesc">Description</label>
								<input type="text" class="form-control" id="linkdesc" aria-describedby="linkdescHelp" placeholder="Link Description" v-model="form.desc">
								<small id="linkdescHelp" class="form-text text-muted">/Optional/ Description of links</small>
							</div>
							<div class="custom-file">
                                <input type="file" class="custom-file-input" id="file" ref="icon" v-on:change="imageFileUpload" accept=".jpeg,.jpg,.png">
                                <label class="custom-file-label">Choose file</label>
                            </div>
                            <div style="width: 52px; height: 52px; border: 1px solid black;">
                            	<img v-if="url" :src="url" style="width:50px; height:50px; object-fit: cover;">
                            </div>
                            <div style="text-align: end; margin-top: 10px;">
								<button type="submit" class="btn btn-primary" style="display: inline-block;" @click="updateLink" v-if="editIndex >= 0">Save Edited</button>
								<button type="submit" class="btn btn-primary" style="display: inline-block;" @click="saveLink" v-else>Save</button>
								<button type="submit" class="btn btn-warning" style="display: inline-block;" @click="closeModal">Cancel</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>
<script>
export default{
	data(){
		return {
			editIndex: -1,
			errmsg: '',
			loading: false,
			showModal: false,
			url: null,
			links: [],
			form: {},
		}
	},
	mounted(){
		this.getLinks()
	},
	methods: {
		closeModal: function(){

			this.showModal = false
			this.$refs['modal'].style.display='none'
			this.form = {}
			this.url = null
			this.editIndex = -1
		},
		openModal: function(){
			// console.log('Clicked')
			this.showModal = true
			this.$refs['modal'].style.display='block'
		},
		imageFileUpload: function(event){
			
			let file = event.target.files[0]
			// console.log(file);
			if (file !== undefined){
				this.url = URL.createObjectURL(file)
				this.form.icon = file
			}
			else{
				this.url = null
				this.form.icon = null
			}
		},

		editShow: function(index){
			// console.log(index)
			this.editIndex = index;
			this.form.name = this.links[index].name;
			this.form.link = this.links[index].link;
			this.form.desc = this.links[index].description;
			// this.form.icon = this.links[index].icon;
			this.url = this.links[index].icon;
			this.openModal()
		},

		getLinks: function(){
			this.loading = true;
            let self = this;
            axios.get ('/api/getlinks')
            .then ((response) => {
            	this.loading = false;
            	this.links = response.data.data;
                //console.log(response.data);
            })
            .catch( function (error) {
                self.loading = false;
                self.errmsg = '';
                //console.log(error.response);
                if (error.response.status == 401)
                    window.location.href = '/login';
                else if (error.response.status == 500)
                    self.errmsg = error.response.data.message;
                else if (error.response.status == 422){
                    self.errmsg = "Incomplete data, make shure fill the required fields and/or image";
                }
                else
                    self.errmsg = "Error: " + error;

                setTimeout(function(){self.errmsg = ''}, 5000);
            })
		},

		saveLink: function(){
			this.loading = true;
            let self = this;
            let formData = new FormData();
            formData.append('icon', this.form.icon);
            formData.append('name', this.form.name);
            formData.append('link', this.form.link);
            formData.append('desc', this.form.desc);
            // console.log(formData.get('name'))
            axios.post ('/api/linkadd', formData)
            .then ((response) => {
            	this.loading = false;
            	this.links.push(...response.data.data)
                //console.log(response.data);
            })
            .catch( function (error) {
            	// console.log(error);
                self.loading = false;
                self.errmsg = '';
                //console.log(error.response);
                if (error.response.status == 401)
                    window.location.href = '/login';
                else if (error.response.status == 500)
                    self.errmsg = error.response.data.message;
                else if (error.response.status == 422){
                    self.errmsg = "Incomplete data, make shure fill the required fields and/or image";
                }
                else
                    self.errmsg = "Error: " + error;

                setTimeout(function(){self.errmsg = ''}, 5000);
            })
		},
		updateLink: function(){
			this.loading = true;
            let self = this;
            let editData = new FormData();
            // console.log(this.form.icon);
            if('icon' in this.form){
            	editData.append('icon', this.form.icon);
            }
        	editData.append('name', this.form.name);
            editData.append('link', this.form.link);
            editData.append('desc', this.form.desc);

            axios.post('/api/linkedit/' + this.links[this.editIndex].id, editData)
            .then ((response) => {
            	this.loading = false;
            	if (response.data.data == 'updated'){
	            	this.links[this.editIndex].name = this.form.name;
					this.links[this.editIndex].link = this.form.link
					this.links[this.editIndex].description = this.form.desc
					if('icon' in this.form){
						this.links[this.editIndex].icon = URL.createObjectURL(this.form.icon)
					}
				}
				this.editIndex = -1
				this.closeModal()
                //console.log(response.data);
            })
            .catch( function (error) {
            	console.log(error);
                self.loading = false;
                self.errmsg = '';
                //console.log(error.response);
                if (error.response.status == 401)
                    window.location.href = '/login';
                else if (error.response.status == 500)
                    self.errmsg = error.response.data.message;
                else if (error.response.status == 422){
                    self.errmsg = "Incomplete data, make shure fill the required fields and/or image";
                }
                else
                    self.errmsg = "Error: " + error;

                setTimeout(function(){self.errmsg = ''}, 5000);
            })
		},
		deleteLink: function(index){
			if(confirm('Are you sure?!!')){
				this.loading = true;
	            let self = this;
	            axios.delete('/api/linkdelete/' + this.links[index].id)
	            .then((response) => {
	            	this.loading = false;
	            	this.links.splice(index, 1)
	            })
	            .catch( function (error) {
	            	console.log(error);
	                self.loading = false;
	                self.errmsg = '';
	                //console.log(error.response);
	                if (error.response.status == 401)
	                    window.location.href = '/login';
	                else if (error.response.status == 500)
	                    self.errmsg = error.response.data.message;
	                else if (error.response.status == 422){
	                    self.errmsg = "Incomplete data, make shure fill the required fields and/or image";
	                }
	                else
	                    self.errmsg = "Error: " + error;

	                setTimeout(function(){self.errmsg = ''}, 5000);
	            })
        	}
		}
	},
}
</script>