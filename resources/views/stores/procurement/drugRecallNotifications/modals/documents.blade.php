<div class="modal" id="show_drug_recall_notification_documents_modal" tabindex="-1">
    <div class="shadow modal-dialog modal-lg">
        <div class="modal-content bg-gray">
            <div class="modal-header">
                <h6 class="modal-title">Drug Recall Return Documents</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
            
                <!--start row-->
                <div class="container">                         
                    <div class="row">
                        <div class="col">

                            <input type="file" id="documents" multiple hidden>

                            <div class="mt-4 card">
                                <div class="card-header document-card-header">
                                    <input type="text" id="search_document_input" class="table_search_input form-control" placeholder="Search...">
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table id="dt_document_table" class="table row-border table-hover" style="width:100% !important;">
                                            <thead></thead>
                                            <tbody>                                   
                                            </tbody>
                                            <tfooter></tfooter>
                                        </table>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <!--end row-->

            </div>

        </div>
    </div>
</div>



<script>   
    function showDrugRecallNotificationDocuments()
    {   
        $('#show_drug_recall_notification_documents_modal .modal-title').html(`Drug Recall Notification Documents <br>Reference #${show_drug_recall_notification_reference_number}`);
        $('#show_drug_recall_notification_documents_modal').modal('show');
    }

    function clickDrugRecallNotificationUploadDocuments()
    {
        $('#show_drug_recall_notification_documents_modal #documents').click();
    }
</script>