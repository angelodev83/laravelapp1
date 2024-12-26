<div class="modal" id="update_for_shipping_today" tabindex="-1">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">

        <div class="modal-header">
            <h6 class="modal-title">Edit For Shipping Today</h6>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          
            <div class="row"><!--start row-->
                
                <form action="" method="POST">
                    <div class="col-lg-12">
                        <div class="row g-3">
                            <input type="hidden" id="eid" name="id" value="">
                            <div class="col-6">
                                <label for="firstname" class="form-label">First Name <span class="text-danger">*</span></label>
                                <input type="text" name="firstname" id="efirstname" class="form-control" placeholder="First Name">
                            </div>
                            <div class="col-6">
                                <label for="lastname" class="form-label">Last Name <span class="text-danger">*</span></label>
                                <input type="text" name="lastname" id="elastname" class="form-control" placeholder="Last Name">
                            </div>
                            <div class="col-6">
                                <label for="dob" class="form-label">Birth Date <span class="text-danger">*</span></label>
                                <div class="input-group"> <span class="input-group-text" id="icon-order-date"><i class="fa fa-calendar"></i></span>
                                    <input type="text" class="form-control" name="dob" id="edob" aria-describedby="icon-order-date" placeholder="YYYY-MM-DD" readonly>
                                </div>
                            </div>
                            <div class="col-6">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="text" name="email" id="eemail" class="form-control" placeholder="johndoe@example.com">
                            </div>
                            <div class="col-6">
                                <label for="tracking_number" class="form-label">Tracking Number <span class="text-danger">*</span></label>
                                <input type="text" name="tracking_number" id="etracking_number" class="form-control" placeholder="1Zxxxxxxxxxxxxx">
                            </div>
                            <div class="col-6">
                                <label for="rx_number" class="form-label">RX Number <span class="text-danger">*</span></label>
                                <input type="text" name="rx_number" id="erx_number" class="form-control">
                            </div>
                            <div class="col-6">
                                <label for="shipped_date" class="form-label">Shipped Date <span class="text-danger">*</span></label>
                                <div class="input-group"> <span class="input-group-text" id="icon-order-date"><i class="fa fa-calendar"></i></span>
                                    <input type="text" class="form-control" name="shipped_date" id="eshipped_date" aria-describedby="icon-order-date" placeholder="YYYY-MM-DD" readonly>
                                </div>
                            </div>
                            <div class="col-6">
                                <label for="phone_number" class="form-label">Phone Number</label>
                                <input type="text" name="phone_number" id="ephone_number" class="form-control" placeholder="+15XXXXXX">
                            </div>
                            <div class="col-12">
                                <label for="address" class="form-label">Address</label>
                                <textarea name="address" row="2" id="eaddress" class="form-control"></textarea>
                            </div>
                            <div class="col-6">
                                <label for="city" class="form-label">City</label>
                                <input type="text" name="city" id="ecity" class="form-control" placeholder="City">
                            </div>
                            <div class="col-6">
                                <label for="state" class="form-label">State</label>
                                <input type="text" name="state" id="estate" class="form-control" placeholder="State">
                            </div>
                        </div>
                    </div>
                </form>
        
            </div><!--end row-->

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary" onclick="updateForm()">Submit</button>
        </div>
      </div>
    </div>
  </div>
  
<script>

    function showEditModal(id)
    {
        var btn = document.querySelector(`#for-shipping-today-edit-btn-${id}`);
        let arr = JSON.parse(btn.dataset.array);

        console.log('arr-------',arr);

        $('#update_for_shipping_today input, #update_for_shipping_today textarea, #update_for_shipping_today select').each(function() {
            let val = arr[this.name] ?? '';
            if(val != '') {
                $(`#${this.id}`).val(val);
            }
        });

        $('#eshipped_date').datepicker({
            format: "yyyy-mm-dd",
            todayHighlight: true,
            uiLibrary: 'bootstrap5',
            modal: true,
            icons: {
                rightIcon: '<i class="material-icons"></i>'
            },
            showRightIcon: false,
            autoclose: true
        });

        $('#edob').datepicker({
            format: "yyyy-mm-dd",
            todayHighlight: true,
            uiLibrary: 'bootstrap5',
            modal: true,
            icons: {
                rightIcon: '<i class="material-icons"></i>'
            },
            showRightIcon: false,
            autoclose: true
        });

        $('#update_for_shipping_today').modal('show');
    }
        
    function updateForm(){
        $('.error_txt').remove();
        let data = {};
        let menu_store_id = {{request()->id}};

        $('#update_for_shipping_today input, #update_for_shipping_today textarea, #update_for_shipping_today select').each(function() {
            data[this.name] = this.value;
        });
        
        sweetAlertLoading();
        $.ajax({
            //laravel requires this thing, it fetches it from the meta up in the head
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "PUT",
            url: `/store/operations/for-shipping-today/edit`,
            data: JSON.stringify(data),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(data) {
                if(data.errors){
                    $.each(data.errors,function (key , val){
                        sweetAlert2('warning', 'Check field inputs.');
                        $("#e"+key ).after( '<span class="error_txt" style="color:red; text-indent:15px;">'+val[0]+'</span>' );
                        console.log(key);
                    });
                }
                else{
                    reloadDataTable(data);
                }
            },error: function(msg) {
                handleErrorResponse(msg);
            if(msg.status == 403) {
                    sweetAlert2('warning', "403 Forbidden: You don't have permission to do this action");
                }
                $("#add_user_btn").val('Save');
                $("#add_user_btn").removeAttr('disabled');
                //general error
                console.log("Error");
                console.log(msg.responseText);
            }


        });
    }

</script>