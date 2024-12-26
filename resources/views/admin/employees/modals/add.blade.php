<div class="modal" id="add_employee_modal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Employee Form</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
            
                    <!--start row-->
                    <div class="row">
                        <form action="" method="POST" id="#employee_add_form">
                            
                            <div class="col">

                                <!--start stepper two--> 
                                <div id="stepper2" class="bs-stepper">
                                    <div class="card">
                                    
                                        <div class="card-header px-3">
                                            <div class="d-lg-flex flex-lg-row align-items-lg-center justify-content-lg-between" role="tablist">
                                                <div class="step" data-target="#test-nl-1" onclick="prevBtn(event, 1)">
                                                    <div class="step-trigger" role="tab" id="stepper2trigger1" aria-controls="test-nl-1">
                                                        <div class="bs-stepper-circle" id="bs-stepper-circle-1"><i class='bx bx-user fs-4'></i></div>
                                                        <div class="">
                                                            <h6 class="mb-0 steper-title">Personal Info</h6>
                                                            <p class="mb-0 steper-sub-title">Enter Your Details</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="bs-stepper-line"></div>
                                                <div class="step" data-target="#test-nl-2" onclick="prevBtn(event, 2)">
                                                    <div class="step-trigger" role="tab" id="stepper2trigger2" aria-controls="test-nl-2">
                                                        <div class="bs-stepper-circle" id="bs-stepper-circle-2"><i class='bx bx-briefcase fs-4'></i></div>
                                                        <div class="">
                                                            <h6 class="mb-0 steper-title">Employment</h6>
                                                            <p class="mb-0 steper-sub-title">Employment Details</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="bs-stepper-line"></div>
                                                <div class="step" data-target="#test-nl-3" onclick="prevBtn(event, 3)">
                                                    <div class="step-trigger" role="tab" id="stepper3trigger3" aria-controls="test-nl-3">
                                                        <div class="bs-stepper-circle" id="bs-stepper-circle-3"><i class='bx bx-file fs-4'></i></div>
                                                        <div class="">
                                                            <h6 class="mb-0 steper-title">Account Details</h6>
                                                            <p class="mb-0 steper-sub-title">Setup Account Details</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="card-body">
                                        
                                            <div id="test-nl-1" class="test-nl" >
                                                <h6 class="mb-1">Your Personal Information</h6>
                                                <p class="mb-4">Enter your personal information, enter next to proceed to other fields</p>
                    
                                                <div class="row g-3">
                                                    <div class="col-12 col-lg-6">
                                                        <label for="firstname" class="form-label">First Name <span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" id="firstname" placeholder="First Name" required>
                                                        <div class="invalid-feedback">
                                                            First Name field is required
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-lg-6">
                                                        <label for="lastname" class="form-label">Last Name <span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" id="lastname" placeholder="Last Name" required>
                                                        <div class="invalid-feedback">
                                                            Last Name field is required
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-lg-6">
                                                        <label for="nickname" class="form-label">Nick Name</label>
                                                        <input type="text" class="form-control" id="nickname" placeholder="Nick Name">
                                                    </div>
                                                    <div class="col-12 col-lg-6">
                                                        <label for="InputEmail" class="form-label">E-mail Address</label>
                                                        <input type="text" class="form-control" id="email" placeholder="johndoe@example.com">
                                                    </div>
                                                    <div class="col-12 col-lg-6">
                                                        <label for="PhoneNumber" class="form-label">Contact Number</label>
                                                        <input type="text" class="form-control" id="contact_number" placeholder="Contact Number">
                                                    </div>
                                                    <div class="col-12 col-lg-6">
                                                        <label for="birthdate" class="form-label">Birth Date</label>
                                                        <input type="text" class="form-control" id="date_of_birth" placeholder="MM/DD.YYYY">
                                                    </div>
                                                    <div class="col-12 col-lg-12">
                                                        <label for="address" class="form-label">Address</label>
                                                        <input type="text" class="form-control" id="address" placeholder="Street, City">
                                                    </div>
                                                    <div class="col-12 col-lg-6">
                                                        <button class="btn btn-primary px-4" onclick="nextBtn(event, 2)">Next<i class='bx bx-right-arrow-alt ms-2'></i></button>
                                                    </div>
                                                </div><!---end row-->
                                            
                                            </div>

                                            <div id="test-nl-2" class="test-nl" style="display: none;">
                    
                                                <h6 class="mb-1">Employment Details</h6>
                                                <p class="mb-4">Enter Your Employment Details.</p>
                    
                                                <div class="row g-3">
                                                    <div class="col-12 col-lg-6">
                                                        <label for="position" class="form-label">Position</label>
                                                        <input type="text" class="form-control" id="position" placeholder="eg. Physician">
                                                    </div>
                                                    <div class="col-12 col-lg-6">
                                                        <label for="department_id" class="form-label">Department</label>
                                                        <select class="form-control" id="department_id">
                                                        </select>
                                                    </div>
                                                    <div class="col-12 col-lg-6">
                                                        <label for="start_date" class="form-label">Start Date</label>
                                                        <input type="text" class="form-control" id="start_date" placeholder="MM/DD/YYYY">
                                                    </div>
                                                    <div class="col-12 col-lg-6">
                                                        <label for="end_date" class="form-label">End Date</label>
                                                        <input type="text" class="form-control" id="end_date" placeholder="MM/DD/YYYY">
                                                    </div>
                                                    <div class="col-12 col-lg-6">
                                                        <label for="position" class="form-label">Employment Type <span class="text-danger">*</span></label>
                                                        <select class="form-control" id="employment_type">
                                                            <option value="Full Time" selected>Full Time</option>
                                                            <option value="Part Time">Part Time</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-12 col-lg-6">
                                                        <label for="position" class="form-label">Assign to Store <span class="text-danger">* </span></label><span><small> Yes/No</small></span>
                                                        <div class="form-check form-switch mt-2 mx-3">
                                                            <label class="form-check-label text-bold ms-1 text-primary" for="flexSwitchCheckChecked"><b> <span id="pharmacy_name">TRP</span></b></label>
                                                            <input class="form-check-input" type="checkbox" id="toggle_pharmacy_store_id" checked disabled>
                                                            <input class="form-control" type="text" id="pharmacy_store_id" hidden>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="d-flex align-items-center gap-3">
                                                            <button class="btn btn-secondary px-4" onclick="prevBtn(event, 1)"><i class='bx bx-left-arrow-alt me-2'></i>Previous</button>
                                                            <button class="btn btn-primary px-4" onclick="nextBtn(event, 3)">Next<i class='bx bx-right-arrow-alt ms-2'></i></button>
                                                        </div>
                                                    </div>

                                                </div><!---end row-->
                                                
                                            </div>
                
                                            <div id="test-nl-3" class="test-nl" style="display: none;">
                    
                                                <h6 class="mb-1">Account Details</h6>
                                                <p class="mb-4">Enter Your Account Details.</p>
                    
                                                <div class="row g-3">
                                                    <div class="col-12 col-lg-6">
                                                        <label for="user_name" class="form-label">Username <span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" id="user_name" placeholder="Enter Username" required>
                                                        <div class="invalid-feedback">
                                                            Username field is required
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-lg-6">
                                                        <label for="user_email" class="form-label">E-mail Address <span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" id="user_email" placeholder="example@xyz.com" required>
                                                        <div class="invalid-feedback">
                                                            User Email field is required
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-lg-6">
                                                        <label for="user_password" class="form-label">Password <span class="text-danger">*</span></label>
                                                        <input type="password" class="form-control" id="user_password" value="" required>
                                                        <div class="invalid-feedback">
                                                            Password field is required
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-lg-6">
                                                        <label for="user_confirm_password" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                                        <input type="password" class="form-control" id="user_confirm_password" value="" required>
                                                        <div class="invalid-feedback" id="confirm_password_validation">
                                                            Confirm password field is required
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="d-flex align-items-center gap-3">
                                                            <button class="btn btn-secondary px-4" onclick="prevBtn(event, 2)"><i class='bx bx-left-arrow-alt me-2'></i>Previous</button>
                                                        </div>
                                                    </div>
                                                </div><!---end row-->
                                                
                                            </div>
                                            
                                    </div>
                                </div>
                                <!--end stepper two--> 

                            </div>
                        </form>
                    </div>
                    <!--end row-->

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="save_btn" onclick="saveNewEmployeeForm()">Submit</button>
            </div>
        </div>
    </div>
