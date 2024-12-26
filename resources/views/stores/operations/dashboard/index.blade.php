@extends('layouts.master')
@section('content')
<!--start page wrapper -->
		<div class="page-wrapper">
			<div class="page-content">
				<!-- PAGE-HEADER -->
				@include('layouts/pageContentHeader/store')

				<div class="card">
                    <div class="card-body">
                        <ul class="nav nav-tabs nav-primary nav-custom-operations" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a class="nav-link nav-link-custom-operations active" data-bs-toggle="tab" href="#operationHome" role="tab" aria-selected="true" onclick="clickTab('operations')">
                                    <div class="d-flex align-items-center">
                                        <div class="tab-icon"><i class='fa fa-laptop-medical font-18 me-1'></i>
                                        </div>
                                        <div class="tab-title">Operations</div>
                                    </div>
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link nav-link-custom-operations" data-bs-toggle="tab" href="#operationLossAndExpensesHome" role="tab" aria-selected="false"  onclick="clickTab('operation-loss-and-expenses')">
                                    <div class="d-flex align-items-center">
                                        <div class="tab-icon"><i class='fa fa-chart-gantt font-18 me-1'></i>
                                        </div>
                                        <div class="tab-title">Operation Loss & Expenses</div>
                                    </div>
                                </a>
                            </li>
                        </ul>
                        <div class="py-3 tab-content">
                            <div class="tab-pane fade show active" id="operationHome" role="tabpanel">
                                
                                @include('stores/operations/dashboard/operations/main')

                            </div>
                            <div class="tab-pane fade" id="operationLossAndExpensesHome" role="tabpanel">
                                
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tabs-under-cards" id="operationsTab">
                    @include('stores/operations/dashboard/operations/revenueAnalysis')
                    @include('stores/operations/dashboard/operations/patientGrowthAndShrinkage')
                </div>

			</div>
			@include('sweetalert2/script')
		</div>
		<!--end page wrapper -->
@stop
@section('pages_specific_scripts')

