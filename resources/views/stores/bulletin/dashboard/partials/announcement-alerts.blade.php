@foreach ($bulletinAnnouncements as $a)
    <div class="alert alert-info border-0 alert-dismissible fade show py-2 shadow-sm dashboard-alert-info"> 
        <div class="d-flex align-items-center">
            <div class="font-35 notify text-primary"><i class="bx bx-mail-send"></i>
            </div>
            <div class="ms-3">
                <h6 class="mb-0 text-dark">{{ $a['subject'] }}</h6>
                <div class="text-dark">
                    <a href="javascript:;"
                        data-subject="<?=htmlspecialchars($a['subject'])?>" onclick="showViewAnnouncementByIdAndTypeModal('store',<?=$a['id']?>)">Read the contents by <u>clicking here</u>
                    </a>   
                </div>
            </div>
            <div class="ms-auto">
                <small>Created by: <b>{{isset($a['user']) ? ($a['user']['employee']['lastname'].', '.$a['user']['employee']['firstname']) : ''}}</b></small><br>
                <small>{{$a['formatted_pst_created_at']}}</small>
            </div>
        </div>
        <button type="button" class="btn-close" onclick="tagAsReadBulletinAnnouncement(<?=$a['id']?>)" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endforeach