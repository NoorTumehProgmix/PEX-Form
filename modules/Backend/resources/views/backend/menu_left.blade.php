<ul class="juzaweb__menuLeft__navigation">
    @php
        use Juzaweb\CMS\Facades\HookAction;
        use Juzaweb\CMS\Support\MenuCollection;

        global $jw_user;
        $adminPrefix = config('juzaweb.admin_prefix');
        $adminUrl = url($adminPrefix);
        $currentUrl = url()->current();
        $segment3 = request()->segment(3);
        $segment2 = request()->segment(2);
        $items = MenuCollection::make(apply_filters('get_admin_menu', HookAction::getAdminMenu()));
        $groupedItems = collect($items)->groupBy(fn($item) => $item->get('group', 'main'));
    @endphp

@foreach ($groupedItems as $group => $groupItems)
@php
    $groupId = Str::slug($group);
    $isOpen = $groupItems->contains(function ($item) use ($adminPrefix) {
        if (!$item->get('url')) return false;
        return request()->is($adminPrefix . '/' . ltrim($item->get('url'), '/') . '*');
    }) || $groupItems->some(fn($item) => $item->hasChildren() && collect($item->getChildrens())->some(fn($child) => request()->is($adminPrefix . '/' . ltrim($child->get('url'), '/') . '*')));

    if(request()->is($adminPrefix) && $groupId=="main"){
        $isOpen = true;
    }
@endphp

{{-- Group Separator --}}
<a class="menu-separator {{ $isOpen ? '' : 'collapsed' }}" data-toggle="collapse"
    href="#collapse{{ ucfirst($groupId) }}" role="button"
    aria-expanded="{{ $isOpen ? 'true' : 'false' }}"
    aria-controls="collapse{{ ucfirst($groupId) }}">
    {{ ucfirst($group) }} Modules
</a>

<div class="collapse {{ $isOpen ? 'show' : '' }}" id="collapse{{ ucfirst($groupId) }}">
    @foreach ($groupItems as $item)
        @if (!$jw_user->canAny($item->get('permissions', ['admin'])))
            @continue
        @endif

        @if ($item->hasChildren())
            @php
                $strChild = '';
                $hasActive = false;

                foreach ($item->getChildrens() as $child) {
                    if (!$jw_user->canAny($child->get('permissions', ['admin']))) {
                        continue;
                    }

                    $active = request()->is($adminPrefix . '/' . ltrim($child->get('url'), '/') . '*');

                    if ($active) {
                        $hasActive = true;
                    }

                    $strChild .= view('cms::backend.items.menu_left_item', [
                        'adminUrl' => $adminUrl,
                        'item' => $child,
                        'active' => $active,
                        'icon' => false,
                    ])->render();
                }
            @endphp

            <li
                class="juzaweb__menuLeft__item juzaweb__menuLeft__submenu juzaweb__menuLeft__item-{{ $item->get('slug') }} @if ($hasActive) juzaweb__menuLeft__submenu--toggled @endif">
                <span class="juzaweb__menuLeft__item__link">
                    <i class="juzaweb__menuLeft__item__icon {{ $item->get('icon') }}"></i>
                    <span class="juzaweb__menuLeft__item__title">{{ $item->get('title') }}</span>
                </span>

                <ul class="juzaweb__menuLeft__navigation" @if ($hasActive) style="display: block;" @endif>
                    {!! $strChild !!}
                </ul>
            </li>
        @else
            @component('cms::backend.items.menu_left_item', [
                'adminUrl' => $adminUrl,
                'item' => $item,
                'active' =>
                    $item->get('url') == 'dashboard'
                        ? request()->is($adminPrefix)
                        : request()->is($adminPrefix . '/' . ltrim($item->get('url'), '/') . '*'),
            ])
            @endcomponent
        @endif
    @endforeach
</div>
@endforeach
