
    <li class="sidebar-store-procurement-nav">
        <a class="sidebar-store-procurement-nav-a has-arrow" href="javascript:;">
            @if($numberOfStorePermissions == 1)
                <div class="parent-icon"><i class='fa-solid fa-store fa-xs' ></i></div>
                <div class="menu-title">Procurement</div>
            @else
                <i class="fa-solid fa-store me-2"></i>Procurement
            @endif
        </a>
        <ul>
            <li> <a class="has-arrow" href="javascript:;"><i class="fa-solid fa-prescription-bottle-medical ms-2 me-3"></i>Pharmacy</a>
                <ul>
                    @canany(['menu_store.procurement.pharmacy.drug_orders.index', 'menu_store.procurement.pharmacy.drug_orders.create', 'menu_store.procurement.pharmacy.drug_orders.update', 'menu_store.procurement.pharmacy.drug_orders.delete'])
                        <li>
                            <a href="/store/procurement/{{$menu->id}}/pharmacy/drug-orders"><i class="bx bx-radio-circle-marked"></i>Drug Orders</a>
                        </li>
                    @endcanany
                    @canany(['menu_store.procurement.pharmacy.drug_order_invoice.index', 'menu_store.procurement.pharmacy.drug_order_invoice.create', 'menu_store.procurement.pharmacy.drug_order_invoice.update', 'menu_store.procurement.pharmacy.drug_order_invoice.delete'])
                        <li>
                            <a href="/store/procurement/{{$menu->id}}/pharmacy/drug-order-invoices/{{date('Y')}}/{{date('n')}}"><i class="bx bx-paperclip"></i>Drug Order Invoices</a>
                        </li>
                    @endcanany
                    @canany(['menu_store.procurement.pharmacy.supplies_orders.index', 'menu_store.procurement.pharmacy.supplies_orders.create', 'menu_store.procurement.pharmacy.supplies_orders.update', 'menu_store.procurement.pharmacy.supplies_orders.delete'])
                        <li>
                            <a href="/store/procurement/{{$menu->id}}/pharmacy/supply-orders"><i class="bx bx-radio-circle"></i>Supplies Orders</a>
                        </li>
                    @endcanany
                    @canany(['menu_store.procurement.pharmacy.wholesale_drug_returns.index', 'menu_store.procurement.pharmacy.wholesale_drug_returns.create', 'menu_store.procurement.pharmacy.wholesale_drug_returns.update', 'menu_store.procurement.pharmacy.wholesale_drug_returns.delete'])
                        <li>
                            <a href="/store/procurement/{{$menu->id}}/pharmacy/wholesale-drug-returns"><i class="bx bx-radio-circle"></i>Wholesale Drugs Returns</a>
                        </li>
                    @endcanany
                    @canany(['menu_store.procurement.pharmacy.inmar_returns.index', 'menu_store.procurement.pharmacy.inmar_returns.create', 'menu_store.procurement.pharmacy.inmar_returns.update', 'menu_store.procurement.pharmacy.inmar_returns.delete'])
                        <li>
                            <a href="/store/procurement/{{$menu->id}}/pharmacy/inmar-returns"><i class="bx bx-radio-circle"></i>PharmaLogistics</a>
                        </li>
                    @endcanany
                </ul>
            </li>

            @canany(['menu_store.procurement.clinical_orders.index', 'menu_store.procurement.clinical_orders.create', 'menu_store.procurement.clinical_orders.update', 'menu_store.procurement.clinical_orders.delete'])
                <li>
                    <a href="/store/procurement/{{$menu->id}}/clinical-orders"><i class="fa-solid fa-truck-medical ms-2 me-2"></i>Clinical Orders</a>
                </li>
            @endcanany

            <!-- inventory reconciliation -->
            @canany(array_keys($menuStoreGroupPermissions,'inventory_reconciliation'))
            @if($numberOfStorePermissions == 1)
                @include('layouts/sidebar/stores/inventory-reconciliation-nav')
            @else
                <ul>
                    @include('layouts/sidebar/stores/inventory-reconciliation-nav')
                </ul>
            @endif
            @endcanany

            @canany(['menu_store.procurement.drug_recall_notifications.index', 'menu_store.procurement.drug_recall_notifications.create', 'menu_store.procurement.drug_recall_notifications.update', 'menu_store.procurement.drug_recall_notifications.delete'])
                <li>
                    <a href="/store/procurement/{{$menu->id}}/drug-recall-notifications"><i class="fa-solid fa-note-sticky ms-2 me-3"></i>Drug Recall Returns</a>
                </li>
            @endcanany

        </ul>
    </li>
