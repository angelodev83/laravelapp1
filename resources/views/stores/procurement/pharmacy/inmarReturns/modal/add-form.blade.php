<div class="modal" id="addInmar_modal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <h6 class="modal-title">INMAR Form</h6>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">      
            <div class="row">
                <form action="" method="POST" id="#inmarAddForm">
                <div class="col-lg-12">
                    
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label for="name" class="form-label">Reference Number</label>
                                <input type="text" name="name" class="form-control" id="name" placeholder="Reference Number">
                            </div>
                            <div class="col-md-12">
                                <label for="po_name" class="form-label">PO Name</label>
                                <input type="text" name="po_name" class="form-control" id="po_name" placeholder="PO Name">
                            </div>
                            <div class="col-md-12">
                                <label for="account_number" class="form-label">Account Number</label>
                                <input type="text" name="account_number" class="form-control" id="account_number" placeholder="Account Number">
                            </div>
                            <div class="col-md-6">
                                <label label for="wholesaler_name" class="form-label">Wholesaler Name</label>
                                {{-- <select class="form-select" name="wholesaler_name" id="wholesaler_name" title="Select..">
                                    <option value='AMENISOURCE'>AMENISOURCE</option>
                                    <option value='McKENSON'>McKENSON</option>
                                </select> --}}
                                <select class="form-select" name="wholesaler_id" id="wholesaler_id" title="Select..">
                                </select>
                            </div>
                            <!-- <div class="col-md-6">
                                <label label for="clinic_name" class="form-label">Clinic/External Location*</label>
                                <select class="form-select" name="clinic_id" id="clinic_id" title="Select Clinic Location"></select>
                            </div> -->
                            <div class="col-md-6">
                                <label for="return_date" class="form-label">Return Date</label>
                                <div class="input-group"> <span class="input-group-text" id="icon-order-date"><i class="fa fa-calendar"></i></span>
                                    <input type="text" class="form-control datepicker" id="return_date" name="return_date" aria-describedby="icon-order-date" placeholder="YYYY-MM-DD" readonly>
                                </div>
                            </div>
                            <!-- <div class="col-md-6" style="margin-top: -1px;">
                                <label for="prescriber_name" class="form-label">Prescriber Full Name*</label>
                                <input type="text" name="prescriber_name" class="form-control" id="prescriber_name" placeholder="Enter Prescriber Full Name">
                            </div> -->
                        </div> 
                        <div class="card" style="margin-top: 20px;">
                            <div class="p-4 card-body">
                                <h6 class="card-title" id="medication_holder">Medications*</h6>
                                
                                
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <th WIDTH="40%">Drug Name</th>
                                            <th WIDTH="10%">Quantity</th>
                                            <th WIDTH="20%">NDC</th>
                                            <!-- <th WIDTH="20%">Return Type</th> -->
                                        </thead>
                                        <tbody>
                                            @for ($i = 0; $i < 3; $i++)
                                                <tr>
                                                    <td><select class="form-select" name="items[{{$i}}][drugname]" id="drugname{{$i}}" title="Drug Selection"></select></td>
                                                    <td><input type="text" class="form-control number_only auto_width" name="items[{{$i}}][quantity]" id="quantity{{$i}}" placeholder="Type Qty"></td>
                                                    <td><input type="text" class="form-control auto_width" name="items[{{$i}}][ndc]" id="ndc{{$i}}" placeholder="Type Here"></td>
                                                    <!-- <td><select class="form-select" name="items[{{$i}}][return_type]" id="return_type{{$i}}" title=""></select></td>
                                                     -->
                                                </tr>
                                            @endfor
                                            <tr><td colspan="3"><a href="javascript:;" onclick="moreMedication()" id="more_med">+ Add more medications</a></td></tr>
                                        </tbody>
                                    </table>
                                </div> 
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label for="comments" class="form-label">Comments</label>
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
        
        // tableRow.closest('tr').after('<tr><td><select class="form-select" name="items['+medCount+'][drugname]" id="drugname'+medCount+'" title="Drug Selection"></select></td><td><input type="text" class="form-control number_only auto_width" name="items['+medCount+'][quantity]" id="quantity'+medCount+'" placeholder="Type Qty"></td><td><input type="text" class="form-control auto_width" name="items['+medCount+'][ndc]" id="ndc'+medCount+'" placeholder="Type Here"></td><td><select class="form-select" name="items['+medCount+'][return_type]" id="return_type'+medCount+'" title=""></select></td></tr>');
        tableRow.closest('tr').after('<tr class="rowToRemove"><td><select class="form-select" name="items['+medCount+'][drugname]" id="drugname'+medCount+'" title="Drug Selection"></select></td><td><input type="text" class="form-control number_only auto_width" name="items['+medCount+'][quantity]" id="quantity'+medCount+'" placeholder="Type Qty"></td><td><input type="text" class="form-control auto_width" name="items['+medCount+'][ndc]" id="ndc'+medCount+'" placeholder="Type Here"></td></tr>');
        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "GET",
            url: "/store/procurement/pharmacy/inmar-returns/get_return_type_data",
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(data) {
                
                var len = data.data.length;
                
                $("#return_type"+medCount+"").empty();
                
                
                for( var b = 0; b<len; b++){
                    var bname = data.data[b];
                    $("#return_type"+medCount+"").append("<option value='"+bname+"'>"+bname+"</option>");
                }
            },
            error: function(msg) {
                handleErrorResponse(msg);
            }
        });

        $( '#drugname'+medCount ).select2( {
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
    }

    function saveForm(){
        $("#save_btn").val('Saving... please wait!');
        $("#save_btn").attr('disabled','disabled');
        $('.error_txt').remove();
        let data = {};

        $('#addInmar_modal input, #addInmar_modal textarea, #addInmar_modal select').each(function() {
            data[this.id] = this.value;
        });
        data['med_count'] = medCount;
        data['menu_store_id'] = menu_store_id;
        console.log(data);
        sweetAlertLoading();
        $.ajax({
            //laravel requires this thing, it fetches it from the meta up in the head
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "POST",
            url: "/store/procurement/pharmacy/inmar-returns/store",
            data: JSON.stringify(data),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(data) {
                $("#save_btn").val('Save');
                $("#save_btn").removeAttr('disabled');
                reloadDataTable();
                if(data.errors){
                    $.each(data.errors,function (key , val){
                        sweetAlert2('warning', 'Check field inputs.');
                        $("#"+key ).after( '<span class="error_txt" style="color:red; text-indent:15px;">'+val[0]+'</span>' );
                        console.log(key);
                    });
                }
                else{
                    table_inmartable.ajax.reload(null, false);
                    sweetAlert2('success', 'Record has been saved.');
                    $('#addInmar_modal').modal('hide');
                }
            },error: function(msg) {
                handleErrorResponse(msg);
                $("#add_user_btn").val('Save');
                $("#add_user_btn").removeAttr('disabled');
                //general error
                console.log("Error");
                console.log(msg.responseText);
            }


        });
    }
</script>