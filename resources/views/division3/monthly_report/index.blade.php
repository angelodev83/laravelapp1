@extends('layouts.master')
@section('content')
<!--start page wrapper -->
<div class="page-wrapper">
	<div class="page-content">
		<!-- PAGE-HEADER -->
		@include('layouts/pageContentHeader/index')
		<!-- PAGE-HEADER END -->
		<div class="card">
			<div class="card-header dt-card-header">
				<h6 class="m-2 float-start ms-3">{{$store->name}} Monthly Clinical Report for {{$year}}</h6>
					<button class="dt-button btn btn-primary float-end" tabindex="0" aria-controls="" type="button" onclick="ShowAddReportModal()"><span>Add New</span></button>
					<select name='year_report_select' id='year_report_select' class="year_report_select form-select float-end" onchange="windowReload()"><option value='{{$year}}'>{{$year}} </option>
					<option value='{{$year}}'> --- </option>
					@foreach ($years as $year)
						<option value='{{$year}}'>{{$year}} </option>

					@endforeach
					</select>
					<select name='store_report_select' id='store_report_select' class="year_report_select form-select float-end" onchange="windowReload()"><option value='{{$store->id}}'>{{$store->name}} </option>
					<option value='{{$store}}'> --- </option>
					@foreach ($stores as $store)
						<option value='{{$store->id}}'>{{$store->name}} </option>

					@endforeach
					</select>
			</div>
			<div class="card-body">
				<div class="table-responsive">
					<table class="table table-bordered ">
							 <thead>
									<tr>
										<th>Name</th>
										<th>Jan</th>
										<th>Feb</th>
										<th>Mar</th>
										<th>Apr</th>
										<th>May</th>
										<th>Jun</th>
										<th>Jul</th>
										<th>Aug</th>
										<th>Sep</th>
										<th>Oct</th>
										<th>Nov</th>
										<th>Dec</th>
										
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
													<td class="editable" data-value="{{$report->value }}" data-id="{{ $report->id }}" data-month="{{ $report->report_month }}" data-year="{{ $report->report_year }}"  data-type="{{ $clinical_report	->data_type }}">
														@if($clinical_report->data_type != 'integer')
															{{ $report->value . '%' }}
														@else
															{{ $report->value }}
														@endif
													@else
														<td>
															{{ '' }}
														</td>
													@endif
												
											@endfor
										</tr>
									@endforeach
							</tbody>
						</table>
				</div>
			</div>
		</div>
	</div>


@include('division3/monthly_report/modal/add-report-form')
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
	year = $('#year_report_select').val();
	store = $('#store_report_select').val();
	window.location.href = '/admin/division3/monthly_report/' +year+'/'+store;
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
                    var originalText = $(this).attr('data-value');
                    var itemId = $(this).attr('data-id'); //  each row has a data-id attribute

                    $(this).html('<input type="text" class="form-control " value="' + originalText + '">');
                    $(this).css('width', '90px');

                    var inputField = $(this).children().first();
					
                    inputField.focus();
                    var val = inputField.val();
                    inputField.val('');
                    inputField.val(val);

                    inputField.blur(function() {
						var userEnteredText = $(this).val();
						console.log(data_type);
						updateItem(itemId, userEnteredText);
						if (data_type == 'percentage') {
							userEnteredText = userEnteredText.replace(/\D/g, '');
							userEnteredText += '%';
						}

						 inputField.parent().text(userEnteredText);
                       	
                    });	
                });
            });

});

function updateItem(itemId, value) {
        
			$.ajax({
                url: '/admin/monthly_report/update_report',
                type: 'POST',
                data: {
                    'id': itemId,
                    'value': value
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
