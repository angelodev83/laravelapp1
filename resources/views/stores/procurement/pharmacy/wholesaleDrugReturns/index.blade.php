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
                                <table id="dt_drug_order_items_table" class="table row-border hover" style="width:100%">
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
                        <div id="return-container" class="gap-3 d-flex container-lists">
                            <!-- LABEL TO BE CREATED SECTION -->
                            <div class="card rounded-4 bg-body-secondary" style="min-width: 24rem;">
                                <div class="p-1">
                                    <div class="px-3 pt-3 mb-2 d-flex justify-content-between">
                                        <div class="gap-2 d-flex align-items-center">
                                            <h6 class="p-2 text-white rounded-3 card-title fs-6 bg-secondary">LABEL TO BE CREATED</h6>
                                            <span id="return-301-count" class="fs-6 fw-medium"></span>
                                        </div>
                                        <button onclick="showAddModal()" class="btn fw-medium">
                                            <i class="fa-solid fa-plus"></i>
                                            New Return
                                        </button>
                                    </div>
                                    <div id="return-301" class="px-3 mb-3 content lists"></div>
                                </div>
                            </div>
                            <!-- LABEL CREATED SECTION -->
                            <div class="bg-blue-200 card rounded-4" style="min-width: 24rem;">
                                <div class="p-1">
                                    <div class="px-3 pt-3 mb-2 d-flex justify-content-between">
                                        <div class="gap-2 d-flex align-items-center">
                                            <h6 class="p-2 text-white bg-blue-500 rounded-3 card-title fs-6">LABEL CREATED</h6>
                                            <span id="return-302-count" class="fs-6 fw-medium"></span>
                                        </div>
                                        <button onclick="showAddModal()" class="btn fw-medium">
                                            <i class="fa-solid fa-plus"></i>
                                            New Return
                                        </button>
                                    </div>
                                    <div id="return-302" class="px-3 mb-3 content lists"></div>
                                </div>
                            </div>
                            <!-- LABEL PRINTED SECTION -->
                            <div class="bg-jade-200 card rounded-4" style="min-width: 24rem;">
                                <div class="p-1">
                                    <div class="px-3 pt-3 mb-2 d-flex justify-content-between">
                                        <div class="gap-2 d-flex align-items-center">
                                            <h6 class="p-2 text-white bg-jade-500 rounded-3 card-title fs-6">LABEL PRINTED</h6>
                                            <span id="return-303-count" class="fs-6 fw-medium"></span>
                                        </div>
                                        <button onclick="showAddModal()" class="btn fw-medium">
                                            <i class="fa-solid fa-plus"></i>
                                            New Return
                                        </button>
                                    </div>
                                    <div id="return-303" class="px-3 mb-3 content lists"></div>
                                </div>
                            </div>
                            <!-- PICKED UP ORDERS SECTION -->
                            <div class="bg-yellow-200 card rounded-4" style="min-width: 24rem;">
                                <div class="p-1">
                                    <div class="px-3 pt-3 mb-2 d-flex justify-content-between">
                                        <div class="gap-2 d-flex align-items-center">
                                            <h6 class="p-2 text-white bg-yellow-500 rounded-3 card-title fs-6">PICKED UP ORDERS</h6>
                                            <span id="return-304-count" class="fs-6 fw-medium"></span>
                                        </div>
                                        <button onclick="showAddModal()" class="btn fw-medium">
                                            <i class="fa-solid fa-plus"></i>
                                            New Return
                                        </button>
                                    </div>
                                    <div id="return-304" class="px-3 mb-3 content lists"></div>
                                </div>
                            </div>
                            <!-- IN TRANSIT ORDERS SECTION -->
                            <div class="bg-lawn-green-200 card rounded-4" style="min-width: 24rem;">
                                <div class="p-1">
                                    <div class="px-3 pt-3 mb-2 d-flex justify-content-between">
                                        <div class="gap-2 d-flex align-items-center">
                                            <h6 class="p-2 text-white bg-lawn-green-500 rounded-3 card-title fs-6">IN TRANSIT ORDERS</h6>
                                            <span id="return-305-count" class="fs-6 fw-medium"></span>
                                        </div>
                                        <button onclick="showAddModal()" class="btn fw-medium">
                                            <i class="fa-solid fa-plus"></i>
                                            New Return
                                        </button>
                                    </div>
                                    <div id="return-305" class="px-3 mb-3 content lists"></div>
                                </div>
                            </div>
                            <!-- PENDING ORDERS SECTION -->
                            <div class="bg-yellow-200 card rounded-4" style="min-width: 24rem;">
                                <div class="p-1">
                                    <div class="px-3 pt-3 mb-2 d-flex justify-content-between">
                                        <div class="gap-2 d-flex align-items-center">
                                            <h6 class="p-2 text-white bg-yellow-500 rounded-3 card-title fs-6">PENDING ORDERS</h6>
                                            <span id="return-306-count" class="fs-6 fw-medium"></span>
                                        </div>
                                        <button onclick="showAddModal()" class="btn fw-medium">
                                            <i class="fa-solid fa-plus"></i>
                                            New Return
                                        </button>
                                    </div>
                                    <div id="return-306" class="px-3 mb-3 content lists"></div>
                                </div>
                            </div>
                            <!-- ON HOLD ORDERS SECTION -->
                            <div class="bg-red-200 card rounded-4" style="min-width: 24rem;">
                                <div class="p-1">
                                    <div class="px-3 pt-3 mb-2 d-flex justify-content-between">
                                        <div class="gap-2 d-flex align-items-center">
                                            <h6 class="p-2 text-white bg-red-500 rounded-3 card-title fs-6">ON HOLD ORDERS</h6>
                                            <span id="return-307-count" class="fs-6 fw-medium"></span>
                                        </div>
                                        <button onclick="showAddModal()" class="btn fw-medium">
                                            <i class="fa-solid fa-plus"></i>
                                            New Return
                                        </button>
                                    </div>
                                    <div id="return-307" class="px-3 mb-3 content lists"></div>
                                </div>
                            </div>
                            <!-- DELIVERED ORDERS SECTION -->
                            <div class="bg-lawn-green-200 card rounded-4" style="min-width: 24rem;">
                                <div class="p-1">
                                    <div class="px-3 pt-3 mb-2 d-flex justify-content-between">
                                        <div class="gap-2 d-flex align-items-center">
                                            <h6 class="p-2 text-white bg-lawn-green-500 rounded-3 card-title fs-6">DELIVERED ORDERS</h6>
                                            <span id="return-308-count" class="fs-6 fw-medium"></span>
                                        </div>
                                        <button onclick="showAddModal()" class="btn fw-medium">
                                            <i class="fa-solid fa-plus"></i>
                                            New Return
                                        </button>
                                    </div>
                                    <div id="return-308" class="px-3 mb-3 content lists"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
         </div>
    </div>
    @include('sweetalert2/script')
    @include('stores/procurement/pharmacy/wholesaleDrugReturns/modals/add')
    @include('stores/procurement/pharmacy/wholesaleDrugReturns/modals/edit')
    @include('stores/procurement/pharmacy/wholesaleDrugReturns/modals/delete')
    @include('stores/procurement/pharmacy/wholesaleDrugReturns/modals/upload-form')
