<div class="card">
    <div class="card-body">
        <h6 class="my-3 mt-0 fs-5 fw-bold text-body-secondary">Recent Uploaded Files</h6>
        <div class="dt-loading-spinner">
            <i class="fas fa-spinner fa-spin fa-3x"></i> <!-- Example: Font Awesome spinner icon -->
        </div>
        <div id="recent_files">

        </div>
    </div>
</div>

<script>
    function loadRecentFiles(page_id)
    {
        let data = {
            pharmacy_store_id: menu_store_id,
            parent_page_id: 34
        };
        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "POST",
            url: `/admin/store-files/recent`,
            data: JSON.stringify(data),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(res) {
                console.log("get res",res)

                const files = res.data;

                $('.dt-loading-spinner').addClass('d-none');

                let recentHtml = '';

                $('#recent_files').empty();

                for (let f in files) {
                    const file = files[f];

                    recentHtml += `<div class="p-2 row">
                        <div class="gap-3 col d-flex fw-medium text-body-secondary">
                            <img rounded-3" style="width: auto; height: 100px; background-color: ${file.background_color}; color: ${file.text_color};"  src="${file.icon_path}" alt="">
                            <div style="word-wrap: break-word; word-break: break-all;">
                                <h6>${file.name}</h6>
                                <small>${file.folder.page.name}</small>
                            </div>
                        </div>
                    </div>`;
                }

                $('#recent_files').html(recentHtml);

            },error: function(msg) {
                handleErrorResponse(msg);
                if(msg.status == 403) {
                    sweetAlert2('warning', "403 Forbidden: You don't have permission to do this action");
                }
                console.log(msg.responseText);
            }
        });
    }
</script>