</div>

<script>    
    function nextBtn(event, num)
    {
        event.preventDefault();
        $('.test-nl').hide();
        $('.bs-stepper-circle').css('background-color', '#f0f0f0');
        $('.bs-stepper-circle').css('color', '#969595');
        $(`#test-nl-${num}`).css('display', 'block');
        $(`#bs-stepper-circle-${num}`).css('color', '#ffffff');
        $(`#bs-stepper-circle-${num}`).css('background-color', '#15a0a3');
    }

    function prevBtn(event, num)
    {
        event.preventDefault();
        $('.test-nl').hide();
        $('.bs-stepper-circle').css('background-color', '#f0f0f0');
        $('.bs-stepper-circle').css('color', '#969595');
        $(`#test-nl-${num}`).css('display', 'block');
        $(`#bs-stepper-circle-${num}`).css('color', '#ffffff');
        $(`#bs-stepper-circle-${num}`).css('background-color', '#15a0a3');
    }

    function saveNewEmployeeForm(){
        let data = {
            employee: {},
            user: {},
            pharmacyStaff: {},
            is_offshore: is_offshore
        };

        const fill_employee = [
            'firstname', 'lastname', 'date_of_birth', 'address', 'contact_number', 'position', 'department_id', 'start_date', 'end_date', 'employment_type', 'position', 'nickname', 'email'
        ];

        const fill_user = [
            'user_name', 'user_email', 'user_password', 'user_confirm_password'
        ];

        const fill_pharmacy_staff = [
            'pharmacy_store_id'
        ];

        $(".form-control").removeClass("is-invalid");
        let flag = true;

        $('#add_employee_modal input, #add_employee_modal textarea, #add_employee_modal select').each(function() {
            if(!$(`#${this.id}`)[0].checkValidity()) {
                $(`#${this.id}`).addClass("is-invalid");
                flag = false;
            }
            if(fill_employee.includes(this.id)) {
                data.employee[this.id] = this.value;
            }
            if(fill_user.includes(this.id)) {
                data.user[this.id] = this.value;
            }
            if(fill_pharmacy_staff.includes(this.id)) {
                data.pharmacyStaff[this.id] = this.value;
            }
        });

        console.log('fire after',data);

        var password = $('#user_password').val();
        var confirm_password = $('#user_confirm_password').val();
        $('#confirm_password_validation').html('Confirm Password field is required');
        if(password != confirm_password) {
            $('#confirm_password_validation').html('Confirm Password Mismatched');
            $('#user_confirm_password').addClass("is-invalid");
            flag = false;
        }

        if(flag === false) {
            return;
        }
        // return;
        
        sweetAlertLoading();

        $.ajax({
            //laravel requires this thing, it fetches it from the meta up in the head
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "POST",
            url: `/admin/employee/add`,
            data: JSON.stringify(data),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(data) {
                $("#save_btn").val('Save');
                $("#save_btn").removeAttr('disabled');
                
                if(data.errors){
                    $.each(data.errors,function (key , val){
                        sweetAlert2('warning', 'Check field inputs.');
                        $("#"+key ).after( '<span class="error_txt" style="color:red; text-indent:15px;">'+val[0]+'</span>' );
                        console.log(key);
                    });
                }
                else{
                    reloadDataTable();
                    sweetAlert2('success', 'Record has been saved.');
                    $('#add_employee_modal').modal('hide');
                }
            },error: function(msg) {

                $("#save_btn").val('Save');
                $("#save_btn").removeAttr('disabled');
                //general error
                console.log("Error");
                handleErrorResponse(msg);
                console.log(msg.responseText);
            }

        });
    }

</script>