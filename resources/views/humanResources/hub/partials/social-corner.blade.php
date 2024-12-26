<div class="card">
    <div class="card-body m-3">
        
        <h6 class="pb-2" style="color: #5e17ec;"><b>Social Corner</b></h6>

        @canany(['hr.hub.create', 'hr.hub.update'])
            <div class="row">
                <div class="col-12">
                    <div id="textarea-container" class="mx-0 px-0">
                        <div id="textarea-inner">
                            <textarea type="text" rows="1" id="chat-input" placeholder="Wtite a post.."></textarea>
                            <button type="submit" class="chat-submit" id="chat-submit" onclick="submitPost()">
                                <i class="fa-solid fa-paper-plane"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endcanany

        <div class="row" id="social_corner_div"></div>

        <div class="row mt-3">
            <div class="col-12 text-center">
                <button class="btn btn-sm btn-outline-primary" onclick="loadMoreSocialCorner(2)" id="social_corner_load_more_btn">Load More</button>
            </div>
            <i id="social_corner_load_more_p" class="text-secondary"></i>
        </div>

    </div>
</div>

<script>
    function submitPost()
    {
        let post = $('#chat-input').val();

        let data = {
            post: post
        };

        loadingSocialCorner();
        $.ajax({
            //laravel requires this thing, it fetches it from the meta up in the head
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "POST",
            url: `/admin/social-corner/add`,
            data: JSON.stringify(data),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(data) {
            
                $('#chat-input').val('');
                loadMoreSocialCorner(1);

            }, error: function(msg) {
                handleErrorResponse(msg);
                if(msg.status == 403) {
                    sweetAlert2('warning', "403 Forbidden: You don't have permission to do this action");
                }
                //general error
                console.log("Error");
                console.log(msg);
                $.each(msg.responseJSON.errors,function (key , val){
                    sweetAlert2('warning', 'Check field inputs.');
                    $("#"+key ).after( '<span class="error_txt" style="color:red; text-indent:15px;">'+val[0]+'</span>' );
                    console.log(key);
                });
            }
        });
    }
</script>