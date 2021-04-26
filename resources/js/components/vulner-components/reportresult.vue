<template>
	<div class="container">
		<button class="btn btn-warning" @click="exportPDF" style="margin-bottom: 10px;">Export To PDF</button>
		<div style="height: 5px; width: 100%; background-color: gray; /*text-align: center;*/"></div>
		<div ref="pdfcontent" style="font-family: TimesNewRoman, 'Times New Roman', Times, Baskerville, Georgia, serif;">

		<h2 style="text-align: center; width: 50%; margin-left: auto; margin-right: auto;" ref="title">{{ reportdata.season }}-н сервер, систем, сүлжээний нүх цоорхойны тайлан</h2>
		<p>
            &nbsp&nbsp&nbsp<span ref="header1">   Мэдээллийн аюулгүй байдлын албаны "{{ reportdata.season }}"-н төлөвлөгөөний дагуу үндсэн серверын бүс, картын серверын бүст аюулгүй байдлын шалгалтыг {{ reportdata.scandate }}-нд хийсэн бөгөөд үүний илэрсэн үр дүндээр үндэслэн тайланг боловсруулсан болно.
            </span> <br>
            <div v-if="reportdata.ips != ''">
                <span ref="headerIP">Энэ нь дараах IP хаяг(ууд)тай сервер(үүд)ийн шалгалтын тайлан болно.
                </span>
                <span ref="ips">{{ reportdata.ips }}</span>
            </div>
            <div v-else ref="headerIP">Шалгалтыг бүх серверийн бүст хийхэд {{ reportdata.hostcount }} серверт эмзэг байдал илэрсэн.</div>
			<div v-if = "reportdata.allcurrentvulners > 0" ref = "headerVulnerCount">Нийт {{ reportdata.allcurrentvulners }} цоорхой илэрсэн бөгөөд нийт цоорхойнуудаас Critical цоорхойнууд {{ (100/reportdata.allcurrentvulners*reportdata.allcurrentcritical).toFixed(2) }}%, High цоорхойнууд {{ (100/reportdata.allcurrentvulners*reportdata.allcurrenthigh).toFixed(2) }}, Medium цоорхойнууд {{ (100/reportdata.allcurrentvulners*reportdata.allcurrentmedium).toFixed(2) }}%, Low цоорхойнууд {{ (100/reportdata.allcurrentvulners*reportdata.allcurrentlow).toFixed(2) }}% тус тус эзэлж байна.
			</div>
			<div v-else ref = "headerVulnerCount">
				Шалгалтаар цоорхой 0 илэрсэн.
			</div>
        </p>
        <p ref="header2">
            Дараах хүснэгтэд хамрагдах газар, хэлтэс: {{ reportdata.groups }}<br>
            <b>Цоорхойнууд эрсдлийн түвшингөөр /Өмнөх улиралтай харьцуулбал/</b>
        </p>
        <table class="table table-stiped" ref="table1">
            <tr>
                <th>Улирал</th><th>Critical</th><th>High</th><th>Medium</th><th>Low</th>
            </tr>
            <tr>
                <td>{{ reportdata.season }}</td>
                <td>{{ reportdata.allcurrentcritical }}</td>
                <td>{{ reportdata.allcurrenthigh }}</td>
                <td>{{ reportdata.allcurrentmedium }}</td>
                <td>{{ reportdata.allcurrentlow }}</td>
            </tr>
            
            <tr v-if="reportdata.prevseason !== 'undefined'">
                <td>{{ reportdata.prevseason }}</td>
                <td>{{ reportdata.allprevcritical }}</td>
                <td>{{ reportdata.allprevhigh }}</td>
                <td>{{ reportdata.allprevmedium }}</td>
                <td>{{ reportdata.allprevlow }}</td>
            </tr>
            <tr v-if="reportdata.prevseason !== 'undefined'">
                <td>Зөрүү</td>
                <td>{{ reportdata.allcurrentcritical - reportdata.allprevcritical }}</td>
                <td>{{ reportdata.allcurrenthigh - reportdata.allprevhigh }}</td>
                <td>{{ reportdata.allcurrentmedium - reportdata.allprevmedium }}</td>
                <td>{{ reportdata.allcurrentlow - reportdata.allprevlow }}</td>
            </tr>
        </table>
        <p><b ref="header3">Илэрсэн байдал /Өмнөх улиралтай харьцуулбал/</b></p>
        <table class="table table-striped" ref="table2">
            <tr>
                <th>Улирал</th><th>Шинэ</th><th>Хостын хувьд шинэ</th><th>Дахин илэрсэн</th><th>Засагдсан</th>
            </tr>
            <tr>
                <td>{{ reportdata.season }}</td>
                <td>{{ reportdata.allcurrent0 }}</td>
                <td>{{ reportdata.allcurrent1 }}</td>
                <td>{{ reportdata.allcurrent2 }}</td>
                <td>{{ reportdata.allcurrent4 }}</td>
            </tr>
            <tr v-if = "reportdata.prevseason !=='undefined'">
                <td>{{ reportdata.prevseason }}</td>
                <td>{{ reportdata.allprev0 }}</td>
                <td>{{ reportdata.allprev1 }}</td>
                <td>{{ reportdata.allprev2 }}</td>
                <td>{{ reportdata.allprev4 }}</td>
            </tr>
            <tr v-if = "reportdata.prevseason !=='undefined'">
                <td>Зөрүү</td>
                <td>{{ reportdata.allcurrent0 - reportdata.allprev0 }}</td>
                <td>{{ reportdata.allcurrent1 - reportdata.allprev1 }}</td>
                <td>{{ reportdata.allcurrent2 - reportdata.allprev2 }}</td>
                <td></td>
            </tr>
        </table>
        <b v-if="reportdata.topfive.length">Хамгийн их илэрсэн топ 5 цоорхой</b>
        <table class="table table-striped" v-if="reportdata.topfive.length">
            <tr>
                <th>Тоо</th>
                <th>Цоорхойны нэр</th>
            </tr>
            <tr v-for="tf in reportdata.topfive">
                <td>{{ tf.counted }}</td>
                <td>{{ tf.name }}</td>
            </tr>
        </table>
        <br>
        <b v-if="reportdata.topfivecrit.length">Хамгийн их илэрсэн топ 5 critical цоорхой</b>
        <table class="table table-striped" v-if="reportdata.topfivecrit.length">
            <tr>
                <th>Тоо</th>
                <th>Цоорхойны нэр</th>
            </tr>
            <tr v-for="tfc in reportdata.topfivecrit">
                <td>{{ tfc.counted }}</td>
                <td>{{ tfc.name }}</td>
            </tr>
        </table>
        <div class="row" v-for="(dep, index) in reportdata.depbydep">
        	<div class="col-md-12" style="margin-bottom: 10px;">
        		<h3>
        			<span v-if = "index == 'Without Group'">Хэлтэст хуваарилаагүй</span>
        			<span v-else>{{index}}</span>
        		</h3>
        		<table class="table table-striped">
                    <tr>
                        <th>Түвшин</th>
                        <th>Илэрсэн</th>
                        <th>Засварласан/Засагдаагүй</th>
                        <th>Өмнөх улиралд илэрсэн</th>
                        <th>Өмнөх улиралд засварласан/засварлаагүй</th>
                    </tr>
                    <tr>
                        <td>Critical</td>
                        <td>
							{{ dep.critical }}
                        </td>
                        <td>
							{{ dep.fixcrit }}/{{ dep.unfixcrit }}
                        </td>
                        <td>
                            {{ dep.prevcrit }}
                        </td>
                        <td>
                            {{ dep.prevfixcrit }}/{{ dep.prevunfixcrit }}
                        </td>
                    </tr>
                    <tr>
                        <td>High</td>
                        <td>
                        	{{ dep.high }}
                        </td>
                        <td>
                            {{ dep.fixhigh }}/{{ dep.unfixhigh }}
                        </td>
                        <td>
                            {{ dep.prevhigh }}
                        </td>
                        <td>
                            {{ dep.prevfixhigh }}/{{ dep.prevunfixhigh }}
                        </td>
                    </tr>
                    <tr>
                        <td>Medium</td>
                        <td>
                        	{{ dep.medium }}
                        </td>
                        <td>
                        	{{ dep.fixmed }}/{{ dep.unfixmed }}
                        </td>
                        <td>
                            {{ dep.prevmed }}
                        </td>
                        <td>
                            {{ dep.prevfixmed }}/{{ dep.prevunfixmed }}
                        </td>
                    </tr>
                    <tr>
                        <td>Low</td>
                        <td>
                            {{ dep.low }}
                        </td>
                        <td>
                            {{ dep.fixlow }}/{{ dep.unfixlow }}
                        </td>
                        <td>
                            {{ dep.prevlow }}
                        </td>
                        <td>
                            {{ dep.prevfixlow }}/{{ dep.prevunfixlow }}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Нийт
                        </td>
                        <td>
                            {{ dep.vulners.length }}
                        </td>
                        <td>
                            {{ dep.f1 }} 
                            <span v-if="dep.vulners.length > 0">
                            	({{ (100/dep.vulners.length*dep.f1).toFixed(2) }}%)
                            </span>/{{ dep.f0 }}
                        </td>
                        <td>
                            {{ dep.prevall }}
                        </td>
                        <td>
                            {{ dep.pf1 }}
                            <span v-if="dep.prevall > 0">
                                ({{ (100/dep.prevall*dep.pf1).toFixed(2) }}%)
                            </span>/{{ dep.pf0 }}
                        </td>
                    </tr>
                </table>
                <b>Шинээр илэрсэн эмзэг байдал /Хостын тоогоор/</b>
                <table class="table table-striped">
                    <tr>
                        <th>Түвшин</th>
                        <th>Тоо</th>
                    </tr>
                    <tr>
                        <td>Critical</td>
                        <td>
                            {{ dep.newcrit }}
                        </td>
                    </tr>
                    <tr>
                        <td>High</td>
                        <td>
                            {{ dep.newhigh }}
                        </td>
                    </tr>
                    <tr>
                        <td>Medium</td>
                        <td>
                            {{ dep.newmed }}
                        </td>
                    </tr>
                    <tr>
                        <td>Low</td>
                        <td>
                            {{ dep.newlow }}
                        </td>
                    </tr>
                    <tr>
                        <td>Нийт шинээр илэрсэн</td>
                        <td>
                            {{ dep.newall }}
                        </td>
                    </tr>
                </table>
                <span v-if="dep.stat1 != 'undefined'">Тухайн хостын хувьд шинэ: {{ dep.stat1 }}</span><br>
                <span v-if="dep.stat1 != 'undefined'">Дахин илэрсэн: {{ dep.stat2 }}</span><br>
        	</div>
        	<div class="btn-div" data-html2canvas-ignore="true">
			<downloadexcel :data="dep.vulners" :fields="excel_fields" :meta="excel_meta" :name="index + '-' + reportdata.season + '-detailed.xls'">
				Export detailed data to excel
				<img src="/img/download.png" style="width: 20px; height: 20px;" />
			</downloadexcel>
			</div>
        </div>
    	</div>
	</div>
