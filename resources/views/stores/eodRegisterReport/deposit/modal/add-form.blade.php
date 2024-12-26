<div class="modal" id="add_modal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <h6 class="modal-title">Deposit Form</h6>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">      
            <div class="row">
                <form action="" method="POST" id="#addForm">
                    <div class="col-lg-12">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="date" class="form-label">Date <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text" id="icon-date"><i class="fa fa-calendar"></i></span>
                                    <input type="text" class="form-control datepicker" id="date" name="date" aria-describedby="icon-date" placeholder="YYYY-MM-DD">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="date" class="form-label">Time <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text" id="icon-time"><i class="fa fa-clock"></i></span>
                                    <input type="text" class="form-control timepicker" id="time" name="time" aria-describedby="icon-time" placeholder="HH:MM">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="firstname" class="form-label">Receiver's First Name <span class="text-danger">*</span></label>
                                <input type="text" name="firstname" class="form-control" id="firstname" placeholder="First Name">
                            </div>
                            <div class="col-md-6">
                                <label for="lastname" class="form-label">Receiver's Last Name <span class="text-danger">*</span></label>
                                <input type="text" name="lastname" class="form-control" id="lastname" placeholder="Last Name">
                            </div>
                            <div class="col-md-6">
                                <label for="amount" class="form-label">Amount <span class="text-danger">*</span></label>
                                <input type="number" min="0" name="amount" class="form-control" id="amount" placeholder="0.00" required>
                            </div>
                            <div class="col-md-12">
                                <label for="signature" class="form-label">Signature <span class="text-danger">*</span></label>
                                <div id="signature_pad" style="border: 1px solid #dee2e6;"></div>
                                <textarea id="#signature" name="signature_pad" style="display: none;"></textarea>
                                <button class="btn btn-sm btn-secondary mt-2" onclick="clearAddSignature(event)"><i class="fa fa-eraser me-2"></i> Clear Signature</button>
                            </div>
                        </div> 
                    </div>
                </form>
            </div><!--end row-->
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            {{-- <button type="button" class="btn btn-danger" onclick="generatePDFWithoutSaving(event, 'add', 0)">Generate PDF</button> --}}
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
        let formData = new FormData();
        let data = {};

        $('#add_modal input, #add_modal textarea, #add_modal select').each(function() {
            data[this.id] = this.value;
        });
        data['pharmacy_store_id'] = menu_store_id;

        formData.append("data", JSON.stringify(data));
   
        var signatureData = $signaturePad.signature('toDataURL', 'image/png');
        // Extract the base64 part of the data URL
        var base64Data = signatureData.split(',')[1];
        formData.append('signature', base64Data);

        sweetAlertLoading();
        $.ajax({
            //laravel requires this thing, it fetches it from the meta up in the head
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "POST",
            url: `/store/eod-register-report/deposit/add`,
            data: formData,
            contentType: false,
            processData: false,
            success: function(res) {
                res = JSON.parse(res);
                $("#add_modal #save_btn").val('Save');
                $("#add_modal #save_btn").removeAttr('disabled');
                reloadDataTable();
                sweetAlert2(res.status, res.message);
                $('#add_modal').modal('hide');  
            },error: function(res) {

                $("#add_modal #save_btn").val('Save');
                $("#add_modal #save_btn").removeAttr('disabled');
                //general error
                console.log("Error");
                console.log(res);
                handleErrorResponse(res);
            }


        });
    }

    function clearAddSignature(event)
    {
        event.preventDefault();
        $signaturePad.signature('clear');
        $('#signature').val('');
    }
</script>