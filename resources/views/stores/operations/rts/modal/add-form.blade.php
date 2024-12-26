<div class="modal" id="add_modal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <h6 class="modal-title"></h6>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">

            <div class="row">
                <form action="" method="POST" id="#addForm">
                <div class="col-lg-12">
                    
                        <div class="row g-3">
                            <!-- <div class="col-md-12">
                                <label for="order_number" class="form-label">Order Number</label>
                                <input type="text" readonly name="order_number" class="form-control" id="order_number" placeholder="Enter Order Number">
                            </div> -->
                            <!-- <div class="col-md-6">
                                <label label for="patient_id" class="form-label">Patient</label>
                                <select class="form-select" data-placeholder="Select Patient.." name="patient_id" id="patient_id"></select>
                            </div> -->
                            <div class="col-md-6">
                                <label for="date" class="form-label">Date</label>
                                <input type="text" readonly name="date" class="form-control" id="date">
                            </div>
                        </div> 
                        <div class="card" style="margin-top: 20px;">
                            <div class="p-4 card-body">
                                <h6 class="card-title" id="medication_holder">Medications*</h6>
                                
                                
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <th WIDTH="40%">Drug Name</th>
                                            
                                            <th WIDTH="10%">Quantity</th>
                                            <th WIDTH="20%">RX Number</th>
                                        </thead>
                                        <tbody>
                                            @for ($i = 0; $i < 3; $i++)
                                                <tr>
                                                    <td><select class="form-select" data-placeholder="Select medication.." name="items[{{$i}}][drugname]" id="drugname{{$i}}"></select></td>
                                                    
                                                    <td><input type="text" class="form-control number_only" name="items[{{$i}}][quantity]" id="quantity{{$i}}" placeholder="Type Qty"></td>
                                                    <td><input type="text" class="form-control auto_width" name="items[{{$i}}][rx_number]" id="rx_number{{$i}}" placeholder="Type Here"></td>
                                                </tr>
                                            @endfor
                                            <tr><td><a href="javascript:;" onclick="moreMedication()" id="more_med">+ Add more medications</a></td></tr>
                                        </tbody>
                                    </table>
                                </div> 
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label for="reason" class="form-label">Reason</label>
                                <textarea rows="3" name="reason" class="form-control" id="reason" placeholder=""></textarea>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" id="save_btn" onclick="saveForm()">Submit</button>
        </div>
        </div>
  </div>
</div>

<script>
    let medCount = 2; //0 included

    function showAddNewForm(){
        $('#add_modal .modal-title').text('RTS FORM');
        $('#add_modal').modal('show');

        $('#add_modal #date').datepicker({
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

        $('.number_only').keyup(function(e){
            if (/\D/g.test(this.value))
            {
                // Filter non-digits from input value.
                this.value = this.value.replace(/\D/g, '');
            }
        });
        
        $('#add_modal #patient_id').select2( {
            theme: "bootstrap-5",
            width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
            placeholder: $( this ).data( 'placeholder' ),
            closeOnSelect: true,
            dropdownParent: $('#add_modal .modal-content'),
            multiple: false,
            minimumInputLength: 1,
            minimumResultsForSearch: 10,
            ajax: {
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                url: `/store/operations/${menu_store_id}/rts/get_patients`,
                dataType: "json",
                type: "GET",
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
                                text: item.firstname+' '+item.lastname,
                                id: item.id
                            }   
                        })
                    };
                }  
            }
        });

        for (b = 0; b < 3; b++){
            $( '#add_modal #drugname'+b ).select2( {
                theme: "bootstrap-5",
                width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
                placeholder: $( this ).data( 'placeholder' ),
                closeOnSelect: true,
                dropdownParent: $('#add_modal .modal-content'),
                multiple: false,
                minimumInputLength: 3,
                minimumResultsForSearch: 10,
                ajax: {
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    url: `/store/operations/${menu_store_id}/rts/get_medications`,
                    dataType: "json",
                    type: "GET",
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
        
        // $.ajax({
        //     headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        //     type: "GET",
        //     url: "/admin/procurement_clinic_orders/next_order_number",
        //     contentType: "application/json; charset=utf-8",
        //     dataType: "json",
        //     success: function(data) {
        //         year = (new Date).getFullYear()
        //         $("#order_number").val(year+''+data);
                
        //     }
        // });
    }

    function moreMedication()
    {
        var tableRow = $('#add_modal #drugname'+medCount+'');
        medCount++;
        tableRow.closest('tr').after('<tr><td><select data-placeholder="Select medication.." class="form-select" name="items['+medCount+'][drugname]" id="drugname'+medCount+'" title="Drug Selection"></select></td><td><input type="text" class="form-control number_only" name="items['+medCount+'][quantity]" id="quantity'+medCount+'" placeholder="Type Qty"></td><td><input type="text" class="form-control auto_width" name="items['+medCount+'][rx_number]" id="rx_number'+medCount+'" placeholder="Type Here"></td></tr>');

        $( '#drugname'+medCount ).select2( {
            theme: "bootstrap-5",
            width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
            placeholder: $( this ).data( 'placeholder' ),
            closeOnSelect: true,
            dropdownParent: $('#add_modal .modal-content'),
          
            multiple: false,
            minimumInputLength: 3,
            minimumResultsForSearch: 10,
            ajax: {
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                url: `/store/operations/${menu_store_id}/rts/get_medications`,
                dataType: "json",
                type: "GET",
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

        // $(".auto_width").on('keyup', function(){
        
        //     elementId = $(this).prop('id');

        //     let width = $(this).val().length * 10 + 25;

        //     $(this).css('width', width +"px");
        // });
    }
    
    function saveForm(){
        $("#add_modal #save_btn").val('Saving... please wait!');
        $("#add_modal #save_btn").attr('disabled','disabled');
        $('.error_txt').remove();
        let data = {};

        $('#add_modal input, #add_modal textarea, #add_modal select').each(function() {
            data[this.id] = this.value;
        });
        data['med_count'] = medCount;
        data['pharmacy_store_id'] = menu_store_id;
        // console.log(data);
        sweetAlertLoading();
        $.ajax({
            //laravel requires this thing, it fetches it from the meta up in the head
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "POST",
            url: `/store/operations/${menu_store_id}/rts/store`,
            data: JSON.stringify(data),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(data) {
                $("#add_modal #save_btn").val('Save');
                $("#add_modal #save_btn").removeAttr('disabled');
                
                if(data.errors){
                    $.each(data.errors,function (key , val){
                        sweetAlert2(data.status, data.message);
                        $("#"+key ).after( '<span class="error_txt" style="color:red; text-indent:15px;">'+val[0]+'</span>' );
                        console.log(key);
                    });
                }
                else{
                    dataTable_global.ajax.reload(null, false);
                    sweetAlert2(data.status, data.message);
                    $('#add_modal').modal('hide');
                }
            },error: function(msg) {
                handleErrorResponse(msg);
                $("#add_modal #save_btn").val('Save');
                $("#add_modal #save_btn").removeAttr('disabled');
                //general error
                console.log("Error");
                console.log(msg.responseText);
            }


        });
    }
</script>