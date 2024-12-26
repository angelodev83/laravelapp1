
<div id="rts-container" class="gap-3 d-flex container-lists">
    @foreach($rtsStatus as $stat)
        <div id="card-rts-status-{{$stat['id']}}" class="card rounded-4 card-rts-status" 
        
        style="
            max-width: 16rem; 
            min-width: 16rem; 
            background-color: {{$stat['light_color']}};
            height: 470px;
        ">

            <div class="p-1">
                <div class="px-2 pt-2 mb-2 d-flex">
                    <div class="gap-2 d-flex justify-content-between flex-container" id="text-container-id-{{$stat['id']}}">
                        <h6 class="py-1 px-2 rounded-3 card-title" style="font-size: 14px; background-color: {{$stat['color']}}; color: {{$stat['text_color']}};">{{$stat['name']}}</h6>
                        <span class="fs-6 fw-medium d-none">0</span>
                    </div>
                </div>
                <div id="rts-status-{{$stat['id']}}" class="px-2 mb-2 content draggable-lists" 

                style="
                    position: relative; 
                    height: 390px;
                ">

                    <button class="btn btn-dark" type="button" disabled> <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
                        Loading... Please wait.</button>
                </div>
            </div>
        </div>
    @endforeach
</div>


<script>

    function loadBoardData(showLoading = false)
    {
        let data = {
            search: $('#rts_search').val(),
            is_archived: $('#is_archived').val()
        };
        if(showLoading === true) {
            sweetAlertLoading();
        }
        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "POST",
            url: `rts/filter-board-data`,
            data: JSON.stringify(data),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(res) {
                console.log("get res",res)

                const groups = res.data;

                let boardData = '';
                
                for (let g in groups) {
                    const group = groups[g].item;
                    const details = groups[g].collection;
                    const count = details.length;
                    
                    let cardsHtml = '';
                    let compute_card_height = 0;

                    $(`#rts-status-${g}`).empty();

                    let flag = false;

                    for(let d in details) {
                        const raw = details[d].raw;
                        const formatted = details[d].formatted;
                        const comments = raw.comments;

                        let archiveHtml = '';

                        if(raw.is_archived == 0) {
                            archiveHtml = `
                                <a class="dropdown-item fw-medium d-flex text-warning" href="javascript:clickArchiveBtn(${raw.id}, '${raw.rx_number}');"> Archive <i class="fa fa-box-archive ms-auto text-end text-warning"></i></a>
                            `;
                        } else {
                            archiveHtml = `
                                <a class="dropdown-item fw-medium d-flex text-success" href="javascript:clickUnarchiveBtn(${raw.id}, '${raw.rx_number}');"> Un-archive <i class="fa fa-arrow-rotate-left ms-auto text-end text-success"></i></a>
                            `;
                        }

                        let appendHtml = `
                            <div id="rts-card-data-id-${raw.id}" class="hover-card card m-0 p-0 mb-2" data-rts-id="${raw.id}" data-rts-status-id="${raw.status_id}">
                                <div class="card-body m-0 p-2" onclick="openRTSCard(${raw.id}, ${raw.is_archived})">
                                    <h6 class="my-0 pt-1 pb-0">${formatted.patient_fullname}</h6>
                                    <p class="my-0 pt-0 pb-2">RX# ${raw.rx_number}</p>
                                    <div class="d-flex justify-content-between flex-container px-0 mx-0">
                                        <span class="badge px-1 me-1" style="font-size: 11px; background-color: ${formatted.days_in_queue_bg_color}; color: ${formatted.days_in_queue_text_color};">
                                            <i class="fa fa-clock"></i>
                                            ${formatted.days_in_queue} Days
                                        </span>
                                        <span class="badge bg-white px-1 text-black me-1" style="font-size: 11px; border: 1px solid black;">
                                            <i class="fa-regular fa-calendar"></i>
                                            ${formatted.fill_date}
                                        </span>
                                        <span class="badge px-1 text-black" style="font-size: 11px; background-color: #b3dcbc;">
                                            <i class="fa fa-phone"></i>
                                            ${raw.call_attempts}/3
                                        </span>
                                    </div>
                                </div>
                                <div class="dropdown dots">
                                    <button type="button" class="btn dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>${archiveHtml}</li>
                                        <li><a class="dropdown-item fw-medium d-flex text-danger" href="javascript:clickDeleteBtn(${raw.id}, '${raw.rx_number}');"> Delete <i class="fa fa-trash-can ms-auto text-end text-danger"></i></a></li>
                                    </ul>
                                </div>
                            </div>
                        `;

                        flag = true;

                        $(`#rts-status-${g}`).append(appendHtml);

                        let h = $(`#rts-card-data-id-${raw.id}`).height();
                        h+=8.3;
                        compute_card_height += h;
                        console.log("card data height ------PX ", h);
                    }

                    console.log("status id ------ ", g);
                    console.log("compute_card_height ------PX ", compute_card_height);

                    let scrollable = true;

                    // if(compute_card_height < $_responsive_scroll_height) { // TODO: un-comment if it is still scrollable via monitor height
                        scrollable = false;
                        if(compute_card_height == 0) {
                            $(`#card-rts-status-${g}`).height(120);
                        } else {
                            let computed = Math.round(compute_card_height);
                            if(g == 924) {
                                computed+=18;
                            }
                            $(`#card-rts-status-${g}`).height(computed+80);
                        }
                    // } // TODO: un-comment if it is still scrollable via monitor height

                    if(flag === false) {
                        $(`#rts-status-${g}`).append('No Records Found.');
                    }
 
                    if(scrollable === true) {
                        $(`#card-rts-status-${g}`).height($_responsive_card_height);
                        $(`#rts-status-${g}`).height($_responsive_scroll_height);
                        new PerfectScrollbar(`#rts-status-${group.id}`);
                    }
                }

                if(showLoading === true) {
                    swal.close();
                }

                // setTimeout(function() {
                    dragSingleBoardCard();
                // }, 1200);

            },error: function(msg) {
                handleErrorResponse(msg);
            }
        });
    }

    // drag
    function dragSingleBoardCard()
    {
        let isAjaxRequestInProgress = false;
        $('.draggable-lists').sortable({
            connectWith: '.draggable-lists',
            cursor: 'grabbing',
            opacity: 0.6,
            placeholder: 'placeholder',
            update: function(event, ui) {
                if (!isAjaxRequestInProgress) {
                    isAjaxRequestInProgress = true;

                    const rts_id = ui.item.data('rts-id');
                    let old_status_id = ui.item.data('rts-status-id')+'';
                    
                    let new_status_id = $(ui.item[0]).closest('.draggable-lists').attr('id');
                    new_status_id = new_status_id.slice(11);

                    console.log('dragging', this.id, rts_id);

                    console.log("old status", old_status_id);
                    console.log("new status", new_status_id);

                    // if(old_status_id != new_status_id) {
                        updateDetails('status_id', rts_id, new_status_id);
                    // }

                }
            }
        }).disableSelection();
    }

    function clickUnarchiveBtn(rts_id, rx_number)
    {
        updateDetails('is_archived', rts_id, 0);
    }
</script>