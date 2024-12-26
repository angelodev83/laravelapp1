
    <li class="sidebar-store-bulletin-nav"> 
        <a class="sidebar-store-bulletin-nav-a has-arrow" href="javascript:;">
            @if($numberOfStorePermissions == 1)
                <div class="parent-icon"><i class='fa-solid fa-chalkboard-user fa-xs' ></i></div>
                <div class="menu-title">Bulletin</div>
            @else
                <i class="fa-solid fa-chalkboard-user me-2"></i>Bulletin
            @endif
        </a>
        <ul>
            @can('menu_store.bulletin.dashboard.index')
                <li>
                    <a href="/store/bulletin/{{$menu->id}}/dashboard"><i class="fa-solid fa-table-columns ms-2 me-3"></i>Dashboard</a>
                </li>
            @endcan
            @canany(['menu_store.bulletin.task_reminders.index', 'menu_store.bulletin.task_reminders.create', 'menu_store.bulletin.task_reminders.update', 'menu_store.bulletin.task_reminders.delete'])
                <li>
                    <a href="/store/bulletin/{{$menu->id}}/task-reminders"><i class="fa-regular fa-note-sticky ms-2 me-3"></i>Task Reminders</a>
                </li>
            @endcanany
            
        </ul>
    </li>
