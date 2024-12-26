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
                                <table id="dt_drug_order_items_table" class="table row-border table-hover" style="width:100%">
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
    @include('stores/procurement/pharmacy/drugOrders/modals/add')
    @include('stores/procurement/pharmacy/drugOrders/modals/edit')
    @include('stores/procurement/pharmacy/drugOrders/modals/delete')
    @include('stores/procurement/pharmacy/drugOrders/modals/show')
    @include('stores/procurement/pharmacy/drugOrders/modals/upload-form')
</div>
<!--end page wrapper -->
@stop

@section('pages_specific_scripts')

<script>
    let menu_store_id = {{request()->id}};
    let table_drug_order;

    $('#searchInput').on('keyup', function() {
        var value = $(this).val().toLowerCase();
        $('#edit_drug_order_modal #drug_order_item_tbody tr').filter(function() {
            $(this).toggle($(this).find('input[name^="product_description"]').val().toLowerCase().indexOf(value) > -1)
        });
    });

    $('#add_drug_order_modal').on('hidden.bs.modal', function(){
        $(".imageuploadify-container").remove();  
        $('.error_txt').remove();
        $(this)
        .find("input,textarea,select")
        .val('')
        .end();    
    });

    $('#edit_drug_order_modal').on('hidden.bs.modal', function(){
        $('.error_txt').remove();
        $(this)
        .find("input,textarea,select")
        .val('')
        .end();    
    });

    $(document).ready(function() {
        menu_store_id = {{request()->id}};

        tinymce.init({
		  selector: 'textarea.tinymce-content',
          height: 225,
          branding: false
		});

        // $('.imageuploadify-file-general-class').imageuploadify();

        // new PerfectScrollbar('#bulletin-tasks-recent');
        new PerfectScrollbar('#order-container');
        new PerfectScrollbar('#order-701');
        new PerfectScrollbar('#order-702');
        new PerfectScrollbar('#order-703');
        new PerfectScrollbar('#order-704');
        new PerfectScrollbar('#order-705');
        new PerfectScrollbar('#order-706');

        loadOrderItems();

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
        const staff_table = $('#dt_drug_order_items_table').DataTable({
            scrollX: true,
            serverSide: true,
            stateSave: false,
            pageLength: 10,
            order: [[6, 'desc']],
            dom: 'fBtp',
            buttons: [
                @can('menu_store.procurement.pharmacy.drug_orders.create')
                { text: '+ New Order', className: 'btn btn-primary', action: function ( e, dt, node, config ) {
                    showAddModal();
                }},
                @endcan
            ],
            searching: true,
            destroy: true,
            ajax: {
                url: "/store/procurement/pharmacy/drug-orders/data",
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
                { data: 'order_number', name: 'order_number', title: 'PO NAME', render: function(data, type, row) {
                    return `<div class="dt-procurement-order-number" onclick="showViewDetailsModal(${row.id})" style="cursor: pointer;">${data}</div>`;
                } },
                { data: 'status', name: 'status', title: 'STATUS' , orderable: false, searchable: false},
                { data: 'order_date', name: 'order_date', title: 'ORDER DATE' },
                // { data: 'po_name', name: 'po_name', title: 'PO NAME' },
                { data: 'account_number', name: 'account_number', title: 'ACCOUNT #' },
                { data: 'wholesaler_name', name: 'wholesaler_name', title: 'WHOLESALER' },
                { data: 'po_memo', name: 'po_memo', title: 'PO MEMO' },
                { data: 'created_at', name: 'created_at', title: 'DATE CREATED' },
                { data: 'actions', name: 'actions', title: 'Action' , orderable: false, searchable: false}
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
        $(".imageuploadify-container").remove(); 
        $('#add_drug_order_modal #order_date').datepicker({
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

        $(`[name='status_id']`).empty();
        $(`[name='wholesaler_id']`).empty();
        searchSelect2Api('patient_id', 'add_drug_order_modal', "/admin/patient/getNames", {source: 'pioneer'});
        populateNormalSelect(`[name='status_id']`, '#add_drug_order_modal', '/admin/search/store-status', {category: 'procurement_order'}, 701)
        populateNormalSelect(`[name='wholesaler_id']`, '#add_drug_order_modal', '/admin/search/wholesaler', {category: 'procurement'}, 6)

        $('#add_drug_order_modal').modal('show');
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
        table_drug_order.ajax.reload(null, false);
        loadCard();
    }

    function loadCard() {
        var month = $('#months').val();
        var year = $('#years').val();
        
        var data = { month: month, year: year };
        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            url: "{{ route('drugOrder.index') }}",
            type: "POST",
            dataType: "json",
            data: data,
            success: function(response) {
                // console.log(response);
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
        const $container = container;
        const $countElement = count;

        let html = '';
        let itemCount = 0;

        data.forEach(data => {
            itemCount++;
            html += `
                <div class="hover-card card" data-drug-id="${data.id}">
                    <div class="card-body drug-items" data-drug-items="${encodeURIComponent(JSON.stringify(data))}" onclick="showGridViewDetailsModal(this, ${data.id})">
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
                    const drugId = ui.item.data('drug-id');
                    let statusId = $(ui.item[0]).closest('.lists').attr('id');
                    statusId = statusId.slice(6);
                    let data = {
                        order: {
                            id: drugId,
                            status_id: statusId,
                        }
                    };
                    formData.append("data", JSON.stringify(data));
                    sweetAlertLoading();
                    // Send an AJAX request to update the drug status
                    $.ajax({
                        //laravel requires this thing, it fetches it from the meta up in the head
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        type: "POST",
                        url: `/store/procurement/pharmacy/drug-orders/edit`,
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
</script>
@stop