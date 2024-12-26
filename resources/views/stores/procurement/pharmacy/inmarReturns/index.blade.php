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
                                <table id="inmar_table" class="table row-border hover" style="width:100%">
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
                                            New Return
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
                                            New Return
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
                                            New Return
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
                                            New Return
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
                                            New Return
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
                                            New Return
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
    @include('stores/procurement/pharmacy/inmarReturns/modal/add-form')
    @include('stores/procurement/pharmacy/inmarReturns/modal/edit-form')
    @include('stores/procurement/pharmacy/inmarReturns/modal/delete-form')
    @include('stores/procurement/pharmacy/inmarReturns/modal/view-form')
    @include('stores/procurement/pharmacy/inmarReturns/modal/upload-form')
</div>
<!--end page wrapper -->
@stop
@section('pages_specific_scripts')

<script>
    let table_inmartable;
    let menu_store_id = {{request()->id}};
    
    $('.number_only').keyup(function(e){
        if (/\D/g.test(this.value))
        {
            // Filter non-digits from input value.
            this.value = this.value.replace(/\D/g, '');
        }
    });

    // $(".auto_width").on('keyup', function(){
    
    //     elementId = $(this).prop('id');

    //     let width = $(this).val().length * 10 + 25;

    //     $(this).css('width', width +"px");
    // });

    $('#addInmar_modal').on('hidden.bs.modal', function(){
        $('.error_txt').remove();
        $(this)
        .find("input,textarea,select")
        .val('')
        .end();    
        $('#addInmar_modal #wholesaler_id').empty();
    });
    
    $('#edit_inmar_return_modal').on('hidden.bs.modal', function(){
        $('.error_txt').remove();
        $(this)
        .find("input,textarea,select")
        .val('')
        .end();    
        $('#edit_inmar_return_modal #wholesaler_id').empty();
    });

    $('#viewInmar_modal').on('hidden.bs.modal', function(){
        $('.error_txt').remove();
        $(this)
        .find("p")
        .text('')
        .end();   
        $('#inmarView_table tbody').empty(); 
    });

    function showAddNewForm(){        
        $("#addInmar_modal [name='wholesaler_id']").empty();
        $('#addInmar_modal').modal('show');
        $('.datepicker').datepicker({
            format: "yyyy-mm-dd",
            todayHighlight: true,
            uiLibrary: 'bootstrap5', modal: true,
            icons: {
                rightIcon: '<i class="material-icons"></i>'
            },
            showRightIcon: false,
            autoclose: true,
   			orientation: "right",
        });
        for (b = 0; b < 3; b++){
            $( '#addInmar_modal #drugname'+b ).select2( {
                theme: "bootstrap-5",
                width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
                placeholder: $( this ).data( 'placeholder' ),
                closeOnSelect: true,
                dropdownParent: $('#addInmar_modal .modal-content'),
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
                }
            });
        }
        populateNormalSelect(`[name='wholesaler_id']`, '#addInmar_modal', '/admin/search/wholesaler', {category: 'procurement'}, 2)
    }

    function showViewForm(id, medications){
        $('#viewInmar_modal').modal('show');
        
        var btn = document.querySelector(`#inmar-show-btn-${id}`);
        let arr = JSON.parse(btn.dataset.array);
        //console.log('fire-------------',arr);
        
        $('#vinmar_id_text').html('Ref no: '+name);
        $("#vid").val(id);
        $("#vpo_name").text(arr.po_name);
        $("#vwholesaler_name").text(arr.wholesaler_name);
        $("#vaccount_number").text(arr.account_number);
        $("#vreturn_date").text(arr.return_date);
        // $("#vprescriber_name").text(prescriberName);
        // $("#vclinic_id").text(clinicName);
        // $("#vreturn_date").text(returnDate);
        $("#vcomments").text(arr.comments);
        $("#vstatus").text(arr.status_name);

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

    function showGridViewForm(items, id){
        data = items.getAttribute('data-order-items');
        let arr = JSON.parse(decodeURIComponent(data));

        $('#viewInmar_modal').modal('show');
        
        $('#vinmar_id_text').html('REF NO: '+ arr.name);
        $("#vid").val(id);
        $("#vpo_name").text(arr.po_name);
        $("#vwholesaler_name").text(arr.wholesaler.name);
        $("#vaccount_number").text(arr.account_number);
        $("#vreturn_date").text(arr.return_date);
        $("#vcomments").text(arr.comments);
        $("#vstatus").text(arr.status.name);

        arr.medications.forEach(function(med){
            $("#inmarView_table > tbody").append('<tr><td><p>'+med.drugname+'</p></td><td><p>'+med.quantity+'</p></td><td><p>'+med.ndc+'</p></td></tr>'); 
        });
    }

    function searchInmarItem(_selector, _modal_id, _i = null, _id = null, _new = null)
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

    function displaySupplyItem(data, i = null, _id = null,  _new = null)
    {
        const is_number = Number.isInteger(i);
        console.log('--fire display item',is_number, data)
        if(is_number) {
            if(Number.isInteger(_new)) {
                console.log("---FIRE for edit modal - editing new items",$(`#new_number_${_new}`).val())
                $(`#new_item_${_new}`).val(data.text);
                $(`#new_code_${_new}`).val(data.model_number);
                $(`#new_description_${_new}`).val(data.description);
            } else {
                console.log("---FIRE for add modal - items")
                $(`#item-${i}`).val(data.text);
                $(`#code-${i}`).val(data.model_number);
                $(`#description-${i}`).val(data.description);
            }
        } else {
            console.log("---FIRE for edit modal - editing exsiting items")
            $(`#item_${_id}`).val(data.text);
            $(`#code_${_id}`).val(data.model_number);
            $(`#description_${_id}`).val(data.description);
        }
    }

    function reloadDataTable()
    {
        table_inmartable.ajax.reload(null, false);
        loadCard();
    }

    $('#edit_inmar_return_modal').on('hidden.bs.modal', function(){
        $('.error_txt').remove();
        $(this)
        .find("input,textarea,select")
        .val('')
        .end();    
    });

    $('#addInmar_modal').on('hidden.bs.modal', function(){
        $('.error_txt').remove();
        $(this)
        .find("input,textarea,select")
        .val('')
        .end();   
        $("#addInmar_modal .rowToRemove").remove(); 
        medCount = 2;
    });

    $(document).ready(function() {
        
        const inmar_table = $('#inmar_table').DataTable({
            scrollX: true,
            serverSide: true,
            stateSave: true,
            dom: 'fBtip',
            order: [[6, 'desc']],
            buttons: [
                @can('menu_store.procurement.pharmacy.inmar_returns.create')
                { text: '+ New Return', className: 'btn btn-primary', action: function ( e, dt, node, config ) {
                    showAddNewForm();
                }},
                @endcan
            ],
            pageLength: 50,
            searching: true,
            ajax: {
                url: `/store/procurement/pharmacy/${menu_store_id}/inmar-returns/data`,
                type: "POST",
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
                { data: 'name', name: 'inmars.name', title: 'REF. NO.'},
                // { data: 'drug_name', name: 'drug_name', title: 'MEDICATION' },
                
                { data: 'status', name: 'store_statuses.name', title: 'STATUS' },
                { data: 'return_date', name: 'return_date', title: 'RETURN DATE' },
                { data: 'po_name', name: 'po_name', title: 'PO NAME' },
                { data: 'account_number', name: 'account_number', title: 'ACCOUNT NUMBER' },
                { data: 'wholesaler_name', name: 'wholesaler_name', title: 'WHOLESALER NAME' },
                { data: 'created_at', name: 'inmars.created_at', title: 'DATE CREATED' },
                { data: 'actions', name: 'actions', title: 'Action' , width: '18%', orderable: false, searchable: false},
            ],
            initComplete: function( settings, json ) {
                selected_len = inmar_table.page.len();
				$('#length_change').append($('<option>', {value: 5,text: 'Show 5',selected:(selected_len == "5") ?true: false}));
				$('#length_change').append($('<option>', {value: 10,text: 'Show 10',selected:(selected_len == "10") ?true: false}));
				$('#length_change').append($('<option>', {value: 50,text: 'Show 50',selected:(selected_len == "50") ?true: false}));
				$('#length_change').append($('<option>', {value: 100,text: 'Show 100',selected:(selected_len == "100") ?true: false}));
				$('#length_change').append($('<option>', {value: 1000,text: 'Show 1000',selected:(selected_len == "1000") ?true: false}));
            }
        });

        table_inmartable = inmar_table;

        // Placement controls for Table filters and buttons
		inmar_table.buttons().container().appendTo( '.dt-card-header' ); 
        $('#search_input').val(inmar_table.search());
		$('#search_input').keyup(function(){ inmar_table.search($(this).val()).draw() ; })
	    $('#length_change').change( function() { inmar_table.page.len($(this).val()).draw() });

        // perfect scroll
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

    function loadCard() {
        var month = $('#months').val();
        var year = $('#years').val();
        
        var data = { month: month, year: year };
        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            url: "{{ route('inmarReturns.index') }}",
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
            error: function(msg) {
                handleErrorResponse(msg);
                console.log('Message:', msg);
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
                <div class="hover-card card" 
                data-name="${data.name}" 
                    data-order-id="${data.id}" 
                    data-po-name="${data.po_name}" 
                    data-comments="${data.comments}" 
                    data-return-date="${data.return_date}" 
                    data-wholesaler-id="${data.wholesaler_id}" 
                    data-account-number="${data.account_number}" 
                    data-pharmacy-store-id="${data.pharmacy_store_id}">
                    <div class="card-body" data-order-items="${encodeURIComponent(JSON.stringify(data))}" onclick="showGridViewForm(this, ${data.id})">
                        <h6 class="card-title">${data.name}</h6>
                        <p class="pt-1 pb-2 card-subtitle text-body-secondary fw-medium">PO Name: ${data.po_name}</p>
                        <p class="pb-2 pb-2 card-subtitle text-body-secondary fw-medium">Acount Number: ${data.account_number}</p>
                        <div class="d-flex gap-2 align-items-end">
                            ${ 
                                data.image
                                ? `<img src="/upload/userprofile/${data.image}" class="rounded-circle" data-bs-toggle="tooltip" data-bs-title="${data.assigned_to}" style="width: 35px; height: 35px;">`
                                : `<span class="rounded-circle employee-avatar-${data.initials_random_color}-initials" data-bs-toggle="tooltip" data-bs-title="${data.assigned_to}">${data.initials}</span>`
                            }
                            <div class="px-1 border border-2 rounded border-secondary">
                                <i class="fa-solid fa-calendar-day"></i>
                                <span class="text-success">${data.return_date ?? ''}</span>
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
                    const orderId = ui.item.data('order-id');
                    const name = ui.item.data('name');
                    const po_name = ui.item.data('po-name');
                    const account_number = ui.item.data('account-number');
                    const return_date = ui.item.data('return-date');
                    const comments = ui.item.data('comments');
                    const pharmacy_store_id = ui.item.data('pharmacy-store-id');
                    const wholesaler_id = ui.item.data('wholesaler-id');
                    let statusId = $(ui.item[0]).closest('.lists').attr('id');
                    statusId = statusId.slice(6);
                    let dataArray = {
                        id: orderId,
                        name: name,
                        po_name: po_name,
                        account_number: account_number,
                        return_date: return_date,
                        comments: comments,
                        status_id: statusId,
                        wholesaler_id: wholesaler_id,
                        pharmacy_store_id: pharmacy_store_id,
                    };
                    formData.append("data", JSON.stringify(dataArray));
                    sweetAlertLoading();
                    // Send an AJAX request to update the supply status
                    $.ajax({
                        //laravel requires this thing, it fetches it from the meta up in the head
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        type: "POST",
                        url: `/store/procurement/pharmacy/inmar-returns/edit`,
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
<style>
    /* .datepicker{ z-index:99999 !important; } */
    /* .datepicker{z-index: 100000;} */

</style>
@stop