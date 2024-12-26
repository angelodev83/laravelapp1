
    <li class="sidebar-store-operations-nav"> 
        <a class="sidebar-store-operations-nav-a has-arrow" href="javascript:;">
            @if($numberOfStorePermissions == 1)
                <div class="parent-icon"><i class='fa-solid fa-laptop-medical fa-xs' ></i></div>
                <div class="menu-title">Operations</div>
            @else
                <i class="fa-solid fa-laptop-medical me-2"></i>Operations
            @endif
        </a>
        <ul>
            <li>
                <a href="/store/operations/{{$menu->id}}/dashboard"><i class="fa-solid fa-chart-column ms-2 me-3"></i>Dashboard</a>
            </li>
            {{-- @canany(['menu_store.operations.mail_orders.index', 'menu_store.operations.mail_orders.create', 'menu_store.operations.mail_orders.update', 'menu_store.operations.mail_orders.delete'])
                <li>
                    <a href="/store/operations/{{$menu->id}}/mail-orders"><i class="bx bx-right-arrow-alt me-2"></i>Mail Orders</a>
                </li>
            @endcanany --}}
            @canany(['menu_store.operations.mail_orders.index', 'menu_store.operations.mail_orders.create', 'menu_store.operations.mail_orders.update', 'menu_store.operations.mail_orders.delete'])
                <li>
                    <a href="/store/operations/{{$menu->id}}/rts"><i class="fa-solid fa-boxes-stacked ms-2 me-3"></i>Return To Stock</a>
                </li>
            @endcanany       

            @canany(['menu_store.operations.for_shipping_today.index', 'menu_store.operations.for_shipping_today.create', 'menu_store.operations.for_shipping_today.update', 'menu_store.operations.for_shipping_today.delete'])
            <li>
                <a href="/store/operations/{{$menu->id}}/for-shipping-today"><i class="fa-solid fa-truck-arrow-right ms-2 me-3"></i>For Shipping Today</a>
            </li>
            @endcanany

            @canany(['menu_store.operations.for_delivery_today.index', 'menu_store.operations.for_delivery_today.create', 'menu_store.operations.for_delivery_today.update', 'menu_store.operations.for_delivery_today.delete'])
            <li>
                <a href="/store/operations/{{$menu->id}}/for-delivery-today"><i class="fa-solid fa-cart-flatbed ms-2 me-3"></i>For Delivery Today</a>
            </li>
            @endcanany

            @canany(['menu_store.operations.meetings.index', 'menu_store.operations.meetings.create', 'menu_store.operations.meetings.update', 'menu_store.operations.meetings.delete'])
            <li>
                <a href="/store/operations/{{$menu->id}}/meetings/{{date('Y')}}/{{date('n')}}"><i class="fa-solid fa-notes-medical ms-2 me-3"></i>Meetings</a>
            </li>
            @endcanany

        </ul>
    </li>
