@foreach($menuStores as $menu)
    @canany(['menu_store.'.$menu->id])
        @if($numberOfStorePermissions == 1)
            @include('layouts/sidebar/stores/navigations')
        @else
            <li>
                <a href="javascript:;" class="has-arrow">
                    <div class="parent-icon"><i class='bx bx-store' ></i></div>
                    {{-- <div class="parent-icon"><i class='fa fa-house-medical-circle-check' ></i></div> --}}
                    <div class="menu-title">{{ $menu->code }}</div>
                </a>

                @include('layouts/sidebar/stores/navigations')
            </li>
        @endif
    @endcanany
@endforeach
