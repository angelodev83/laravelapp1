@extends('layouts.master')
@section('content')
<!--start page wrapper -->
<div class="page-wrapper">
    <div class="page-content">
        <!-- PAGE-HEADER -->
        @include('layouts/pageContentHeader/store')

        <!-- PAGE-HEADER END -->

        <div class="m-4">
            <ul class="border border-0 nav nav-tabs" id="views">
                <li class="nav-item">
                    <a href="#list-view" class="nav-link active" data-bs-toggle="tab">
                        <i class="fa-solid fa-list"></i>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#grid-view" class="nav-link" data-bs-toggle="tab">
                        <i class="bx bx-grid-alt"></i>
                    </a>
                </li>
            </ul>
            <div class="tab-content">               
                <div id="list-view" class="tab-pane fade show active">
                    <!-- list view -->
                    <div class="p-3 bg-white border border-top-0 rounded-bottom-4">
                        <div class="card-header dt-card-header">
                            <select name='length_change' id='length_change' class="table_length_change form-select">
                            </select>
                            <input type="text" id="search_input" class="table_search_input form-control" placeholder="Search...">
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="clinicOrders_table" class="table row-border hover" style="width:100%">
                                    <thead></thead>
                                    <tbody>                                   
                                    </tbody>
                                    <tfooter></tfooter>
                                </table>
                            </div>
                        </div>
                    </div>  
                </div>
                <div id="grid-view" class="tab-pane fade">
                    <!-- grid view -->
                    <div class="p-3 bg-white rounded-bottom-4">
                        <!-- Date Filters -->
                        <div class="mb-3 row">
                            <div id="result" class="col-7"></div>
                            <div class="gap-2 d-flex col align-items-center">
                                <h6 class="mb-0">Date Filter: </h6>
                                <select id="months" class="col form-select">
                                    @foreach ($months as $key => $month)
                                    <option value="{{ $key }}"@if ($key == $currentMonth) selected @endif>{{ $month }}</option>
                                    @endforeach
                                </select>
                                <select id="years" class="col form-select">
                                    @foreach ($years as $year)
                                    <option value="{{ $year }}"@if ($key == $currentYear) selected @endif>{{ $year }}</option>
                                    @endforeach
                                </select>
                                <button id="filter" class="btn btn-primary">Filter</button>
                            </div>
                        </div>
                        <div id="order-container" class="gap-3 d-flex container-lists">
                            <!-- NEW REQUEST SECTION -->
                            <div class="card rounded-4 bg-body-secondary" style="min-width: 22rem;">
                                <div class="p-1">
                                    <div class="px-3 pt-3 mb-2 d-flex justify-content-between">
                                        <div class="gap-2 d-flex align-items-center">
                                            <h6 class="p-2 text-white rounded-3 card-title fs-6 bg-secondary">NEW REQUEST</h6>
                                            <span id="order-701-count" class="fs-6 fw-medium"></span>
                                        </div>
                                        <button onclick="showAddNewForm()" class="btn fw-medium">
                                            <i class="fa-solid fa-plus"></i>
                                            New Order
                                        </button>
                                    </div>
                                    <div id="order-701" class="px-3 mb-3 content lists"></div>
                                </div>
                            </div>
                            <!-- RECEIVED SECTION -->
                            <div class="bg-blue-200 card rounded-4" style="min-width: 22rem;">
                                <div class="p-1">
                                    <div class="px-3 pt-3 mb-2 d-flex justify-content-between">
                                        <div class="gap-2 d-flex align-items-center">
                                            <h6 class="p-2 text-white bg-blue-500 rounded-3 card-title fs-6">RECEIVED</h6>
                                            <span id="order-702-count" class="fs-6 fw-medium"></span>
                                        </div>
                                        <button onclick="showAddNewForm()" class="btn fw-medium">
                                            <i class="fa-solid fa-plus"></i>
                                            New Order
                                        </button>
                                    </div>
                                    <div id="order-702" class="px-3 mb-3 content lists"></div>
                                </div>
                            </div>
                            <!-- IN TRANSIT SECTION -->
                            <div class="bg-jade-200 card rounded-4" style="min-width: 22rem;">
                                <div class="p-1">
                                    <div class="px-3 pt-3 mb-2 d-flex justify-content-between">
                                        <div class="gap-2 d-flex align-items-center">
                                            <h6 class="p-2 text-white bg-jade-500 rounded-3 card-title fs-6">IN TRANSIT</h6>
                                            <span id="order-703-count" class="fs-6 fw-medium"></span>
                                        </div>
                                        <button onclick="showAddNewForm()" class="btn fw-medium">
                                            <i class="fa-solid fa-plus"></i>
                                            New Order
                                        </button>
                                    </div>
                                    <div id="order-703" class="px-3 mb-3 content lists"></div>
                                </div>
                            </div>
                            <!-- SUBMITTED SECTION -->
                            <div class="bg-yellow-200 card rounded-4" style="min-width: 22rem;">
                                <div class="p-1">
                                    <div class="px-3 pt-3 mb-2 d-flex justify-content-between">
                                        <div class="gap-2 d-flex align-items-center">
                                            <h6 class="p-2 text-white bg-yellow-500 rounded-3 card-title fs-6">SUBMITTED</h6>
                                            <span id="order-704-count" class="fs-6 fw-medium"></span>
                                        </div>
                                        <button onclick="showAddNewForm()" class="btn fw-medium">
                                            <i class="fa-solid fa-plus"></i>
                                            New Order
                                        </button>
                                    </div>
                                    <div id="order-704" class="px-3 mb-3 content lists"></div>
                                </div>
                            </div>
                            <!-- MISSING ORDER SECTION -->
                            <div class="bg-red-200 card rounded-4" style="min-width: 22rem;">
                                <div class="p-1">
                                    <div class="px-3 pt-3 mb-2 d-flex justify-content-between">
                                        <div class="gap-2 d-flex align-items-center">
                                            <h6 class="p-2 text-white bg-red-500 rounded-3 card-title fs-6">MISSING ORDER</h6>
                                            <span id="order-705-count" class="fs-6 fw-medium"></span>
                                        </div>
                                        <button onclick="showAddNewForm()" class="btn fw-medium">
                                            <i class="fa-solid fa-plus"></i>
                                            New Order
                                        </button>
                                    </div>
                                    <div id="order-705" class="px-3 mb-3 content lists"></div>
                                </div>
                            </div>
                            <!-- COMPLETED SECTION -->
                            <div class="bg-lawn-green-200 card rounded-4" style="min-width: 22rem;">
                                <div class="p-1">
                                    <div class="px-3 pt-3 mb-2 d-flex justify-content-between">
                                        <div class="gap-2 d-flex align-items-center">
                                            <h6 class="p-2 text-white bg-lawn-green-500 rounded-3 card-title fs-6">COMPLETED</h6>
                                            <span id="order-706-count" class="fs-6 fw-medium"></span>
                                        </div>
                                        <button onclick="showAddNewForm()" class="btn fw-medium">
                                            <i class="fa-solid fa-plus"></i>
                                            New Order
                                        </button>
                                    </div>
                                    <div id="order-706" class="px-3 mb-3 content lists"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('sweetalert2/script')
    @include('stores/procurement/clinicalOrders/modals/add')
    @include('stores/procurement/clinicalOrders/modals/edit-form')
    @include('stores/procurement/clinicalOrders/modals/delete')
    @include('stores/procurement/clinicalOrders/modals/view-form')
    @include('stores/procurement/clinicalOrders/modals/upload-form')
