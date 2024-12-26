<div class="modal" id="show_announcement_modal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header bg-gray">
                <h6 class="mx-3 modal-title text-dark" id="show_announcement_subject">
                    Subject
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="px-5 py-3 modal-body" id="show_announcement_body">
                Content
            </div>
            <div class="mx-3 modal-footer"  id="show_announcement_footer">
                Footer
            </div>

        </div>
    </div>
</div>

<script>
    function showStoreAnnouncementModal(id){
        let btn = document.querySelector(`#marketing-announcement-edit-btn-${id}`);
        let subject = btn.dataset.subject;

           $.ajax({
            url: '/store/marketing/announcements/get/' + id, 
            method: 'POST',
            headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
            success: function(response) {
                let content = response.content; 
                 $('#show_announcement_body').html(content);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                handleErrorResponse(errorThrown);
                // Handle any errors here
                console.error(textStatus, errorThrown);
            }
        });

        let created_at = btn.dataset.createdAt;

        $('#show_announcement_subject').html(subject);
        
        $('#show_announcement_footer').html(created_at);

        tagAsReadBulletinAnnouncement(id)

        $('#show_announcement_modal').modal('show');
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