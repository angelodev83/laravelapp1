@extends('layouts.master')
@section('content')
<!--start page wrapper -->
<style>
    /* table {
    font-family: "Fraunces", serif;
    font-size: 125%;
    white-space: nowrap;
    margin: 0;
    border: none;
    border-collapse: separate;
    border-spacing: 0;
    table-layout: fixed;
    border: 1px solid black;
    }
    table td,
    table th {
    border: 1px solid black;
    padding: 0.5rem 1rem;
    }
    table thead th {
    padding: 3px;
    position: sticky;
    top: 0;
    z-index: 1;
    width: 25vw;
    background: white;
    }
    table td {
    background: #fff;
    padding: 4px 5px;
    text-align: center;
    }

    table tbody th {
    font-weight: 100;
    font-style: italic;
    text-align: left;
    position: relative;
    }
    table thead th:first-child {
    position: sticky;
    left: 0;
    z-index: 2;
    }
    table tbody th {
    position: sticky;
    left: 0;
    background: white;
    z-index: 1;
    }
    caption {
    text-align: left;
    padding: 0.25rem;
    position: sticky;
    left: 0;
    }

    [role="region"][aria-labelledby][tabindex] {
    width: 100%;
    max-height: 98vh;
    overflow: auto;
    }
    [role="region"][aria-labelledby][tabindex]:focus {
    box-shadow: 0 0 0.5em rgba(0, 0, 0, 0.5);
    outline: 0;
    } */

    table {
        border-collapse: collapse; 
        font-family: helvetica;
        caption-side: top;
        text-transform: capitalize;
    }
    caption {
        text-align: left; position: fixed; left: 0; top:0
    }
    /* td, th {border:  1px solid;
        padding: 10px;
        min-width: 200px;
        background: white;
        box-sizing: border-box;
        text-align: left;
    } */

    /* th {
        box-shadow: 0 0 0 1px black;
    } */
    
    /* .table-container {
        position: relative;
        max-height:  300px;
        width: 500px;
        overflow: scroll;
    } */

    /* thead th, 
    tfoot th {
        position: -webkit-sticky;
        position: sticky;
        top: 0;
        z-index: 2;
        background: hsl(20, 50%, 70%);
    } */

    /* thead th:first-child,
        tfoot th:first-child {
        left: 0;
        z-index: 3;
    } */

    /* tfoot {
    position: -webkit-sticky;
    bottom: 0;
    z-index: 2;
    }

    tfoot th {
    position: sticky;
    bottom: 0;
    z-index: 2;
    background: hsl(20, 50%, 70%);
    }

    tfoot td:first-child {
    z-index: 3;
    }

    tbody {
    overflow: scroll;
    height: 200px;
    } */

    /* MAKE LEFT COLUMN FIXEZ */
    /* thead th:nth-child(2){
        left: 0;
        z-index: 1;
    } */
    /* tr > :nth-child(2){
        position: -webkit-sticky;
        position: sticky; 
        left: 0;  */
        /* background: hsl(180, 50%, 70%); */
    /* } */
    

    /* don't do this */
    /* tr > :first-child {
    box-shadow: inset 0px 1px black;
    } */
    .cell-button-popover{
        background-color: transparent;
        border: none;
    }
    .cell-column-hand-pointer:hover {
        cursor: pointer;
    }
    .circle-button {
        width: 20px; 
        height: 20px; 
        border-radius: 50%; /* Makes the button circular */
        background-color: transparent; /* Background color of the button */
        color: #87909e; /* Text color */
        border: 5px #87909e; /* Remove border */
        cursor: pointer; /* Change cursor to pointer on hover */
        box-shadow: 0 0px 0px rgba(0, 0, 0, 0.2); /* Add gray shadow */
        text-align: left; 
        line-height: 22%;
    }
    .tr-circle-icon{
        margin-left: -30%;
    }

