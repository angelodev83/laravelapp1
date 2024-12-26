
    <li class="sidebar-store-hr-nav">
        <a class="sidebar-store-hr-nav-a has-arrow" href="javascript:;">
            @if($numberOfStorePermissions == 1)
                <div class="parent-icon"><i class='fa-solid fa-user-tie fa-xs' ></i></div>
                <div class="menu-title">People & Culture</div>
            @else
                <i class="fa-solid fa-user-tie me-2"></i>People & Culture
            @endif
        </a>
        <ul>
            @canany(['menu_store.hr.organization.index'])
                <li>
                    <a href="/store/human-resource/{{$menu->id}}/organization"><i class="fa-solid fa-sitemap ms-2 me-3"></i>CTCLUSI Pharmacy</a>
                </li>
            @endcanany
            
            
            @canany(['menu_store.hr.employees.index', 'menu_store.hr.employees.create', 'menu_store.hr.employees.update', 'menu_store.hr.employees.delete', 'menu_store.hr.employees.import'])
            <li> 
                <a class="has-arrow" href="javascript:;"><i class="fa-solid fa-building-user ms-2 me-3"></i>In Pharmacy</a>
                <ul>
                    <li>
                        <a href="/store/human-resource/{{$menu->id}}/onshore"><i class="bx bx-right-arrow-alt"></i>Employees</a>
                    </li>
                    <li>
                        <a href="/store/human-resource/{{$menu->id}}/onshore/org-chart"><i class="bx bx-right-arrow-alt"></i>TRHC Org Chart</a>
                    </li>

                    @canany(['menu_store.hr.schedules.index', 'menu_store.hr.schedules.create', 'menu_store.hr.schedules.update', 'menu_store.hr.schedules.delete', 'menu_store.hr.schedules.import'])
                        <li>
                            <a href="/store/human-resource/{{$menu->id}}/schedules/0/calendar"><i class="bx bx-right-arrow-alt"></i>Schedules</a>
                        </li>
                    @endcanany
                </ul>
            </li>
            @endcanany

            @canany(['menu_store.hr.employees.index', 'menu_store.hr.employees.create', 'menu_store.hr.employees.update', 'menu_store.hr.employees.delete', 'menu_store.hr.employees.import'])
            <li> 
                <a class="has-arrow" href="javascript:;"><i class="fa-solid fa-users ms-2 me-3"></i>Central Team</a>
                <ul>

                    <li>
                        <a href="/store/human-resource/{{$menu->id}}/employees"><i class="bx bx-right-arrow-alt"></i>Employees</a>
                    </li>
                    <li>
                        <a href="/store/human-resource/{{$menu->id}}/offshore/org-chart"><i class="bx bx-right-arrow-alt"></i>Central Org Chart</a>
                    </li>

                    @canany(['menu_store.hr.schedules.index', 'menu_store.hr.schedules.create', 'menu_store.hr.schedules.update', 'menu_store.hr.schedules.delete', 'menu_store.hr.schedules.import'])
                        <li>
                            <a href="/store/human-resource/{{$menu->id}}/schedules/1/calendar"><i class="bx bx-right-arrow-alt"></i>Schedules</a>
                        </li>
                    @endcanany

                </ul>
            </li>
            @endcanany
        </ul>
    </li>
