
    <li class="sidebar-store-sop-nav">
        <a class="sidebar-store-sop-nav-a has-arrow" href="javascript:;">
            @if($numberOfStorePermissions == 1)
                <div class="parent-icon"><i class='fa-solid fa-book-open fa-2xs' ></i></div>
                <div class="menu-title">Knowledge Base</div>
            @else
                <i class="fa-solid fa-users-gear me-2"></i>Knowledge Base
            @endif
        </a>
        <ul>
            @canany(['menu_store.knowledge_base.sops.index', 'menu_store.knowledge_base.sops.create', 'menu_store.knowledge_base.sops.update', 'menu_store.knowledge_base.sops.delete'])
                <li>
                    <a href="/store/knowledge-base/{{$menu->id}}/sops"><i class="fa-solid fa-users-gear ms-2 me-2"></i>SOPs</a>
                </li>
            @endcanany
            @canany(['menu_store.knowledge_base.pnps.index', 'menu_store.knowledge_base.pnps.create', 'menu_store.knowledge_base.pnps.update', 'menu_store.knowledge_base.pnps.delete'])
                <li>
                    <a href="/store/knowledge-base/{{$menu->id}}/pnps"><i class="fa-solid fa-certificate ms-2 me-3"></i>P&Ps</a>
                </li>
            @endcanany
            @canany(['menu_store.knowledge_base.pd.index', 'menu_store.knowledge_base.pd.create', 'menu_store.knowledge_base.pd.update', 'menu_store.knowledge_base.pd.delete'])
                <li>
                    <a href="/store/knowledge-base/{{$menu->id}}/process-documents"><i class="fa-solid fa-globe ms-2 me-3"></i>Process Documents (Scribe)</a>
                </li>
            @endcanany
            @canany(['menu_store.knowledge_base.htg.index', 'menu_store.knowledge_base.htg.create', 'menu_store.knowledge_base.htg.update', 'menu_store.knowledge_base.htg.delete'])
                <li>
                    <a href="/store/knowledge-base/{{$menu->id}}/how-to-guide"><i class="fa-solid fa-circle-info ms-2 me-3"></i>Video Guide</a>
                </li>
            @endcanany
            {{-- @canany(['menu_store.knowledge_base.bp.index', 'menu_store.knowledge_base.bp.create', 'menu_store.knowledge_base.bp.update', 'menu_store.knowledge_base.bp.delete'])
                <li>
                    <a href="/store/knowledge-base/{{$menu->id}}/board-of-pharmacy"><i class="fa-solid fa-shield ms-2 me-3"></i>Board of Pharmacy</a>
                </li>
            @endcanany
            @canany(['menu_store.knowledge_base.pf.index', 'menu_store.knowledge_base.pf.create', 'menu_store.knowledge_base.pf.update', 'menu_store.knowledge_base.pf.delete'])
                <li>
                    <a href="/store/knowledge-base/{{$menu->id}}/pharmacy-forms"><i class="fa-solid fa-file-circle-check ms-2 me-3"></i>Pharmacy Forms</a>
                </li>
            @endcanany --}}
        </ul>
    </li>