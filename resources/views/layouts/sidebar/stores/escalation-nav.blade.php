
    <li class="sidebar-store-escalation-nav">
        <a class="sidebar-store-escalation-nav-a has-arrow" href="javascript:;">
            @if($numberOfStorePermissions == 1)
                <div class="parent-icon"><i class='fa-solid fa-circle-exclamation fa-2xs' ></i></div>
                <div class="menu-title">Escalation</div>
            @else
                <i class="fa-solid fa-circle-exclamation me-2"></i>Escalation
            @endif
        </a>
        <ul>
            @canany(['menu_store.escalation.reviews.index', 'menu_store.escalation.reviews.create', 'menu_store.escalation.reviews.update', 'menu_store.escalation.reviews.delete'])
                <li>
                    <a href="/store/escalation/{{$menu->id}}/reviews"><i class="fa-solid fa-star ms-2 me-3"></i>Reviews</a>
                </li>
            @endcanany
            @canany(['menu_store.escalation.tickets.index', 'menu_store.escalation.tickets.create', 'menu_store.escalation.tickets.update', 'menu_store.escalation.tickets.delete'])
                <li>
                    <a href="/store/escalation/{{$menu->id}}/tickets"><i class="fa-solid fa-ticket ms-2 me-3"></i>Tickets</a>
                </li>
            @endcanany
        </ul>
    </li>
