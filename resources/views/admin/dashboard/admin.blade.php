@extends('layouts.master')
@section('content')
    <!--start page wrapper -->
	<div class="page-wrapper">
		<div class="page-content">

            <!-- body starts -->
            @include('admin/dashboard/partials/announcement-alerts')
            @include('admin/dashboard/partials/evergreen')
            @include('admin/dashboard/partials/operations')
            <!-- body ends -->

		</div>
    </div>
	<!--end page wrapper -->
@stop

@section('pages_specific_scripts')  

<script>
    let pharmacy_store_id;
    let month_from;
    let month_to;

    let charts = {};

    $(document).ready(function() {
        let data = {
            date_from: null,
            date_to: null,
            pharmacy_store_id: $('#pharmacy_store_id').val()
        };
        charts = {
            evergreenGrossSales: {id: '#evergreen-monthly-gross-sales-chart', chart: null},
            evergreenCollectedPayments: {id: '#evergreen-monthly-collected-payments-chart', chart: null},
            evergreenAccountReceivables: {id: '#evergreen-monthly-account-receivables-chart', chart: null},
            evergreenRevenuePerEmployee: {id: '#evergreen-monthly-revenue-per-employee-chart', chart: null},
            operationsGrossSales: {id: '#operations-monthly-gross-sales-chart', chart: null},
            operationsAvgRevenuePerScript: {id: '#operations-monthly-revenue-per-script-chart', chart: null},
            operationsGrowthAndShrinkageBilledRatio: {id: '#operations-monthly-growth-and-shrinkage-chart', chart: null},
            operationsAvgTurnaroundTimeHours: {id: '#operations-monthly-avg-turnaround-time-chart', chart: null},
        };
        loadAllCharts();
        loadDataInsightsData(data);
        loadClinicalData(data);
    });

    function loadDataInsightsData(data)
    {
        let monthlyGrossSalesChart = [];
        let monthlyCollectedPaymentsChart = [];
        let monthlyAccountReceivablesChart = [];
        let monthlyRevenuePerEmployeeChart = [];
        let monthlyGrossProfitRevenuePerScriptChart = [];
        let monthlyTurnaroundTimeHoursChart = [];

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

                    let avgTurnaroundTimeDay = 0;
                    let avgTurnaroundTimeDayText = '--';
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
                        $('#evergreen-widget-gross-sales h4').html(`$ ${monthlyGrossSales.formatted}`);
                        $('#operations-monthly-gross-sales-chart-title').html(`$ ${monthlyGrossSales.raw_with_comma}`);
                        if(monthlyGrossSales.percentage_mark == '') {
                            $('#evergreen-widget-gross-sales p').html(`<span class="text-secondary">--%</span> <span>from last month</span>`);
                        } else {
                            $('#evergreen-widget-gross-sales p').html(`<i class="fa fa-${monthlyGrossSales.percentage_mark} text-${monthlyGrossSales.percentage_class}"></i><span class="text-${monthlyGrossSales.percentage_class}"> ${monthlyGrossSales.percentage}%</span> <span>from last month</span>`);
                        }
                        // Collected Payments Widget
                        $('#evergreen-widget-collected-payments h4').html(`$ ${monthlyCollectedPayments.formatted}`);
                        if(monthlyCollectedPayments.percentage_mark == '') {
                            $('#evergreen-widget-collected-payments p').html(`<span class="text-secondary">--%</span> <span>from last month</span>`);
                        } else {
                            $('#evergreen-widget-collected-payments p').html(`<i class="fa fa-${monthlyCollectedPayments.percentage_mark} text-${monthlyCollectedPayments.percentage_class}"></i><span class="text-${monthlyCollectedPayments.percentage_class}"> ${monthlyCollectedPayments.percentage}%</span> <span>from last month</span>`);
                        }
                        // Account Receivables Widget
                        $('#evergreen-widget-account-receivables h4').html(`$ ${monthlyAccountReceivables.formatted}`);
                        if(monthlyAccountReceivables.percentage_mark == '') {
                            $('#evergreen-widget-account-receivables p').html(`<span class="text-secondary">--%</span> <span>from last month</span>`);
                        } else {
                            $('#evergreen-widget-account-receivables p').html(`<i class="fa fa-${monthlyAccountReceivables.percentage_mark} text-${monthlyAccountReceivables.percentage_class}"></i><span class="text-${monthlyAccountReceivables.percentage_class}"> ${monthlyAccountReceivables.percentage}%</span> <span>from last month</span>`);
                        }
                        // Revenue per Employee Widget
                        $('#evergreen-widget-revenue-per-employee h4').html(`$ ${monthlyRevenuePerEmployee.formatted}`);
                        if(monthlyRevenuePerEmployee.percentage_mark == '') {
                            $('#evergreen-widget-revenue-per-employee p').html(`<span class="text-secondary">--%</span> <span>from last month</span>`);
                        } else {
                            $('#evergreen-widget-revenue-per-employee p').html(`<i class="fa fa-${monthlyRevenuePerEmployee.percentage_mark} text-${monthlyRevenuePerEmployee.percentage_class}"></i><span class="text-${monthlyRevenuePerEmployee.percentage_class}"> ${monthlyRevenuePerEmployee.percentage}%</span> <span>from last month</span>`);
                        }
                        // Operations start
                        $('#operations-monthly-revenue-per-script-chart-title').html(`$ ${monthlyGrossProfitRevenuePerScript.raw_with_comma}`);
                        $('#operations-monthly-avg-turnaround-time-chart-title').html(`${avgTurnaroundTimeDayText}`);
                    }

                    monthlyGrossSalesChart.push(monthlyGrossSales.raw);
                    monthlyCollectedPaymentsChart.push(monthlyCollectedPayments.raw);
                    monthlyAccountReceivablesChart.push(monthlyAccountReceivables.raw);
                    monthlyRevenuePerEmployeeChart.push(monthlyRevenuePerEmployee.raw);
                    monthlyGrossProfitRevenuePerScriptChart.push(monthlyGrossProfitRevenuePerScript.raw);
                    monthlyTurnaroundTimeHoursChart.push(avgTurnaroundTimeDay);
                });

                updateChartSeriesByKey('evergreenGrossSales', monthlyGrossSalesChart, []);
                updateChartSeriesByKey('evergreenCollectedPayments', monthlyCollectedPaymentsChart, []);
                updateChartSeriesByKey('evergreenAccountReceivables', monthlyAccountReceivablesChart, []);
                updateChartSeriesByKey('evergreenRevenuePerEmployee', monthlyRevenuePerEmployeeChart, []);

                updateChartSeriesByKey('operationsGrossSales', monthlyGrossSalesChart, []);
                updateChartSeriesByKey('operationsAvgRevenuePerScript', monthlyGrossProfitRevenuePerScriptChart, []);
                updateChartSeriesByKey('operationsAvgTurnaroundTimeHours', monthlyTurnaroundTimeHoursChart, []);
            },
            error: function(xhr, status, error) {
                console.error(error);
                handleErrorResponse(error);
            }
        });
    }

    function loadClinicalData(data)
    {
        let monthlyPatientGrowthAndShrinkageChart = [];

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
                    
                    // if(filter.current_month == month_number) {

                    // }

                    monthlyPatientGrowthAndShrinkageChart.push(monthlyPatientGrowthAndShrinkage.percentage);
                });

                updateChartSeriesByKey('operationsGrowthAndShrinkageBilledRatio', monthlyPatientGrowthAndShrinkageChart, []);

            },
            error: function(xhr, status, error) {
                console.error(error);
                handleErrorResponse(error);
            }
        });
    }

    function filterStore()
    {
        let data = {
            date_from: null,
            date_to: null,
            pharmacy_store_id: $('#pharmacy_store_id').val()
        };

        loadDataInsightsData(data);
        loadClinicalData(data);
    }

    function updateChartSeriesByKey(__key, __newData, __newCategories)
    {
        var newSeries = [{
            data: __newData
        }];
        charts[__key].updateSeries(newSeries);
    }

    function loadAllCharts()
    {
        let data = [0,0,0,0,0,0,0,0,0,0,0,0];
        // Object.entries(charts).forEach(([key, value]) => {
            
        // });
        generateCurveLinePerMonthChart(
            data, 
            'Gross Sales (Billed)', 
            '#evergreen-monthly-gross-sales-chart', 
            {
                'color': "#ff92da",
                'height': 500,
                'marker_size': 6,
                'stroke_dash': 4,
                'show_grid': true,
                'prefix': '$ ',
                'suffix': '',
            },
            'evergreenGrossSales'
        );
        generateBarPerMonthChart(
            data, 
            'Collected Payments', 
            '#evergreen-monthly-collected-payments-chart', 
            {
                'color': "#21CAF1",
                'height': 116,
                'marker_size': 6,
                'stroke_dash': 4,
                'show_grid': true,
                'prefix': '$ ',
                'suffix': '',
            },
            'evergreenCollectedPayments'
        );
        generateBarPerMonthChart(
            data, 
            'Account Receivables', 
            '#evergreen-monthly-account-receivables-chart', 
            {
                'color': "#FDB202",
                'height': 116,
                'marker_size': 6,
                'stroke_dash': 4,
                'show_grid': true,
                'prefix': '$ ',
                'suffix': '',
            },
            'evergreenAccountReceivables'
        );
        generateBarPerMonthChart(
            data, 
            'Revenue Per Employee', 
            '#evergreen-monthly-revenue-per-employee-chart', 
            {
                'color': "#5ECE43",
                'height': 116,
                'marker_size': 6,
                'stroke_dash': 4,
                'show_grid': true,
                'prefix': '$ ',
                'suffix': '',
            },
            'evergreenRevenuePerEmployee'
        );

        // operations
        let operation_style = {
            'color': "#27CC3A",
            'height': 170,
            'marker_size': 3,
            'stroke_dash': 0,
            'show_grid': true,
            'prefix': '$ ',
            'suffix': '',
        };
        generateCurveLinePerMonthChart(
            data, 
            'Gross Sales (Billed)', 
            '#operations-monthly-gross-sales-chart',
            {
                'color': "#27CC3A",
                'height': 170,
                'marker_size': 0,
                'stroke_dash': 0,
                'show_grid': false,
                'prefix': '$ ',
                'suffix': '',
            }, 
            'operationsGrossSales'
        );
        generateBarPerMonthChart(
            data, 
            'Avg Revenue Per Script', 
            '#operations-monthly-revenue-per-script-chart', 
            operation_style, 
            'operationsAvgRevenuePerScript'
        );
        generateStraightLinePerMonthChart(
            data,
            'Growth and Shrinkage $ - Billed Ratio', 
            '#operations-monthly-growth-and-shrinkage-chart', 
            {
                'color': "#27CC3A",
                'height': 170,
                'marker_size': 3,
                'stroke_dash': 0,
                'show_grid': true,
                'prefix': '',
                'suffix': ' %',
            },
            'operationsGrowthAndShrinkageBilledRatio'
        );
        generateBarPerMonthChart(
            data, 
            'Avg Turnaround Time Days', 
            '#operations-monthly-avg-turnaround-time-chart', 
            {
                'color': "#27CC3A",
                'height': 170,
                'marker_size': 3,
                'stroke_dash': 0,
                'show_grid': true,
                'prefix': '',
                'suffix': ' Days',
            }, 
            'operationsAvgTurnaroundTimeHours'
        );
    }


    /* *
     * * Generating CHARTS functions - start ===================================================================
     * */

    function generateCurveLinePerMonthChart(__data_array = [], __title = '', __selector = '', __style_array = [], __key)
    {
        const _prefix = __style_array['prefix'] ?? '';
        const _suffix = __style_array['suffix'] ?? '';

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
                categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
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
                    formatter: function (value) {
                        return `${_prefix}${value.toLocaleString()}${_suffix}`;
                    }
                },
                marker: {
                    show: true
                }
            }
        };
        charts[__key] = new ApexCharts(document.querySelector(__selector), options);
        charts[__key].render();
    }

    function generateStraightLinePerMonthChart(__data_array = [], __title = '', __selector = '', __style_array = [], __key)
    {
        const _prefix = __style_array['prefix'] ?? '';
        const _suffix = __style_array['suffix'] ?? '';

        var options = {
            series: [{
                name: __title,
                data: __data_array
            }],
            chart: {
                type: 'line',
                foreColor: '#9a9797',
                height: 250,
                toolbar: {
                    show: false
                },
                zoom: {
                    enabled: false
                },
                sparkline: {
                    enabled: false
                }
            },
            markers: {
                size: __style_array['marker_size'],
                colors: [__style_array['color']],
                strokeColors: __style_array['color'],
                hover: {
                    size: __style_array['marker_size']+2,
                }
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '45%',
                    endingShape: 'rounded'
                },
            },
            
            dataLabels: {
                enabled: false
            },
            stroke: {
                show: true,
                width: 3,
                curve: 'straight'
            },
            colors: [__style_array['color']],
            grid: {
                show: __style_array['show_grid'],
                borderColor: '#dfdfdfc7',
                strokeDashArray: __style_array['stroke_dash'],
            },
            xaxis: {
                categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            },
            tooltip: {
                theme: 'dark',
                y: {
                    formatter: function (value) {
                        return `${_prefix}${value.toLocaleString()}${_suffix}`;
                    }
                }
            }
        };
        charts[__key] = new ApexCharts(document.querySelector(__selector), options);
        charts[__key].render();
    }

    function generateBarPerMonthChart(__data_array = [], __title = '', __selector = '', __style_array = [], __key)
    {
        const _prefix = __style_array['prefix'] ?? '';
        const _suffix = __style_array['suffix'] ?? '';

        var options = {
            series: [{
                name: __title,
                data: __data_array
            }],
            chart: {
                foreColor: '#9a9797',
                type: 'bar',
                height: __style_array['height'],
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
                show: __style_array['show_grid'],
                borderColor: '#dfdfdfc7',
                strokeDashArray: __style_array['stroke_dash'],
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '50%',
                    endingShape: 'rounded'
                },
            },
            markers: {
                size: __style_array['marker_size'],
                strokeColors: "#fff",
                strokeWidth: 2,
                hover: {
                    size: __style_array['marker_size']+2,
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
            colors: [__style_array['color']],
            xaxis: {
                categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            },
            tooltip: {
                theme: 'dark',
                y: {
                    formatter: function (value) {
                        return `${_prefix}${value.toLocaleString()}${_suffix}`;
                    }
                }
            },
            fill: {
                opacity: 1
            }
        };
        charts[__key] = new ApexCharts(document.querySelector(__selector), options);
        charts[__key].render();
    }

    /* *
     * * Generating CHARTS functions - ends ====================================================================
     * */

</script>

@stop