</style>
<div class="page-wrapper">
    <div class="page-content">
        <!-- PAGE-HEADER -->
        @include('layouts/pageContentHeader/store')
        <!-- PAGE-HEADER END -->
        @can('menu_store.patient_support.transfer_rx.create')
        <div class="card">
            <div class="card-body">
                <!-- <button class="btn btn-primary" onclick="showDefaultAutomationModal({{ request()->id }})">Default Automation</button> -->
                <button class="btn btn-primary" onclick="showAddForm()">Add Patient</button>
            </div>
        </div>
        @endcan
                
        @foreach ($transferTasks as $task)
            
            <div class="card" id="task{{$task->id}}_card">
                <div class="card-header" id="card_header{{$task->id}}">
                    <select name='length_change' id='length_change{{$task->id}}' class="table_length_change form-select">
                    </select>
                    <input type="text" id="table_search{{$task->id}}" class="table_search_input form-control" placeholder="Search...">
                    <button class="{{$task->class}}" style="background-color: {{$task->color}}; margin-left: 2%; color: {{$task->text_color}};"><i class="{{$task->widget_icon}}"></i> {{$task->name}}</button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="task{{$task->id}}_table" class="table row-border table-hover" style="width:100%">
                            <thead></thead>
                            <tbody>                                   
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endforeach
        
    </div>
    @include('sweetalert2/script')
    @include('executiveDashboard/trp/patientSupport/tribeMembers/modal/change-task-form')
    @include('executiveDashboard/trp/patientSupport/tribeMembers/modal/default-automation-form')
    @include('executiveDashboard/trp/patientSupport/tribeMembers/modal/edit-form')
    @include('executiveDashboard/trp/patientSupport/tribeMembers/modal/add-form')
    @include('executiveDashboard/trp/patientSupport/tribeMembers/modal/add-assignee-form')
    @include('executiveDashboard/trp/patientSupport/tribeMembers/modal/shipping-type')
    @include('executiveDashboard/trp/patientSupport/tribeMembers/modal/medication-form')


</div>
<!--end page wrapper -->

