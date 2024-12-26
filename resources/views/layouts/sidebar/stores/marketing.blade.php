
    <li class="sidebar-store-marketing-nav">
        <a class="sidebar-store-marketing-nav-a has-arrow" href="javascript:;">
            @if($numberOfStorePermissions == 1)
                <div class="parent-icon"><i class='fa-solid fa-heart fa-xs' ></i></div>
                <div class="menu-title">Marketing</div>
            @else
                <i class="fa-solid fa-heart me-2"></i>Marketing
            @endif
        </a>
        <ul>
            @canany(['menu_store.marketing.news.index', 'menu_store.marketing.news.create', 'menu_store.marketing.news.update', 'menu_store.marketing.news.delete'])
                <li>
                    <a href="/store/marketing/{{$menu->id}}/news-and-events"><i class="fa-solid fa-calendar-check ms-2 me-3"></i>News & Events</a>
                </li>
            @endcanany
            @canany(['menu_store.marketing.announcements.index', 'menu_store.marketing.announcements.create', 'menu_store.marketing.announcements.update', 'menu_store.marketing.announcements.delete'])
                <li>
                    <a href="/store/marketing/{{$menu->id}}/announcements"><i class="fa-solid fa-bullhorn ms-2 me-3"></i>Announcements</a>
                </li>
            @endcanany
            @canany(['menu_store.marketing.decks.index', 'menu_store.marketing.decks.create', 'menu_store.marketing.decks.update', 'menu_store.marketing.decks.delete'])
                <li>
                    <a href="/store/marketing/{{$menu->id}}/decks"><i class="fa-solid fa-panorama ms-2 me-3"></i>Decks</a>
                </li>
            @endcanany
            @canany(['menu_store.marketing.references.index', 'menu_store.marketing.references.create', 'menu_store.marketing.references.update', 'menu_store.marketing.references.delete'])
                <li>
                    <a href="/store/marketing/{{$menu->id}}/references"><i class="fa-regular fa-folder-open ms-2 me-3"></i>References</a>
                </li>
            @endcanany
        </ul>
    </li>
