    @extends('layouts.master')

    @section('content')

    <!-- PAGE-HEADER -->
        <div class="page-header">
            <div>
            <h1 class="page-title">Medications</h1>
            </div>
        </div>
        <!-- PAGE-HEADER END -->

        
        <!-- EOF ERX ORDER -->

        <div class="p-5 bg-light rounded-3" >
                <table id="medications_table" class="table text-center table-light" style="width:100%">
                    <thead></thead>
                    <tbody>
                        
                    </tbody>
                </table>
        </div>

        

    @stop

    @section('pages_specific_scripts')
                <script>

        
                    $(document).ready(function() {

                        var patients_table = $('#medications_table').DataTable({
                            colReorder: true,
                            scrollX: true,
                            serverSide: true,
                            pageLength: 10,
                            dom: 'fBltip',
                            buttons: [
                                { extend: 'csv', className: 'btn btn-info', text:'Export to CSV' },
                            ],
                            
                            ajax: {
                                url: "/admin/medications/data",
                                type: "GET",
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                error: function (msg) {
                                    handleErrorResponse(msg);
                                }
                            },
                        
                            columns: [
                                
                                { data: 'name', name: 'name', title: 'Name' },
                                { data: 'ndc', name: 'ndc', title: 'NDC' },
                                { data: 'package_size', name: 'package_size', title: 'Package Size' },
                                { data: 'balance_on_hand', name: 'balance_on_hand', title: 'Balance Onhand' },
                                { data: 'therapeutic_class', name: 'therapeutic_class', title: 'Therapeutic Class' },
                                { data: 'category', name: 'category', title: 'Category' },
                                { data: 'manufacturer', name: 'manufacturer', title: 'Manufacturer' },
                                { data: 'rx_price', name: 'rx_price', title: 'RX Price' },
                                { data: '340b_price', name: '340b_price', title: '340B Price' },
                                { data: 'last_update_date', name: 'last_update_date', title: 'Last Update' },
                            ],
                            createdRow: function (row, data, dataIndex) {
                            
                                $(row).attr('id', 'row-' + data.id);
                            },
                        });
                        

                    });
                


                        
                                    
            
            </script>
    @stop


