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
                            <div class="col-md-4">
                                <label label for="task_name" class="form-label">TASK NAME</label>
                                <select class="form-select" data-placeholder="Select task.." name="task_name" id="task_name" title="Select Task"></select>
                            </div>
                            <div class="col-md-4">
                                <label for="patient_name" class="form-label">PATIENT NAME</label>
                                <input type="text" name="patient_name" class="form-control" id="patient_name" placeholder="">
                            </div>
                            <div class="col-md-4">
                                <label for="patient_birthdate" class="form-label">PATIENT BIRTH DATE</label>
                                <input type="text" readonly name="patient_birthdate" class="form-control datepicker" id="patient_birthdate">
                            </div>
                            <div class="col-md-12 div_medications">
                                <label for="medications" class="form-label">MEDICATIONS <i> *Press ENTER for each Medications</i></label>
                                <textarea rows="3" name="medications" class="form-control" id="medications"></textarea>
                            </div>
                            <div class="col-md-4 div_outlier_type">
                                <label label for="outlier_type" class="form-label">OUTLIER TYPE</label>
                                <select class="form-select" data-placeholder="Select outlier type.." name="outlier_type" id="outlier_type"></select>
                            </div>
                            <div class="col-md-4 div_completed_date">
                                <label for="completed_date" class="form-label">COMPLETED DATE</label>
                                <input type="text" readonly name="completed_date" class="form-control datepicker" id="completed_date">
                            </div>
                            <div class="col-md-4 div_date_of_interaction">
                                <label for="date_of_interaction" class="form-label">DATE OF INTERACTION</label>
                                <input type="text" readonly name="date_of_interaction" class="form-control datepicker" id="date_of_interaction">
                            </div>
                            <div class="col-md-4 div_date_of_initiation">
                                <label for="date_of_initiation" class="form-label">DATE OF INITIATION</label>
                                <input type="text" readonly name="date_of_initiation" class="form-control datepicker" id="date_of_initiation">
                            </div>
                            <div class="col-md-4 div_side_effects">
                                <label for="side_effects" class="form-label">SIDE EFFECTS </label>
                                <input type="text" name="side_effects" class="form-control" id="side_effects" placeholder="">
                            </div>
                            <div class="col-md-4 div_date_side_effects">
                                <label for="date_side_effects" class="form-label">DATE SIDE EFFECTS</label>
                                <input type="text" readonly name="date_side_effects" class="form-control datepicker" id="date_side_effects">
                            </div>
                            <div class="col-md-4 div_date_follow_up">
                                <label for="date_follow_up" class="form-label">DATE FOLLOW UP</label>
                                <input type="text" readonly name="date_follow_up" class="form-control datepicker" id="date_follow_up">
                            </div>
                            <div class="col-md-6 div_recommended_vitamins">
                                <label for="recommended_vitamins" class="form-label">RECOMMENDED VITAMINS</label>
                                <input type="text" name="recommended_vitamins" class="form-control" id="recommended_vitamins" placeholder="">
                            </div>
                            <div class="col-md-4 div_pdc_rate">
                                <label for="pdc_rate" class="form-label">PDC RATE</label>
                                <input type="text" name="pdc_rate" class="form-control" id="pdc_rate" placeholder="">
                            </div>
                            <div class="col-md-12">
                                <label for="comments" class="form-label">COMMENTS</label>
                                <textarea rows="3" name="comments" class="form-control" id="comments"></textarea>
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
    
    var medCount = 2; //0 included

    function moreMedication()
    {
        var tableRow = $('#drugname'+medCount+'');
        medCount++;
        tableRow.closest('tr').after('<tr><td><select class="form-select" name="items['+medCount+'][drugname]" id="drugname'+medCount+'" title="Drug Selection"></select></td><td><input type="text" class="form-control number_only" name="items['+medCount+'][quantity]" id="quantity'+medCount+'" placeholder="Type Qty"></td><td><input type="text" class="form-control" name="items['+medCount+'][ndc]" id="ndc'+medCount+'" placeholder="Type Here"></td><td><select class="form-select" name="items['+medCount+'][return_type]" id="return_type'+medCount+'" title=""></select></td></tr>');

        // $( '#return_type'+medCount ).select2( {
        //     theme: "bootstrap-5",
        //     width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
        //     placeholder: $( this ).data( 'placeholder' ),
        //     closeOnSelect: true,
        //     dropdownParent: $('#addInmar_modal'),
        // });
        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "GET",
            url: "/admin/inmar/get_return_type_data",
            //data: JSON.stringify(data),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(data) {
                
                var len = data.data.length;
                
                $("#return_type"+medCount+"").empty();
                
                
                for( var b = 0; b<len; b++){
                    var bname = data.data[b];
                    // if(i==0){$("#role_id").append("<option selected value='"+id+"'>"+name+"</option>");}
                    $("#return_type"+medCount+"").append("<option value='"+bname+"'>"+bname+"</option>");
                }
            },
            error: function (msg) {
                handleErrorResponse(msg);
            }
        });

        $( '#drugname'+medCount ).select2( {
            theme: "bootstrap-5",
            width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
            placeholder: $( this ).data( 'placeholder' ),
            closeOnSelect: true,
            dropdownParent: $('#addInmar_modal'),
            //tags: true,
            multiple: false,
            //tokenSeparators: [',', ' '],
            minimumInputLength: 3,
            minimumResultsForSearch: 10,
            ajax: {
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                url: "/admin/inmar/get_medication_data",
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
                                id: item.id
                            }   
                        })
                    };
                }  
            }
        });

        $('.number_only').keyup(function(e){
            if (/\D/g.test(this.value))
            {
                // Filter non-digits from input value.
                this.value = this.value.replace(/\D/g, '');
            }
        });
    }

    function saveForm(){
        $("#add_modal #save_btn").val('Saving... please wait!');
        $("#add_modal #save_btn").attr('disabled','disabled');
        $('.error_txt').remove();
        let data = {};

        $('#add_modal input, #add_modal textarea, #add_modal select').each(function() {
            data[this.id] = this.value;
        });
        //console.log(data);
        sweetAlertLoading();
        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "POST",
            url: "/admin/divisionthree/task/store",
            data: JSON.stringify(data),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(data) {
                $("#add_modal #save_btn").val('Save');
                $("#add_modal #save_btn").removeAttr('disabled');
                
                if(data.errors){
                    $.each(data.errors,function (key , val){
                        sweetAlert2('warning', 'Check field inputs.');
                        $("#add_moadal #"+key ).after( '<span class="error_txt" style="color:red; text-indent:15px;">'+val[0]+'</span>' );
                        console.log(key);
                    });
                }
                else{
                    table_task.ajax.reload(null, false);
                    sweetAlert2('success', 'Record has been saved.');
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