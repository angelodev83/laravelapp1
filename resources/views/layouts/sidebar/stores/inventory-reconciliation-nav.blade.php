
    {{-- <li class="sidebar-store-inventory-reconciliation-nav">
        <a class="sidebar-store-inventory-reconciliation-nav-a has-arrow" href="javascript:;">
            @if($numberOfStorePermissions == 1)
                <div class="parent-icon"><i class='fa-solid fa-warehouse fa-2xs' ></i></div>
                <div class="menu-title">Inventory Reconciliation</div>
            @else
                <i class="fa-solid fa-warehouse me-2"></i>Inventory Reconciliation
            @endif
        </a> --}}
    <li>
        <a class="has-arrow" href="javascript:;"><i class="fa-solid fa-warehouse ms-2 me-2"></i>Inventory Reconciliation</a>
        
        <ul>
            <!-- @canany(['menu_store.inventory_reconciliation.monthly.c2.index', 'menu_store.inventory_reconciliation.monthly.c2.create', 'menu_store.inventory_reconciliation.monthly.c2.delete'])
            <li> 
                <a class="has-arrow" href="javascript:;"><i class="fa-solid fa-pills ms-2 me-3"></i>Control Counts</a>
                <ul>
                    @canany(['menu_store.inventory_reconciliation.monthly.c2.index', 'menu_store.inventory_reconciliation.monthly.c2.create', 'menu_store.inventory_reconciliation.monthly.c2.delete'])
                        <li>
                            <a href="/store/inventory-reconciliation/{{$menu->id}}/monthly-control-counts/c2"><i class="bx bx-right-arrow-alt"></i>C2</a>
                        </li>
                    @endcanany
                    @canany(['menu_store.inventory_reconciliation.monthly.c3_5.index', 'menu_store.inventory_reconciliation.monthly.c3_5.create', 'menu_store.inventory_reconciliation.monthly.c3_5.delete'])
                        <li>
                            <a href="/store/inventory-reconciliation/{{$menu->id}}/monthly-control-counts/c3-5"><i class="bx bx-right-arrow-alt"></i>C3 - 5</a>
                        </li>
                    @endcanany
                </ul>
            </li>
            @endcanany -->
            @canany(['menu_store.inventory_reconciliation.daily.index', 'menu_store.inventory_reconciliation.daily.create', 'menu_store.inventory_reconciliation.daily.delete'])
                <li>
                    <a href="/store/inventory-reconciliation/{{$menu->id}}/daily-inventory-evaluation"><i class="fa-solid fa-tablets ms-2 me-3"></i>Daily Inventory Evaluation</a>
                </li>
            @endcanany
            @canany(['menu_store.inventory_reconciliation.weekly.index', 'menu_store.inventory_reconciliation.weekly.create', 'menu_store.inventory_reconciliation.weekly.delete'])
                <li>
                    <a href="/store/inventory-reconciliation/{{$menu->id}}/weekly-inventory-audit"><i class="fa-solid fa-house-medical-circle-check ms-2 me-3"></i>Inventory Audit (Weekly)</a>
                </li>
            @endcanany
        </ul>
    </li>
