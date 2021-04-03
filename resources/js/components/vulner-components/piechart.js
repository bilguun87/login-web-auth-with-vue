import { Doughnut, mixins } from 'vue-chartjs'
const { reactiveProp } = mixins

export default {
	extends: Doughnut,
	mixins: [reactiveProp],
	props: ['chartData','options'],
	/*watch: {
		chartData () {
			console.log(this)
			this.$data._chart.update()
		},
	},*/
	/*computed: {
		chartData: function() {
		  return this.data;
		}
	},*/
	mounted () {
		this.render()
	},
	methods: {
		render: function(){
			this.renderChart(this.chartData, this.options)
		},
		updateChart: function(index, risk){
			var legend = this.chartData.datasets[0]._meta[index].data[risk];
			legend.hidden = !legend.hidden;
			this.$data._chart.update()
			//console.log(this)
		}
	}
}