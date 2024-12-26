<div class="modal" id="edit_modal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <h6 class="modal-title" id="order_id_text"></h6>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">     
            <div class="row">
                <form action="" method="POST" id="#editForm">
                <div class="col-lg-12">
                        <input type="hidden" id="id" name="id" value="">
                        <!-- <input type="hidden" id="old_order_number" name="order_number" value=""> -->
                        <div class="row g-3">
                            <!-- <div class="col-md-6">
                                <label label for="patient_id" class="form-label">Patient</label>
                                <select class="form-select" data-placeholder="Select Patient.." name="patient_id" id="patient_id"></select>
                            </div> -->
                            <div class="col-md-6">
                                <label for="date" class="form-label">Date</label>
                                <input type="text" readonly name="date" class="form-control" id="date">
                            </div>
                            <div class="col-md-6">
                                <label label for="status_id" class="form-label">Status</label>
                                <select class="form-select" name="status_id" id="status_id" data-placeholder="Select Status.."></select>
                            </div>
                        </div> 
                        <div class="card" style="margin-top: 20px;">
                            <div class="p-4 card-body">
                                <h6 class="card-title" id="medication_holder">Medications*</h6>
                                
                                
                                <div class="table-responsive">
                                    <table class="table table-bordered table-responsive">
                                        <thead>
                                            <th WIDTH="40%">Drug Name</th>
                                            <th WIDTH="10%">Quantity</th>
                                            <th WIDTH="40%">RX Number</th>
                                        </thead>
                                        <tbody>
                                            
                                            <tr>
                                                <td><select class="form-select" name="drugname" id="drugname" data-placeholder="Select medication.."></select></td>
                                                
                                                <td><input type="text" class="form-control number_only" name="quantity" id="quantity" placeholder="Type Qty"></td>
                                                <td><input type="text" class="form-control auto_width" name="rx_number" id="rx_number" placeholder="Type Here"></td>
                                            </tr>
                                            
                                        </tbody>
                                    </table>
                                </div> 
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label for="reason" class="form-label">Reason</label>
                                <textarea rows="3" name="reason" class="form-control" id="reason"></textarea>
                            </div>
                        </div>
                    </div>
                </form>
            </div><!--end row-->
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" id="save_btn" onclick="updateForm()">Submit</button>
        </div>
        </div>
  </div>
