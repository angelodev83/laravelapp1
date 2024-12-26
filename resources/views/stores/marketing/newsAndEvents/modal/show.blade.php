<div class="modal" id="show_modal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header bg-gray">
                <h6 class="mx-3 modal-title text-dark" id="show_subject">
                    Subject
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="px-5 py-3 modal-body">
                <!-- <div class="card">
                    <div class="row g-0">
                        <div class="col-md-4">
                            <img src="" alt="..." class="card-img" id="show_img">
                        </div>
                        <div class="col-md-8">
                            <div class="card-body d-flex flex-column">
                                <h6 class="card-title" id="show_title"></h6>
                                <p class="card-text" id="show_caption"></p>
                                
                            </div>
                        </div>
                    </div>
                </div> -->
                <div id="show_body">Content</div>
            </div>
            <div class="mx-3 modal-footer"  id="show_footer">
                Footer
            </div>

        </div>
    </div>
</div>

<script>
    function showMore(newsData){
        // let btn = document.querySelector(`#marketing-announcement-edit-btn-${id}`);
        // let subject = btn.dataset.subject;
        // let content = btn.dataset.content;
        // let created_at = btn.dataset.createdAt;
        let data = JSON.parse(newsData);
        // console.log(data);
        

        $('#show_subject').html(data.name);
        // $('#show_title').html(data.name);
        // $('#show_caption').html(data.caption);
        // $('#show_img').attr('src', data.path);
        console.log(data.content);
        $('#show_body').html(data.content);
        let date = new Date(data.updated_at);
            let formattedDate = date.toLocaleString('en-US', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: 'numeric',
                minute: 'numeric',
                second: 'numeric',
                hour12: true
            });
        $('#show_footer').html(date);

        // tagAsReadBulletinAnnouncement(id)

        $('#show_modal').modal('show');
    }

    function tagAsReadBulletinAnnouncement(id)
    {
        $.ajax({
            url: `/store/marketing/announcements/view/${id}`,
            type: 'get',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {},
            dataType: 'json',
            success:function(response){
                console.log("response");
            },
            error: function(msg) {
                handleErrorResponse(msg);
            }
        });
    }
</script>