<div class="modal" id="edit_modal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Edit Deposit</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <form action="" method="POST" id="#editForm">
                        <div class="col-lg-12">
                            <input type="hidden" id="id" name="id">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="date" class="form-label">Date</label>
                                    <div class="input-group">
                                        <span class="input-group-text" id="icon-date"><i class="fa fa-calendar"></i></span>
                                        <input type="text" class="form-control datepicker" id="date" name="date" aria-describedby="icon-date" placeholder="YYYY-MM-DD">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="date" class="form-label">Time</label>
                                    <div class="input-group">
                                        <span class="input-group-text" id="icon-time"><i class="fa fa-clock"></i></span>
                                        <input type="text" class="form-control timepicker" id="time" name="time" aria-describedby="icon-time" placeholder="HH:MM">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="firstname" class="form-label">Receiver's First Name</label>
                                    <input type="text" name="firstname" class="form-control" id="firstname" placeholder="First Name">
                                </div>
                                <div class="col-md-6">
                                    <label for="lastname" class="form-label">Receiver's Last Name</label>
                                    <input type="text" name="lastname" class="form-control" id="lastname" placeholder="Last Name">
                                </div>
                                <div class="col-md-6">
                                    <label for="amount" class="form-label">Amount</label>
                                    <input type="number" min="0" name="amount" class="form-control" id="amount" placeholder="0.00">
                                </div>
                                <div class="col-md-12" id="edit-signature-img">
                                    <label for="esignature" class="form-label">Signature</label>
                                    <div>
                                        <img id="esignature_img" src="" alt="Base64 Image">
                                    </div>
                                    <button class="btn btn-sm btn-danger" onclick="recreateSignature(event)"><i class="fa fa-file-signature me-2"></i> Re-create Signature</button>
                                </div>
                                <div class="col-md-12" id="edit-signature-pad" style="display: none;">
                                    <label for="signature" class="form-label">Signature</label>
                                    <div id="esignature_pad" style="border: 1px solid #dee2e6;"></div>
                                    <textarea id="#esignature" name="esignature_pad" style="display: none;"></textarea>
                                    <button class="btn btn-sm btn-secondary mt-2" onclick="clearEditSignature(event)"><i class="fa fa-eraser me-2"></i> Clear Signature</button>
                                    <button class="btn btn-sm btn-danger mt-2" onclick="cancelSignature(event)">Cancel</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div><!--end row-->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                {{-- <button type="button" class="btn btn-danger" onclick="generatePDFWithoutSaving(event, 'edit', $('#edit_modal #id').val())">Generate PDF</button> --}}
                <button type="button" class="btn btn-primary" id="update_btn" onclick="updateForm()">Submit</button>
            </div>
            
        </div>
    </div>
</div>

<script>
    function recreateSignature(event)
    {
        event.preventDefault();
        $('#edit-signature-img').css('display', 'none');
        $('#edit-signature-pad').css('display', 'block');
    }

    function cancelSignature(event)
    {
        event.preventDefault();
        $('#edit-signature-img').css('display', 'block');
        $('#edit-signature-pad').css('display', 'none');
        clearEditSignature(event);
    }

    function showEditForm(id)
    {
        $signaturePad = $('#esignature_pad').signature({
            syncField: '#esignature',
            syncFormat: 'PNG'
        });
        
        $('.datepicker').datepicker({
            format: "yyyy-mm-dd",
            todayHighlight: true,
            uiLibrary: 'bootstrap5', modal: true,
            icons: {
                rightIcon: '<i class="material-icons"></i>'
            },
            showRightIcon: false,
            autoclose: true,
   			orientation: "right",
        });

        let btn = $(`#data-show-btn-${id}`);
        let arr = btn.data('array');

        $('#edit_modal input, #edit_modal textarea, #edit_modal select').each(function() {
            let val = arr[this.name] ?? '';
            if(val != '') {
                $(`#edit_modal #${this.id}`).val(val);
            }
        });

        $('#esignature_img').attr('src',`data:image/png;base64,${arr['signature']}`)
        
        $('#edit_modal').modal('show');
    }

    function clearEditSignature(event)
    {
        event.preventDefault();
        $signaturePad.signature('clear');
        $('#esignature').val('');
    }
    
    function updateForm()
    {
        $("#edit_modal #update_btn").val('Saving... please wait!');
        $("#edit_modal #update_btn").attr('disabled','disabled');
        $('.error_txt').remove();

        let data = {
            id: $(`#edit_modal #id`).val(),
            date: $(`#edit_modal #date`).val(),
            time: $(`#edit_modal #time`).val(),
            firstname: $(`#edit_modal #firstname`).val(),
            lastname: $(`#edit_modal #lastname`).val(),
            amount: $(`#edit_modal #amount`).val(),
            pharmacy_store_id: menu_store_id,
        };

        var div = document.getElementById('edit-signature-pad');
        var style = window.getComputedStyle(div);
        if (style.display == 'block') {
            var signatureData = $signaturePad.signature('toDataURL', 'image/png');
            var base64Data = signatureData.split(',')[1];
            data['signature'] = base64Data;
        }

        sweetAlertLoading();
        $.ajax({
            //laravel requires this thing, it fetches it from the meta up in the head
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "PUT",
            url: `/store/eod-register-report/deposit/edit`,
            data: JSON.stringify(data),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(res) {
                $("#edit_modal #update_btn").val('Save');
                $("#edit_modal #update_btn").removeAttr('disabled');
                reloadDataTable();
                sweetAlert2(res.status, res.message);
                $('#edit-signature-img').css('display', 'block');
                $('#edit-signature-pad').css('display', 'none');
                $('#edit_modal').modal('hide');  
            },error: function(msg) {
                $("#edit_modal #update_btn").val('Save');
                $("#edit_modal #update_btn").removeAttr('disabled');
                //general error
                console.log("Error");
                handleErrorResponse(msg);
                console.log(msg.responseText);
            }

        });
    }
</script>