</div>
<!--end page wrapper -->
@stop
@section('pages_specific_scripts')

<script>
    let menu_store_id = {{request()->id}};
    let table_drug_order;

    $('#add_wholesale_drug_return_modal').on('hidden.bs.modal', function(){
        $('.error_txt').remove();
        $(this)
        .find("input,textarea,select")
        .val('')
        .end();    
    });

    $('#edit_wholesale_drug_return_modal').on('hidden.bs.modal', function(){
        $('.error_txt').remove();
        $(this)
        .find("input,textarea,select")
        .val('')
        .end();    
    });

    $(document).ready(function() {
        menu_store_id = {{request()->id}};
        loadReturnItems();

        // new PerfectScrollbar('#bulletin-tasks-recent');
        new PerfectScrollbar('#return-container');
        new PerfectScrollbar('#return-301');
        new PerfectScrollbar('#return-302');
        new PerfectScrollbar('#return-303');
        new PerfectScrollbar('#return-304');
        new PerfectScrollbar('#return-305');
        new PerfectScrollbar('#return-306');
        new PerfectScrollbar('#return-307');
        new PerfectScrollbar('#return-308');

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

    function loadReturnItems() 
    {
        let data = {};        
        const staff_table = $('#dt_drug_order_items_table').DataTable({
            scrollX: true,
            serverSide: true,
            stateSave: true,
            pageLength: 50,
            dom: 'fBtip',
            buttons: [
                { text: 'New Return', className: 'btn btn-primary', action: function ( e, dt, node, config ) {
                    showAddModal();
                }},
            ],
            searching: true,
            destroy: true,
            ajax: {
                url: "/store/procurement/pharmacy/wholesale-drug-returns/data",
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
                { data: 'reference_number', name: 'reference_number', title: 'REF NO.'},
                // { data: 'date_filed', name: 'date_filed', title: 'FILED DATE' },
                { data: 'drugname', name: 'drugname', title: 'DRUG NAME' },
                { data: 'dispense_quantity', name: 'dispense_quantity', title: 'DISPENSE QTY' },
                { data: 'price', name: 'price', title: 'PRICE' },
                { data: 'reject_comments', name: 'reject_comments', title: 'REJECT COMMENT' },
                // { data: 'shipment_tracking_number', name: 'shipment_tracking_number', title: 'TRACKING NO.' },
                { data: 'shipment_status', name: 'shipment_status', title: 'SHIPMENT', render: function(data, type, row) {
                    return '<div>' + row.status + '</div>';
                } },
                { data: 'actions', name: 'actions', title: 'Action' , orderable: false},
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

        table_drug_order = staff_table;
        table_drug_order.buttons().container().appendTo( '.card-index-header' );
        $('#search_input').val(table_drug_order.search());
        $('#search_input').keyup(function(){ table_drug_order.search($(this).val()).draw() ; })
        $('#length_change').change( function() { table_drug_order.page.len($(this).val()).draw() });
    }

    function showAddModal()
    {
        for (let b = 0; b < 3; b++){
            searchSelect2ApiDrug(`#med_id-${b}`, 'add_wholesale_drug_return_modal');
        }

        $(`[name='shipment_status_id']`).empty();
        searchSelect2Api('patient_id', 'add_wholesale_drug_return_modal', "/admin/patient/getNames", {source: 'pioneer'});
        populateNormalSelect(`[name='shipment_status_id']`, 'edit_wholesale_drug_return_modal', '/admin/search/store-status', {category: 'shipment'}, 301)

        $('#add_wholesale_drug_return_modal').modal('show');
    }
    
    function showEditModal(id)
    {
        var btn = document.querySelector(`#wholesale-drug-return-edit-btn-${id}`);
        let array = JSON.parse(btn.dataset.array);

        $(`[name='emed_id']`).append("<option selected value='"+array.drugid+"'>"+array.drugname+"</option>");
        searchSelect2ApiDrug(`[name='emed_id']`, 'edit_wholesale_drug_return_modal', array.drugid);

        $(`[name='ereference_number']`).val(array.reference_number);
        $(`[name='ereject_comments']`).val(array.reject_comments);
        $(`[name='edispense_quantity']`).val(array.dispense_quantity);
        $(`[name='endc']`).val(array.ndc);
        $(`[name='eprescriber_name']`).val(array.prescriber_name);
        $(`[name='eid']`).val(array.id);
        $(`[name='einventory_type']`).val(array.inventory_type);
        $(`[name='epatient_name']`).val(array.patient_firstname+' '+array.patient_lastname);
        $(`[name='eshipment_status_id']`).empty();

        populateNormalSelect(`[name='eshipment_status_id']`, 'edit_wholesale_drug_return_modal', '/admin/search/store-status', {category: 'shipment'}, array.shipment_status_id)

        $('#edit_wholesale_drug_return_modal').modal('show');
    }

    function showGridEditModal(items, id)
    {
        data = items.getAttribute('data-return-items');
        let array = JSON.parse(decodeURIComponent(data));
        console.log(array);
        $(`[name='ereference_number']`).val(array.reference_number);
        $(`[name='ereject_comments']`).val(array.reject_comments);
        $(`[name='eprescriber_name']`).val(array.prescriber_name ?? '');
        $(`[name='epatient_name']`).val(array.patient_name);
        $(`[name='eshipment_status_id']`).empty();
        
        array.items.forEach(meds => {
            if (meds.id == id) {
                $(`[name='eid']`).val(meds.id);
                $(`[name='emed_id']`).append("<option selected value='"+meds.medication.med_id+"'>"+meds.medication.name+"</option>");
                $(`[name='endc']`).val(meds.ndc);
                $(`[name='einventory_type']`).val(meds.inventory_type);
                $(`[name='edispense_quantity']`).val(meds.dispense_quantity);
                searchSelect2ApiDrug(`[name='emed_id']`, 'edit_wholesale_drug_return_modal', meds.med_id);
            }
        });

        populateNormalSelect(`[name='eshipment_status_id']`, 'edit_wholesale_drug_return_modal', '/admin/search/store-status', {category: 'shipment'}, array.shipment_status_id)

        $('#edit_wholesale_drug_return_modal').modal('show');
    }

    function searchSelect2Api(_select_id, _modal_id, _url, condition = null)
    {
        $(`#${_select_id}`).select2( {
            theme: "bootstrap-5",
            width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
            placeholder: $( this ).data( 'placeholder' ),
            closeOnSelect: true,
            dropdownParent: $(`#${_modal_id}`),

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
        console.log("fire")
        $(_selector).select2( {
            theme: "bootstrap-5",
            width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
            placeholder: $( this ).data( 'placeholder' ),
            closeOnSelect: true,
            dropdownParent: $(`#${_modal_id}`),

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

    function reloadDataTable() {
        loadCard();
    }

    function loadCard() {
        var month = $('#months').val();
        var year = $('#years').val();
        
        var data = { month: month, year: year };
        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            url: "{{ route('wholesaleDrugReturn.index') }}",
            type: "POST",
            dataType: "json",
            data: data,
            success: function(response) {
                return301(response.data['return301'], response.data['selectedYear'], response.data['selectedMonth']);
                return302(response.data['return302'], response.data['selectedYear'], response.data['selectedMonth']);
                return303(response.data['return303'], response.data['selectedYear'], response.data['selectedMonth']);
                return304(response.data['return304'], response.data['selectedYear'], response.data['selectedMonth']);
                return305(response.data['return305'], response.data['selectedYear'], response.data['selectedMonth']);
                return306(response.data['return306'], response.data['selectedYear'], response.data['selectedMonth']);
                return307(response.data['return307'], response.data['selectedYear'], response.data['selectedMonth']);
                return308(response.data['return308'], response.data['selectedYear'], response.data['selectedMonth']);
            },
            error: function(xhr, status, error) {
                handleErrorResponse(error);
                console.log('Error:', error);
            }
        });
    };
    // TASK RETURN - LABEL TO BE CREATED
    function return301(data, year, month) {
        render($('#return-301'), $('#return-301-count'), data, year, month);
    }
    // TASK RETURN - LABEL PRINTED
    function return302(data, year, month) {
        render($('#return-302'), $('#return-302-count'), data, year, month);
    }
    // TASK RETURN - LABEL PRINTED
    function return303(data, year, month) {
        render($('#return-303'), $('#return-303-count'), data, year, month);
    }
    // TASK RETURN - PICKUP ORDERS
    function return304(data, year, month) {
        render($('#return-304'), $('#return-304-count'), data, year, month);
    }
    // TASK RETURN - IN TRANSIT ORDERS
    function return305(data, year, month) {
        render($('#return-305'), $('#return-305-count'), data, year, month);
    }
    // TASK RETURN - PENDING ORDERS
    function return306(data, year, month) {
        render($('#return-306'), $('#return-306-count'), data, year, month);
    }
    // TASK RETURN - ON HOLD ORDERS
    function return307(data, year, month) {
        render($('#return-307'), $('#return-307-count'), data, year, month);
    }
    // TASK RETURN - DELIVERED ORDERS
    function return308(data, year, month) {
        render($('#return-308'), $('#return-308-count'), data, year, month);
    }

    function render(container, count, data, year, month) {
        const $container = container;
        const $countElement = count;

        let html = '';
        let itemCount = 0;

        data.forEach(returnItems => {
            returnItems.items.forEach(item => {
                if (returnItems.id === item.return_id) {
                    itemCount++;
                    html += `
                        <div class="hover-card card" data-return-id="${item.id}">
                            <div class="card-body" data-return-items="${encodeURIComponent(JSON.stringify(returnItems))}" onclick="showGridEditModal(this, ${item.id})">
                                <h6 class="card-title">${returnItems.reference_number}</h6>
                                <div>
                                    <p class="pt-1 pb-2 card-subtitle text-body-secondary fw-medium">${item.medication.name}</p>
                                    <div class="d-flex gap-2 align-items-end">
                                        <div class="px-1 border border-2 rounded border-secondary">
                                            <i class="fa-solid fa-hashtag text-body-secondary"></i>
                                            <span class="text-success">${item.dispense_quantity}
                                        </div>
                                        <div class="px-1 border border-2 rounded border-secondary">
                                            <i class="fa-solid fa-dollar-sign text-body-secondary"></i>
                                            <span class="text-success">${item.inventory_type === 'RX' ? item.medication.rx_price : item.medication['340b_price']}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                }
            });
        });

        $container.html(html || '<p class="fst-italic">No record found.</p>');
        $countElement.text(itemCount);
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

                    let returnId = ui.item.data('return-id');
                    let statusId = $(ui.item[0]).closest('.lists').attr('id');
                    statusId = statusId.slice(7);
                    let data = {
                        order: {
                            shipment_status_id: statusId,
                        },
                        items: {
                            id: returnId
                        }
                    };
                    sweetAlertLoading();
                    // Send an AJAX request to update the supply status
                    $.ajax({
                        //laravel requires this thing, it fetches it from the meta up in the head
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        type: "POST",
                        url: `/store/procurement/pharmacy/wholesale-drug-returns/edit`,
                        data: JSON.stringify(data),
                        contentType: "application/json; charset=utf-8",
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
</script>
@stop