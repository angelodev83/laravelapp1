<div class="modal" id="event_modal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <h6 class="modal-title">Add Event Form</h6>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">      
            <div class="row">
                <form action="" method="POST" id="#eventForm">
                    <div class="col-lg-12">
                        
                            <div class="col-md-12">
                                <label for="date" class="form-label">Date</label>
                                <div class="input-group"> <span class="input-group-text" id="icon-order-date"><i class="fa fa-calendar"></i></span>
                                    <input type="text" class="form-control datepicker" id="date" name="date" aria-describedby="icon-order-date" placeholder="YYYY-MM-DD" readonly>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label for="name" class="form-label">Title</label>
                                <input type="text" name="name" class="form-control" id="name">
                            </div>
                            
                            <div class="col-md-12">
                                <label for="content" class="form-label">Content</label>
                                <textarea id="content" name="content" class="form-control" rows="3" placeholder=""></textarea>
                            </div>
                        
                        
                    </div>
                </form>
            </div><!--end row-->
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" id="save_btn" onclick="saveEvent()">Submit</button>
        </div>
    </div>
  </div>
</div>

<script>
    function saveEvent(){
        $("#event_modal #save_btn").val('Saving... please wait!');
        $("#event_modal #save_btn").attr('disabled','disabled');
        $('.error_txt').remove();
        let formData = new FormData();
        let data = {};

        $('#event_modal input, #event_modal textarea, #event_modal select').each(function() {
            data[this.id] = this.value;
        });
        data['menu_store_id'] = menu_store_id;
        

        formData.append("data", JSON.stringify(data));
        sweetAlertLoading();
        $.ajax({
            //laravel requires this thing, it fetches it from the meta up in the head
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "POST",
            url: `/store/marketing/${menu_store_id}/news-and-events/addEvent`,
            data: formData,
            contentType: false,
            processData: false,
            success: function(data) {
                $("#event_modal #save_btn").val('Save');
                $("#event_modal #save_btn").removeAttr('disabled');
                data = JSON.parse(data);
                if(data.errors){
                    $.each(data.errors,function (key , val){
                        sweetAlert2('warning', 'Check field inputs.');
                        $("#event_modal #"+key ).after( '<span class="error_txt" style="color:red; text-indent:15px;">'+val[0]+'</span>' );
                        console.log(key);
                    });
                }
                else{
                    loadEvents();
                    Swal.close();
                    $('#event_modal').modal('hide');
                }
            },error: function(msg) {
                handleErrorResponse(msg);
                $("#event_modal #save_btn").val('Save');
                $("#event_modal #save_btn").removeAttr('disabled');
                //general error
                console.log("Error");
                console.log(msg.responseText);
            }


        });
    }
</script>