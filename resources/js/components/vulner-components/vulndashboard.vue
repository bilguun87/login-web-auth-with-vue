<template>
	<div class="row general-height" style="padding-bottom: 30px; padding-top: 30px;">
		<div style="width: 100%;"><img v-show="loading" style="margin-left: auto; margin-right: auto; height: 30px; width: 30px; display: block;" src="/img/loading_2.gif" alt="Loading"></div>
		<div class="col-xl-12 col-lg-12 col-md-12 col-sm-6">
			<div v-if="errmsg != ''" class="alert-maba">{{ errmsg }}</div>
			<div class="row justify-content-center" v-if="piefilled" style="margin-top: 10px;">
				<h4>Last 5 season's Vulnerabilities By Risk</h4>
				<div style="width: 100%;">
					<div v-for="pd in Pies">
						<pie-chart :chartData="pd.data" :options="pd.options" style="height: 140px; display: inline-block; width: 20%; float: left;" ref="piechart"/>
					</div>
					<div v-for="pd in Pies">
						<span style="display: inline-block; width: 20%; float: left; text-align: center;">All:&nbsp;{{ pd.all }}</span>
					</div>
				</div>
				
				<ul style="margin-top: 20px; list-style-type: none;">
					<li class="legendli" @click="toggleLegend(0)">
						<span class="critlegend" v-bind:class="{critshow: isCritshow}">Crtical</span>
					</li>
					<li class="legendli" @click="toggleLegend(1)">
						<span class="highlegend" v-bind:class="{highshow: isHighshow}">High</span>
					</li>
					<li class="legendli" @click="toggleLegend(2)">
						<span class="medlegend" v-bind:class="{medshow: isMedshow}">Medium</span>
					</li>
					<li class="legendli" @click="toggleLegend(3)">
						<span class="lowlegend" v-bind:class="{lowshow: isLowshow}">Low</span>
					</li>
				</ul>
			</div>
			<br><br>
			<div class="row" v-if="fixedfilled && newfilled">
				<h4 style="margin-left: auto; margin-right: auto; width: 60%;">Last 5 season's Vulnerabilities By Fixed and New status</h4>
				<div class="col-md-6"><line-chart :data="fixedData" :options="lineOptions" style="height: 140px;" /></div>
				<div class="col-md-6"><line-chart :data="newData" :options="lineOptions" style="height: 140px;" /></div>
			</div>
		</div>
	</div>
</template>
<script>
	import LineChart from "./linechart.js";
	import PieChart from "./piechart.js";
	export default {
		components: {LineChart, PieChart},
		data() {
			return {
				fixedfilled: false,
				newfilled: false,
				piefilled: false,
				errmsg: '',
				loading: true,
				fixedData: {},
				newData: {},
				Pies: [],
				lineOptions: {
					responsive: true,
					maintainAspectRatio: false,
					scales: {
						xAxes:[{
							gridLines: {
								lineWidth: 0,
								zeroLineWidth: 1,
							}
						}],
						yAxes:[{
							gridLines: {
								lineWidth: 0,
								zeroLineWidth: 1,
							},
							ticks: {
								fontSize: 8
							}
						}],
					}
				},
				isCritshow: true,
				isHighshow: true,
				isMedshow: true,
				isLowshow: true,
			}
		},
        async mounted() {
            this.getfixeds();
        },
        methods: {
        	toggleLegend(risk){
        		if (risk == 0)
        			this.isCritshow = !this.isCritshow;
        		else if (risk == 1)
        			this.isHighshow = !this.isHighshow;
        		else if (risk == 2)
        			this.isMedshow = !this.isMedshow;
        		else 
        			this.isLowshow = !this.isLowshow;

        		for(const index in this.$refs.piechart){
        			this.$refs.piechart[index].updateChart(index,risk);	
        		}
        	},
        	getfixeds: function(){
        		self = this;
				this.loading = true;
				axios.get('/api/getnfix')
				.then((response) => {
					this.errmsg = '';
					var fixdatasets = [];
					var newdatasets = [];
					var fixdata = [];
					var newdata = [];
					var pdata = [];
					var piedatas = [];
					var labels = [];
					//console.log(Object.keys(this.reportdata).length);
					
					/*Zasagdsan vulner-iin data oruulj ban*/
					for(const key in response.data){
						fixdata.push(response.data[key].fixed);
						newdata.push(response.data[key].new);
						labels.push(response.data[key].sname);
						pdata.push(response.data[key].crit);
						pdata.push(response.data[key].high);
						pdata.push(response.data[key].med);
						pdata.push(response.data[key].low);
						piedatas[key] = { 
							'datas': {'data': pdata, 'label': response.data[key].sname, 'backgroundColor': ['rgba(128, 0, 128, 0.6)','rgba(255, 0, 0, 0.8)','rgba(255, 153, 0, 0.8)','rgba(0, 0, 255, 0.6)']},
							'options': {responsive: true,	maintainAspectRatio: false,	legend: {display: false	}, title: {	display: true, text: response.data[key].sname,}},
							'all' : response.data[key].crit + response.data[key].high + response.data[key].med + response.data[key].low
						}
						pdata = [];
					}

					fixdatasets.push({
						label: 'Fixed numbers of last 5 quarter',
						data: fixdata,
						borderColor: '#49e673',
						backgroundColor: 'rgba(73,230,115,0.3)',
					});
					this.fixedData = {labels: labels, datasets: fixdatasets}
					this.fixedfilled = true;

					newdatasets.push({
						label: 'New vulnerabilities',
						data: newdata,
						borderColor: '#e64949',
						backgroundColor: 'rgba(230,73,73,0.3)',
					});
					this.newData = {labels: labels, datasets: newdatasets}
					this.newfilled = true;

					for (const ds in piedatas){
						this.Pies.push({'data': {labels: ['critical', 'high', 'medium', 'low'], datasets: [piedatas[ds].datas]}, 'options': piedatas[ds].options, 'all': piedatas[ds].all});
					}
					this.piefilled = true;
					this.loading = false;
					//console.log(this.Pies);
				})
				.catch( function (error) {
                    self.loading = false;
                    self.errmsg = '';
                    console.log(error);
                    if (error.response.status == 401)
                        window.location.href = '/login';
                    else if (error.response.status == 500)
                        self.errmsg = error.response.data.message;
                    else
                        self.errmsg = error.response.data.message;
                        //console.log("Error: " + error.response.data.message);
                    setTimeout(function(){self.errmsg = ''}, 5000);
                })
        	},
        },
    }
</script>