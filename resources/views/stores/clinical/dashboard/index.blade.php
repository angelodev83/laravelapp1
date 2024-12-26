@extends('layouts.master')
@section('content')
<!--start page wrapper -->
		<div class="page-wrapper">
			<div class="page-content">
				<!-- PAGE-HEADER -->
				@include('layouts/pageContentHeader/store')
				<!-- PAGE-HEADER END -->
                @include('stores/clinical/dashboard/partials/tab')
			</div>
		</div>
		<!--end page wrapper -->

@stop
@section('pages_specific_scripts')  
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    let lineChart1 = '';
	let radialBarChartDiabetes = '';
	let radialBarChartRasa = '';
	let radialBarChartCholesterol = '';
	let radialBarChartStatin = '';
	let radialBarChartOutocmes = '';
	let barChartTips = '';
	let barChartCmr = '';
	let barChartFfsCount = '';
	let barChartCcoCount = '';
	let barChartpbm1Count = '';
	let barChartpbm2Count = '';
	let areaChartTo = '';
	let areaChartPto = '';
	let barChartFfspCount = '';
	let barChartCcopCount = '';
	let barChartpbm1pCount = '';
	let barChartpbm2pCount = '';
	let barChartToa = '';
	let radialBarChartaverageOpportunitiesCpp = '';
	let barChartTotalBilledAmount = '';
	let radialBarChartBilledVsTotalOar = '';
	let barChartTotalRrcAndTotalRc = '';
	let radialBarChartRefillRenewed = '';
	let barChartPendingRenewal = '';
	let radialBarChartPendingRenewalPercent= '';
	let barChartDeniedRenewal = '';
	let radialBarChartDeniedRenewalPercent = '';
    let menu_store_id = {{request()->id}};

	function addMonths(date, months) {       
        date.setMonth(date.getMonth() + months);
        return date;
    }

    function subMonths(date, months) {
        date.setMonth(date.getMonth() - months);
        return date;
    }

    function getMonthsBetweenDates(startDate, endDate) {
        let start = new Date(startDate);
        let end = new Date(endDate);

        let months = [];
        while (start < end) {
            let monthIndex = start.getMonth();
            let monthName = [
                "Jan", "Feb", "Mar", "Apr", "May", "Jun",
                "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
            ][monthIndex];
            months.push(monthName);
            start.setMonth(start.getMonth() + 1);
        }
        return months;
    }
    
    var start;

	$('#search_fromdate').datepicker({
        format: "yyyy-mm",
        startView: "months", 
        minViewMode: "months",
        autoclose:true
    });

    $('#search_todate').datepicker({
        format: "yyyy-mm",
        startView: "months", 
        minViewMode: "months",
        autoclose:true
    });

	$("#search_fromdate").on('changeDate',function(date){
        //start = new Date(date);
        var e_date = $('#search_todate').datepicker("getDate");
        let maxDate = subMonths(e_date, 11);
        let changeDate = addMonths(date.date, 11);
        
        var selectedDate = $(this).datepicker("getDate");

        if (selectedDate.valueOf() < maxDate) {
            // Get the input date value
            let inputDate = changeDate;
            // Convert the input date to a JavaScript Date object
            let dateObj = new Date(inputDate);
            // Format the date as YYYY-MM
            let formattedDate = dateObj.getFullYear() + '-' + ('0' + (dateObj.getMonth() + 1)).slice(-2);     
            $('#search_todate').val(formattedDate);
        }
        fetchData();
    });

    $("#search_todate").on('changeDate',function(date){
        start = new Date(date);
        var s_date = $('#search_fromdate').datepicker("getDate");
        let maxDate = addMonths(s_date, 11);
        let changeDate = subMonths(date.date, 11);
        
        var selectedDate = $(this).datepicker("getDate");
        
        if (selectedDate.valueOf() > maxDate) {
            // Get the input date value
            let inputDate = changeDate;
            // Convert the input date to a JavaScript Date object
            let dateObj = new Date(inputDate);
            // Format the date as YYYY-MM
            let formattedDate = dateObj.getFullYear() + '-' + ('0' + (dateObj.getMonth() + 1)).slice(-2);
            $('#search_fromdate').val(formattedDate);
        }
        
        fetchData();
    });

	$(document).ready(function() {
		let currentYear = new Date().getFullYear();
		let sSetDate = new Date(currentYear, 0 , 1);
		let eSetDate = new Date(currentYear, 11 , 1);
		$('#search_fromdate').datepicker('setDate', sSetDate);
		$('#search_todate').datepicker('setDate', eSetDate);
		$('#search_fromdate').val(currentYear+'-01');
		$('#search_todate').val(currentYear+'-12');

		$('a[href="#open-clinical"]').on('shown.bs.tab', function (e) {
            $('#clinical2').addClass('active show');
			$('#kpi2').removeClass('active show');
        });
		$('a[href="#open-kpi"]').on('shown.bs.tab', function (e) {
            $('#kpi2').addClass('active show');
			$('#clinical2').removeClass('active show');
        });

		barChart();
        fetchData();
		areaChart();
		pieChart();
    });

	function lineChart(arrLabels, arrDataSets){
		const ctx = document.getElementById('lineChart');
		
		const labels = arrLabels;
		const data = {
			labels: labels,
			datasets: arrDataSets,
		};

		let myLineChart1 = new Chart(ctx, {
			type: 'line',
			data: data,
			options: {
				plugins:{
					legend:{
						labels:{
							usePointStyle:true,
							pointStyle:'rect',
						}
					}
				},
				responsive: true,
    			maintainAspectRatio: false,
				scales: {
					x: {
						grid: {
							display:false,
						}
					},
					y: {
						grid: {
							beginAtZero: true,
							display:true,	
						},
						min: 0,
						max:100,
						ticks:{
							stepSize: 20,
							maxTicksLimit:10,
							callback: function(value, index, values) {
								
								return value;
							
							}
						}
					},
				}
			}
		});

		lineChart1 = myLineChart1;
				
		myLineChart1.update();
	}

	function radialBar(){
		let optionsDiabetes = {
			chart: {
				height: 280,
				type: "radialBar",
				
			},
			
			series: [],
			colors: ["#FF7116"],
			responsive: [{
				breakpoint: 1195,
				options: {
					chart: {
						height: 250
					},
				}
			}],
			plotOptions: {
				radialBar: {
					hollow: {
						margin: 15,
						size: "35%"
					},
					
					dataLabels: {
						showOn: "always",
						show: true,
						name: {
							offsetY: -110,
							show: true,
							color: "#111",
							fontSize: "20px"
						},
						value: {
							offsetY: -10,
							color: "#111",
							fontSize: "20px",
							show: true
						}
					},
					
				}
			},

			stroke: {
				show: false,
				lineCap: "round",
				width: 0,
			},
			labels: ['Diabetes']
		};

		let rbChartDiabetes = new ApexCharts(document.querySelector("#diabetesRb"), optionsDiabetes);

		radialBarChartDiabetes = rbChartDiabetes;

		radialBarChartDiabetes.render();

		let optionsRasa = {
			chart: {
				height: 280,
				type: "radialBar",
				
			},
			
			series: [],
			colors: ["#3AC64D"],
			responsive: [{
				breakpoint: 1195,
				options: {
					chart: {
						height: 250
					},
				}
			}],
			plotOptions: {
				radialBar: {
					hollow: {
						margin: 15,
						size: "35%"
					},
					
					dataLabels: {
						showOn: "always",
						show: true,
						name: {
							offsetY: -110,
							show: true,
							color: "#111",
							fontSize: "20px"
						},
						value: {
							offsetY: -10,
							color: "#111",
							fontSize: "20px",
							show: true
						}
					},
					
				}
			},

			stroke: {
				show: false,
				lineCap: "round",
				width: 0,
			},
			labels: ['Rasa']
		};

		let rbChartRasa = new ApexCharts(document.querySelector("#rasaRb"), optionsRasa);

		radialBarChartRasa = rbChartRasa;

		radialBarChartRasa.render();

		let optionsCholesterol = {
			chart: {
				height: 280,
				type: "radialBar",
				
			},
			
			series: [],
			colors: ["#4F57E6"],
			responsive: [{
				breakpoint: 1195,
				options: {
					chart: {
						height: 250
					},
				}
			}],
			plotOptions: {
				radialBar: {
					hollow: {
						margin: 15,
						size: "35%"
					},
					
					dataLabels: {
						showOn: "always",
						show: true,
						name: {
							offsetY: -110,
							show: true,
							color: "#111",
							fontSize: "20px"
						},
						value: {
							offsetY: -10,
							color: "#111",
							fontSize: "20px",
							show: true
						}
					},
					
				}
			},

			stroke: {
				show: false,
				lineCap: "round",
				width: 0,
			},
			labels: ['Cholesterol']
		};

		let rbChartCholesterol = new ApexCharts(document.querySelector("#cholesterolRb"), optionsCholesterol);

		radialBarChartCholesterol = rbChartCholesterol;

		radialBarChartCholesterol.render();

		let optionsStatin = {
			chart: {
				height: 280,
				type: "radialBar",
				
			},
			
			series: [],
			colors: ["#6835CF"],
			responsive: [{
				breakpoint: 1195,
				options: {
					chart: {
						height: 250
					},
				}
			}],
			plotOptions: {
				radialBar: {
					hollow: {
						margin: 15,
						size: "35%"
					},
					
					dataLabels: {
						showOn: "always",
						show: true,
						name: {
							offsetY: -110,
							show: true,
							color: "#111",
							fontSize: "20px"
						},
						value: {
							offsetY: -10,
							color: "#111",
							fontSize: "20px",
							show: true
						}
					},
					
				}
			},

			stroke: {
				show: false,
				lineCap: "round",
				width: 0,
			},
			labels: ['Statin']
		};

		let rbChartStatin = new ApexCharts(document.querySelector("#statinRb"), optionsStatin);

		radialBarChartStatin = rbChartStatin;

		radialBarChartStatin.render();

		let optionsOutcomes = {
			chart: {
				height: 330,
				type: "radialBar",
				
			},
			
			series: [],
			colors: ["#8833ff"],
			responsive: [{
				breakpoint: 1195,
				options: {
					chart: {
						height: 250
					},
				}
			}],
			plotOptions: {
				radialBar: {
					hollow: {
						margin: 15,
						size: "50%"
					},
					
					dataLabels: {
						//showOn: "always",
						show: false,
						name: {
							offsetY: -10,
							show: true,
							color: "#888",
							fontSize: "13px"
						},
						value: {
							color: "#111",
							fontSize: "30px",
							show: true
						}
					},
					
				}
			},

			stroke: {
				show: false,
				lineCap: "round",
				width: 0,
			},
			labels: ['']
		};

		let rbChartOutcomes = new ApexCharts(document.querySelector("#mtm_rb"), optionsOutcomes);

		radialBarChartOutocmes = rbChartOutcomes;

		radialBarChartOutocmes.render();

		let optionsAverageOpportunitiesCpp = {
			chart: {
				height: 360,
				type: "radialBar",
				
			},
			
			series: [],
			colors: ["#363A86"],
			responsive: [{
				breakpoint: 1195,
				options: {
					chart: {
						height: 250
					},
				}
			}],
			plotOptions: {
				radialBar: {
					hollow: {
						margin: 15,
						size: "40%"
					},
					
					dataLabels: {
						//showOn: "always",
						show: true,
						name: {
							offsetY: -10,
							show: false,
							color: "#888",
							fontSize: "13px"
						},
						value: {
							offsetY: 10,
							color: "#58585A",
							fontSize: "40px",
							show: true
						}
					},
					
				}
			},

			stroke: {
				show: false,
				lineCap: "round",
				width: 0,
			},
			labels: ['Progress']
		};

		let rbChartAverageOpportunitiesCpp = new ApexCharts(document.querySelector("#averageOpportunitiesCpp"), optionsAverageOpportunitiesCpp);

		radialBarChartaverageOpportunitiesCpp = rbChartAverageOpportunitiesCpp;

		radialBarChartaverageOpportunitiesCpp.render();

		let optionsBilledVsTotalOar = {
			chart: {
				height: 360,
				type: "radialBar",
				
			},
			
			series: [],
			colors: ["#363A86"],
			responsive: [{
				breakpoint: 1195,
				options: {
					chart: {
						height: 250
					},
				}
			}],
			plotOptions: {
				radialBar: {
					hollow: {
						margin: 15,
						size: "40%"
					},
					
					dataLabels: {
						//showOn: "always",
						show: true,
						name: {
							offsetY: -10,
							show: false,
							color: "#888",
							fontSize: "13px"
						},
						value: {
							offsetY: 10,
							color: "#58585A",
							fontSize: "40px",
							show: true
						}
					},
					
				}
			},

			stroke: {
				show: false,
				lineCap: "round",
				width: 0,
			},
			labels: ['Progress']
		};

		let rbChartBilledVsTotalOar = new ApexCharts(document.querySelector("#billedVsTotalOar"), optionsBilledVsTotalOar);

		radialBarChartBilledVsTotalOar = rbChartBilledVsTotalOar;

		radialBarChartBilledVsTotalOar.render();

		let optionsRefillRenewed = {
			chart: {
				height: 271,
				type: "radialBar",
				
			},
			
			series: [],
			colors: ["#FD86B5"],
			responsive: [{
				breakpoint: 1195,
				options: {
					chart: {
						height: 250
					},
				}
			}],
			plotOptions: {
				radialBar: {
					hollow: {
						margin: 15,
						size: "40%"
					},
					
					dataLabels: {
						//showOn: "always",
						show: true,
						name: {
							offsetY: -10,
							show: false,
							color: "#888",
							fontSize: "13px"
						},
						value: {
							offsetY: 10,
							color: "#58585A",
							fontSize: "40px",
							show: true
						}
					},
					
				}
			},

			stroke: {
				show: false,
				lineCap: "round",
				width: 0,
			},
			labels: ['Progress']
		};

		let rbChartRefillRenewed = new ApexCharts(document.querySelector("#refillRenewed"), optionsRefillRenewed);

		radialBarChartRefillRenewed = rbChartRefillRenewed;

		radialBarChartRefillRenewed.render();

		let optionsPendingRenewalPercent = {
			chart: {
				height: 271,
				type: "radialBar",
				
			},
			
			series: [],
			colors: ["#41B8D5"],
			responsive: [{
				breakpoint: 1195,
				options: {
					chart: {
						height: 250
					},
				}
			}],
			plotOptions: {
				radialBar: {
					hollow: {
						margin: 15,
						size: "40%"
					},
					
					dataLabels: {
						//showOn: "always",
						show: true,
						name: {
							offsetY: -10,
							show: false,
							color: "#888",
							fontSize: "13px"
						},
						value: {
							offsetY: 10,
							color: "#58585A",
							fontSize: "40px",
							show: true
						}
					},
					
				}
			},

			stroke: {
				show: false,
				lineCap: "round",
				width: 0,
			},
			labels: ['Progress']
		};

		let rbChartPendingRenewalPercent = new ApexCharts(document.querySelector("#pendingRenewalPercent"), optionsPendingRenewalPercent);

		radialBarChartPendingRenewalPercent = rbChartPendingRenewalPercent;

		radialBarChartPendingRenewalPercent.render();

		let optionsDeniedRenewalPercent = {
			chart: {
				height: 271,
				type: "radialBar",
				
			},
			
			series: [],
			colors: ["#55BDA3"],
			responsive: [{
				breakpoint: 1195,
				options: {
					chart: {
						height: 250
					},
				}
			}],
			plotOptions: {
				radialBar: {
					hollow: {
						margin: 15,
						size: "40%"
					},
					
					dataLabels: {
						//showOn: "always",
						show: true,
						name: {
							offsetY: -10,
							show: false,
							color: "#888",
							fontSize: "13px"
						},
						value: {
							offsetY: 10,
							color: "#58585A",
							fontSize: "40px",
							show: true
						}
					},
					
				}
			},

			stroke: {
				show: false,
				lineCap: "round",
				width: 0,
			},
			labels: ['Progress']
		};

		let rbChartDeniedRenewalPercent = new ApexCharts(document.querySelector("#deniedRenewalPercent"), optionsDeniedRenewalPercent);

		radialBarChartDeniedRenewalPercent = rbChartDeniedRenewalPercent;

		radialBarChartDeniedRenewalPercent.render();
	}

	function fetchData(){
		if(lineChart1 != ''){
			lineChart1.destroy();
		}

		let data = {};
		data['start'] = $('#search_fromdate').val();
        data['end'] = $('#search_todate').val();
        data['store'] = menu_store_id;

		$.ajax({
			url: `/store/clinical/dashboard/get-chart`,
			type: "GEt",
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			data: data,
			contentType: "application/json; charset=utf-8",
			dataType: "json",
			success: function(data) {
				if (data && data.data) {
					// Count the number of keys in the arr.data object
					let dataKeys = Object.keys(data.data);
					let count = Object.keys(data.data).length;
					console.log('Number of items in data:', count);
					
					let dataSets = dataKeys.map((key, index) => {
						let item = data.data[key];
						return {
							label: item.name,
							data: item.data, // Assuming item.data is an array of data points
							fill: false,
							borderColor: item.color,
							backgroundColor: item.color,
							lineTension: 0.4,
							pointRadius: 7,
							pointHoverRadius: 10,
						};
					});

					// console.log(datasets);

					let labels = data.labels;
					radialBar();
					lineChart(labels, dataSets);
					radialBarChartDiabetes.updateSeries([(data.avg_diabetes==null)?0:data.avg_diabetes]);
					radialBarChartRasa.updateSeries([(data.avg_rasa==null)?0:data.avg_rasa]);
					radialBarChartCholesterol.updateSeries([(data.avg_cholesterol==null)?0:data.avg_cholesterol]);
					radialBarChartStatin.updateSeries([(data.avg_statin==null)?0:data.avg_statin]);
					radialBarChartOutocmes.updateSeries([(data.mtm_percent==null)?0:data.mtm_percent]);
					$('#avg_message').text(data.mtm_text);
					$('#mtm_average').text(data.mtm_score);
					$('#tips_sum').text(data.tips_sum);
					$('#cmr_sum').text(data.cmr_sum);
					$('#tips_icon').removeClass();
					$('#tips_icon').addClass(data.tips_icon);
					$('#tips_text').text(' '+data.tips_difference+'%');
					$('#cmr_icon').removeClass();
					$('#cmr_icon').addClass(data.cmr_icon);
					$('#cmr_text').text(' '+data.cmr_difference+'%');
					
					barChartTips.updateSeries([{ data: data.tips_data }]);
					barChartCmr.updateSeries([{ data: data.cmr_data }]);

				} else {
					console.error("data.data does not exist");
				}

				
			},
			error: function (msg) {
				handleErrorResponse(msg);
			}
		});
	} 

	function barChart(){
		// chart 2
		var options1 = {
			chart: {
				type: 'bar',
				width: 300,
				height: 85,
				zoom: {
					enabled: false
				},
				sparkline: {
					enabled: true
				}
			},
			dataLabels: {
				enabled: false
			},
			fill: {
				type: 'gradient',
				gradient: {
					shade: 'light',
					gradientToColors: ['#fff'],
					shadeIntensity: 1,
					type: 'vertical',
					opacityFrom: 1,
					opacityTo: 1,
					stops: [0, 100, 100, 100]
				},
			},
			colors: ["#fff"],
			series: [],
			plotOptions: {
				bar: {
					horizontal: false,
					columnWidth: '50%',
					endingShape: 'rounded'
				},
			},
			xaxis: {
				categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
			},
			tooltip: {
				theme: 'dark',
				fixed: {
					enabled: false
				},
				x: {
					show: true
				},
				y: {
					title: {
						formatter: function (seriesName) {
							return ''
						}
					}
				},
				marker: {
					show: false
				}
			}
		}
		let tipsChart = new ApexCharts(document.querySelector("#tipsChart"), options1);
		
		barChartTips = tipsChart;

		barChartTips.render();


		// chart 3
		var options1 = {
			chart: {
				type: 'bar',
				width: 300,
				height: 85,
				sparkline: {
					enabled: true
				}
			},
			dataLabels: {
				enabled: false
			},
			fill: {
				type: 'gradient',
				gradient: {
					shade: 'light',
					gradientToColors: ['#fff'],
					shadeIntensity: 1,
					type: 'vertical',
					opacityFrom: 1,
					opacityTo: 1,
					stops: [0, 100, 100, 100]
				},
			},
			colors: ["#fff"],
			series: [],
			plotOptions: {
				bar: {
					horizontal: false,
					columnWidth: '50%',
					endingShape: 'rounded'
				},
			},
			xaxis: {
				categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
			},
			tooltip: {
				theme: 'dark',
				fixed: {
					enabled: false
				},
				x: {
					show: true
				},
				y: {
					title: {
						formatter: function (seriesName) {
							return ''
						}
					}
				},
				marker: {
					show: false
				}
			}
		}
		let cmrChart = new ApexCharts(document.querySelector("#cmrChart"), options1);
		barChartCmr = cmrChart;
		barChartCmr.render();

		let optionsFfsCount = {
			// series: [{
			// 	name: 'FFS Count',
			// 	data: [440, 505, 414, 671, 427, 613, 901, 257, 160, 99, 10, 190]
			// }],
			series:[],
			chart: {
				foreColor: '#63bed0',
				type: 'bar',
				height: 100,
				toolbar: {
					show: false
				},
				zoom: {
					enabled: false
				},
				dropShadow: {
					enabled: false,
					top: 3,
					left: 14,
					blur: 4,
					opacity: 0.10,
				},
				sparkline: {
					enabled: true
				}
			},
			grid: {
				show: false,
				borderColor: 'rgba(0, 0, 0, 0.15)',
				strokeDashArray: 4,
			},
			plotOptions: {
				bar: {
					horizontal: false,
					columnWidth: '40%',
					endingShape: 'rounded'
				},
			},
			markers: {
				size: 4,
				// colors: ["#007bff"],
				strokeColors: "#fff",
				strokeWidth: 2,
				hover: {
					size: 7,
				}
			},
			dataLabels: {
				enabled: false
			},
			stroke: {
				show: true,
				width: 0,
				curve: 'smooth'
			},
			colors: ["#55BDA1"],
			xaxis: {
				categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
			},
			fill: {
				type: 'gradient',
				gradient: {
					shade: '#55BDA1',
					gradientToColors: ['#55BDA1'],
					shadeIntensity: 1,
					type: 'vertical',
					opacityFrom: 1,
					opacityTo: 1,
					stops: [0, 100, 100, 100]
				},
			},
			tooltip: {
				theme: 'dark',
				fixed: {
					enabled: false
				},
				x: {
					show: true
				},
				y: {
					title: {
						formatter: function (seriesName) {
							return ''
						}
					}
				},
				marker: {
					show: false
				}
			}
		};
		let ffsCountChart = new ApexCharts(document.querySelector("#ffsCountChart"), optionsFfsCount);
		barChartFfsCount = ffsCountChart;
		barChartFfsCount.render();

		let optionsCcoCount = {
			// series: [{
			// 	name: 'FFS Count',
			// 	data: [110, 909, 303, 571, 127, 313, 701, 157, 260, 199, 110, 790]
			// }],
			series:[],
			chart: {
				foreColor: '#63bed0',
				type: 'bar',
				height: 100,
				toolbar: {
					show: false
				},
				zoom: {
					enabled: false
				},
				dropShadow: {
					enabled: false,
					top: 3,
					left: 14,
					blur: 4,
					opacity: 0.10,
				},
				sparkline: {
					enabled: true
				}
			},
			grid: {
				show: false,
				borderColor: 'rgba(0, 0, 0, 0.15)',
				strokeDashArray: 4,
			},
			plotOptions: {
				bar: {
					horizontal: false,
					columnWidth: '40%',
					endingShape: 'rounded'
				},
			},
			markers: {
				size: 4,
				// colors: ["#007bff"],
				strokeColors: "#fff",
				strokeWidth: 2,
				hover: {
					size: 7,
				}
			},
			dataLabels: {
				enabled: false
			},
			stroke: {
				show: true,
				width: 0,
				curve: 'smooth'
			},
			colors: ["#55BDA1"],
			xaxis: {
				categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
			},
			fill: {
				type: 'gradient',
				gradient: {
					shade: '#55BDA1',
					gradientToColors: ['#55BDA1'],
					shadeIntensity: 1,
					type: 'vertical',
					opacityFrom: 1,
					opacityTo: 1,
					stops: [0, 100, 100, 100]
				},
			},
			tooltip: {
				theme: 'dark',
				fixed: {
					enabled: false
				},
				x: {
					show: true
				},
				y: {
					title: {
						formatter: function (seriesName) {
							return ''
						}
					}
				},
				marker: {
					show: false
				}
			}
		};
		let ccoCountChart = new ApexCharts(document.querySelector("#ccoCountChart"), optionsCcoCount);
		barChartCcoCount = ccoCountChart;
		barChartCcoCount.render();

		let optionsPbm1Count = {
			// series: [{
			// 	name: 'FFS Count',
			// 	data: [91, 90, 303, 71, 27, 31, 21, 57, 20, 99, 10, 90]
			// }],
			series:[],
			chart: {
				foreColor: '#63bed0',
				type: 'bar',
				height: 100,
				toolbar: {
					show: false
				},
				zoom: {
					enabled: false
				},
				dropShadow: {
					enabled: false,
					top: 3,
					left: 14,
					blur: 4,
					opacity: 0.10,
				},
				sparkline: {
					enabled: true
				}
			},
			grid: {
				show: false,
				borderColor: 'rgba(0, 0, 0, 0.15)',
				strokeDashArray: 4,
			},
			plotOptions: {
				bar: {
					horizontal: false,
					columnWidth: '40%',
					endingShape: 'rounded'
				},
			},
			markers: {
				size: 4,
				// colors: ["#007bff"],
				strokeColors: "#fff",
				strokeWidth: 2,
				hover: {
					size: 7,
				}
			},
			dataLabels: {
				enabled: false
			},
			stroke: {
				show: true,
				width: 0,
				curve: 'smooth'
			},
			colors: ["#41B8D5"],
			xaxis: {
				categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
			},
			fill: {
				type: 'gradient',
				gradient: {
					shade: '#41B8D5',
					gradientToColors: ['#41B8D5'],
					shadeIntensity: 1,
					type: 'vertical',
					opacityFrom: 1,
					opacityTo: 1,
					stops: [0, 100, 100, 100]
				},
			},
			tooltip: {
				theme: 'dark',
				fixed: {
					enabled: false
				},
				x: {
					show: true
				},
				y: {
					title: {
						formatter: function (seriesName) {
							return ''
						}
					}
				},
				marker: {
					show: false
				}
			}
		};
		let pbm1CountChart = new ApexCharts(document.querySelector("#pbm1CountChart"), optionsPbm1Count);
		barChartpbm1Count = pbm1CountChart;
		barChartpbm1Count.render();

		let optionsPbm2Count = {
			// series: [{
			// 	name: 'FFS Count',
			// 	data: [10, 99, 30, 51, 12, 33, 71, 57, 20, 199, 11, 90]
			// }],
			series:[],
			chart: {
				foreColor: '#63bed0',
				type: 'bar',
				height: 100,
				toolbar: {
					show: false
				},
				zoom: {
					enabled: false
				},
				dropShadow: {
					enabled: false,
					top: 3,
					left: 14,
					blur: 4,
					opacity: 0.10,
				},
				sparkline: {
					enabled: true
				}
			},
			grid: {
				show: false,
				borderColor: 'rgba(0, 0, 0, 0.15)',
				strokeDashArray: 4,
			},
			plotOptions: {
				bar: {
					horizontal: false,
					columnWidth: '40%',
					endingShape: 'rounded'
				},
			},
			markers: {
				size: 4,
				// colors: ["#007bff"],
				strokeColors: "#fff",
				strokeWidth: 2,
				hover: {
					size: 7,
				}
			},
			dataLabels: {
				enabled: false
			},
			stroke: {
				show: true,
				width: 0,
				curve: 'smooth'
			},
			colors: ["#41B8D5"],
			xaxis: {
				categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
			},
			fill: {
				type: 'gradient',
				gradient: {
					shade: '#41B8D5',
					gradientToColors: ['#41B8D5'],
					shadeIntensity: 1,
					type: 'vertical',
					opacityFrom: 1,
					opacityTo: 1,
					stops: [0, 100, 100, 100]
				},
			},
			tooltip: {
				theme: 'dark',
				fixed: {
					enabled: false
				},
				x: {
					show: true
				},
				y: {
					title: {
						formatter: function (seriesName) {
							return ''
						}
					}
				},
				marker: {
					show: false
				}
			}
		};
		let pbm2CountChart = new ApexCharts(document.querySelector("#pbm2CountChart"), optionsPbm2Count);
		barChartpbm2Count = pbm2CountChart;
		barChartpbm2Count.render();

		let optionsFfspCount = {
			// series: [{
			// 	name: 'FFS Count',
			// 	data: [440, 505, 414, 671, 427, 613, 901, 257, 160, 99, 10, 190]
			// }],
			series:[],
			chart: {
				foreColor: '#63bed0',
				type: 'bar',
				height: 100,
				toolbar: {
					show: false
				},
				zoom: {
					enabled: false
				},
				dropShadow: {
					enabled: false,
					top: 3,
					left: 14,
					blur: 4,
					opacity: 0.10,
				},
				sparkline: {
					enabled: true
				}
			},
			grid: {
				show: false,
				borderColor: 'rgba(0, 0, 0, 0.15)',
				strokeDashArray: 4,
			},
			plotOptions: {
				bar: {
					horizontal: false,
					columnWidth: '40%',
					endingShape: 'rounded'
				},
			},
			markers: {
				size: 4,
				// colors: ["#007bff"],
				strokeColors: "#fff",
				strokeWidth: 2,
				hover: {
					size: 7,
				}
			},
			dataLabels: {
				enabled: false
			},
			stroke: {
				show: true,
				width: 0,
				curve: 'smooth'
			},
			colors: ["#B938B0"],
			xaxis: {
				categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
			},
			fill: {
				type: 'gradient',
				gradient: {
					shade: '#B938B0',
					gradientToColors: ['#B938B0'],
					shadeIntensity: 1,
					type: 'vertical',
					opacityFrom: 1,
					opacityTo: 1,
					stops: [0, 100, 100, 100]
				},
			},
			tooltip: {
				theme: 'dark',
				fixed: {
					enabled: false
				},
				x: {
					show: true
				},
				y: {
					title: {
						formatter: function (seriesName) {
							return ''
						}
					}
				},
				marker: {
					show: false
				}
			}
		};
		let ffspCountChart = new ApexCharts(document.querySelector("#ffspCountChart"), optionsFfspCount);
		barChartFfspCount = ffspCountChart;
		barChartFfspCount.render();

		let optionsCcopCount = {
			// series: [{
			// 	name: 'FFS Count',
			// 	data: [110, 909, 303, 571, 127, 313, 701, 157, 260, 199, 110, 790]
			// }],
			series:[],
			chart: {
				foreColor: '#63bed0',
				type: 'bar',
				height: 100,
				toolbar: {
					show: false
				},
				zoom: {
					enabled: false
				},
				dropShadow: {
					enabled: false,
					top: 3,
					left: 14,
					blur: 4,
					opacity: 0.10,
				},
				sparkline: {
					enabled: true
				}
			},
			grid: {
				show: false,
				borderColor: 'rgba(0, 0, 0, 0.15)',
				strokeDashArray: 4,
			},
			plotOptions: {
				bar: {
					horizontal: false,
					columnWidth: '40%',
					endingShape: 'rounded'
				},
			},
			markers: {
				size: 4,
				// colors: ["#007bff"],
				strokeColors: "#fff",
				strokeWidth: 2,
				hover: {
					size: 7,
				}
			},
			dataLabels: {
				enabled: false
			},
			stroke: {
				show: true,
				width: 0,
				curve: 'smooth'
			},
			colors: ["#B938B0"],
			xaxis: {
				categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
			},
			fill: {
				type: 'gradient',
				gradient: {
					shade: '#B938B0',
					gradientToColors: ['#B938B0'],
					shadeIntensity: 1,
					type: 'vertical',
					opacityFrom: 1,
					opacityTo: 1,
					stops: [0, 100, 100, 100]
				},
			},
			tooltip: {
				theme: 'dark',
				fixed: {
					enabled: false
				},
				x: {
					show: true
				},
				y: {
					title: {
						formatter: function (seriesName) {
							return ''
						}
					}
				},
				marker: {
					show: false
				}
			}
		};
		let ccopCountChart = new ApexCharts(document.querySelector("#ccopCountChart"), optionsCcopCount);
		barChartCcopCount = ccopCountChart;
		barChartCcopCount.render();

		let optionsPbm1pCount = {
			// series: [{
			// 	name: 'FFS Count',
			// 	data: [91, 90, 303, 71, 27, 31, 21, 57, 20, 99, 10, 90]
			// }],
			series:[],
			chart: {
				foreColor: '#63bed0',
				type: 'bar',
				height: 100,
				toolbar: {
					show: false
				},
				zoom: {
					enabled: false
				},
				dropShadow: {
					enabled: false,
					top: 3,
					left: 14,
					blur: 4,
					opacity: 0.10,
				},
				sparkline: {
					enabled: true
				}
			},
			grid: {
				show: false,
				borderColor: 'rgba(0, 0, 0, 0.15)',
				strokeDashArray: 4,
			},
			plotOptions: {
				bar: {
					horizontal: false,
					columnWidth: '40%',
					endingShape: 'rounded'
				},
			},
			markers: {
				size: 4,
				// colors: ["#007bff"],
				strokeColors: "#fff",
				strokeWidth: 2,
				hover: {
					size: 7,
				}
			},
			dataLabels: {
				enabled: false
			},
			stroke: {
				show: true,
				width: 0,
				curve: 'smooth'
			},
			colors: ["#8896DD"],
			xaxis: {
				categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
			},
			fill: {
				type: 'gradient',
				gradient: {
					shade: '#8896DD',
					gradientToColors: ['#8896DD'],
					shadeIntensity: 1,
					type: 'vertical',
					opacityFrom: 1,
					opacityTo: 1,
					stops: [0, 100, 100, 100]
				},
			},
			tooltip: {
				theme: 'dark',
				fixed: {
					enabled: false
				},
				x: {
					show: true
				},
				y: {
					title: {
						formatter: function (seriesName) {
							return ''
						}
					}
				},
				marker: {
					show: false
				}
			}
		};
		let pbm1pCountChart = new ApexCharts(document.querySelector("#pbm1pCountChart"), optionsPbm1pCount);
		barChartpbm1pCount = pbm1pCountChart;
		barChartpbm1pCount.render();

		let optionsPbm2pCount = {
			// series: [{
			// 	name: 'FFS Count',
			// 	data: [10, 99, 30, 51, 12, 33, 71, 57, 20, 199, 11, 90]
			// }],
			series:[],
			chart: {
				foreColor: '#63bed0',
				type: 'bar',
				height: 100,
				toolbar: {
					show: false
				},
				zoom: {
					enabled: false
				},
				dropShadow: {
					enabled: false,
					top: 3,
					left: 14,
					blur: 4,
					opacity: 0.10,
				},
				sparkline: {
					enabled: true
				}
			},
			grid: {
				show: false,
				borderColor: 'rgba(0, 0, 0, 0.15)',
				strokeDashArray: 4,
			},
			plotOptions: {
				bar: {
					horizontal: false,
					columnWidth: '40%',
					endingShape: 'rounded'
				},
			},
			markers: {
				size: 4,
				// colors: ["#007bff"],
				strokeColors: "#fff",
				strokeWidth: 2,
				hover: {
					size: 7,
				}
			},
			dataLabels: {
				enabled: false
			},
			stroke: {
				show: true,
				width: 0,
				curve: 'smooth'
			},
			colors: ["#8896DD"],
			xaxis: {
				categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
			},
			fill: {
				type: 'gradient',
				gradient: {
					shade: '#8896DD',
					gradientToColors: ['#8896DD'],
					shadeIntensity: 1,
					type: 'vertical',
					opacityFrom: 1,
					opacityTo: 1,
					stops: [0, 100, 100, 100]
				},
			},
			tooltip: {
				theme: 'dark',
				fixed: {
					enabled: false
				},
				x: {
					show: true
				},
				y: {
					title: {
						formatter: function (seriesName) {
							return ''
						}
					}
				},
				marker: {
					show: false
				}
			}
		};
		let pbm2pCountChart = new ApexCharts(document.querySelector("#pbm2pCountChart"), optionsPbm2pCount);
		barChartpbm2pCount = pbm2pCountChart;
		barChartpbm2pCount.render();

		let optionsToa = {
			// series: [{
			// 	name: '$',
			// 	data: [440, 505, 414, 671, 427, 613, 901, 257, 160, 200, 110, 300]
			// }],
			series:[],
			chart: {
				foreColor: '#9a9797',
				type: 'bar',
				height: 260,
				toolbar: {
					show: false
				},
				zoom: {
					enabled: false
				},
				dropShadow: {
					enabled: false,
					top: 3,
					left: 14,
					blur: 4,
					opacity: 0.10,
				},
				sparkline: {
					enabled: false
				}
			},
			grid: {
				show: true,
				borderColor: 'rgba(0, 0, 0, 0.15)',
				strokeDashArray: 0,
			},
			plotOptions: {
				bar: {
					horizontal: false,
					columnWidth: '40%',
					endingShape: 'rounded'
				},
			},
			markers: {
				size: 4,
				// colors: ["#007bff"],
				strokeColors: "#fff",
				strokeWidth: 2,
				hover: {
					size: 7,
				}
			},
			dataLabels: {
				enabled: false
			},
			stroke: {
				show: true,
				width: 0,
				curve: 'smooth'
			},
			colors: ["#363A86"],
			xaxis: {
				categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
			},
			fill: {
				opacity: 1
			}
		};
		let chartTotalOpportunitiesAmount = new ApexCharts(document.querySelector("#total_opportunities_amount"), optionsToa);
		barChartToa = chartTotalOpportunitiesAmount;
		barChartToa.render();

		let optionsTotalBilledAmount = {
			// series: [{
			// 	name: '$',
			// 	data: [440, 505, 414, 671, 427, 613, 901, 257, 160, 200, 110, 300]
			// }],
			series:[],
			chart: {
				foreColor: '#9a9797',
				type: 'bar',
				height: 260,
				toolbar: {
					show: false
				},
				zoom: {
					enabled: false
				},
				dropShadow: {
					enabled: false,
					top: 3,
					left: 14,
					blur: 4,
					opacity: 0.10,
				},
				sparkline: {
					enabled: false
				}
			},
			grid: {
				show: true,
				borderColor: 'rgba(0, 0, 0, 0.15)',
				strokeDashArray: 0,
			},
			plotOptions: {
				bar: {
					horizontal: false,
					columnWidth: '40%',
					endingShape: 'rounded'
				},
			},
			markers: {
				size: 4,
				// colors: ["#007bff"],
				strokeColors: "#fff",
				strokeWidth: 2,
				hover: {
					size: 7,
				}
			},
			dataLabels: {
				enabled: false
			},
			stroke: {
				show: true,
				width: 0,
				curve: 'smooth'
			},
			colors: ["#363A86"],
			xaxis: {
				categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
			},
			fill: {
				opacity: 1
			}
		};
		let chartTotalBilledAmount = new ApexCharts(document.querySelector("#total_billed_amount"), optionsTotalBilledAmount);
		barChartTotalBilledAmount = chartTotalBilledAmount;
		barChartTotalBilledAmount.render();

		let optionsTotalRrcAndTotalRc = {
			// series: [{
			// 	name: 'Total Refill Renewal Count',
			// 	data: [66, 76, 85, 101, 65, 87, 105, 91, 86, 24, 45, 87]

			// }, {
			// 	name: 'Total Renewed Count',
			// 	data: [55, 44, 55, 57, 56, 61, 58, 63, 60, 31, 11, 99]
			// }],
			series:[],
			chart: {
				foreColor: '#9a9797',
				type: 'bar',
				height: 260,
				toolbar: {
					show: false
				},
				zoom: {
					enabled: false
				},
				dropShadow: {
					enabled: false,
					top: 3,
					left: 14,
					blur: 4,
					opacity: 0.10,
				},
				sparkline: {
					enabled: false
				}
			},
			grid: {
				show: true,
				borderColor: 'rgba(0, 0, 0, 0.15)',
				strokeDashArray: 0,
			},
			plotOptions: {
				bar: {
					horizontal: false,
					columnWidth: '40%',
					endingShape: 'rounded'
				},
			},
			markers: {
				size: 4,
				// colors: ["#007bff"],
				strokeColors: "#fff",
				strokeWidth: 2,
				hover: {
					size: 7,
				}
			},
			dataLabels: {
				enabled: false
			},
			stroke: {
				show: true,
				width: 0,
				curve: 'smooth'
			},
			colors: ["#FAC8CD", "#FD86B3"],
			legend: {
				show: true,
				position: 'top',
				horizontalAlign: 'center',
				offsetX: -20
			},
			xaxis: {
				categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
			},
			fill: {
				opacity: 1
			}
		};
		let chartTotalRrcAndTotalRc = new ApexCharts(document.querySelector("#totalRrcAndTotalRc"), optionsTotalRrcAndTotalRc);
		barChartTotalRrcAndTotalRc = chartTotalRrcAndTotalRc;
		barChartTotalRrcAndTotalRc.render();

		let optionsPendingRenewal = {
			// series: [{
			// 	name: 'Total Refill Renewal Count',
			// 	data: [66, 76, 85, 101, 65, 87, 105, 91, 86, 24, 45, 87]

			// }, {
			// 	name: 'Total Renewed Count',
			// 	data: [55, 44, 55, 57, 56, 61, 58, 63, 60, 31, 11, 99]
			// }],
			series:[],
			chart: {
				foreColor: '#9a9797',
				type: 'bar',
				height: 260,
				toolbar: {
					show: false
				},
				zoom: {
					enabled: false
				},
				dropShadow: {
					enabled: false,
					top: 3,
					left: 14,
					blur: 4,
					opacity: 0.10,
				},
				sparkline: {
					enabled: false
				}
			},
			grid: {
				show: true,
				borderColor: 'rgba(0, 0, 0, 0.15)',
				strokeDashArray: 0,
			},
			plotOptions: {
				bar: {
					horizontal: false,
					columnWidth: '40%',
					endingShape: 'rounded'
				},
			},
			markers: {
				size: 4,
				// colors: ["#007bff"],
				strokeColors: "#fff",
				strokeWidth: 2,
				hover: {
					size: 7,
				}
			},
			dataLabels: {
				enabled: false
			},
			stroke: {
				show: true,
				width: 0,
				curve: 'smooth'
			},
			colors: ["#29ABE2", "#B4D4FD"],
			legend: {
				show: true,
				position: 'top',
				horizontalAlign: 'center',
				offsetX: -20
			},
			xaxis: {
				categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
			},
			fill: {
				opacity: 1
			}
		};
		let chartPendingRenewal = new ApexCharts(document.querySelector("#pendingRenewal"), optionsPendingRenewal);
		barChartPendingRenewal = chartPendingRenewal;
		barChartPendingRenewal.render();

		let optionsDeniedRenewal = {
			// series: [{
			// 	name: 'Total Refill Renewal Count',
			// 	data: [66, 76, 85, 101, 65, 87, 105, 91, 86, 24, 45, 87]

			// }, {
			// 	name: 'Total Renewed Count',
			// 	data: [55, 44, 55, 57, 56, 61, 58, 63, 60, 31, 11, 99]
			// }],
			series:[],
			chart: {
				foreColor: '#9a9797',
				type: 'bar',
				height: 260,
				toolbar: {
					show: false
				},
				zoom: {
					enabled: false
				},
				dropShadow: {
					enabled: false,
					top: 3,
					left: 14,
					blur: 4,
					opacity: 0.10,
				},
				sparkline: {
					enabled: false
				}
			},
			grid: {
				show: true,
				borderColor: 'rgba(0, 0, 0, 0.15)',
				strokeDashArray: 0,
			},
			plotOptions: {
				bar: {
					horizontal: false,
					columnWidth: '40%',
					endingShape: 'rounded'
				},
			},
			markers: {
				size: 4,
				// colors: ["#007bff"],
				strokeColors: "#fff",
				strokeWidth: 2,
				hover: {
					size: 7,
				}
			},
			dataLabels: {
				enabled: false
			},
			stroke: {
				show: true,
				width: 0,
				curve: 'smooth'
			},
			colors: ["#ACDDAA", "#55BDA3"],
			legend: {
				show: true,
				position: 'top',
				horizontalAlign: 'center',
				offsetX: -20
			},
			xaxis: {
				categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
			},
			fill: {
				opacity: 1
			}
		};
		let chartDeniedRenewal = new ApexCharts(document.querySelector("#deniedRenewal"), optionsDeniedRenewal);
		barChartDeniedRenewal = chartDeniedRenewal;
		barChartDeniedRenewal.render();
	}

	function areaChart(){
		let optionsTotalOpportunities = {
			// series: [{
			// 	name: 'Patients',
			// 	data: [440, 505, 414, 671, 427, 613, 901, 257, 160]
			// }],
			series:[],
			chart: {
				type: 'area',
				height: 417,
				toolbar: {
					show: false
				},
				zoom: {
					enabled: false
				},
				dropShadow: {
					enabled: false,
					top: 3,
					left: 14,
					blur: 4,
					opacity: 0.10,
				},
				sparkline: {
					enabled: true
				}
			},
			markers: {
				size: 4,
				// colors: ["#007bff"],
				strokeColors: "#fff",
				strokeWidth: 2,
				hover: {
					size: 7,
				}
			},
			dataLabels: {
				enabled: false
			},
			stroke: {
				show: true,
				width: 3,
				curve: 'smooth'
			},
			colors: ["#438F9D"],
			xaxis: {
				categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
			},
			tooltip: {
				theme: 'dark',
				x: {
				show: false
				},
			},
			fill: {
				opacity: 0.2
			}
		};
		let toChart = new ApexCharts(document.querySelector("#total_opportunities"), optionsTotalOpportunities);
		areaChartTo = toChart;
		
		areaChartTo.render();

		let optionsTotalPOpportunities = {
			// series: [{
			// 	name: 'Patients',
			// 	data: [440, 505, 414, 671, 427, 613, 901, 257, 160]
			// }],
			series:[],
			chart: {
				type: 'area',
				height: 417,
				toolbar: {
					show: false
				},
				zoom: {
					enabled: false
				},
				dropShadow: {
					enabled: false,
					top: 3,
					left: 14,
					blur: 4,
					opacity: 0.10,
				},
				sparkline: {
					enabled: true
				}
			},
			markers: {
				size: 4,
				// colors: ["#007bff"],
				strokeColors: "#fff",
				strokeWidth: 2,
				hover: {
					size: 7,
				}
			},
			dataLabels: {
				enabled: false
			},
			stroke: {
				show: true,
				width: 3,
				curve: 'smooth'
			},
			colors: ["#B938B0"],
			xaxis: {
				categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
			},
			tooltip: {
				theme: 'dark',
				x: {
				show: false
				},
			},
			fill: {
				opacity: 0.2
			}
		};
		let ptoChart = new ApexCharts(document.querySelector("#total_Popportunities"), optionsTotalPOpportunities);
		areaChartPto = ptoChart;
		
		areaChartPto.render();
	}

	function pieChart(){
		
		Highcharts.chart('denialReasons', {
			chart: {
				height: 350,
				plotBackgroundColor: null,
				plotBorderWidth: null,
				plotShadow: false,
				type: 'pie',
				styledMode: true
			},
			credits: {
				enabled: false
			},
			title: {
				text: ''
			},
			subtitle: {
				text: ''
			},
			tooltip: {
				pointFormat: '<b>{point.percentage:.1f}%</b>'
			},
			accessibility: {
				point: {
					valueSuffix: '%'
				}
			},
			plotOptions: {
				pie: {
					allowPointSelect: true,
					cursor: 'pointer',
					innerSize: 90,
					dataLabels: {
						enabled: true,
						format: '<b>{point.name}</b>: {point.percentage:.1f} %'
					},
					showInLegend: false
				}
			},
			navigation: {
				buttonOptions: {
					enabled: false
				}
			},
			
			// colors: ['#E3CBF8', '#FCAE7C', '#FEE69A', '#B3F5BC', '#D6F6FF'],
			
			// series: [{
			// 	name: '',
			// 	colorByPoint: true,
			// 	data: [{
			// 			name: 'Medicine',
			// 			y: 56,
			// 			color: '#E3CBF8'
			// 		}, {
			// 			name: 'Service not covered',
			// 			y: 30,
			// 			color: '#FCAE7C'
			// 		}, {
			// 			name: 'Prior authorizations',
			// 			y: 14,
			// 			color: '#EE69A'
			// 		}, {
			// 			name: 'Incorrect patient information',
			// 			y: 56,
			// 			color: '#B3F5BC'
			// 		}, {
			// 			name: 'Duplicate billing',
			// 			y: 56,
			// 			color: '#D6F6FF'
			// 		},
			// 	]
			// }],
			series:[],
			responsive: {
				rules: [{
					condition: {
						maxWidth: 500
					},
					chartOptions: {
						plotOptions: {
							pie: {
								innerSize: 140,
								dataLabels: {
									enabled: false
								}
							}
						},
					}
				}]
			}
		});
	}

	function generateRandomColor() {
		var letters = '0123456789ABCDEF';
		var color = '#';
		for (var i = 0; i < 6; i++) {
			color += letters[Math.floor(Math.random() * 16)];
		}
		return color;
	}

</script>


@stop
