<div class="modal" id="view_modal" tabindex="-1">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
        <div class="modal-header">
            <h6 class="modal-title" id="modal_title"></h6>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">      
            <div class="row">
                <div  style="margin-top: 20px;">
                            <div class="card-body p-4">
                    <div class="col-lg-12">
                        <input type="hidden" id="id" name="id" value="">
                    
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label label for="task_name" class="form-label">TASK NAME</label>
                                <ul class="list-group list-group-flush" id="task_name"></ul>
                            </div>
                            <div class="col-md-4">
                                <label for="patient_name" class="form-label">PATIENT NAME</label>
                                <ul class="list-group list-group-flush" id="patient_name"></ul>
                            </div>
                            <div class="col-md-4">
                                <label for="patient_birthdate" class="form-label">PATIENT BIRTH DATE</label>
                                <ul class="list-group list-group-flush" id="patient_birthdate"></ul>
                            </div>
                            <div class="col-md-12 div_medications">
                                <label for="medications" class="form-label">MEDICATION(S)</label>
                                <ul class="list-group list-group-flush" id="medications"></ul>
                            </div>
                            <div class="col-md-4 div_outlier_type">
                                <label label for="outlier_type" class="form-label">OUTLIER TYPE</label>
                                <ul class="list-group list-group-flush" id="outlier_type"></ul>
                            </div>
                            <div class="col-md-4 div_completed_date">
                                <label for="completed_date" class="form-label">COMPLETED DATE</label>
                                <ul class="list-group list-group-flush" id="completed_date"></ul>
                            </div>
                            <div class="col-md-4 div_date_of_interaction">
                                <label for="date_of_interaction" class="form-label">DATE OF INTERACTION</label>
                                <ul class="list-group list-group-flush" id="date_of_interaction"></ul>
                            </div>
                            <div class="col-md-4 div_date_of_initiation">
                                <label for="date_of_initiation" class="form-label">DATE OF INITIATION</label>
                                <ul class="list-group list-group-flush" id="date_of_initiation"></ul>
                            </div>
                            <div class="col-md-4 div_side_effects">
                                <label for="side_effects" class="form-label">SIDE EFFECTS </label>
                                <ul class="list-group list-group-flush" id="side_effects"></ul>
                            </div>
                            <div class="col-md-4 div_date_side_effects">
                                <label for="date_side_effects" class="form-label">DATE SIDE EFFECTS</label>
                                <ul class="list-group list-group-flush" id="date_side_effects"></ul>
                            </div>
                            <div class="col-md-4 div_date_follow_up">
                                <label for="date_follow_up" class="form-label">DATE FOLLOW UP</label>
                                <ul class="list-group list-group-flush" id="date_follow_up"></ul>
                            </div>
                            <div class="col-md-6 div_recommended_vitamins">
                                <label for="recommended_vitamins" class="form-label">RECOMMENDED VITAMINS</label>
                                <ul class="list-group list-group-flush" id="recommended_vitamins"></ul>
                            </div>
                            <div class="col-md-4 div_pdc_rate">
                                <label for="pdc_rate" class="form-label">PDC RATE</label>
                                <ul class="list-group list-group-flush" id="pdc_rate"></ul>
                            </div>
                            <div class="col-md-12">
                                <label for="comments" class="form-label">COMMENTS</label>
                                <ul class="list-group list-group-flush" id="comments"></ul>
                            </div>
                        </div> 

                    </div>
                            </div>
                </div>
            </div><!--end row-->
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
        </div>
  </div>
</div>

<script>

    
</script>