<div class="modal" id="edit_task_modal" tabindex="-1" style="display:none;">
    <div class="modal-dialog custom-modal-fullsize">
      <div class="modal-content">
        <div class="modal-header">
          <h2 class="modal-title">
            <span id="show-task-subject" onclick="clickSubject()"></span>
            <div class="row" id="show-task-subject-div-input" style="display: none;">
                <div class="col-md-12">
                    <input type="text" name="show-subject" id="show-subject" class="form-control" placeholder="Subject" autocomplete="off" style="height: 65px; font-size: 30px; min-width: 800px !important;">
                </div>
            </div>
          </h2>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          
            <div class="form-body">
                <div class="row">
                    <form action="" method="POST" id="#task_show_form">
                        <div class="col-lg-12">
                            <div class="row">
                                <!-- start of part 1 (md-9) -->
                                <div class="col-md-8" id="task-content-coldiv">
                                    <div class="scrollable-content">
                                        <div class="row g-3">
                                            <div class="col-md-12">
                                                <input type="text" name="show-id" id="show-id" class="form-control" placeholder="Task ID" autocomplete="off" hidden>
                                                <input type="text" name="show-drug-order-id" id="show-drug-order-id" value="" hidden>
                                                <input type="text" name="show-supply-order-id" id="show-supply-order-id" value="" hidden>
                                            </div>
                                        </div>
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <div class="row g-3">
                                                    <div class="col-md-12 d-flex" style="align-items: center">
                                                        <div class="col-md-4">
                                                            <label for="Status" class="form-label"><i class="fa fa-circle me-3"></i>Status:</label>
                                                        </div>
                                                        <div class="col">
                                                            <div class="dropdown">
                                                                <button class="btn btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="show-status-btn-dropdown"></button>
                                                                <ul class="dropdown-menu" id="show-status-btn-dropdown-ul"></ul>
                                                            </div>
                                                            <input type="text" name="show-status-id" id="show-status-id" hidden>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12 d-flex" style="align-items: center">
                                                        <div class="col-md-4">
                                                            <label for="Status" class="form-label"><i class="fa fa-calendar me-3"></i>Due Date:</label>
                                                        </div>
                                                        <div class="col">
                                                            
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="row g-3">
                                                    <div class="col-md-12 d-flex" style="align-items: center">
                                                        <div class="col-md-4">
                                                            <label for="Status" class="form-label"><i class="fa fa-user me-3"></i>Assignee:</label>
                                                        </div>
                                                        <div class="col">
                                                            <div class="d-flex" id="show-assigned-to" onclick="clickAssignee()"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12 d-flex" style="align-items: center">
                                                        <div class="col-md-4">
                                                            <label for="Status" class="form-label"><i class="fa fa-flag me-3"></i>Priority:</label>
                                                        </div>
                                                        <div class="col">
                                                            <div class="dropdown">
                                                                <button class="btn btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="show-priority-status-btn-dropdown"></button>
                                                                <ul class="dropdown-menu" id="show-priority-status-btn-dropdown-ul"></ul>
                                                            </div>
                                                            <input type="text" name="show-priority-status-id" id="show-priority-status-id" hidden>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row g-3 mt-2">
                                            <div class="col-md-12">
                                                <textarea class="form-control tinymce-content" name="show-description" id="show-description" rows="5" placeholder="Description"></textarea>
                                            </div>
                                        </div>
                                        <div class="row g-3 mt-2">
                                            <div class="col-md-8">
                                                <label for="edocuments" class="form-label">Attachments</label>
                                                <small class="attachment-label-color ms-2">Only accepts maximum size of 100 MB per file</small>
                                                <!-- <input id="show-documents" class="imageuploadify-file-general-class" name="show-documents[]" type="file" accept="*" multiple> -->
                                                <div id="for-file"></div>
                                            </div>
                                            <div class="col-md-4">
                                                <!-- task reminders starts -->
                                                <div class="col-md-12 d-flex m-1">
                                                    <div class="card radius-10 w-100 ">
                                                        <div id="bulletin-tasks-recent" class="customers-list pt-2 p-3 mb-3" style="height: 300px !important;">                                                
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- task reminders ends -->
            
                                            </div>
                                        </div>

                                        <div class="row g-3">
                                            <div class="col-md-12">
                                                <label for="show-task-comment" class="form-label">Comment</label>
                                                <textarea name="show-task-comment" row="4" id="show-task-comment" class="form-control" placeholder="Subject"> </textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--end of part 1 (md-9) -->
    
                                <!-- start of part 2 (md-3) -->
                                <div class="col-md-4" id="task-relation-coldiv">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="card">
                                                <div class="card-body">
                                                    <ul class="list-group">
                                                        <li class="list-group-item" id="task-relation-subject">DRUG ORDER DETAILS</li>
                                                        {{-- <li class="list-group-item">Order Number: <b class="ms-auto" id="show-drug-order-number"></b></li>
                                                        <li class="list-group-item">Order Date: <b class="ms-auto" id="show-drug-order-date"></b></li> --}}
                                                        {{-- <li class="list-group-item">Order Comment: <p id="show-drug-order-comment"></p></li> --}}
                                                    </ul>
                                                    <div class="row g-3 mt-2">
                                                        <div class="col-md-6">
                                                            <label for="show-order-number" class="form-label">Order Number:</label>
                                                            <input type="text" name="show-order-number" id="show-order-number" class="form-control" placeholder="Order #" autocomplete="off">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label for="show-account-number" class="form-label">Account Number:</label>
                                                            <input type="text" name="show-account-number" id="show-account-number" class="form-control" placeholder="Account #" autocomplete="off">
                                                        </div>
                                                        <div class="col-md-12">
                                                            <label for="show-po-name" class="form-label">PO Name:</label>
                                                            <input type="text" name="show-po-name" id="show-po-name" class="form-control" placeholder="PO Name" autocomplete="off">
                                                        </div>
                                                        <div class="col-md-12">
                                                            <label for="show-wholesaler-name" class="form-label">Wholesaler:</label>
                                                            <input type="text" name="show-wholesaler-name" id="show-wholesaler-name" class="form-control" placeholder="Wholesaler" autocomplete="off">
                                                        </div>
                                                        <div class="col-md-12">
                                                            <label for="show-do-comment" class="form-label">Drug Order Comment</label>
                                                            <textarea name="show-do-comment" row="2" id="show-do-comment" class="form-control" placeholder="Comment"> </textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--end of part 2 (md-3) -->
                            </div>
                            

                        </div>
                    </form>
                </div><!--end row-->
            </div>
  
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-primary" onclick="clickSubmitBtnShowModal()">SAVE</button>
        </div>
      </div>
    </div>
    </div>
</div>