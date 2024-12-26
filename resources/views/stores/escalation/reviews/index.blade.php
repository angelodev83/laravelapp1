@extends('layouts.master')
@section('content')

<!--start page wrapper -->
<div class="page-wrapper">

    <div class="page-content">
        <!-- PAGE-HEADER -->
        @include('layouts/pageContentHeader/store')
        <!-- PAGE-HEADER END -->

        @include('stores/escalation/reviews/partials/average')
        @include('stores/escalation/reviews/partials/widgets')
        
        <div class="card">
            <div class="card-header dt-card-header">
                <select name='length_change' id='length_change' class="table_length_change form-select">
                </select>
                <input type="text" id="search_input" class="table_search_input form-control" placeholder="Search...">
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="dt_table" class="table row-border hover" style="width:100%">
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

</div>
<!--end page wrapper -->
@stop

@section('pages_specific_scripts')  
<script>
    let table_dt;
    let menu_store_id = {{request()->id}};

    $(document).ready(function() {
        menu_store_id = {{request()->id}};
        
        loadTable();
    });

    function loadTable()
    {
        const dt_table = $('#dt_table').DataTable({
            scrollX: true,
            serverSide: true,
            stateSave: false,
            dom: 'fBtip',
            buttons: [

            ],
            pageLength: 10,
            searching: true,
            order: [[0, 'asc']],
            ajax: {
                url: "reviews/data",
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
                { data: 'fullname', name: 'fullname', title: 'Name' },
                // { data: 'how_satisfied_with_our_pharmacy_overall_score', name: 'how_satisfied_with_our_pharmacy_overall_score', title: 'Overall Score' },
                // { data: 'stars_range', name: 'stars_range', title: 'Range', orderable: false, searchable: false},
                // { data: 'form_id', name: 'form_id', title: 'Form ID' },
                { data: 'email_address', name: 'email_address', title: 'Email Address' },
                { data: 'what_would_have_made_experience_5_stars', name: 'what_would_have_made_experience_5_stars', title: 'What would have made your experience 5 stars?' },
                { data: 'suggestions_for_improvement', name: 'suggestions_for_improvement', title: 'What is one thing we could do better on?' },
                // { data: 'formatted_created_at', name: 'created_at', title: 'Date Created' }
            ],
            initComplete: function( settings, json ) {
                selected_len = dt_table.page.len();
				$('#length_change').append($('<option>', {value: 5,text: 'Show 5',selected:(selected_len == "5") ?true: false}));
				$('#length_change').append($('<option>', {value: 10,text: 'Show 10',selected:(selected_len == "10") ?true: false}));
				$('#length_change').append($('<option>', {value: 50,text: 'Show 50',selected:(selected_len == "50") ?true: false}));
				$('#length_change').append($('<option>', {value: 100,text: 'Show 100',selected:(selected_len == "100") ?true: false}));
				$('#length_change').append($('<option>', {value: 1000,text: 'Show 1000',selected:(selected_len == "1000") ?true: false}));
            }
        });

        table_dt = dt_table;
        
        // Placement controls for Table filters and buttons
		table_dt.buttons().container().appendTo( '.dt-card-header' );
        $('#search_input').val(dt_table.search());
		$('#search_input').keyup(function(){ table_dt.search($(this).val()).draw() ; })
	    $('#length_change').change( function() { table_dt.page.len($(this).val()).draw() });
    }

    
</script>  
@stop