</div>
<script>

    function showEditForm(data){
        $('#edit_modal #date').datepicker({
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

        let authId = {{ auth()->user()->id }};
        let id = $(data).data('id');
        let pId = $(data).data('pid');
        // let patientName = $(data).data('patientname');
        let uId = $(data).data('uid');
        let sId = $(data).data('sid');
        let mId = $(data).data('mid');
        let drugName = $(data).data('drugname');
        let date = $(data).data('date');
        let quantity = $(data).data('quantity');
        let rxNumber = $(data).data('rxnumber');
        let reason = $(data).data('reason');
        $('#edit_modal .modal-title').text('RTS ID:'+id);
        $('#edit_modal').modal('show');

        $("#edit_modal #id").val(id);
        $("#edit_modal #quantity").val(quantity);
        $("#edit_modal #rx_number").val(rxNumber);
        $("#edit_modal #reason").val(reason);
        $("#edit_modal #date").val(date);

        if(uId != authId){
            $('#edit_modal #quantity').prop('disabled', true);
            $('#edit_modal #rx_number').prop('disabled', true);
            $('#edit_modal #reason').prop('disabled', true);
            $('#edit_modal #date').prop('disabled', true);
            $('#edit_modal #status_id').prop('disabled', true);
            $('#edit_modal #drug_name').prop('disabled', true);
            $('#edit_modal #drugname').select2({ disabled: true });
            $('#edit_modal #save_btn').prop('disabled', true);
        }
        else{
            $('#edit_modal #quantity').prop('disabled', false);
            $('#edit_modal #rx_number').prop('disabled', false);
            $('#edit_modal #reason').prop('disabled', false);
            $('#edit_modal #date').prop('disabled', false);
            $('#edit_modal #status_id').prop('disabled', false);
            $('#edit_modal #drug_name').prop('disabled', false);
            $('#edit_modal #drugname').select2({ disabled: false });
            $('#edit_modal #save_btn').prop('disabled', false);
        }

         $('#edit_modal #status_id').select2( {
            theme: "bootstrap-5",
            width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
            placeholder: $( this ).data( 'placeholder' ),
            closeOnSelect: true,
            dropdownParent: $('#edit_modal .modal-content'),
         });

        $("#edit_modal #drugname").append("<option selected value='"+mId+"'>"+drugName+"</option>");
        $('#edit_modal #drugname').select2( {
            theme: "bootstrap-5",
            width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
            placeholder: $( this ).data( 'placeholder' ),
            closeOnSelect: true,
            dropdownParent: $('#edit_modal .modal-content'),
            multiple: false,
            minimumInputLength: 3,
            minimumResultsForSearch: 20,
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

        // $("#edit_modal #patient_id").append("<option selected value='"+pId+"'>"+patientName+"</option>");
        // $('#edit_modal #patient_id').select2( {
        //     theme: "bootstrap-5",
        //     width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
        //     placeholder: $( this ).data( 'placeholder' ),
        //     closeOnSelect: true,
        //     dropdownParent: $('#edit_modal .modal-content'),
        //     multiple: false,
        //     minimumInputLength: 1,
        //     minimumResultsForSearch: 20,
        //     ajax: {
        //         headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        //         url: `/store/operations/${menu_store_id}/rts/get_patients`,
        //         dataType: "json",
        //         type: "GET",
        //         data: function (params) {
        //             var queryParameters = {
        //                 term: params.term
        //             }
        //             return queryParameters;
        //         },
        //         processResults: function (data) {
        //             return {
        //                 results: $.map(data.data, function (item) {
        //                     return {
        //                         text: item.firstname+' '+item.lastname,
        //                         id: item.id
        //                     }   
        //                 })
        //             };
        //         }  
        //     }
        // });

        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "GET",
            url: `/store/operations/${menu_store_id}/rts/get_status`,
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            data: {
                id: id,
            },
            success: function(data) {

                $.each(Object.values(data.statuses), function(index, status) {
                    let name = status.name;
                    let id = status.id;
                    $("#edit_modal #status_id").empty();
                    $("#edit_modal #status_id").append("<option value='' disabled selected>Select Status..</option>");
                
                    if(id === sId){
                        $("#edit_modal #status_id").append("<option selected value='"+id+"'>"+name+"</option>");
                    }
                    else{
                        $("#edit_modal #status_id").append("<option value='"+id+"'>"+name+"</option>");
                    }
                });

            },
            error: function(msg) {
                handleErrorResponse(msg);
            }
            
        });
    }
    
    function updateForm(){

        $("#edit_modal #save_btn").val('Saving... please wait!');
        $("#edit_modal #save_btn").attr('disabled','disabled');

        $('.error_txt').remove();
        let data = {};

        $('#edit_modal input, #edit_modal textarea, #edit_modal select').each(function() {
            data[this.id] = this.value;
        });
        data['pharmacy_store_id'] = menu_store_id;
        //console.log(data);
        sweetAlertLoading();
        $.ajax({
            //laravel requires this thing, it fetches it from the meta up in the head
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "PUT",
            url: `/store/operations/${menu_store_id}/rts/update`,
            data: JSON.stringify(data),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(data) {
                $("#edit_modal #save_btn").val('Save');
                $("#edit_modal #save_btn").removeAttr('disabled');
                
                if(data.errors){
                    $.each(data.errors,function (key , val){
                        sweetAlert2(data.status, data.message);
                        $("#e"+key ).after( '<span class="error_txt" style="color:red; text-indent:15px;">'+val[0]+'</span>' );
                        console.log(key);
                    });
                }
                else{
                    dataTable_global.ajax.reload(null, false);
                    sweetAlert2(data.status, data.message);
                    $('#edit_modal').modal('hide');
                }
            },error: function(msg) {
                handleErrorResponse(msg);
                $("#edit_modal #save_btn").val('Save');
                $("#edit_modal #save_btn").removeAttr('disabled');
                //general error
                console.log("Error");
                console.log(msg.responseText);
            }


        });
    }
</script>