@extends('layouts.master')
@section('content')
<!--start page wrapper -->
<div class="page-wrapper">
	<div class="page-content">
		<!-- PAGE-HEADER -->
		@include('layouts/pageContentHeader/store')
		<!-- PAGE-HEADER END -->
		<div class="card">
			<div class="card-header dt-card-header">
				<h6 class="m-2 float-start ms-3">{{$store->name}} - Monthly Clinical Report for {{$year}}</h6>
                    @can('menu_store.clinical.adherence_report.create')
					    <button class="dt-button btn btn-primary float-end" tabindex="0" aria-controls="" type="button" onclick="ShowAddReportModal()"><span>Add New</span></button>
                    @endcan
					<select name='year_report_select' id='year_report_select' class="year_report_select form-select float-end" onchange="windowReload()"><option value='{{$year}}'>{{$year}} </option>
					<option value='{{$year}}'> --- </option>
					@foreach ($years as $year)
						<option value='{{$year}}'>{{$year}} </option>

					@endforeach
					</select>
					{{-- <select name='store_report_select' id='store_report_select' class="year_report_select form-select float-end" onchange="windowReload()"><option value='{{$store->id}}'>{{$store->name}} </option>
					<option value='{{$store}}'> --- </option>
					@foreach ($stores as $store)
						<option value='{{$store->id}}'>{{$store->name}} </option>

					@endforeach
					</select> --}}
			</div>
			<div class="card-body">
				<div class="table-responsive">
					<table class="table table-bordered ">
							 <thead>
									<tr>
										<th>Name</th>
										<th colspan="2">Jan</th>
										<th colspan="2">Feb</th>
										<th colspan="2">Mar</th>
										<th colspan="2">Apr</th>
										<th colspan="2">May</th>
										<th colspan="2">Jun</th>
										<th colspan="2">Jul</th>
										<th colspan="2">Aug</th>
										<th colspan="2">Sep</th>
										<th colspan="2">Oct</th>
										<th colspan="2">Nov</th>
										<th colspan="2">Dec</th>
										
										<!-- Add more months as needed -->
									</tr>
								</thead>
							<tbody>
									@foreach($clinical_reports as $clinical_report)
										<tr>
											<td class="fw-bold">{{ $clinical_report->name }}</td>
											@for ($month = 1; $month <= 12; $month++)
												
													@php
														$report = $clinical_report->monthly_reports->firstWhere('report_month', $month);
													@endphp
												
													@if($report)
													<td class="editable" data-value="{{$report->value }}" data-id="{{ $report->id }}" data-field="value" data-month="{{ $report->report_month }}" data-year="{{ $report->report_year }}"  data-type="{{ $clinical_report	->data_type }}">
														
													@if($clinical_report->data_type != 'integer')
															{{ rtrim(rtrim(number_format($report->value, 2), '0'), '.') }}%
														@else
															{{ rtrim(rtrim(number_format($report->value, 2), '0'), '.') }}
														@endif
													@else
														<td>
															{{ '' }}
														</td>
													@endif
													<!-- goal -->
													@if($report)
													<td class="editable" data-value="{{$report->goal }}" data-id="{{ $report->id }}" data-field="goal" data-month="{{ $report->report_month }}" data-year="{{ $report->report_year }}"  data-type="{{ $clinical_report	->data_type }}">
														
													@if($clinical_report->data_type != 'integer')
															{{ rtrim(rtrim(number_format($report->goal, 2), '0'), '.') }}%
														@else
															{{ rtrim(rtrim(number_format($report->goal, 2), '0'), '.') }}
														@endif
													@else
														<td>
															{{ '' }}
														</td>
													@endif
													<!-- end-goal -->
											@endfor

											
										</tr>
									@endforeach
							</tbody>
						</table>
				</div>
			</div>
		</div>
	</div>


@include('stores/clinical/adherenceReport/modal/add-report-form')
</div>
<!--end page wrapper -->
@stop
@section('pages_specific_scripts')  
<script>
$('.number_only').on('keyup',function(e){
	if (/\D/g.test(this.value))
	{
		// Filter non-digits from input value.
		newValue = this.value.replace(/[^0-9.]/g, '');

		// Limit to 100 decimal places
		let decimalIndex = newValue.indexOf('.');
		if (decimalIndex !== -1) {
			//newValue = newValue.substring(0, decimalIndex + 101);
			decimalEntered = true; // Decimal point is entered
		} else {
			decimalEntered = false; // Reset if decimal point is removed
		}

		// Check if decimal point is already entered
		if (decimalEntered && newValue.indexOf('.') !== -1) {
			let integerPart = newValue.substring(0, decimalIndex);
			let decimalPart = newValue.substring(decimalIndex + 1);
			newValue = integerPart + '.' + decimalPart.replace(/\./g, '');
		}

		// Limit decimal places to 100
		let parts = newValue.split('.');
		if (parts.length === 2 && parts[1].length >= 3) {
			let limitedDecimalPart = parts[1].substring(0, 2);
			newValue = parts[0] + '.' + limitedDecimalPart;
		}
		
		// If there is a decimal point
		if (decimalIndex !== -1) {
			// Limit whole number to 99
			let wholeNumber = newValue.substring(0, decimalIndex);
			if (parseInt(wholeNumber) > 99) {
				wholeNumber = '99';
			}
			// Combine whole number and decimal part
			newValue = wholeNumber + newValue.substring(decimalIndex);
		} else {
			// If no decimal point, limit to 100
			if (parseFloat(newValue) > 100) {
				newValue = '100';
			}
		}

		this.value = newValue;
	}
});

function windowReload(){
    let menu_store_id = {{request()->id}}
	year = $('#year_report_select').val();
	// store = $('#store_report_select').val();
	window.location.href = `/store/clinical/${menu_store_id}/adherence-reports/${year}`
}

$(document).ready(function() {
	//$('#add_report_modal').modal('show');

	$(document).ready(function() {
                $('td.editable').on('click', function() {
                    // Check if the td already contains an input element
                    if ($(this).find('input').length > 0) {
                        return;
                    }

                    var data_type = $(this).attr('data-type');
					var data_field = $(this).attr('data-field');
                    var originalText = $(this).attr('data-value');
                    var itemId = $(this).attr('data-id'); //  each row has a data-id attribute

                    $(this).html('<input type="text" class="form-control " value="' + originalText + '" style="width: 100px;">');
                    //$(this).css('width', '200px');

                    var inputField = $(this).children().first();
					
                    inputField.focus();
                    var val = inputField.val();
                    inputField.val('');
                    inputField.val(val);

                    inputField.blur(function() {
						var userEnteredText = $(this).val();
						userEnteredText = userEnteredText.match(/^-?\d+(\.\d+)?/);
						
						if (userEnteredText) {
							// Round the number to two decimal places
							userEnteredText = Math.round(parseFloat(userEnteredText[0]) * 100) / 100;
							
							userEnteredText.toString();
						}
						updateItem(itemId, userEnteredText, data_field);
						if (data_type == 'percentage') {
							//userEnteredText = userEnteredText.replace(/\D/g, '');
							userEnteredText += '%';
						}

						inputField.parent().text(userEnteredText);
                       	
                    });	
                });
            });

});

function updateItem(itemId, value, field) {
        
			$.ajax({
                url: '/admin/monthly_report/update_report',
                type: 'POST',
                data: {
                    'id': itemId,
                    'value': value,
					'field': field
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    console.log('Update successful');
                },
                error: function(jqXHR, textStatus, errorThrown) {
					handleErrorResponse(errorThrown);
                    console.log('Update failed: ' + errorThrown);
                }
            });

        }
</script> 
@stop
