<div class="card">
    <div class="card-body m-2">

        <!-- row start -->
        <div class="row gy-1">
            <h6 class="mb-0">Attachments</h6>
            <small class="attachment-label-color">Only accepts maximum size of 100 MB per file</small>

            <input type="file" id="upload_documents" name="files[]" multiple hidden>
            
            <div class="col-12 mb-0 pb-0">
                <div class="drop-area" id="dropArea">
                    <small class="mb-2">Drag & Drop Anywhere</small>
                    <div for="fileInput" class="btn btn-outline-primary w-100" style="border-radius: 100px;" id="upload_button" onclick="clickUploadBtn()">or select files to upload</div>
                </div>
                <div id="efileList mb-0 pb-0">
                    <!-- Display dragged files here -->
                </div>
            </div>

            <div class="col-12 mt-2" >

                <div class="store-metrics pe-0 mb-1 pb-1" id="attachmentsList"></div>

            </div>

        </div>
        <!-- row end -->

    </div>
</div>