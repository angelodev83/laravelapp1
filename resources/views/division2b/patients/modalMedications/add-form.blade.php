<div class="modal" id="add_modal" tabindex="-1">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
        <div class="modal-header">
            <h6 class="modal-title" id="modal_title"></h6>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">      
            <div class="row">
                <form action="" method="POST" id="#addForm">
                    <div class="col-lg-12">
                    
                        <div class="row g-3">
                            <input type="hidden" name="patient_id"  id="patient_id"> 
                            <label for="prescribed_on" class="form-label" id="medication_holder" style="margin-bottom:-10px;">MEDICATION(S)</label>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <th WIDTH="40%">DRUG NAME</th>
                                        <th WIDTH="20%">QUANTITY</th>
                                        <th WIDTH="10%">REFILLS</th>
                                        <th WIDTH="20%">STORE LOCATION</th>
                                    </thead>
                                    <tbody>
                                        @for ($i = 0; $i < 1; $i++)
                                            <tr>
                                                <td><input type="text" class="form-control auto_width" name="items[{{$i}}][drugname]" id="drugname{{$i}}"></td>
                                                <td><input type="text" class="form-control number_only auto_width" name="items[{{$i}}][quantity]" id="quantity{{$i}}" title=""></td>
                                                <td><input type="text" class="form-control number_only auto_width" name="items[{{$i}}][refills]" id="refills{{$i}}"></td>
                                                <td><input type="text" class="form-control auto_width" name="items[{{$i}}][store_location]" id="store_location{{$i}}"></td>
                                            </tr>
                                        @endfor
                                        <tr>
                                            <td><a href="javascript:;" onclick="moreMedication()" id="more_med">+ Add more medications</a></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div> 
                            <div class="col-md-6">
                                <label for="prescribed_on" class="form-label">PRESCRIBED ON</label>
                                <input type="text" readonly name="prescribed_on" class="form-control datetimepicker" id="prescribed_on">
                            </div>
                            <div class="col-md-6">
                                <label for="prescribed_by" class="form-label">PRESCRIBED BY </label>
                                <input type="text" name="prescribed_by" class="form-control" id="prescribed_by" placeholder="">
                            </div>
                        </div> 

                    </div>
                </form>
            </div><!--end row-->
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" id="save_btn" onclick="saveForm()">Submit</button>
        </div>
        </div>
  </div>
</div>

<script>
    
    function saveForm(){
        $("#add_modal #save_btn").val('Saving... please wait!');
        $("#add_modal #save_btn").attr('disabled','disabled');
        $('.error_txt').remove();
        let data = {};

        $('#add_modal input, #add_modal textarea, #add_modal select').each(function() {
            data[this.id] = this.value;
        });
        data['med_count'] = medCount;
        console.log(data);
        sweetAlertLoading();
        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "POST",
            url: "/admin/divisiontwob/patients/medications_store",
            data: JSON.stringify(data),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(data) {
                $("#add_modal #save_btn").val('Save');
                $("#add_modal #save_btn").removeAttr('disabled');
                
                if(data.errors){
                    $.each(data.errors,function (key , val){
                        sweetAlert2('warning', 'Check field inputs.');
                        $("#add_modal #"+key ).after( '<span class="error_txt" style="color:red; text-indent:15px;">'+val[0]+'</span>' );
                        console.log(key);
                    });
                }
                else{
                    table_medications.ajax.reload(null, false);
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