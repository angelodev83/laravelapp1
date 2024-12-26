<div class="card">
    <div class="card-header dt-card-header">
        
        <a style="width: fit-content;" class="btn btn-primary ms-2 table_search_input" onclick="showAddNewForm()">+ Schedule</a>
        
        <select name='length_change' id='length_change' class="table_length_change form-select">
        </select>
        <input type="text" id="search_input" class="table_search_input form-control" placeholder="Search...">

        <h6 style="float: left;" class="table_search_input ms-3">Outreach</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="table" class="table row-border table-hover" style="width:100%">
                <thead></thead>
                <tbody>
                        <tr>
                        <td>
                            <div class="dt-loading-spinner text-center">
                                <i class="fas fa-spinner fa-spin fa-3x"></i> <!-- Example: Font Awesome spinner icon -->
                            </div>
                        </td>
                    </tr>                                     
                </tbody>
                <tfooter></tfooter>
            </table>
        </div>
    </div>
</div>