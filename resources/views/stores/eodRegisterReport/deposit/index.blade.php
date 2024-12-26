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
                        <table id="table" class="table row-border table-hover" style="width:100%">
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
        @include('stores/eodRegisterReport/deposit/modal/add-form')
        @include('stores/eodRegisterReport/deposit/modal/edit-form')
        @include('stores/eodRegisterReport/deposit/modal/delete-form')
        
    </div>
    <!--end page wrapper -->
@stop
@section('pages_specific_scripts')

<script>
    let dt_table;
    let file_table;
    let menu_store_id = {{request()->id}};

    // Touch event handlers
    function touchHandler(event) {
        var touches = event.changedTouches,
            first = touches[0],
            type = "";

        switch (event.type) {
            case "touchstart": type = "mousedown"; break;
            case "touchmove":  type = "mousemove"; break;        
            case "touchend":   type = "mouseup"; break;
            default: return;
        }

        // Create and dispatch a mouse event
        var simulatedEvent = document.createEvent("MouseEvent");
        simulatedEvent.initMouseEvent(type, true, true, window, 1, 
                                      first.screenX, first.screenY, 
                                      first.clientX, first.clientY, false, 
                                      false, false, false, 0/*left*/, null);

        first.target.dispatchEvent(simulatedEvent);
        event.preventDefault();
    }

    function showAddNewForm(){  
        $signaturePad = $('#signature_pad').signature({
            syncField: '#signature',
            syncFormat: 'PNG',
            'UndoButton':true
        });
        
        let fileInput = $('<input/>', {
            id: 'file',
            class: 'imageuploadify-file-general-class',
            name: 'file',
            type: 'file',
            accept: '*',
            multiple: ''
        });
        $('#add_modal #for-file').html(fileInput); 
        $('#add_modal #file').imageuploadify();
        
        $("#add_modal .imageuploadify-container").remove();
        $('#add_modal .imageuploadify-message').html('Drag&Drop Your File(s) Here To Upload');     
        $('#add_modal').modal('show');
        $('.datepicker').datepicker({
            format: "yyyy-mm-dd",
            todayHighlight: true,
            uiLibrary: 'bootstrap5',
            modal: true,
            icons: {
                rightIcon: '<i class="material-icons"></i>'
            },
            showRightIcon: false,
            autoclose: true,
   			orientation: "right",
        });
    }

    $('#edit_modal').on('hidden.bs.modal', function(){
        $('.error_txt').remove();
        $(this)
        .find("input,textarea,select")
        .val('')
        .end();    
    });

    $('#add_modal').on('hidden.bs.modal', function(){
        $('.error_txt').remove();
        $(this)
        .find("input,textarea,select")
        .val('')
        .end();   
        $('#add_modal #file').remove();
        $('#add_modal .imageuploadify').remove();
    });

    $(document).ready(function() {     
        var $signaturePad;

        const table = $('#table').DataTable({
            scrollX: true,
            serverSide: true,
            stateSave: true,
            dom: 'fBtip',
            order: [[5, 'desc']],
            buttons: [
                @can('menu_store.eod_register_report.register.create')
                { text: '+ New Deposit', className: 'btn btn-primary', action: function ( e, dt, node, config ) {
                    showAddNewForm();
                }},
                @endcan
            ],
            pageLength: 50,
            searching: true,
            ajax: {
                url: `/store/eod-register-report/deposit/data`,
                type: "GET",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: function (data) {
                    data.search = $('input[type="search"]').val();
                    data.pharmacy_store_id = menu_store_id;
                },
                error: function(msg)
                {
                    handleErrorResponse(msg);
                }
            },
            columns: [
                { data: 'date', name: 'date', title: 'Date', 
                    render: function(data, type, row) {
                        return `${row.formatted_date}`;
                    } 
                },
                { data: 'time', name: 'time', title: 'Time', 
                    render: function(data, type, row) {
                        return `${row.formatted_time}`;
                    } 
                },
                { data: 'amount', name: 'amount', title: 'Amount', 
                    render: function(data, type, row) {
                        return `${row.formatted_amount}`;
                    } 
                },
                { data: 'firstname', name: 'firstname', title: 'First Name' },
                { data: 'lastname', name: 'lastname', title: 'Last Name' },
                { data: 'created_at', name: 'created_at', title: 'Date Created', 
                    render: function(data, type, row) {
                        return `${row.formatted_created_at}`;
                    } 
                },
                { data: 'created_by', name: 'created_by', title: 'Created By', orderable: false, searchable: false},
                { data: 'actions', name: 'actions', title: 'Action', orderable: false, searchable: false},
            ],
            initComplete: function( settings, json ) {
                selected_len = table.page.len();
				$('#length_change').append($('<option>', {value: 5,text: 'Show 5',selected:(selected_len == "5") ?true: false}));
				$('#length_change').append($('<option>', {value: 10,text: 'Show 10',selected:(selected_len == "10") ?true: false}));
				$('#length_change').append($('<option>', {value: 50,text: 'Show 50',selected:(selected_len == "50") ?true: false}));
				$('#length_change').append($('<option>', {value: 100,text: 'Show 100',selected:(selected_len == "100") ?true: false}));
				$('#length_change').append($('<option>', {value: 1000,text: 'Show 1000',selected:(selected_len == "1000") ?true: false}));
            }
        });

        dt_table = table;

        // Placement controls for Table filters and buttons
		table.buttons().container().appendTo( '.dt-card-header' ); 
        $('#search_input').val(table.search());
		$('#search_input').keyup(function(){ table.search($(this).val()).draw() ; })
	    $('#length_change').change( function() { table.page.len($(this).val()).draw() });

        // Bind touch events to the signature pad
        document.getElementById("signature_pad").addEventListener("touchstart", touchHandler, true);
        document.getElementById("signature_pad").addEventListener("touchmove", touchHandler, true);
        document.getElementById("signature_pad").addEventListener("touchend", touchHandler, true);
        document.getElementById("signature_pad").addEventListener("touchcancel", touchHandler, true);

        // Bind touch events to the signature pad
        document.getElementById("esignature_pad").addEventListener("touchstart", touchHandler, true);
        document.getElementById("esignature_pad").addEventListener("touchmove", touchHandler, true);
        document.getElementById("esignature_pad").addEventListener("touchend", touchHandler, true);
        document.getElementById("esignature_pad").addEventListener("touchcancel", touchHandler, true);

    });

    function reloadDataTable()
    {
        dt_table.ajax.reload(null, false);
    }

    function downloadPDF(id)
    {
        const pdfWindow = window.open('/store/eod-register-report/deposit/download/'+id, '_blank');
    }

    function generatePDFWithoutSaving(event, type = 'add', id = null)
    {
        event.preventDefault();

        var data = {
            id: id
        };

        $(`#${type}_modal input, #${type}_modal textarea, #${type}_modal select`).each(function() {
            data[this.id] = this.value;
        });

        var signatureData = $signaturePad.signature('toDataURL', 'image/png');
        var base64Data = signatureData.split(',')[1];
        data['signature'] = base64Data;

        if(type == 'edit'){
            // Select the div element
            var div = document.getElementById('edit-signature-pad');

            // Get the computed style of the div
            var style = window.getComputedStyle(div);

            // Check if the display property is block
            if (style.display == 'block') {
                data['id'] = 0;
            }
        }

        sweetAlertLoading();
        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "POST",
            url: "/store/eod-register-report/deposit/pdf/"+id,
            data: JSON.stringify(data),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(res) {
                console.log(res);
                const pdfWindow = window.open(res.pdf_url, '_blank');
                pdfWindow.onload = function() {
                    pdfWindow.print();
                };
                swal.close();
            },error: function(msg) {
                $("#delete_form_modal #delete_btn").val('DELETE').removeAttr('disabled');
                handleErrorResponse(msg);
                console.log(msg.responseText);
            }

        });
    }

    function generatePDF(id = null)
    {
        var data = {
            id: id
        };

        if(isTablet()) {
            downloadPDF(id);
            return;
        }

        sweetAlertLoading();
        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "POST",
            url: "/store/eod-register-report/deposit/pdf/"+id,
            data: JSON.stringify(data),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(res) {
                console.log(res);
                const pdfWindow = window.open(res.pdf_url, '_blank');
                pdfWindow.onload = function() {
                    pdfWindow.print();
                };
                swal.close();
            },error: function(msg) {
                $("#delete_form_modal #delete_btn").val('DELETE').removeAttr('disabled');
                console.log(msg);
                // alert(msg.message);
                handleErrorResponse(msg);
            }

        });
    }

    function isTablet() {
        const userAgent = navigator.userAgent.toLowerCase();
        // const res =  /ipad|android(?!.*mobi)/i.test(userAgent);
        const res =  /iPad/.test(navigator.userAgent) || 
                       (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);
        return res;
    }
    
</script>
@stop