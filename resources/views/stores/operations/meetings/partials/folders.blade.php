@if(empty(request()->month_number))

    <div class="row gx-3 mt-3 mb-0 pb-0">

        @for($i = 1; $i <= 12; $i++)

            <div class="col-3 py-0 ">
                <div class="card py-0 bg-all-files shadow-none">
                    <a href="/store/operations/{{request()->id}}/meetings/{{request()->year}}/{{$i}}" >
                        <div class="px-3 py-2 card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="knowledge-base-icon text-all-files">
                                    <i class="fa-regular fa-folder-open"></i>
                                </div>
                                <div>
                                    <p class="my-0 me-1 folder-widget-text text-body-secondary">
                                        {{ date('F', strtotime(request()->year.'-'.sprintf("%0d", $i).'-01')) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

        @endfor

    </div>
@else

    <div class="my-3 mx-2 row">
        @foreach ($folders as $folder)
            <div class="my-2 col-12 col-md-4 col-sm-4">
                <div id="knowledge-base-folder-card-id-{{$folder->id}}" class="text-center parent rounded-5 knowledge-base-folder-card" onclick="clickFolder({{$folder->id}})" style="background-color: {{$folder->background_color}}; border: solid 2px {{$folder->background_color}} ;">
                    <div>
                        <img width="230" src="{{$folder->icon_path}}" alt="">
                    </div>
                    <div id="folder_bg_{{$folder->id}}" class="py-3 bg-white rounded-bottom-5 folder_bg_all">
                        <p class="my-0 me-1 folder-widget-text text-body-secondary">{{ $folder->name }}</p>
                        <small class="p-0 m-0 text-secondary">{{ $monthlyCountFolders[$folder->id] }} file(s)</small>
                    </div>
                    <span class="folder-btn-group-icon">
                        @can($permissions['update'])
                            <i class="fa-regular fa-pen-to-square folder-edit-icon p-1" style="width: 20px; height: 20px"  onclick="clickEditFolder({{$folder}})"></i>
                        @endcan
                        @can($permissions['delete'])
                            <i class="fa-regular fa-trash-can folder-delete-icon p-1" style="width: 20px; height: 20px"  onclick="clickDeleteFolderBtn(event, {{$folder}}, {{request()->id}})"></i>
                        @endcan
                    </span>
                </div>
            </div>
        @endforeach
    </div>

@endif