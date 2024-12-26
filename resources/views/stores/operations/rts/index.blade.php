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
                        <select name='length_change' id='length_change' class="table_length_change form-select">
                        </select>
                        <input type="text" id="search_input" class="table_search_input form-control" placeholder="Search...">
					</div>
					<div class="card-body">
						<div class="table-responsive">
							<table id="data_table" class="table row-border hover" style="width:100%">
								<thead></thead>
								<tbody>                                   
								</tbody>
								<tfooter></tfooter>
							</table>
						</div>
					</div>
				</div>
			</div>
			@include('sweetalert2/script')
            @include('stores/operations/rts/modal/add-form')
            @include('stores/operations/rts/modal/delete-form')
            @include('stores/operations/rts/modal/edit-form')
		</div>
		<!--end page wrapper -->
@stop
@section('pages_specific_scripts')

<script>
    let dataTable_global;
    let menu_store_id = {{request()->id}};

    // $('#editClinicOrder_modal').on('hidden.bs.modal', function(){
    //     $('.error_txt').remove();
    //     $(this)
    //     .find("input,textarea,select")
    //     .val('')
    //     .end();    
    // });

    // $('.number_only').keyup(function(e){
    //     if (/\D/g.test(this.value))
    //     {
    //         // Filter non-digits from input value.
    //         this.value = this.value.replace(/\D/g, '');
    //     }
    // });

    // function showAddNewForm(){
    //     $('#addClinicOrder_modal').modal('show');
        
    //     $('#order_date').datepicker({
    //         format: "yyyy-mm-dd",
    //         todayHighlight: true,
    //         uiLibrary: 'bootstrap5', 
    //         modal: true,
    //         icons: {
    //             rightIcon: '<i class="material-icons"></i>'
    //         },
    //         showRightIcon: false,
    //         autoclose: true,
    //     });
       

    //     $( '#clinic_id' ).select2( {
    //         theme: "bootstrap-5",
    //         width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
    //         placeholder: $( this ).data( 'placeholder' ),
    //         closeOnSelect: true,
    //         dropdownParent: $('#addClinicOrder_modal'),
    //         multiple: false,
    //         minimumInputLength: 1,
    //         minimumResultsForSearch: 10,
    //         ajax: {
    //             headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
    //             url: "/admin/procurement_clinic_orders/get_clinic_data",
    //             dataType: "json",
    //             type: "POST",
    //             data: function (params) {
    //                 var queryParameters = {
    //                     term: params.term
    //                 }
    //                 return queryParameters;
    //             },
    //             processResults: function (data) {
    //                 return {
    //                     results: $.map(data.data, function (item) {
    //                         return {
    //                             text: item.name,
    //                             id: item.id
    //                         }   
    //                     })
    //                 };
    //             }  
    //         }
    //     });

    //     for (b = 0; b < 3; b++){
    //         $( '#drugname'+b ).select2( {
    //             theme: "bootstrap-5",
    //             width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
    //             placeholder: $( this ).data( 'placeholder' ),
    //             closeOnSelect: true,
    //             dropdownParent: $('#addClinicOrder_modal'),
    //             multiple: false,
    //             minimumInputLength: 3,
    //             minimumResultsForSearch: 10,
    //             ajax: {
    //                 headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
    //                 url: "/admin/procurement_clinic_orders/get_medication_data",
    //                 dataType: "json",
    //                 type: "POST",
    //                 data: function (params) {
    //                     var queryParameters = {
    //                         term: params.term
    //                     }
    //                     return queryParameters;
    //                 },
    //                 processResults: function (data) {
    //                     return {
    //                         results: $.map(data.data, function (item) {
    //                             return {
    //                                 text: item.name,
    //                                 id: item.med_id
    //                             }   
    //                         })
    //                     };
    //                 }  
    //             }
    //         });
    //     }
        
    //     $.ajax({
    //         headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
    //         type: "GET",
    //         url: "/admin/procurement_clinic_orders/next_order_number",
    //         contentType: "application/json; charset=utf-8",
    //         dataType: "json",
    //         success: function(data) {
    //             year = (new Date).getFullYear()
    //             $("#order_number").val(year+''+data);
                
    //         }
    //     });


    // }

    // function showEditForm(data){
    //     $('#editClinicOrder_modal').modal('show');
    //     //auto width field ndc
    //     let width = 30 * 10 + 25;

    //     $('.auto_width').css('width', width +"px");
        
    //     $('#eorder_date').datepicker({
    //         format: "yyyy-mm-dd",
    //         todayHighlight: true,
    //         uiLibrary: 'bootstrap5',
    //         modal: true,
    //         icons: {
    //             rightIcon: '<i class="material-icons"></i>'
    //         },
    //         showRightIcon: false,
    //         autoclose: true,
    //     });

    //     $( '#eclinic_id' ).select2( {
    //         theme: "bootstrap-5",
    //         width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
    //         placeholder: $( this ).data( 'placeholder' ),
    //         closeOnSelect: true,
    //         dropdownParent: $('#editClinicOrder_modal'),
    //     });
        
    //     let orderNumber = $(data).data('ordernumber');
    //     let id = $(data).data('id');
    //     let orderDate = $(data).data('orderdate');
    //     let inventoryType = $(data).data('inventorytype');
    //     let shipmentStatusId = $(data).data('shipmentstatusid');
    //     let drugId = $(data).data('drugid');
    //     let drugName = $(data).data('drugname');
    //     let ndc = $(data).data('ndc');
    //     let comments = $(data).data('comments');
    //     let prescriber = $(data).data('prescriber');
    //     let quantity = $(data).data('quantity');
    //     let clinicId = $(data).data('clinicid');
    //     let clinic = $(data).data('clinic');
    //     let shipmentStatus = $(data).data('shipmentstatus');
    //     let shipmentTrackingNumber = $(data).data('shipmenttrackingnumber');
        
    //     $('#order_id_text').html('Item ID: '+id);
    //     $('#eorder_number').val(orderNumber);
    //     $('#old_order_number').val(orderNumber);
    //     $("input#eid").val(id);
    //     $("input#eprescriber_name").val(prescriber);
    //     $("input#equantity").val(quantity);
    //     $("input#endc").val(ndc);
    //     $("textarea#ecomments").val(comments);
    //     $("#eshipment_tracking_number").val(shipmentTrackingNumber);
        

    //     const startDateInput = document.getElementById('eorder_date');
    //     startDateInput.value = orderDate;

    //     let arr = ['RX', '340B'];
    //     var arr_len = arr.length;
    //     for( var a = 0; a<arr_len; a++){

    //         if(arr[a]==inventoryType){$("#einventory_type").append("<option selected value='"+arr[a]+"'>"+arr[a]+"</option>");}
    //         else{
    //             $("#einventory_type").append("<option value='"+arr[a]+"'>"+arr[a]+"</option>");
    //         }
    //     }

    //     $("#edrugname").append("<option selected value='"+drugId+"'>"+drugName+"</option>");
    //     $( '#edrugname' ).select2( {
    //         theme: "bootstrap-5",
    //         width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
    //         placeholder: $( this ).data( 'placeholder' ),
    //         closeOnSelect: true,
    //         dropdownParent: $('#editClinicOrder_modal'),
    //         //tags: true,
    //         multiple: false,
    //         //tokenSeparators: [',', ' '],
    //         minimumInputLength: 3,
    //         minimumResultsForSearch: 10,
    //         ajax: {
    //             headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
    //             url: "/admin/procurement_clinic_orders/get_medication_data",
    //             dataType: "json",
    //             type: "POST",
    //             data: function (params) {
    //                 var queryParameters = {
    //                     term: params.term
    //                 }
    //                 return queryParameters;
    //             },
    //             processResults: function (data) {
    //                 return {
    //                     results: $.map(data.data, function (item) {
    //                         return {
    //                             text: item.name,
    //                             id: item.med_id
    //                         }   
    //                     })
    //                 };
    //             }  
    //         }
    //     });
        
        
    //     $.ajax({
    //         headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
    //         type: "GET",
    //         url: "/admin/procurement_clinic_orders/get_shipment_status_data",
    //         contentType: "application/json; charset=utf-8",
    //         dataType: "json",
    //         success: function(data) {
            
    //             var len = data.data.length;
                
    //             $("#eshipment_status").empty();
                
    //             for( var k = 0; k<len; k++){
    //                 var kid = data.data[k]['id'];
    //                 var kname = data.data[k]['name'];
    //                 if(id==shipmentStatusId){$("#eshipment_status").append("<option selected value='"+kid+"'>"+kname+"</option>");}
    //                 else{
    //                     $("#eshipment_status").append("<option value='"+kid+"'>"+kname+"</option>");
    //                 }
    //             }
    //         }
    //     });

    //     $("#eclinic_id").append("<option selected value='"+clinicId+"'>"+clinic+"</option>");
    //     $( '#eclinic_id' ).select2( {
    //         theme: "bootstrap-5",
    //         width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
    //         placeholder: $( this ).data( 'placeholder' ),
    //         closeOnSelect: true,
    //         dropdownParent: $('#editClinicOrder_modal'),

    //         multiple: false,
    //         minimumInputLength: 1,
    //         minimumResultsForSearch: 10,
    //         ajax: {
    //             headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
    //             url: "/admin/procurement_clinic_orders/get_clinic_data",
    //             dataType: "json",
    //             type: "POST",
    //             data: function (params) {
    //                 var queryParameters = {
    //                     term: params.term
    //                 }
    //                 return queryParameters;
    //             },
    //             processResults: function (data) {
    //                 return {
    //                     results: $.map(data.data, function (item) {
    //                         return {
    //                             text: item.name,
    //                             id: item.id
    //                         }   
    //                     })
    //                 };
    //             }  
    //         }
    //     });
        
    // }

    $(document).ready(function() {
        const dataTable = $('#data_table').DataTable({
            scrollX: true,
            serverSide: true,
            stateSave: true,
            dom: 'fBtip',
            buttons: [
                { text: 'Add New', className: 'btn btn-primary', action: function ( e, dt, node, config ) {
                    showAddNewForm();
                }},
            ],
            pageLength: 50,
            searching: true,
            ajax: {
                url: `/store/operations/${menu_store_id}/rts/data`,
                type: "GET",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: function (data) {
                    data.search = $('input[type="search"]').val();
                },
                error: function (msg) {
                    handleErrorResponse(msg);
                }
            },
            columns: [
                { data: 'id', name: 'operation_returns.id', title: 'ID'},
                // { data: 'patient_name', name: 'patient_name', title: 'PATIENT NAME'},
                { data: 'med_name', name: 'medications.name', title: 'MEDICATION'},
                { data: 'quantity', name: 'return_items.quantity', title: 'QUANTITY'},
                { data: 'rx_number', name: 'rx_number', title: 'RX NUMBER'},
                { data: 'date', name: 'date', title: 'DATE'},
                { data: 'status', name: 'store_statuses.name', title: 'STATUS'},
                { data: 'actions', name: 'actions', title: 'ACTION' , orderable: false, searchable: false},
            ],
            initComplete: function( settings, json ) {
                selected_len = dataTable.page.len();
				$('#length_change').append($('<option>', {value: 5,text: 'Show 5',selected:(selected_len == "5") ?true: false}));
				$('#length_change').append($('<option>', {value: 10,text: 'Show 10',selected:(selected_len == "10") ?true: false}));
				$('#length_change').append($('<option>', {value: 50,text: 'Show 50',selected:(selected_len == "50") ?true: false}));
				$('#length_change').append($('<option>', {value: 100,text: 'Show 100',selected:(selected_len == "100") ?true: false}));
				$('#length_change').append($('<option>', {value: 1000,text: 'Show 1000',selected:(selected_len == "1000") ?true: false}));
            }
        });

        dataTable_global = dataTable;

        // Placement controls for Table filters and buttons
		dataTable_global.buttons().container().appendTo( '.dt-card-header' );
        $('#search_input').val(dataTable_global.search());
		$('#search_input').keyup(function(){ dataTable_global.search($(this).val()).draw() ; })
	    $('#length_change').change( function() { dataTable_global.page.len($(this).val()).draw() });


    });

    
</script>
@stop