</template>
<script>
	import  jspdf from 'jspdf';
	import 'jspdf-autotable';
	import html2pdf from 'html2pdf-jspdf2';
	/*Times new roman UTF-8 font import хийж байна*/
	import './times-normal.js';
	//import './timesbd-bold.js';
	import downloadexcel from "vue-json-excel";
	/**/
	export default{
		props: ['reportdata'],
		data() {
			return {
				seasonname: '',
				excel_fields: {
					"IP": "host",
					"Risk": "risk",
					"Name of Vulnerability": "name",
					"Port": "port",
					"Protocol": "protocol",
					"Fixed or Not": {
						field: "fix",
						callback: (value) => {
							if (value == 0)
								return "Үгүй";
							else
								return "Тийм";
						}
					},
					"CVE": "cve",
					"CVSS": "cvss",
					"Status": {
						field: "status",
						callback: (value) => {
							if (value == 0)
								return "Шинэ";
							else if (value == 1)
								return "Хостын хувьд шинэ";
							else
								return "Дахин илэрсэн";
						}
					},
				},
				excel_meta: [
					[{"key":"charset", "value": "utf-8"}]
				],
			}
		},
		components: {downloadexcel},
		mounted() {
			//console.log(this.data);
		},
		methods: {
			exportPDF: function(){
				var dt = new Date();
				if (dt.getMonth() < 3)
					var season = "I";
				else if (dt.getMonth() >= 3 && dt.getMonth() < 6)
					var season = "II";
				else if (dt.getMonth() >= 6 && dt.getMonth() < 9)
					var season = "III";
				else
					var season = "IV";
				var element = this.$refs.pdfcontent;
				var opt = {
					margin: [10,20,30,10],
					pagebreak: {mode: 'css'},
					html2canvas: {scale: 3, y: 0, scrollY: 0},
					filename: "vulnerresult_" + dt.getFullYear() + "_" + season + ".pdf",
					jsPDF: {unit: 'mm', format: 'a4', orientation: 'portrait'},
					pdfCallback: this.pdfCallback
				};
				html2pdf().set(opt).from(element).toPdf().get('pdf').then(function (pdf){
					var number_of_pages = pdf.internal.getNumberOfPages();
				    var pdf_pages = pdf.internal.pages;
				    var myFooter = "";
				    for (var i = 1; i < pdf_pages.length; i++) {
				        // We are telling our pdf that we are now working on this page
				        pdf.setFont('times');
				        pdf.setFontSize(12);
				        pdf.setPage(i);
				        myFooter = "Хуудас: " + i;
				        pdf.setDrawColor(53, 103, 184);
				        pdf.line(20, 270, 200, 270);
				        // The 10,200 value is only for A4 landscape. You need to define your own for other page sizes
				        pdf.text(myFooter, 20, 280);
				    }
				}).save();
				//html2pdf(element, opt);
			},
			/*
			getLinesWithText: function(lines) {
				var ln = [];
				for (var i = 0; i < lines.length; i++)
					if (lines[i].replace(/\s/g, "") != "")
						ln.push(lines[i]);
				return ln;
			},

			linenumber: function(lines) {
				var ln = 0;
				for (var i = 0; i < lines.length; i++)
					if (lines[i].replace(/\s/g, "") != "")
						ln ++;
				return ln
			},*/
		}
	}
</script>