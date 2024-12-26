<div class="top-menu ms-auto end">
    <ul class="navbar-nav align-items-center gap-1">

        <li class="nav-item dropdown dropdown-large">
            <a id="common-header-bell-notification" class="nav-link dropdown-toggle dropdown-toggle-nocaret position-relative" href="javascript:;" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class='fa fa-bell fa-sm ms-2'></i>
            </a>
            <div class="dropdown-menu dropdown-menu-end">
                <a href="javascript:;">
                    <div class="msg-header">
                        <p class="msg-header-title">Announcements</p>
                        {{-- <p class="msg-header-clear ms-auto">Marks all as read</p> --}}
                    </div>
                </a>
                <div class="header-message-list" id="common-header-bell-notificaion-list">
                    
                </div>
                {{-- <a href="{{auth()->user()->can('executive.dashboard') ? '/admin/human_resources/announcements' : '/store/bulletin/'.request()->id.'/announcements'}}" id="common-header-bell-view-all-announcement-list">
                    <div class="text-center msg-footer">View All Announcements</div>
                </a> --}}
                <a href="/store/marketing/{{ isset(request()->id) ? request()->id : 1 }}/announcements" id="common-header-bell-view-all-announcement-list">
                    <div class="text-center msg-footer">View All Announcements</div>
                </a>
            </div>
        </li>
    </ul>
</div>