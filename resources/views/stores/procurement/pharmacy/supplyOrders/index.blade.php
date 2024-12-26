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
                        <div class="card-header card-index-header">
                            <select name='length_change' id='length_change' class="table_length_change form-select">
                            </select>
                            <input type="text" id="search_input" class="table_search_input form-control" placeholder="Search...">
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="dt_supply_order_items_table" class="table row-border table-hover" style="width:100%">
                                    <thead></thead>
                                    <tbody></tbody>
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
                                        <button onclick="showAddModal()" class="btn fw-medium">
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
                                        <button onclick="showAddModal()" class="btn fw-medium">
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
                                        <button onclick="showAddModal()" class="btn fw-medium">
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
                                        <button onclick="showAddModal()" class="btn fw-medium">
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
                                        <button onclick="showAddModal()" class="btn fw-medium">
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
                                        <button onclick="showAddModal()" class="btn fw-medium">
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
    @include('stores/procurement/pharmacy/supplyOrders/modals/show')
    @include('stores/procurement/pharmacy/supplyOrders/modals/add')
    @include('stores/procurement/pharmacy/supplyOrders/modals/edit')
    @include('stores/procurement/pharmacy/supplyOrders/modals/upload-form')
    @include('stores/procurement/pharmacy/supplyOrders/modals/delete')
</div>
<!--end page wrapper -->
@stop
@section('pages_specific_scripts')

