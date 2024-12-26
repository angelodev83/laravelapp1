@extends('layouts.master')

@section('content')

 <!-- PAGE-HEADER -->
      <div class="page-header">
        <div>
          <h1 class="page-title">Upload and Update Medication List</h1>
        </div>
      </div>
      <!-- PAGE-HEADER END -->

     
     <!-- EOF ERX ORDER -->

      <div class="p-5 bg-light rounded-3" >
            <table id="medications_table" class="table text-center table-light" style="width:100%">
                <thead></thead>
                <tbody>
                    
                </tbody>
            </table>
    </div>

    

@stop

@section('pages_specific_scripts')
            <script>

        
             


                    
                                  
          
        </script>
@stop




@extends('layouts.master')

@section('content')
            <div id="layoutSidenav_content">
                <main>
                    <div class="px-4 container-fluid">
                     <h1 class="mt-4 float-start">Upload Files</h1>

                         <div class="clearfix"></div>

                         <div id="fileDropArea" class="p-5 mt-4 text-center border d-flex border-3 align-items-center justify-content-center">
                            <span class="fw-bold lead " id="droparea_text">Drag and drop PDF files here to upload</span>
                        </div>

                        <div class="clearfix"></div>
                        <div class="table-responsive">
                        <table class="table mt-4 table-responsive" id="FilesTable">
                            <thead>
                            <tr>
                                <th>File</th>
                                <th>Size</th>
                                <th>Status</th>  
                            </tr>
                            </thead>
                            <tbody>
                           
                            </tbody>
                        </table>
                        </div>
                       


                    </div>
                </main>
                <footer class="py-4 mt-auto bg-light">
                    <div class="px-4 container-fluid">
                        <div class="d-flex align-items-center justify-content-between small">
                            <div class="text-muted">Ubacare Portal</div>
                            
                        </div>
                    </div>
                </footer>
            </div>



@stop

@section('pages_specific_scripts')
            <script>

    $(document).ready(function() {

             async function uploadFilesSequentially(files) {
                        for (let i = 0; i < files.length; i++) {
                            
                            try {
                                await uploadFile(files[i]);
                                
                            } catch (error) {
                                console.error('Upload failed:', error);
                                // Handle the error if uploadFile encounters any issues
                            }
                        }
            }

            $('#fileDropArea').on('dragover', function(event) {
                event.preventDefault();
                $(this).addClass('dragover');
                $(this).css('border-style', 'dotted'); // Change border style to dotted on dragover
                $(this).css('background', '#eee'); // Change border style to dotted on dragover
            }).on('dragleave', function(event) {
                event.preventDefault();
                $(this).removeClass('dragover');
                $(this).css('background', '#fff'); // Change border style to dotted on dragover
            }).on('drop', function(event) {
                    event.preventDefault();
                    var files = event.originalEvent.dataTransfer.files;
                    var pdfFiles = [];
                   
                   
                    
                    for (var i = 0; i < files.length; i++) {
                        
                        if (files[i].type === 'application/pdf') {
                            pdfFiles.push(files[i]);
                            appendRowToFilesTable(files[i]);

                        }

                    }

                     $('#droparea_text').html('Uploading:'+ pdfFiles.length +' PDF file(s)');

                    // Call the function to upload files sequentially
                    uploadFilesSequentially(pdfFiles);

            });
    
            $( ".datepicker" ).datepicker();                 
        });



   

      function appendRowToFilesTable(file) {
            
                // Get file details
                var fileName = file.name;
                var fileSize = file.size;

                // Create a unique ID for the row based on file details
               // var uniqueId = 'fileRow_' + fileName.replace(/\s+/g, '') + '_' + fileSize ;
               var uniqueId = 'fileRow_' + fileName.replace(/\.[^/.]+$/, '').replace(/\s+/g, '') + '_' + fileSize;

                // Create a new row in the FilesTable
                var newRow = '<tr id="' + uniqueId + '">' +
                  
                    '<td>' + fileName + '</td>' +
                    '<td>' + fileSize + ' bytes</td>' +
                    '<td class="status">Ready s</td>' +
                    '</tr>';

                // Append the new row to the FilesTable
                $('#FilesTable').append(newRow);
    }



    function uploadFile(file) {
            return new Promise(function(resolve, reject) {
                var formData = new FormData();
                formData.append('pdf', file); // 'pdf' to indicate a single file named 'pdf'

                var fileName = file.name;
                var fileSize = file.size;
                var uniqueId = 'fileRow_' + fileName.replace(/\.[^/.]+$/, '').replace(/\s+/g, '') + '_' + fileSize;
                $('#' + uniqueId + ' .status').html('Uploading...');

                $.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    url: '/admin/upload', // Modify this endpoint if needed
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        $('#' + uniqueId + ' .status').html('<span class="p-2 badge bg-green" style="color: #fff; background-color: green">Uploaded</span>');
                        console.log('File uploaded successfully woohoo:', response.file, uniqueId);
                        resolve(); // Resolve the promise on successful upload
                    },
                    error: function(xhr, status, error) {
                        handleErrorResponse(error);
                        console.error('Error uploading file:', error);
                        $('#' + uniqueId + ' .status').html('<span class="p-2 badge bg-danger" style="color: #fff; background-color: green">Upload Failed</span>');
                        
                       
                        reject(error); // Reject the promise on upload error
                    },
                    statusCode: {
                        500: function(xhr, status, error) {
                            console.error(status.message);
                             $('#' + uniqueId + ' .status').html('<span class="p-2 badge bg-danger" style="color: #fff; background-color: green">ERROR</span>');
                        
                            reject(error); // Reject the promise on server error
                        }
                    }
                });
            });
        }


    
        </script>
@stop
