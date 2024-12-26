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
                            <input type="hidden" name="target_list"  id="target_list" value=""> 
                            <div class="col-md-6">
                                <label for="firstname" class="form-label">First Name* </label>
                                <input type="text" name="firstname" class="form-control" id="firstname" placeholder="">
                            </div>
                            <div class="col-md-6">
                                <label for="lastname" class="form-label">Last Name* </label>
                                <input type="text" name="lastname" class="form-control" id="lastname" placeholder="">
                            </div>
                            <div class="col-md-6">
                                <label label for="gender" class="form-label">Gender</label>
                                <select class="form-select select2modal" data-placeholder="Select Gender.." name="gender" id="gender">
                                    <option value=""disabled selected>Select Gender..</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                    <option value="others">Others</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="birthdate" class="form-label">Birthdate</label>
                                <input type="text" readonly name="birthdate" class="form-control datepicker" id="birthdate">
                            </div>
                            <div class="col-md-12">
                                <label for="home_address" class="form-label">Home Address</label>
                                <input type="text" name="home_address" class="form-control" id="home_address" placeholder="">
                            </div>
                            <div class="col-md-6">
                                <label for="city" class="form-label">City</label>
                                <input type="text" name="city" class="form-control" id="city" placeholder="">
                            </div>
                            <div class="col-md-6">
                                <label for="county" class="form-label">County</label>
                                <select class="form-select select2modal" data-placeholder="Select County.." name="county" id="county">
                                    <option value="" disabled selected>Select..</option>
                                    <option value="Coos">Coos</option>
                                    <option value="Curry">Curry</option>
                                    <option value="Lincoln">Lincoln</option>
                                    <option value="Douglas">Douglas</option>
                                    <option value="Douglas">Lane</option>
                                    <option value="Others">Others</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="state" class="form-label">State/Province</label>
                                <input type="text" name="state" class="form-control" id="state" placeholder="">
                            </div>
                            <div class="col-md-6">
                                <label for="zip" class="form-label">Zip Postal</label>
                                <input type="text" name="zip" class="form-control" id="zip" placeholder="">
                            </div>
                            <div class="col-md-6">
                                <label for="phone_number" class="form-label">Phone Number</label>
                                <input type="text" name="phone_number" class="form-control" id="phone_number" placeholder="">
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="text" name="email" class="form-control" id="email" placeholder="">
                            </div>
                            <div class="col-md-12">
                                <label label for="affiliated" class="form-label">Please indicate the group with which you are affiliated:</label>
                                <select class="form-select select2modal" data-placeholder="--Select--" name="affiliated" id="affiliated">
                                    <option value="" disabled selected>Select..</option>
                                    <option value="Tribal Member">Tribal Member</option>
                                    <option value="Tribal Member's Family">Tribal Member's Family</option>
                                    <option value="Casino Employees">Casino Employees</option>
                                    <option value="Governtment Employees">Governtment Employees</option>
                                    <option value="Others">Others</option>
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label label for="affiliated" class="form-label">Preferred Form of Communication</label><br>
                                <div class="col-md-3" style="display: inline-block;">
                                    <label for="phonecall">
                                        <input type="checkbox" id="phonecall" name="communication[]" value="Phone Call">
                                        Phone Call
                                    </label>
                                </div>
                                <div class="col-md-3" style="display: inline-block;">
                                    <label for="textmessage">
                                        <input type="checkbox" id="textmessage" name="communication[]" value="Text Message">
                                        Text Message
                                    </label>
                                </div>
                                <div class="col-md-3" style="display: inline-block;">
                                    <label for="email">
                                        <input type="checkbox" id="email" name="communication[]" value="Email">
                                        Email
                                    </label>
                                </div>
                            </div>
                            <h3>Current Pharmacy Details</h3>
                            <div class="col-md-6">
                                <label for="current_pharmacy" class="form-label">Current Pharmacy</label>
                                <input type="text" name="current_pharmacy" class="form-control" id="current_pharmacy" placeholder="">
                            </div>
                            <div class="col-md-6">
                                <label for="pharmacy_phone_number" class="form-label">Pharmacy Number</label>
                                <input type="text" name="pharmacy_phone_number" class="form-control" id="pharmacy_phone_number" placeholder="">
                            </div>
                            <div class="col-md-12">
                                <label for="pharmacy_address" class="form-label">Address</label>
                                <input type="text" name="pharmacy_address" class="form-control" id="pharmacy_address" placeholder="">
                            </div>
                            <div class="col-md-4">
                                <label for="pharmacy_city" class="form-label">City</label>
                                <input type="text" name="pharmacy_city" class="form-control" id="pharmacy_city" placeholder="">
                            </div>
                            <div class="col-md-4">
                                <label for="pharmacy_state" class="form-label">State/Province</label>
                                <input type="text" name="pharmacy_state" class="form-control" id="pharmacy_state" placeholder="">
                            </div>
                            <div class="col-md-4">
                                <label for="pharmacy_zip" class="form-label">Zip/Postal</label>
                                <input type="text" name="pharmacy_zip" class="form-control" id="pharmacy_zip" placeholder="">
                            </div>

                            <h3>Prescriber Information</h3>
                            <div class="col-md-6">
                                <label for="prescriber_firstname" class="form-label">First Name</label>
                                <input type="text" name="prescriber_firstname" class="form-control" id="prescriber_firstname" placeholder="">
                            </div>
                            <div class="col-md-6">
                                <label for="prescriber_lastname" class="form-label">Last Name</label>
                                <input type="text" name="prescriber_lastname" class="form-control" id="prescriber_lastname" placeholder="">
                            </div>
                            <div class="col-md-6">
                                <label for="prescriber_phone_number" class="form-label">Phone Number</label>
                                <input type="text" name="prescriber_phone_number" class="form-control" id="prescriber_phone_number" placeholder="">
                            </div>
                            <div class="col-md-6">
                                <label for="prescriber_fax_number" class="form-label">Fax Number</label>
                                <input type="text" name="prescriber_fax_number" class="form-control" id="prescriber_fax_number" placeholder="">
                            </div>
                            
                            <!-- <label for="prescribed_on" class="form-label" id="medication_holder" style="margin-bottom:-10px;">MEDICATION(S)</label> -->
                            <h3 id="medication_holder">Medication Information</h3>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <th WIDTH="40%">Drug Name*</th>
                                        <th WIDTH="20%">Strength*</th>
                                    </thead>
                                    <tbody>
                                        @for ($i = 0; $i < 1; $i++)
                                            <tr>
                                                <td><input type="text" class="form-control auto_width" name="items[{{$i}}][drugname]" id="drugname{{$i}}"></td>
                                                <td><input type="text" class="form-control auto_width" name="items[{{$i}}][strength]" id="strength{{$i}}" title=""></td>
                                            </tr>
                                        @endfor
                                        <tr>
                                            <td><a href="javascript:;" onclick="moreMedication()" id="more_med">+ Add more medications</a></td>
                                            <td></td>
                                        </tr>
                                    </tbody>
                                </table>
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
    function showAddForm()
    {
        // Get the current URL
        let currentUrl = window.location.href;
        // Split the URL by '/' to get individual segments
        let segments = currentUrl.split('/');
        // Find the segment containing the desired value (in this case, the segment at index 4)
        let transferListId = segments[7];
        $( '#add_modal .select2modal' ).select2( {
            theme: "bootstrap-5",
            width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
            placeholder: $( this ).data( 'placeholder' ),
            closeOnSelect: true,
            dropdownParent: $('#add_modal .modal-content'),
		});

        $('.datetimepicker').each(function() {
            new tempusDominus.TempusDominus(this, {
                useCurrent: false,
                stepping: 1,
                localization: {
                    format: 'yyyy-MM-dd hh:mm T', // Modified format
                }
            });
        });

        $('.datepicker').each(function() {
            new tempusDominus.TempusDominus(this, {
                useCurrent: false,
                stepping: 1,
                display: {
                    viewMode: 'calendar',
                    components: {
                        clock: false,
                        hours: false,
                        minutes: false,
                        seconds: false,
                        useTwentyfourHour: undefined
                    },
                },
                localization: {
                    format: 'yyyy-MM-dd', // Modified format
                }
            });
        });

        $(".auto_width").on('keyup', function(){
            
            elementId = $(this).prop('id');

            let width = $(this).val().length * 10 + 25;

            $(this).css('width', width +"px");
        });

        //only number input
        $('.number_only').keyup(function(e){
            if (/\D/g.test(this.value))
            {
                // Filter non-digits from input value.
                this.value = this.value.replace(/\D/g, '');
            }
        });

        $('#add_modal #target_list').val('list'+transferListId);
        $('#add_modal').modal('show');
        $('#add_modal #modal_title').text('PATIENT FORM');
    }

    function moreMedication()
    {   
        let tableRow = $('#drugname'+medCount+'');
        medCount++;
        tableRow.closest('tr').after('<tr class="additional_row"><td><input type="text" class="form-control auto_width" name="items['+medCount+'][drugname]" id="drugname'+medCount+'"></td><td><input class="form-control auto_width" name="strength['+medCount+'][quantity]" id="strength'+medCount+'"></td></tr>');

        $('.number_only').keyup(function(e){
            if (/\D/g.test(this.value))
            {
                // Filter non-digits from input value.
                this.value = this.value.replace(/\D/g, '');
            }
        });

        $(".auto_width").on('keyup', function(){
        
            elementId = $(this).prop('id');

            let width = $(this).val().length * 10 + 25;

            $(this).css('width', width +"px");
        });
    }
    
    function saveForm(){
        let menu_store_id = {{request()->id}};
        $("#add_modal #save_btn").val('Saving... please wait!');
        $("#add_modal #save_btn").attr('disabled','disabled');
        $('.error_txt').remove();
        let data = {};

        $('#add_modal input, #add_modal textarea, #add_modal select').each(function() {
            // Check if the element is a checkbox and if it's checked
            if (this.type === 'checkbox' && this.checked) {
                if (!data.hasOwnProperty(this.name) || !Array.isArray(data[this.name])) {
                    data[this.name] = [];
                }
                data[this.name].push(this.value);
            } else {
                data[this.id] = this.value;
            }
        });
        data['med_count'] = medCount;
        //console.log(data);
        sweetAlertLoading();
        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "POST",
            url: `/store/patient-support/${menu_store_id}/transfer_rx/tribe_members/patient_store`,
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
                    tableTasks['table_task_' + data.task_to].ajax.reload(null, false);
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