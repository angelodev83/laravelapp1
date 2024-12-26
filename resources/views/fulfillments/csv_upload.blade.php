@extends('layouts.master')

@section('content')

 <!-- PAGE-HEADER -->
      <div class="page-header">
        <div>
          <h1 class="page-title">Upload Products CSV</h1>
        </div>
      </div>
      <!-- PAGE-HEADER END -->

     
     <!-- EOF ERX ORDER -->

      <div class="p-5 bg-light rounded-3" >
            <div class="clearfix"></div>
                        <form action="/upload" method="post" enctype="multipart/form-data"  id="medication_table">
                            @csrf
                            <div class="mb-3">
                                 <input type="file" class="form-control" id="csvFile" name="csvFile" accept=".csv">
                            </div>
                            <button type="submit" class="btn btn-primary">Upload</button> 
                        </form>
    </div>

    

@stop
x

@section('pages_specific_scripts')
            <script>
            $(document).ready(function() {
                $('form').on('submit', function(event) {
                    event.preventDefault();
                    $('.alert').remove(); // Remove any existing alerts
                    var file = $('#csvFile')[0].files[0]; // Get the file from the input field
                    

                    if (!file) {
                        $('#medication_table').before('<div class="mt-4 text-center alert alert-danger" role="alert">No file selected.</div>');
                        return;
                    }

                    var fileSize = file.size / 1024 / 1024; // in MB
                    var fileName = file.name;
                    var fileExtension = fileName.split('.').pop().toLowerCase();

                    if (fileExtension !== 'csv') {
                        $('#medication_table').before('<div class="mt-4 text-center alert alert-danger" role="alert">File is not a CSV.</div>');
                        return;
                    } else if (fileSize > 20) {
                        $('#medication_table').before('<div class="mt-4 text-center alert alert-danger" role="alert">File is larger than 20MB.</div>');
                        return;
                    }

                    var formData = new FormData();
                    formData.append('csvFile', file); // 'csvFile' to indicate a single file named 'csvFile'
                    $('#uploading_notif').removeClass('d-none');

                    var rowCount = 0;
                    if (file) {
                        var reader = new FileReader();
                        reader.onload = function(e) {
                            var contents = e.target.result;
                            var rowCount = contents.split('\n').length;
                           $('#medication_table').before('<div class="mt-4 text-center alert alert-primary" role="alert"><i class="fas fa-sync fa-spin"></i> Processing <span class="row_count">'+rowCount+'</span> rows. This may take a minute. Do not close this page until the process is completed.</div>');
                     
                        };
                        reader.readAsText(file);
                    }
                       

                    $.ajax({
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                        url: '/admin/upload_csv', // Modify this endpoint if needed
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(data) {
                            
                        console.log(data);
                            
                        $('.alert').remove(); // Remove any existing alerts
                        $('#medication_table').before('<div class="mt-4 text-center alert alert-success" role="alert"><i class="fas fa-sync fa-check"></i> Uploading complete. Click <a href="/admin/medications">here</a> to view the medication list. </div>');
                       
                   
                        },
                        error: function(msg) {
                            handleErrorResponse(msg);
                        }
                    });



                });
            });
            </script>
@stop
