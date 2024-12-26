@extends('layouts.master')

@section('content')
            <div id="layoutSidenav_content">
                <main>
                    <div class="px-4 container-fluid">
                     <h1 class="mt-4 float-start">Files</h1>
                    

                        <div class="clearfix"></div>
                        <div class="table-responsive">
                      <table class="table mt-4 table-responsive" id="FilesTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>File Name</th>
                                    <th>RX Number</th>
                                    <th>Date Uploaded</th>
                                    <th>Download</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($files as $file)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                     <td>{{ $file->prescription->order_number }}</td>
                                    <td>{{ $file->filename }}</td>
                                     <td>{{ $file->created_at}}</td>

                                    <td>
                                   <a target="_new" href="{{$file->secure_path}}" class="btn btn-primary btn-sm" tabindex="-1" role="button" aria-disabled="true"><i class="fa-solid fa-file-pdf" ></i></a>

                                   
                                    </td>
                                        
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        </div>
                        <div class="d-flex justify-content-center">
                                {{ $files->links('pagination::bootstrap-4') }}
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
@include('cs/modals/delete-file-confirmation')


@stop

@section('pages_specific_scripts')
            <script>

            $(document).ready(function() {
   

        $('#fileDropArea').on('dragover', function(event) {
        event.preventDefault();
        $(this).addClass('dragover');
    }).on('dragleave', function(event) {
        event.preventDefault();
        $(this).removeClass('dragover');
    }).on('drop', function(event) {
        event.preventDefault();

        var files = event.originalEvent.dataTransfer.files;
        var pdfFiles = [];
        
        for (var i = 0; i < files.length; i++) {
            if (files[i].type === 'application/pdf') {
                pdfFiles.push(files[i]);

                  // Append a row in FilesTable for each dropped file
                 appendRowToFilesTable(files[i]);
            }
        }

        if (pdfFiles.length > 0) {
            uploadFiles(pdfFiles); // Call the function to upload the files
        } else {
            alert('Please drop one or more PDF files.');
        }
    });

    function appendRowToFilesTable(file) {
    // Create a unique ID for the row
    var uniqueId = 'fileRow_' + Date.now();

    // Get file details
    var fileName = file.name;
    var fileSize = file.size;

    // Create a new row in the FilesTable
    var newRow = '<tr id="' + uniqueId + '">' +
        '<td>' + fileName + '</td>' +
        '<td>' + fileSize + ' bytes</td>' +
        '<td>Status: Pending</td>' +
        '</tr>';

    // Append the new row to the FilesTable
    $('#FilesTable').append(newRow);
}


    // Function to upload the file
    function uploadFile(file) {
        console.log(file);
        var formData = new FormData();
        formData.append('pdf', file); // 'pdf' is the name of the file input on the server side
        
        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            url: '/admin/upload', // Replace with your Laravel route or endpoint
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                // Handle success response
                console.log('File uploaded successfully!');
            },
            error: function(xhr, status, error) {
                // Handle error response
                handleErrorResponse(error);
                console.error('Error uploading file:', error);
            },
            statusCode: {
               500: function(xhr, status, error) {
                     console.error( status.message);
                  }
             }
            
        });
    }

    function uploadFiles(files) {
    var formData = new FormData();

    for (var i = 0; i < files.length; i++) {
        formData.append('pdf[]', files[i]); // 'pdf[]' to indicate an array of files named 'pdf'
    }

    $.ajax({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        url: '/admin/upload',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,

        success: function (response) {
            console.log('Files uploaded successfully!');
        },
        error: function (xhr, status, error) {
            handleErrorResponse(error);
            console.error('Error uploading files:', error);
        },
        statusCode: {
            500: function (xhr, status, error) {
                console.error(status.message);
            }
        }
    });
}

});

              $(document).ready(function() {
                



                    $( ".datepicker" ).datepicker();                 
              });
        </script>
@stop
