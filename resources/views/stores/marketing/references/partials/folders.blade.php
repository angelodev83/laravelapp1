<div class="my-3 mx-2 row">
    @foreach ($folders as $folder)
        <div class="my-2 col-12 col-md-4 col-sm-4">
            <div id="knowledge-base-folder-card-id-{{$folder->id}}" class="text-center parent rounded-5 knowledge-base-folder-card" onclick="clickFolder({{$folder->id}})" style="background-color: {{$folder->background_color}}; border: solid 2px {{$folder->background_color}} ;">
                <div>
                    <img width="230" src="{{$folder->icon_path}}" alt="">
                </div>
                <div id="folder_bg_{{$folder->id}}" class="py-3 bg-white rounded-bottom-5 folder_bg_all">
                    <p class="my-0 me-1 folder-widget-text text-body-secondary">{{ $folder->name }}</p>
                    <small class="p-0 m-0 text-secondary">{{ $folder->files->count() }} file(s)</small>
                </div>
                <span class="folder-btn-group-icon">
                    @can($permissions['update'])
                        <i class="fa-regular fa-pen-to-square folder-edit-icon p-1" style="width: 20px; height: 20px"  onclick="clickEditFolder({{$folder}})"></i>
                    @endcan
                    @can($permissions['delete'])
                        <i class="fa-regular fa-trash-can folder-delete-icon p-1 d-none" style="width: 20px; height: 20px"  onclick="clickDeleteFolderBtn(event, {{$folder}}, {{request()->id}})"></i>
                    @endcan
                </span>
            </div>
        </div>
        @endforeach
    </div>