</div>
		<!--end page wrapper -->
@stop
@section('pages_specific_scripts')

<script>
    let table_clinicOrders;
    let grid;
    

    $('#addClinicOrder_modal').on('hidden.bs.modal', function(){
        $('.error_txt').remove();
        $(this)
        .find("input,textarea,select")
        .val('')
        .end();  
        $("#addClinicOrder_modal .rowToRemove").remove(); 
        medCount = 2;  
    });

    $('#edit_clinical_order_modal').on('hidden.bs.modal', function(){
        $('.error_txt').remove();
        $(this)
        .find("input,textarea,select")
        .val('')
        .end();    
    });

     $('#view_modal').on('hidden.bs.modal', function(){
        $('.error_txt').remove();
        $(this)
        .find("p")
        .text('')
        .end();   
        $('#inmarView_table tbody').empty(); 
    });

    $('.number_only').keyup(function(e){
        if (/\D/g.test(this.value))
        {
            // Filter non-digits from input value.
            this.value = this.value.replace(/\D/g, '');
        }
    });

    function showAddNewForm(){
        $('#addClinicOrder_modal').modal('show');
        
        $('#addClinicOrder_modal #order_date').datepicker({
            format: "yyyy-mm-dd",
            todayHighlight: true,
            uiLibrary: 'bootstrap5', 
            modal: true,
            icons: {
                rightIcon: '<i class="material-icons"></i>'
            },
            showRightIcon: false,
            autoclose: true,
        });
       
        searchSelect2Api('clinic_id', 'addClinicOrder_modal', "/admin/clinics/getNames");
        searchSelect2Api('patient_id', 'addClinicOrder_modal', "/admin/patient/getNames", {source: 'pioneer'});

        for (b = 0; b < 3; b++){
            // =========== CALL HERE appends more medication table ================= //
            searchSelect2ApiDrug(`drugname${b}`, 'addClinicOrder_modal');
        }

        const d = new Date();
        const num5 = Math.floor(Math.random() * 90000) + 10000;
        const generateOrderNumber = d.getFullYear()+''+num5;
        $("#order_number").val(generateOrderNumber);
    }

    function showViewForm(id, medications){
        grid = localStorage.getItem('activeTab');
        $('#view_modal').modal('show');
        
        var btn = document.querySelector(`#inmar-show-btn-${id}`);
        let arr = JSON.parse(btn.dataset.array);
        //console.log('fire-------------',arr);
        
        $('#vinmar_id_text').html('View Clinic Order');
        
        $("#view_modal #order_number").text(arr.order_number);
        $("#view_modal #tracking_number").text(arr.shipment_tracking_number);
        $("#view_modal #prescriber_name").text(arr.prescriber_name);
        $("#view_modal #order_date").text(arr.order_date);
        $("#view_modal #clinic").text(arr.clinic);
        $("#view_modal #status").text(arr.status);
        // $("#vreturn_date").text(returnDate);
        $("#view_modal #comments").text(arr.comments);
        
        medications.forEach(function(med){
            $("#inmarView_table > tbody").append('<tr><td><p>'+med.drugname+'</p></td><td><p>'+med.quantity+'</p></td><td><p>'+med.ndc+'</p></td></tr>'); 
        });
        
        // $.ajax({
        //     headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        //     type: "GET",
        //     url: "/store/procurement/pharmacy/inmar-returns/get_inmar_medications_data/"+id,
        //     contentType: "application/json; charset=utf-8",
        //     dataType: "json",
        //     success: function(data) {
                
        //         var len = data.data.length;
                
        //         for( var z = 0; z<len; z++){
        //             var zname = data.data[z].drug_name;
        //             var zquantity = data.data[z].quantity;
        //             var zndc = data.data[z].drug_name;
        //             var zreturn = (data.data[z].type == null)?'':data.data[z].type;
                    
        //             $("#inmarView_table > tbody").append('<tr><td><p>'+zname+'</p></td><td><p>'+zquantity+'</p></td><td><p>'+zndc+'</p></td><td><p>'+zreturn+'</p></td></tr>');   
        //         }
        //     }
        // });

    }

    function showGridViewForm(meds, id){
        $('#view_modal').modal('show');
        
        let medications = meds.getAttribute('data-order-items');
        let arr = JSON.parse(decodeURIComponent(medications));
        $('#vinmar_id_text').html('View Clinic Order');
        
        $("#view_modal #order_number").text(arr.order_number);
        $("#view_modal #tracking_number").text(arr.shipment_tracking_number);
        $("#view_modal #prescriber_name").text(arr.prescriber_name);
        $("#view_modal #order_date").text(arr.order_date);
        $("#view_modal #clinic").text(arr.clinic);
        $("#view_modal #status").text(arr.status);
        $("#view_modal #comments").text(arr.comments);
        
        arr.medications.forEach(function(med){
            $("#inmarView_table > tbody").append('<tr><td><p>'+med.drugname+'</p></td><td><p>'+med.quantity+'</p></td><td><p>'+med.ndc+'</p></td></tr>'); 
        });
    }

    function searchSelect2Api(_select_id, _modal_id, _url, condition = null)
    {
        console.log("_select_id", _select_id);
        console.log("_modal_id", _modal_id);
        console.log("_url", _url);
        $(`#${_select_id}`).select2( {
            theme: "bootstrap-5",
            width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
            placeholder: $( this ).data( 'placeholder' ),
            closeOnSelect: true,
            dropdownParent: $(`#${_modal_id} .modal-content`),

            multiple: false,
            minimumInputLength: 1,
            minimumResultsForSearch: 10,
            ajax: {
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                url: _url,
                dataType: "json",
                type: "POST",
                data: function (params) {
                    var queryParameters = {
                        term: params.term,
                        limit: 10
                    }
                    if(condition != null) {
                        queryParameters = {...queryParameters, ...condition};
                    }
                    return queryParameters;
                },
                processResults: function (data) {
                    return {
                        results: $.map(data.data, function (item) {
                            console.log("item", item);
                            console.log("===============================================");
                            return {
                                text: item.name,
                                id: item.id
                            }   
                        })
                    };
                }  
            },
        });
    }

    function searchSelect2ApiDrug(_select_id, _modal_id)
    {
        $(`#${_select_id}`).select2( {
            theme: "bootstrap-5",
            width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
            placeholder: $( this ).data( 'placeholder' ),
            closeOnSelect: true,
            dropdownParent: $(`#${_modal_id} .modal-content`),

            multiple: false,
            minimumInputLength: 1,
            minimumResultsForSearch: 10,
            ajax: {
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                url: "/admin/medications/getNames",
                dataType: "json",
                type: "POST",
                data: function (params) {
                    var queryParameters = {
                        term: params.term,
                        limit: 10
                    }
                    return queryParameters;
                },
                processResults: function (data) {
                    return {
                        results: $.map(data.data, function (item) {
                            return {
                                text: item.name,
                                id: item.med_id
                            }   
                        })
                    };
                }  
            },
        });
    }

    function searchItem(_selector, _modal_id, _i = null, _id = null, _new = null)
    {
        //console.log("fire")
        $(_selector).select2( {
            theme: "bootstrap-5",
            width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
            placeholder: $( this ).data( 'placeholder' ),
            closeOnSelect: true,
            dropdownParent: $(`#${_modal_id} .modal-content`),

            multiple: false,
            minimumInputLength: 1,
            minimumResultsForSearch: 10,
            ajax: {
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                url: "/store/procurement/pharmacy/inmar-returns/get_medication_data",
                dataType: "json",
                type: "POST",
                data: function (params) {
                    var queryParameters = {
                        term: params.term
                    }
                    return queryParameters;
                },
                processResults: function (data) {
                    return {
                        results: $.map(data.data, function (item) {
                            return {
                                text: item.name,
                                id: item.med_id
                            }   
                        })
                    };
                }
            },
        });
        // }).on('select2:select', function(e) {
        //     // Get the selected option data
        //     var selectedData = e.params.data;

        //     // Call your function with the selected data
        //     console.log("selected", selectedData)
        //     displaySupplyItem(selectedData, _i, _id, _new);
        // });
    }

    $(document).ready(function() {
        const clinicOrders_table = $('#clinicOrders_table').DataTable({
            scrollX: true,
            serverSide: true,
            stateSave: true,
            dom: 'fBtp',
            order: [[5, 'desc']],
            buttons: [
                @can('menu_store.procurement.clinical_orders.create')
                { text: '+ New Order', className: 'btn btn-primary', action: function ( e, dt, node, config ) {
                    showAddNewForm();
                }},
                @endcan
            ],
            pageLength: 50,
            searching: true,
            ajax: {
                url: "/store/procurement/clinical-orders/data",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: function (data) {
                    data.search = $('input[type="search"]').val();
                    data.pharmacy_store_id = {{request()->id}}
                },
                error: function (msg) {
                    handleErrorResponse(msg);
                }
            },
            columns: [
                { data: 'order_number', name: 'clinical_orders.order_number', title: 'ORDER NO.'},
                { data: 'status', name: 'store_statuses.name', title: 'STATUS' },
                { data: 'order_date', name: 'order_date', title: 'ORDER DATE' },
                { data: 'shipment_tracking_number', name: 'clinical_orders.shipment_tracking_number', title: 'TRACKING NO.' },
                { data: 'order_by', name: 'clinics.name', title: 'ORDERED BY' },
                { data: 'created_at', name: 'clinical_orders.created_at', title: 'DATE CREATED' },
                { data: 'actions', name: 'actions', title: 'Action' , orderable: false, searchable: false},
            ],
            initComplete: function( settings, json ) {
                selected_len = clinicOrders_table.page.len();
				$('#length_change').append($('<option>', {value: 5,text: 'Show 5',selected:(selected_len == "5") ?true: false}));
				$('#length_change').append($('<option>', {value: 10,text: 'Show 10',selected:(selected_len == "10") ?true: false}));
				$('#length_change').append($('<option>', {value: 50,text: 'Show 50',selected:(selected_len == "50") ?true: false}));
				$('#length_change').append($('<option>', {value: 100,text: 'Show 100',selected:(selected_len == "100") ?true: false}));
				$('#length_change').append($('<option>', {value: 1000,text: 'Show 1000',selected:(selected_len == "1000") ?true: false}));
            },
        });

        table_clinicOrders = clinicOrders_table;

        // Placement controls for Table filters and buttons
		clinicOrders_table.buttons().container().appendTo( '.dt-card-header' );
        $('#search_input').val(clinicOrders_table.search());
		$('#search_input').keyup(function(){ clinicOrders_table.search($(this).val()).draw() ; })
	    $('#length_change').change( function() { clinicOrders_table.page.len($(this).val()).draw() });


    });

    // $(".auto_width").on('keyup', function(){
        
    //     elementId = $(this).prop('id');

    //     let width = $(this).val().length * 10 + 25;

    //     $(this).css('width', width +"px");
    // });
    function reloadDataTable()
    {
        table_clinicOrders.ajax.reload(null, false);
        loadCard();
    }

    // onload
    $(document).ready(function() {
        
        new PerfectScrollbar('#order-container');
        new PerfectScrollbar('#order-701');
        new PerfectScrollbar('#order-702');
        new PerfectScrollbar('#order-703');
        new PerfectScrollbar('#order-704');
        new PerfectScrollbar('#order-705');
        new PerfectScrollbar('#order-706');
        
        
        // Retrieve the last active tab from local storage
        var lastActiveTab = localStorage.getItem('activeTab');
        if (lastActiveTab) {
            $('.nav-link[href="' + lastActiveTab + '"]').tab('show');
        }

        // Save the active tab to local storage when a tab is clicked
        $('.nav-link').on('shown.bs.tab', function(e) {
            var activeTab = $(e.target).attr('href');
            localStorage.setItem('activeTab', activeTab);
        });

        loadCard();

        $('#filter').click(function(e) {
            e.preventDefault();
            loadCard();
        });
    });

    function loadCard() {
        var month = $('#months').val();
        var year = $('#years').val();
        
        var data = { month: month, year: year };
        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            url: "{{ route('clinicalOrder.index') }}",
            type: "POST",
            dataType: "json",
            data: data,
            success: function(response) {
                order701(response.data['order701'], response.data['selectedYear'], response.data['selectedMonth']);
                order702(response.data['order702'], response.data['selectedYear'], response.data['selectedMonth']);
                order703(response.data['order703'], response.data['selectedYear'], response.data['selectedMonth']);
                order704(response.data['order704'], response.data['selectedYear'], response.data['selectedMonth']);
                order705(response.data['order705'], response.data['selectedYear'], response.data['selectedMonth']);
                order706(response.data['order706'], response.data['selectedYear'], response.data['selectedMonth']);
            },
            error: function(xhr, status, error) {
                handleErrorResponse(error);
                console.log('Error:', error);
            }
        });
    };

    // task Order - NEW REQUEST
    function order701(data, year, month ) {
        render($('#order-701'), $('#order-701-count'), data, year, month);
    }
    // task Order - RECEIVED
    function order702(data, year, month ) {
        render($('#order-702'), $('#order-702-count'), data, year, month);
    }
    // task Order - IN TRANSIT
    function order703(data, year, month ) {
        render($('#order-703'), $('#order-703-count'), data, year, month);
    }
    // task Order - SUBMITTED
    function order704(data, year, month ) {
        render($('#order-704'), $('#order-704-count'), data, year, month);
    }
    // task Order - MISSING ORDER
    function order705(data, year, month ) {
        render($('#order-705'), $('#order-705-count'), data, year, month);
    }
    // task Order - COMPLETED
    function order706(data, year, month ) {
        render($('#order-706'), $('#order-706-count'), data, year, month);
    }

    function render(container, count, data, year, month) {
        const $OrderContainer = container;
        const $OrderCountElement = count;

        let html = '';
        let itemCount = 0;

        data.forEach(data => {
            itemCount++;
            html += `
                <div class="hover-card card" data-order-id="${data.id}">
                    <div class="card-body" data-order-items="${encodeURIComponent(JSON.stringify(data))}" onclick="showGridViewForm(this, ${data.id})">
                        <h6 class="card-title">${data.order_number}</h6>
                        <div class="d-flex gap-2 align-items-end">
                            ${ 
                                data.image
                                ? `<img src="/upload/userprofile/${data.image}" class="rounded-circle" data-bs-toggle="tooltip" data-bs-title="${data.assigned_to}" style="width: 35px; height: 35px;">`
                                : `<span class="rounded-circle employee-avatar-${data.initials_random_color}-initials" data-bs-toggle="tooltip" data-bs-title="${data.assigned_to}">${data.initials}</span>`
                            }
                            <div class="px-1 border border-2 rounded border-secondary">
                                <i class="fa-solid fa-calendar-day"></i>
                                <span class="text-success">${data.order_date ?? ''}</span>
                            </div>
                        </div>
                    </div>
                    <div class="dropdown dots d-none">
                        <button type="button" class="btn dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <ul class="dropdown-menu">
                        <li><a class="dropdown-item fw-medium" href="#"> Archive</a></li>
                        <li><a class="dropdown-item fw-medium" href="#"> Delete</a></li>
                        </ul>
                    </div>
                </div>
            `;
        });

        $OrderContainer.html(html || '<p class="fst-italic">No record found.</p>');
        $OrderCountElement.text(itemCount);

        // activate tooltip
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
    }

    $(document).ready(function() {
        let isAjaxRequestInProgress = false;
        $('.lists').sortable({
            connectWith: '.lists',
            cursor: 'grabbing',
            opacity: 0.6,
            placeholder: 'placeholder',
            update: function(event, ui) {
                if (!isAjaxRequestInProgress) {
                    isAjaxRequestInProgress = true;
                    const orderId = ui.item.data('order-id');
                    let statusId = $(ui.item[0]).closest('.lists').attr('id');
                    statusId = statusId.slice(6);
                    let data = {
                        id: orderId,
                        status_id: statusId
                    };
                    sweetAlertLoading();
                    $.ajax({
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        type: "PUT",
                        url: "{{ route('status.update') }}",
                        data: JSON.stringify(data),
                        contentType: "application/json; charset=utf-8",
                        dataType: "json",
                        success: function(response) {
                            Swal.fire({
                                position: 'center',
                                icon: 'success',
                                title: response.message,
                                showConfirmButton: false,
                                timer: 1500
                            });
                            // Handle success response
                            console.log(response.message);
                            reloadDataTable();
                            isAjaxRequestInProgress = false;
                        },
                        error: function(xhr, status, error) {
                                // Handle error response
                                console.error(error);
                                handleErrorResponse(error);
                                isAjaxRequestInProgress = false;
                            }
                        });
                    }
                }
            }
        ).disableSelection();
    });
</script>
@stop