<script>
    let menu_store_id = {{request()->id}};
    let table_supply_order;

    $('#add_supply_order_modal').on('hidden.bs.modal', function(){
        $('.error_txt').remove();
        $(this)
        .find("input,textarea,select")
        .val('')
        .end();    
        $('.add-hidden').hide();
        $('.remove-after').remove();
        i = 2;
    });

    $('#edit_supply_order_modal').on('hidden.bs.modal', function(){
        $('.error_txt').remove();
        $(this)
        .find("input,textarea,select")
        .val('')
        .end();    
        $('.add-hidden').hide();
    });

    $(document).ready(function() {
        menu_store_id = {{request()->id}};
        loadOrderItems();
        $(`#number-0`).val('hey');

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

    function loadOrderItems() 
    {
        let data = {};        
        const staff_table = $('#dt_supply_order_items_table').DataTable({
            scrollX: true,
            serverSide: true,
            stateSave: true,
            pageLength: 10,
            dom: 'fBtp',
            order: [[4, 'desc']],
            buttons: [
                @php
                    if(Auth::user()->can('menu_store.procurement.pharmacy.supplies_orders.create')) {
                @endphp
                { text: '+ New Order', className: 'btn btn-primary', action: function ( e, dt, node, config ) {
                    showAddModal();

                }},
                @php
                    }
                @endphp
            ],
            searching: true,
            destroy: true,
            ajax: {
                url: `/store/procurement/pharmacy/${menu_store_id}/supply-orders/data`,
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: function (data) {
                    data.search = $('input[type="search"]').val();
                    data.pharmacy_store_id = menu_store_id;
                },
                error: function (msg) {
                    handleErrorResponse(msg);
                }
            },
            columns: [
                { data: 'order_number', name: 'order_number', title: 'Order Number', render: function(data, type, row) {
                    return `<div class="dt-procurement-order-number" onclick="showViewDetailsModal(${row.id})" style="cursor: pointer;">${data}</div>`;
                } },
                { data: 'status', name: 'status', title: 'Status' , orderable: false, searchable: false, width: '15%'},
                { data: 'order_date', name: 'order_date', title: 'Order Date' },
                { data: 'wholesaler', name: 'wholesaler', title: 'Wholesaler' },
                { data: 'created_at', name: 'created_at', title: 'Date Created' },
                { data: 'actions', name: 'actions', title: 'Action' , orderable: false, searchable: false, width: '10%'},
            ],
            initComplete: function( settings, json ) {
                selected_len = staff_table.page.len();
				$('#length_change').append($('<option>', {value: 5,text: 'Show 5',selected:(selected_len == "5") ?true: false}));
				$('#length_change').append($('<option>', {value: 10,text: 'Show 10',selected:(selected_len == "10") ?true: false}));
				$('#length_change').append($('<option>', {value: 50,text: 'Show 50',selected:(selected_len == "50") ?true: false}));
				$('#length_change').append($('<option>', {value: 100,text: 'Show 100',selected:(selected_len == "100") ?true: false}));
				$('#length_change').append($('<option>', {value: 1000,text: 'Show 1000',selected:(selected_len == "1000") ?true: false}));
            }
        });

        table_supply_order = staff_table;
        table_supply_order.buttons().container().appendTo( '.card-index-header' );
        $('#search_input').val(table_supply_order.search());
		$('#search_input').keyup(function(){ table_supply_order.search($(this).val()).draw() ; })
	    $('#length_change').change( function() { table_supply_order.page.len($(this).val()).draw() });
    }

    function showAddModal()
    {
        $('#add_supply_order_modal #wholesaler_id').empty();
        populateNormalSelect(`#add_supply_order_modal #wholesaler_id`, '#add_supply_order_modal', '/admin/search/wholesaler', {category: 'supply'}, 2);
        for (let b = 0; b < 3; b++){
            searchSupplyItem(`#number-${b}`, 'add_supply_order_modal', b);
        }
        
        $('#add_supply_order_modal #order_date').datepicker({
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

        $('#add_supply_order_modal').modal('show');
        // Trigger change event on page load to set initial state
        $('#add_supply_order_modal .supplies-url-holder').hide();
        $('#add_supply_order_modal .hide-for-url').show();
    }

    function showEditModal(id)
    {
        @php
            if(Auth::user()->can('menu_store.procurement.pharmacy.supplies_orders.updateall')) {
        @endphp
            $('#edit_supply_order_modal #number').prop('disabled', false);
            $('#edit_supply_order_modal #quantity').prop('disabled', false);
            $('#edit_supply_order_modal #status_id').prop('disabled', false);
            $('#edit_supply_order_modal #wholesaler_id').prop('disabled', false);
            $('#edit_supply_order_modal #order_number').prop('disabled', false);
            $('#edit_supply_order_modal #comments').prop('disabled', false);
        @php
            }
        @endphp
        @php
            if(Auth::user()->can('menu_store.procurement.pharmacy.supplies_orders.updateactualqty')) {
        @endphp
            $('#edit_supply_order_modal #aquantity').prop('disabled', false);
        @php
            }
        @endphp
        var btn = document.querySelector(`#supply-order-edit-btn-${id}`);
        let array = JSON.parse(btn.dataset.array);
        let statuses = JSON.parse(btn.dataset.status);
        stat_id = array.status_id;

        statuses.forEach(function(status) {
            if(status.id === stat_id){
                $("#edit_supply_order_modal #status_id").append("<option selected value='"+status.id+"'>"+status.name+"</option>");
            }
            else{
                $("#edit_supply_order_modal #status_id").append("<option value='"+status.id+"'>"+status.name+"</option>");
            }
        });

        $(`[name='eorder_number']`).val(array.order_number);
        $(`[name='ecomments']`).val(array.comments);
        $(`[name='enumber']`).val(array.item_number);
        $(`[name='ecode']`).val(array.item_model_number);
        $(`[name='edescription']`).val(array.item_description);
        $(`[name='equantity']`).val(array.quantity);
        $(`[name='eid']`).val(array.id);
        $('#edit_supply_order_modal #aquantity').val(array.actual_quantity);

        $(`[name='enumber']`).append("<option selected value='"+array.id+"'>"+array.item_number+"</option>");
        searchSupplyItem(`[name="enumber"]`, 'edit_supply_order_modal');

        $('#edit_supply_order_modal').modal('show');
    }

    function searchSupplyItem(_selector, _modal_id, _i = null, _id = null, _new = null)
    {
        console.log("fire")
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
                url: '/admin/search/supply-item',
                dataType: "json",
                type: "POST",
                data: function (params) {
                    var queryParameters = {
                        term: params.term,
                        limit: 10
                    }
                    if(_id != null) {
                        var q = { id: _id, not: 'id' };
                        queryParameters = {...queryParameters, ...q}
                    }
                    return queryParameters;
                },
                processResults: function (data) {
                    return {
                        results: $.map(data.data, function (item) {
                            return {
                                text: 'item #: '+item.item_number+' code: '+item.model_number,
                                id: item.id,
                                size: item.size,
                                description: item.description,
                                model_number: item.model_number,
                                item_number: item.item_number,
                            }   
                        })
                    };
                }  
            },
        }).on('select2:select', function(e) {
            // Get the selected option data
            var selectedData = e.params.data;

            // Call your function with the selected data
            console.log("selected", selectedData)
            displaySupplyItem(selectedData, _i, _id, _new);
        });
    }

    function displaySupplyItem(data, i = null, _id = null,  _new = null)
    {
        const is_number = Number.isInteger(i);
        console.log('--fire display item',is_number, data)
        if(is_number) {
            if(Number.isInteger(_new)) {
                console.log("---FIRE for edit modal - editing new items",$(`#new_number_${_new}`).val())
                $(`#new_item_${_new}`).val(data.item_number);
                $(`#new_code_${_new}`).val(data.model_number);
                $(`#new_description_${_new}`).val(data.description);

                $(`#new_number_${_new}`).next('.select2-container').hide();
                $(`#new_item_${_new}`).removeAttr('hidden');
                $(`#new_item_${_new}`).show();
            } else {
                console.log("---FIRE for add modal - items")
                $(`#item-${i}`).val(data.item_number);
                $(`#code-${i}`).val(data.model_number);
                $(`#description-${i}`).val(data.description);
               
                $(`#number-${i}`).next('.select2-container').hide();
                $(`#item-${i}`).removeAttr('hidden');
                $(`#item-${i}`).show();
                
            }
        } else {
            console.log("---FIRE for edit modal - editing exsiting items")
            $(`#item_${_id}`).val(data.item_number);
            $(`#code_${_id}`).val(data.model_number);
            $(`#description_${_id}`).val(data.description);

            $(`#number_${_id}`).next('.select2-container').hide();
            $(`#item_${_id}`).removeAttr('hidden');
            $(`#item_${_id}`).show();
        }
    }
    
    function openSelect(i){
        let $select2Container = $(`#number-${i}`).next('.select2-container');
        $select2Container.show();
        
        // Open Select2 dropdown to focus the search field automatically
        $(`#number-${i}`).select2('open');
        
    }

    function openNewEditSelect(i){
        let $select2Container = $(`#new_number_${i}`).next('.select2-container');
        $select2Container.show();
        
        // Open Select2 dropdown to focus the search field automatically
        $(`#new_number_${i}`).select2('open');
        
    }

    function openEditSelect(i){
        let $select2Container = $(`#number_${i}`).next('.select2-container');
        $select2Container.show();
        
        // Open Select2 dropdown to focus the search field automatically
        $(`#number_${i}`).select2('open');
        
    }
    

    function populateNormalSelect(_selector, _model_id, _url, params = {}, _id = null)
    {
        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "POST",
            url: _url,
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            data: JSON.stringify(params),
            success: function(data) {
            
                var len = data.data.length;
                
                for( var k = 0; k<len; k++){
                    var kid = data.data[k]['id'];
                    var kname = data.data[k]['name'];
                    if(kid==_id){$(_selector).append("<option selected value='"+kid+"'>"+kname+"</option>");}
                    else{
                        $(_selector).append("<option value='"+kid+"'>"+kname+"</option>");
                    }
                }
            },
            error: function(msg) {
                handleErrorResponse(msg);
            }
        });
    }

    function searchSelect2ApiDrug(_selector, _modal_id, _med_id = null)
    {
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
                url: "/admin/medications/getNames",
                dataType: "json",
                type: "POST",
                data: function (params) {
                    var queryParameters = {
                        term: params.term,
                        limit: 10
                    }
                    if(_med_id != null) {
                        var q = { med_id: _med_id, not: 'med_id' };
                        queryParameters = {...queryParameters, ...q}
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

    function reloadDataTable()
    {
        table_supply_order.ajax.reload(null, false);
        loadCard();
    }

    function loadCard() {
        var month = $('#months').val();
        var year = $('#years').val();
        
        var data = { month: month, year: year };
        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            url: "{{ route('supplyOrder.index') }}",
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

    // TASK ORDER - NEW REQUEST
    function order701(data, year, month ) {
        render($('#order-701'), $('#order-701-count'), data, year, month);
    }
    // TASK ORDER - RECEIVED
    function order702(data, year, month ) {
        render($('#order-702'), $('#order-702-count'), data, year, month);
    }
    // TASK ORDER - IN TRANSIT
    function order703(data, year, month ) {
        render($('#order-703'), $('#order-703-count'), data, year, month);
    }
    // TASK ORDER - SUBMITTED
    function order704(data, year, month ) {
        render($('#order-704'), $('#order-704-count'), data, year, month);
    }
    // TASK ORDER - MISSING ORDER
    function order705(data, year, month ) {
        render($('#order-705'), $('#order-705-count'), data, year, month);
    }
    // TASK ORDER - COMPLETED
    function order706(data, year, month ) {
        render($('#order-706'), $('#order-706-count'), data, year, month);
    }

    
    function render(container, count, data, year, month) {
        const $container = container;
        const $countElement = count;

        let html = '';
        let itemCount = 0;

        data.forEach(data => {
            itemCount++;
            html += `
                <div class="hover-card card" data-supply-id="${data.id}" data-order-number="${data.order_number}" data-order-date="${data.order_date}" data-comments="${data.comments}" data-wholesaler-id="${data.wholesaler_id}">
                    <div class="card-body" data-supply-items="${encodeURIComponent(JSON.stringify(data))}" onclick="showGridViewModal(this, ${data.id})">
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
                    <div class="dropdown dots">
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

        $container.html(html || '<p class="fst-italic">No record found.</p>');
        $countElement.text(itemCount);

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
                    
                    let formData = new FormData();
                    const supplyId = ui.item.data('supply-id');
                    const order_number = ui.item.data('order-number');
                    const comments = ui.item.data('comments');
                    const wholesaler_id = ui.item.data('wholesaler-id');
                    const order_date = ui.item.data('order-date');
                    let statusId = $(ui.item[0]).closest('.lists').attr('id');
                    statusId = statusId.slice(6);
                    let data = {
                        order: {
                            id: supplyId,
                            status_id: statusId,
                            order_number: order_number,
                            order_date: order_date,
                            comments: comments,
                            wholesaler_id: wholesaler_id
                        }
                    };
                    formData.append("data", JSON.stringify(data));
                    sweetAlertLoading();
                    // Send an AJAX request to update the supply status
                    $.ajax({
                        //laravel requires this thing, it fetches it from the meta up in the head
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        type: "POST",
                        url: `/store/procurement/pharmacy/supply-orders/edit`,
                        data: formData,
                        contentType: false,
                        processData: false,
                        dataType: "json",
                        success: function(data) {
                            Swal.fire({
                                position: 'center',
                                icon: data.status,
                                title: data.message,
                                showConfirmButton: false,
                                timer: 1500
                            });
                            // Handle success response
                            console.log(data.message);
                            reloadDataTable();
                            isAjaxRequestInProgress = false;
                        },error: function(msg) {
                            handleErrorResponse(msg);
                            //general error
                            console.log("Error");
                            console.log(msg.responseText);
                        }
                    });
                }
            }
        }).disableSelection();
    });

    $('#add_supply_order_modal #wholesaler_id').off('change').on('change', function() {
        let selectedValue = $(this).val();
        console.log(selectedValue);
        
        // if (selectedValue === '3') {
        //     $('#add_supply_order_modal .supplies-url-holder').hide();
        // } else {
        //     $('#add_supply_order_modal .supplies-url-holder').show();
        // }

        if (selectedValue === '7') {
            $('#add_supply_order_modal .hide-for-url').hide();
            $('#add_supply_order_modal .supplies-url-holder').show();
        } else {
            $('#add_supply_order_modal .hide-for-url').show();
            $('#add_supply_order_modal .supplies-url-holder').hide();
        }


    });
</script>
@stop