<script>
    let menu_store_id = {{request()->id}};
    let months_list_fullname = [];
    let months_list_shortname = [];

    $(document).ready(function() {
        menu_store_id = {{request()->id}};
        months_list_fullname = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        months_list_shortname = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        // $('#date').datepicker({
        //     format: "yyyy-mm-dd",
        //     todayHighlight: true,
        //     uiLibrary: 'bootstrap5',
        //     modal: true,
        //     icons: {
        //         rightIcon: '<i class="material-icons"></i>'
        //     },
        //     showRightIcon: false,
        //     autoclose: true
        // });

        loadDataInsightsData();   
        loadClinicalData();     
        
    });

    function clickTab(title)
    {
        $('.tabs-under-cards').css('display', 'none');
        if(title == 'operations') {
            $('#operationsTab').css('display', 'block');
        }
        if(title == 'operation-loss-and-expenses') {

        }
    }

    function loadDataInsightsData()
    {
        let monthlyGrossSalesChart = [];
        let monthlyCollectedPaymentsChart = [];
        let monthlyAccountReceivablesChart = [];
        let monthlyRevenuePerEmployeeChart = [];
        let monthlyGrossProfitRevenuePerScriptChart = [];
        let monthlyTurnaroundTimeHoursChart = [];

        let monthyRxCountChart = [];

        let data = {
            date_from: null,
            date_to: null,
            pharmacy_store_id: menu_store_id
        };
        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "POST",
            url: `/admin/executive-dashboard/data-insights/charts`,
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            data: JSON.stringify(data),
            success: function(data) {
                console.log('--------evergreen',data);
                let res = data.data;
                const filter = res.filter;
                const monthly = res.monthly;
                
                $.each(monthly, function(index, item) {

                    const year_number = item.year_number;
                    const month_number = index;
                    const monthlyGrossSales = item.monthlyGrossSales;
                    const monthlyCollectedPayments = item.monthlyCollectedPayments;
                    const monthlyAccountReceivables = item.monthlyAccountReceivables;
                    const monthlyRevenuePerEmployee = item.monthlyRevenuePerEmployee;
                    const monthlyGrossProfit = item.monthlyGrossProfit;
                    const monthlyGrossProfitRevenuePerScript = item.monthlyGrossProfitRevenuePerScript;
                    const monthlyTurnaroundTimeHours = item.monthlyTurnaroundTimeHours;

                    const monthlyRxCount = item.monthlyRxCount;

                    let avgTurnaroundTimeDay = 0;
                    let avgTurnaroundTimeDayText = 0;
                    if(monthlyTurnaroundTimeHours.custom.days) {
                        avgTurnaroundTimeDay = monthlyTurnaroundTimeHours.custom.days;
                        if(avgTurnaroundTimeDay > 1) {
                            avgTurnaroundTimeDayText = avgTurnaroundTimeDay + ' Days';
                        } else {
                            avgTurnaroundTimeDayText = avgTurnaroundTimeDay + ' Day';
                        }
                    }
                    
                    if(filter.current_month == month_number) {
                        // Gross Sales Widget
                        $('#actualBilledCard h3').html(`$ ${monthlyGrossSales.raw_with_comma}`);
                        if(monthlyGrossSales.percentage_mark == '') {
                            $('#actualBilledCardSubtext').html(`
                                <span class="text-secondary"><i class="lni lni-arrow-left-right"></i> -- </span> vs last month
                            `);
                        } else {
                            $('#actualBilledCardSubtext').html(`
                                <span class="text-${monthlyGrossSales.percentage_class}">
                                    <i class="lni lni-${monthlyGrossSales.percentage_mark}"></i> ${monthlyGrossSales.percentage}% 
                                </span> vs last month
                            `);
                        }
                        // Collected Payments Widget
                        $('#actualCollectedCard h3').html(`$ ${monthlyCollectedPayments.raw_with_comma}`);
                        if(monthlyCollectedPayments.percentage_mark == '') {
                            $('#actualCollectedCardSubtext').html(`
                                <span class="text-secondary"><i class="lni lni-arrow-left-right"></i> -- </span> vs last month
                            `);
                        } else {
                            $('#actualCollectedCardSubtext').html(`
                                <span class="text-${monthlyCollectedPayments.percentage_class}">
                                    <i class="lni lni-${monthlyCollectedPayments.percentage_mark}"></i> ${monthlyCollectedPayments.percentage}% 
                                </span> vs last month
                            `);
                        }
                        // Revenue per Employee Widget
                        $('#revenuePerEmployeeCard h3').html(`$ ${monthlyRevenuePerEmployee.raw_with_comma}`);
                        if(monthlyRevenuePerEmployee.percentage_mark == '') {
                            $('#revenuePerEmployeeCardSubtext').html(`
                                <span class="text-secondary"><i class="lni lni-arrow-left-right"></i> -- </span> vs last month
                            `);
                        } else {
                            $('#revenuePerEmployeeCardSubtext').html(`
                                <span class="text-${monthlyRevenuePerEmployee.percentage_class}">
                                    <i class="lni lni-${monthlyRevenuePerEmployee.percentage_mark}"></i> ${monthlyRevenuePerEmployee.percentage}% 
                                </span> vs last month
                            `);
                        }

                        $('#trpRxCountCard h4').html(monthlyRxCount.raw);
                        if(monthlyRxCount.percentage_mark == '') {
                            $('#trpRxCountCardSubtext').html(`
                                <span class="text-secondary"><i class="lni lni-arrow-left-right"></i> -- </span> vs last month
                            `);
                        } else {
                            $('#trpRxCountCardSubtext').html(`
                                <span class="text-${monthlyRxCount.percentage_class}">
                                    <i class="lni lni-${monthlyRxCount.percentage_mark}"></i> ${monthlyRxCount.percentage}% 
                                </span> vs last month
                            `);
                        }

                        $('#trpRxRevenuePerScriptCard h4').html(`$ ${monthlyGrossProfitRevenuePerScript.formatted}`);
                        if(monthlyGrossProfitRevenuePerScript.percentage_mark == '') {
                            $('#trpRxRevenuePerScriptCardSubtext').html(`
                                <span class="text-secondary"><i class="lni lni-arrow-left-right"></i> -- </span> vs last month
                            `);
                        } else {
                            $('#trpRxRevenuePerScriptCardSubtext').html(`
                                <span class="text-${monthlyGrossProfitRevenuePerScript.percentage_class}">
                                    <i class="lni lni-${monthlyGrossProfitRevenuePerScript.percentage_mark}"></i> ${monthlyGrossProfitRevenuePerScript.percentage}% 
                                </span> vs last month
                            `);
                        }
                        $('#avgTurnaroundTimeCard h4').html(`${avgTurnaroundTimeDayText}`);
                        if(monthlyTurnaroundTimeHours.percentage_mark == '') {
                            $('#avgTurnaroundTimeCardSubtext').html(`
                                <span class="text-secondary"><i class="lni lni-arrow-left-right"></i> -- </span> vs last month
                            `);
                        } else {
                            $('#avgTurnaroundTimeCardSubtext').html(`
                                <span class="text-${monthlyTurnaroundTimeHours.percentage_class}">
                                    <i class="lni lni-${monthlyTurnaroundTimeHours.percentage_mark}"></i> ${monthlyTurnaroundTimeHours.percentage}% 
                                </span> vs last month
                            `);
                        }
                    }

                    monthlyGrossSalesChart.push(monthlyGrossSales.raw);
                    monthlyCollectedPaymentsChart.push(monthlyCollectedPayments.raw);
                    monthlyAccountReceivablesChart.push(monthlyAccountReceivables.raw);
                    monthlyRevenuePerEmployeeChart.push(monthlyRevenuePerEmployee.raw);
                    monthlyGrossProfitRevenuePerScriptChart.push(monthlyGrossProfitRevenuePerScript.raw);
                    monthlyTurnaroundTimeHoursChart.push(avgTurnaroundTimeDay);

                    monthyRxCountChart.push(monthlyRxCount);
                });


                generateBarChartPerMonth(
                    monthlyGrossSalesChart,
                    'TRP Actual Billed',
                    "#actualBilledChart",
                    {
                        height: 100,
                        color: '#ff8eaf'
                    },
                    months_list_fullname
                );

                generateBarChartPerMonth(
                    monthlyCollectedPaymentsChart,
                    'TRP Actual Collected',
                    "#actualCollectedChart",
                    {
                        height: 100,
                        color: '#21caf1'
                    },
                    months_list_fullname
                );

                generateBarChartPerMonth(
                    monthlyRevenuePerEmployeeChart,
                    'TRP Revenue per Employee',
                    "#revenuePerEmployeeChart",
                    {
                        height: 100,
                        color: '#5ece43'
                    },
                    months_list_fullname
                );
            },
            error: function(xhr, status, error) {
                handleErrorResponse(error);
                console.error(error);
            }
        });
    }

    function loadClinicalData()
    {
        let monthlyPatientGrowthAndShrinkageChart = [];

        let data = {
            date_from: null,
            date_to: null,
        };
        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "POST",
            url: `/admin/executive-dashboard/clinical/charts`,
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            data: JSON.stringify(data),
            success: function(data) {
                console.log('--------evergreen',data);
                let res = data.data;
                const filter = res.filter;
                const monthly = res.monthly;
                
                $.each(monthly, function(index, item) {

                    const year_number = item.year_number;
                    const month_number = index;
                    const monthlyPatientGrowthAndShrinkage = item.monthlyPatientGrowthAndShrinkage;
                    
                    if(filter.current_month == month_number) {
                        $('#newPatientsCard h4').html(`${monthlyPatientGrowthAndShrinkage.raw}`);
                        if(monthlyPatientGrowthAndShrinkage.percentage_mark == '') {
                            $('#newPatientsCardSubtext').html(`
                                <span class="text-secondary"><i class="lni lni-arrow-left-right"></i> -- </span> vs last month
                            `);
                        } else {
                            $('#newPatientsCardSubtext').html(`
                                <span class="text-${monthlyPatientGrowthAndShrinkage.percentage_class}">
                                    <i class="lni lni-${monthlyPatientGrowthAndShrinkage.percentage_mark}"></i> ${monthlyPatientGrowthAndShrinkage.percentage}% 
                                </span> vs last month
                            `);
                        }
                    }

                    monthlyPatientGrowthAndShrinkageChart.push(monthlyPatientGrowthAndShrinkage.percentage);
                });

                // generateStraightLinePerMonthChart(
                //     monthlyPatientGrowthAndShrinkageChart,
                //     'Growth and Shrinkage $ - Billed Ratio', 
                //     '#operations-monthly-growth-and-shrinkage-chart', 
                //     {
                //         'color': "#27CC3A",
                //         'height': 170,
                //         'marker_size': 3,
                //         'stroke_dash': 0,
                //         'show_grid': true
                //     });
            
                generateCurveLinePerMonthChart(
                    monthlyPatientGrowthAndShrinkageChart, 
                    'New Patients', 
                    '#newPatientsCardChart',
                    {
                        'color': "#27CC3A",
                        'height': 170,
                        'marker_size': 0,
                        'stroke_dash': 0,
                        'show_grid': false
                    },
                    months_list_fullname
                );
            },
            error: function(xhr, status, error) {
                handleErrorResponse(error);
                console.error(error);
            }
        });
    }

    function generateBarChartPerMonth(__dataArray = [], __title = '', __selector = '', __styleArray = [], __categories = [])
    {
        var options = {
            series: [{
                name: __title,
                data: __dataArray
            }],
            chart: {
                type: 'bar',
                height: __styleArray['height'],
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
                    opacity: 0.12,
                    color: __styleArray['color'],
                },
                sparkline: {
                    enabled: true
                }
            },
            markers: {
                size: 0,
                colors: [__styleArray['color']],
                strokeColors: "#fff",
                strokeWidth: 2,
                hover: {
                    size: 7,
                }
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '40%',
                    endingShape: 'rounded'
                },
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                show: true,
                width: 2.5,
                curve: 'smooth'
            },
            colors: [__styleArray['color']],
            xaxis: {
                categories: __categories,
            },
            yaxis: {
                labels: {
                    formatter: function (value) {
                        return `$ ${value.toLocaleString()}`;
                    }
                },
            },
            fill: {
                opacity: 1
            },
        };
        var chart = new ApexCharts(document.querySelector(__selector), options);
        chart.render();
    }

    function generateCurveLinePerMonthChart(__data_array = [], __title = '', __selector = '', __style_array = [], __categories = [])
    {
        var options = {
            series: [{
                name: __title,
                data: __data_array
            }],
            chart: {
                type: 'area',
                height: __style_array['height'],
                toolbar: {
                    show: false
                },
                zoom: {
                    enabled: false
                },
                dropShadow: {
                    enabled: true,
                    top: 3,
                    left: 14,
                    blur: 4,
                    opacity: 0.12,
                    color: __style_array['color'],
                },
                sparkline: {
                    enabled: true
                }
            },
            markers: {
                size: __style_array['marker_size'],
                colors: [__style_array['color']],
                strokeColors: "#fff",
                strokeWidth: 3,
                hover: {
                    size: __style_array['marker_size']+2,
                }
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                show: true,
                width: 6,
                curve: 'smooth'
            },
            colors: [__style_array['color']],
            grid: {
                show: __style_array['show_grid'],
                color: '#dfdfdfc7',
                strokeDashArray: __style_array['stroke_dash'],
            },
            xaxis: {
                categories: __categories,
            },
            yaxis: {
                labels: {
                    formatter: function (value) {
                        return `$ ${value.toLocaleString()}`;
                    }
                },
            },
            fill: {
                opacity: 1
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
                            return seriesName
                        }
                    },
                },
                marker: {
                    show: true
                }
            }
        };
        var chart = new ApexCharts(document.querySelector(__selector), options);
        chart.render();
    }

</script>
@stop