@stop
@section('pages_specific_scripts')   
<script>
    let medCount = 0;
    let transferTasks = {!! json_encode($transferTasks) !!}; 
    let tableTasks = {};
    let transferTasksArray = Object.values(transferTasks);
    // Get the current URL
    let currentUrl = window.location.href;
    // Split the URL by '/' to get individual segments
    let segments = currentUrl.split('/');
    // Find the segment containing the desired value (in this case, the segment at index 4)
    let transferListId = segments[7];
     
    let menu_store_id = {{request()->id}};
     
    // Ensure transferTasks is an array
    if (Array.isArray(transferTasksArray)) {
        // Sort transferTasks by sort_id
        transferTasksArray.sort(function(a, b) {
            let sortA = parseFloat(a.sort);
            let sortB = parseFloat(b.sort);
            return sortA - sortB;
        });
    }

    
    transferTasksArray.forEach(function(task) {
        tableTasks['table_task_' + task.id] = ''; // Initializing the tableTasks object with empty strings

        // Dynamically create variables using bracket notation
        let tableName = 'table' + task.id;
        window[tableName] = $('#task' + task.id + '_table').DataTable({
            scrollX: true,
            serverSide: true,
            stateSave: true,
            dom: 'fBtp',
            buttons: [],
            pageLength: 10,
            order: [[0, 'desc']],
            searching: true,
            ajax: {
                url: `/store/patient-support/${menu_store_id}/transfer_rx/tribe_members/get_data`,
                type: "GET",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: function (data) {
                    data.search = $('#table_search'+task.id).val();
                    data.task_id = task.id;
                    data.list_id = transferListId;
                },
                error: function (msg) {
                    handleErrorResponse(msg);
                }
            },
            columns: [
                { data: 'id', name: 'transfer_task_status_logs.id', title: 'ID', visible: true, searchable: false},
                { data: 'patient_name', name: 'patient_name', title: 'Name', visible: true, className: 'cell-column-hand-pointer fw-bold'},
                { data: 'assignee', name: 'assignee', title: 'Assignee', visible: true, orderable: false, searchable: false},
                { data: 'total_time_in_status', name: 'total_time_in_status', title: 'Total Time In Status', visible: true, orderable: false, searchable: false},
                { data: 'due_date', name: 'due_date', title: 'Due Date', visible: true, orderable: false, searchable: false},
                { data: 'shipping_type', name: 'shipping_type', title: 'Shipping Type', visible: true, orderable: false, searchable: false, className: 'cell-column-hand-pointer'},
                { data: 'affiliated', name: 'affiliated', title: 'Affiliation', visible: true},
                { data: 'birthdate', name: 'birthdate', title: 'Date of Birth', visible: true},
                { data: 'created_at', name: 'transfer_patients.created_at', title: 'Created Date', visible: true},
                { data: 'communication', name: 'communication', title: 'Preferred Communication', visible: true},
                { data: 'phone_number', name: 'phone_number', title: 'CB#', visible: true},
                { data: 'notes', name: 'notes', title: 'Notes', visible: true, orderable: false, searchable: false,
                    render: function(data, type, row) {
                        if (type === 'display') {
                            if (data === null) {
                                // Handle the case where data is null
                                return ''; // Or any other default value or behavior you prefer
                            }
                            // Replace newline characters with HTML line breaks
                            let formattedData = data.replace(/\n/g, '<br>');
                            let displayData = data.length > 20 ? data.substring(0, 20) + '...' : data;
                            return '<button class="cell-button-popover" data-toggle="popover"  data-content="' + formattedData + '">' + displayData + '</button>';
                        } 
                        return data;
                    }
                },
                { data: 'home_address', name: 'home_address', title: 'Address', visible: true},
                { data: 'city', name: 'city', title: 'City', visible: true},
                { data: 'state', name: 'state', title: 'State', visible: true},
                { data: 'county', name: 'county', title: 'County', visible: true},
                { data: 'current_pharmacy', name: 'current_pharmacy', title: 'Current Pharmacy', visible: true},
                { data: 'pharmacy_phone_number', name: 'pharmacy_phone_number', title: 'Pharmacy Phone #', visible: true},
                { data: 'pharmacy_address', name: 'pharmacy_address', title: 'Pharmacy Address', visible: true},
                { data: 'pharmacy_city', name: 'pharmacy_city', title: 'Pharmacy City', visible: true},
                { data: 'pharmacy_state', name: 'pharmacy_state', title: 'Pharmacy State', visible: true},
                { data: 'prescriber_firstname', name: 'prescriber_firstname', title: 'Prescriber First Name', visible: true},
                { data: 'prescriber_lastname', name: 'prescriber_lastname', title: 'Prescriber Last Name', visible: true},
                { data: 'prescriber_phone_number', name: 'prescriber_phone_number', title: 'Prescriber #', visible: true},
                { data: 'prescriber_fax_number', name: 'prescriber_fax_number', title: 'Prescriber Fax #', visible: true},
                { data: 'medication_details', name: 'medication_details', title: 'Medication Info', visible: true,
                    render: function(data, type, row) {
                        if (type === 'display') {
                            if (data === null) {
                                // Handle the case where data is null
                                return ''; // Or any other default value or behavior you prefer
                            }
                            // Replace newline characters with HTML line breaks
                            let formattedData = data.replace(/\n/g, '<br>');
                            let displayData = data.length > 20 ? data.substring(0, 20) + '...' : data;
                            return '<button class="cell-button-popover" data-toggle="popover"  data-content="' + formattedData + '">' + displayData + '</button>';
                        } 
                        return data;
                    }
                },
            ],
            initComplete: function( settings, data ) {
                selected_len = window[tableName].page.len();
                $('#length_change'+ task.id).append($('<option>', {value: 5,text: 'Show 5',selected:(selected_len == "5") ?true: false}));
                $('#length_change'+ task.id).append($('<option>', {value: 10,text: 'Show 10',selected:(selected_len == "10") ?true: false}));
                $('#length_change'+ task.id).append($('<option>', {value: 50,text: 'Show 50',selected:(selected_len == "50") ?true: false}));
                $('#length_change'+ task.id).append($('<option>', {value: 100,text: 'Show 100',selected:(selected_len == "100") ?true: false}));
                $('#length_change'+ task.id).append($('<option>', {value: 1000,text: 'Show 1000',selected:(selected_len == "1000") ?true: false}));
            },
            drawCallback: function(settings) {
                var api = this.api();
                var dataLength = api.rows().data().length;
                
                if (dataLength == 0) {
                    // Do something when no data is available
                    $('#task' + task.id + '_card').hide();
                } else {
                    // Do something when there is available data
                    $('#task' + task.id + '_card').show();
                }
            },
        });

        tableTasks['table_task_' + task.id] = window[tableName]; // Assigning the DataTable instance to tableTasks

        // Placement controls for Table filters and buttons
		tableTasks['table_task_' + task.id].buttons().container().appendTo( '#card_header'+task.id);
        $('#table_search'+task.id).val(tableTasks['table_task_' + task.id].search());
		$('#table_search'+task.id).keyup(function(){ tableTasks['table_task_' + task.id].search($(this).val()).draw() ; })
	    $('#length_change'+ task.id).change( function() { tableTasks['table_task_' + task.id].page.len($(this).val()).draw() });
		$('#task' + task.id + '_table').on('click', 'tr', function(event) {
			// Check if the clicked element is a button or is contained within a button
			if ($(event.target).is('button') || $(event.target).closest('button').length > 0) {
				return; // Do nothing if it's a button or contained within a button
			}
			 // Get the index of the clicked cell within the row
            let cellIndex = $(event.target).closest('td').index();
            
            // Redirect only if the clicked cell is in the second column (index 1)
            if (cellIndex === 1) { // Adjust index as needed (index 0 is the first column)
                let rowData = tableTasks['table_task_' + task.id].row(this).data();
                if (rowData) {
                    let id = rowData.id;
                    let name = rowData.patient_name
                    // window.location.href = '{id}/divisiontwob/patients/facesheet/' + id;
                    //showEditModal(id,name);
                }
            }

		});

        @php
            if(Auth::user()->can('menu_store.patient_support.transfer_rx.update')) {
        @endphp
        $('#task' + task.id + '_table tbody').on('click', 'td', function() {
            var cell = $(this);
            let cellIndex = cell.index(); // Get the index of the clicked cell within the row
            let excludedColumns = [2,3,4,8,9,11,25]; 
            // Check if the clicked cell is in an excluded column
            if (excludedColumns.includes(cellIndex)) {
                return; // Do nothing if the clicked cell is in an excluded column
            }
            
            // Check if the cell is already in edit mode
            if (!cell.hasClass('edit-mode')) {
                //let columnIndexToEdit = 2; // Set the index of the column you want to make editable (e.g., 2 for the third column)

                // Check if the clicked cell is in the specified column
                if (cellIndex > 1 && cellIndex !== 5) {
                    let currentValue = cell.text();
                    let rowId = cell.closest('tr').find('td:first').text(); // Get the ID from the first column of the current row
                    //let columnName = cell.closest('table').find('th').eq(cellIndex).text(); // Get the column name from the table header
                    let columnName = getColumnByIndex(cellIndex); // Get the column name dynamically


                    // Replace the cell content with an input field
                    let inputField = $('<input type="text" class="form-control input-dynamic-width" value="' + currentValue + '">');
                    inputField.prop('size', currentValue.length + 2);
                    cell.html(inputField);

                    // Focus on the input field
                    inputField.focus();

                    // Add a class to mark the cell as in edit mode
                    cell.addClass('edit-mode');

                    // Handle blur event on the input field
                    inputField.on('blur', function() {
                        let newValue = inputField.val();

                        if(newValue === currentValue){
                            cell.text(newValue);
                            cell.removeClass('edit-mode');
                            return;
                        }
                        cell.text(newValue);

                        // Remove the edit-mode class to allow editing again
                        cell.removeClass('edit-mode');

                        $.ajax({
                            //laravel requires this thing, it fetches it from the meta up in the head
                            headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            type: "PUT",
                            url: `/store/patient-support/${menu_store_id}/transfer_rx/tribe_members/update_clicked_column`,
                            data: JSON.stringify({
                                task_log_id: rowId,
                                column_name: columnName,
                                value: newValue
                            }),
                            contentType: "application/json; charset=utf-8",
                            dataType: "json",
                            success: function(data) {
                            if (data.errors) {
                                tableTasks['table_task_' + data.task_to].ajax.reload(null, false);  
                                sweetAlert2(data.status,data.errors);
                            }
                            else{        
                                tableTasks['table_task_' + data.task_to].ajax.reload(null, false);  
                                sweetAlert2(data.status, data.message);
                                //$('#change_task_modal').modal('hide');
                                // window.location.href = '{id}/division3/monthly_report/' + $('#report_year').val()+'/'+$('#store').val();
                            }
                            },
                            error: function(data) {
                                handleErrorResponse(data);
                                //general error
                                console.log("Error");
                                console.log(data.responseText);
                            }
                        });
                    });
                }
            }
        });
        @php
            }
        @endphp

        function getColumnByIndex(index) {
            // Get DataTable instance
            var dataTable = $('#task' + task.id + '_table').DataTable();
            // Get column definition by index
            var column = dataTable.settings().init().columns[index];
            // Return the name of the column
            return column.name;
        }

    });
    
    // Dynamically adjust input field width based on content
    $(document).on('input', '.input-dynamic-width', function() {
        var $this = $(this);
        var textWidth = $this.val().length * 10; // Adjust multiplier to suit your font and styling
        $this.css('width', textWidth + 'px');
    });



    $(document).ready(function() {
        // Event delegation for popovers
        $(document).on('mouseenter', '.cell-button-popover', function() {
            var $cell = $(this);
            var popoverContent = $cell.data('content');
            $cell.popover({
                trigger: 'manual',
                placement: 'top',
                html: true,
                content: popoverContent
            }).popover('show');
        });

        $(document).on('mouseleave', '.cell-button-popover', function() {
            $(this).popover('hide');
        });

    });


   // console.log(tableTasks);
	
	$('.dataTables_scrollBody').scroll(function (){
        var cols = 2 // how many columns should be fixed
        var container = $(this)
        var offset = container.scrollLeft()
        container.add(container.prev()).find('tr').each(function (index,row){ // .add(container.prev()) to include the header
            $(row).find('th').each(function (index, th) {
                if (index < cols) {
                    $(th).css({ position: 'relative', left: offset + 'px', zIndex: '1' });
                }
            });
            $(row).find('td, th').each(function (index,cell){
                if(index>=cols) return
                $(cell).css({position:'relative',left:offset+'px'})
            })
        })
    })
    
    $('#add_assignee_modal #assignee').change(function() {
        // Get the selected value
        let selectedId = $(this).val();
        let task_id = $('#add_assignee_modal #task_id').val();
        sweetAlertLoading(),
        $.ajax({
        //laravel requires this thing, it fetches it from the meta up in the head
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: "PUT",
        url: `/store/patient-support/${menu_store_id}/transfer_rx/tribe_members/update_assignees`,
        data: JSON.stringify({
            user_id: selectedId,
            task_status_id: $('#add_assignee_modal #task_status_id').val()
        }),
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        success: function(data) {
            console.log(data);
            $('#edit_modal #assignees').text(data.assignees);
            $('#add_assignee_modal').modal('hide');
            tableTasks['table_task_' + task_id].ajax.reload(null, false);
            sweetAlert2(data.status, data.message);
        },
        error: function(data) {
            handleErrorResponse(data);
            //general error
            console.log("Error");
            console.log(data.responseText);
        }
        });

    });

    $('#edit_modal #fileDropArea').on('click', function (e) {
        e.preventDefault();
        $('#edit_modal #file').trigger('click');//open file selection
        e.stopPropagation();
    });

    $('#change_task_modal').on('hidden.bs.modal', function () {
        $('#change_task_modal #selected_button, #change_task_modal #selection_button').empty(); // Remove existing buttons
        $('.error_txt').remove();
        $(this)
        .find("input,textarea,select")
        .val('')
        .end(); 
    });

    $('#add_modal').on('hidden.bs.modal', function(){
        $(this)
        .find("input,textarea,select")
        .val('')
        .end();
        medCount = 0;
        $('.error_txt').remove();
        $('.additional_row').remove();
        $('td > input[type="text"]').css('width', '100%');
    });

    $('#medication_modal').on('hidden.bs.modal', function(){
        $(this)
        .find("input,textarea,select")
        .val('')
        .end();
        $('.error_txt').remove();
        $('#medication_modal .med_row').remove();
    });

    $('#add_assignee_modal').on('hidden.bs.modal', function(){
        $(this)
        .find("input,textarea,select")
        .val('')
        .end();
        medCount = 0;
        $('.error_txt').remove();
        $("#add_assignee_modal .chip_assignee").remove();
    })
</script>


@stop
