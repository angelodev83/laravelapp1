<div class="row mt-3">

    @foreach ($folders as $folder)
        <div class="col-12 col-md-4 col-sm-4">
            <div id="financial-reports-folder-card-id-{{$folder->id}}" class="card shadow-none border radius-10 financial-reports-folder-card" onclick="clickFolder({{$folder->id}})">
                <div class="card-body py-2 px-3">
                    <div class="d-flex align-items-center">
                        <div>
                            <p class="my-0 me-3  folder-widget-text">{{ $folder->name }}</p>
                            <small class="m-0 p-0 text-secondary">{{ $folder->files->count() }} file(s)</small>
                        </div>
                        <div class="widgets-icons bg-light-custom-financial-report text-custom-financial-report2 ms-auto">
                            <i id="financial-reports-folder-card-icon-id-{{$folder->id}}" class="financial-reports-folder-card-icon fa fa-folder-closed"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

</div>