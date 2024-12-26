<!-- Modal -->
<div class="modal fade" id="edit_folder_modal" tabindex="-1" aria-labelledby="editModal" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="editFolderModalLabel">Edit Folder</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div id="folder_modal_body" class="modal-body">
                <div class="row g-3">
                    <div class="col-4">
                        <label class="form-label" for="color_picker">Background Color: </label>
                        <input class="form-control color-picker" type="text" id="background_color">
                        <input class="form-control" type="text" id="icon_path" hidden>
                        <input class="form-control" type="text" id="id" hidden>
                    </div>
                    <div class="col-4">
                        <label class="form-label" for="color_picker">Border Color: </label>
                        <input class="form-control color-picker" type="text" id="border_color">
                    </div>
                    <div class="col-4">
                        <label class="form-label" for="color_picker">Text Color: </label>
                        <input class="form-control color-picker" type="text" id="text_color">
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="name">Folder Name: </label>
                        <input class="form-control" type="text" id="name">
                    </div>
                    <div class="col-12">
                        <div>
                            Select Folder Icon: <img id="current_icon" src="" alt="" width="100" height="100">
                        </div>

                        <div class="gap-2 row">
                            <div class="col store-metrics" id="knowledge-base-icon-lists">
                                @foreach ($icons as $icon)
                                    <img id="icon" class="icon-card" src="{{$icon->path}}" width="100" height="100" alt="" onclick="selectFolderIconEditModal('{{$icon->path}}')">
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="updateEditFolder()">Save changes</button>
            </div>
        </div>
    </div>
</div>

<script>
    function clickEditFolder(data) {
        console.log(data);
        $('#edit_folder_modal').modal('show');
        $('#edit_folder_modal #id').val(data.id);
        $('#edit_folder_modal #name').val(data.name);
        $('#edit_folder_modal #background_color').val(data.background_color);
        $('#edit_folder_modal #text_color').val(data.text_color);
        $('#edit_folder_modal #border_color').val(data.border_color);
        $('#edit_folder_modal #icon_path').val(data.icon_path);

        if (!$('#current_icon').attr('src')) {
            $('#current_icon').attr('src', data.icon_path);
        }
    }

    function updateEditFolder()
    {
        let data = {
            id: $('#edit_folder_modal #id').val(),
            name: $('#edit_folder_modal #name').val(),
            background_color: $('#edit_folder_modal #background_color').val(),
            text_color: $('#edit_folder_modal #text_color').val(),
            border_color: $('#edit_folder_modal #border_color').val(),
            icon_path: $('#edit_folder_modal #icon_path').val(),
        };

        sweetAlertLoading();
        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "PUT",
            url: "/admin/store-folders/update",
            data: JSON.stringify(data),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(msg) {
                //success
                Swal.fire({
                    position: 'center',
                    icon: msg.status,
                    title: msg.message,
                    showConfirmButton: false,
                    timer: 4000
                });

                // $('#edit_folder_modal').modal('hide');
                window.location.reload(true);
            },error: function(msg) {
                handleErrorResponse(msg);
                if(msg.status == 403) {
                    sweetAlert2('warning', "403 Forbidden: You don't have permission to do this action");
                }
            }
        });
    }
    
    function selectFolderIconEditModal(icon) {
        $('#current_icon').attr('src', icon);
        $('#edit_folder_modal #icon_path').val(icon);
    }
</script>