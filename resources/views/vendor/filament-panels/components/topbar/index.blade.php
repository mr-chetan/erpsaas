@props([
    'navigation',
])

<div
    {{
        $attributes->class([
            'fi-topbar sticky top-0 z-20 overflow-x-clip',
            'fi-topbar-with-navigation' => filament()->hasTopNavigation(),
        ])
    }}
>
    <nav
        class="flex h-16 items-center gap-x-4 bg-white px-4 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 md:px-6 lg:px-8"
    >
        {{ \Filament\Support\Facades\FilamentView::renderHook('panels::topbar.start') }}

        <x-filament::icon-button
            color="gray"
            icon="heroicon-o-bars-3"
            icon-alias="panels::topbar.open-sidebar-button"
            icon-size="lg"
            :label="__('filament-panels::layout.actions.sidebar.expand.label')"
            x-cloak
            x-data="{}"
            x-on:click="$store.sidebar.open()"
            x-show="! $store.sidebar.isOpen"
            @class([
                'lg:hidden' => (! filament()->isSidebarFullyCollapsibleOnDesktop()) || filament()->isSidebarCollapsibleOnDesktop(),
            ])
        />

        <x-filament::icon-button
            color="gray"
            icon="heroicon-o-x-mark"
            icon-alias="panels::topbar.close-sidebar-button"
            icon-size="lg"
            :label="__('filament-panels::layout.actions.sidebar.collapse.label')"
            x-cloak
            x-data="{}"
            x-on:click="$store.sidebar.close()"
            x-show="$store.sidebar.isOpen"
            class="lg:hidden"
        />

        @if (filament()->hasTopNavigation())
            <div class="me-6 hidden lg:flex">
                @if ($homeUrl = filament()->getHomeUrl())
                    <a {{ \Filament\Support\generate_href_html($homeUrl) }}>
                        <x-filament-panels::logo />
                    </a>
                @else
                    <x-filament-panels::logo />
                @endif
            </div>

            @if (filament()->hasTenancy())
                <x-filament-panels::tenant-menu class="hidden lg:block" />
            @endif

            @if (filament()->hasNavigation())
                <ul class="me-4 hidden items-center gap-x-4 lg:flex">
                    @foreach($navigation as $group)
                        @if ($group->getLabel() === null)
                            @foreach ($group->getItems() as $item)
                                <x-filament-panels::topbar.item
                                    :active="$item->isActive()"
                                    :active-icon="$item->getActiveIcon()"
                                    :badge="$item->getBadge()"
                                    :badge-color="$item->getBadgeColor()"
                                    :icon="$item->getIcon()"
                                    :should-open-url-in-new-tab="$item->shouldOpenUrlInNewTab()"
                                    :url="$item->getUrl()"
                                >
                                    {{ $item->getLabel() }}
                                </x-filament-panels::topbar.item>
                            @endforeach
                        @endif
                    @endforeach

                    @foreach ($navigation as $group)
                        @php
                            $groupLabel = $group->getLabel();
                            $groupItems = collect($group->getItems());
                            $subgroups = $groupItems->groupBy(fn(\Filament\Navigation\NavigationItem $item) => $item->getParentItem())->filter(fn($subgroup, $key) => filled($key));
                            $standaloneItems = $groupItems->reject(fn(\Filament\Navigation\NavigationItem $item) => filled($item->getParentItem()));
                            $itemsCount = $groupItems->count();
                        @endphp

                        @if ($groupLabel)
                            <x-filament::dropdown
                                offset="0"
                                width="max-w-fit"
                                placement="bottom-start"
                                teleport
                                x-on:mouseenter="open"
                                x-on:mouseleave="close"
                                :attributes="
                                    \Filament\Support\prepare_inherited_attributes($attributes)
                                    ->class(['fi-topbar-dropdown'])
                                "
                            >
                                <x-slot name="trigger">
                                    <x-filament-panels::topbar.item
                                        :active="$group->isActive()"
                                        :icon="$group->getIcon()"
                                    >
                                        {{ $groupLabel }}
                                    </x-filament-panels::topbar.item>
                                </x-slot>

                                <x-filament::dropdown.list>
                                    @if($subgroups->isNotEmpty() || $standaloneItems->isNotEmpty())
                                        <ul class="menu-groups">
                                            @foreach($subgroups as $subgroupTitle => $subgroupItems)
                                                <li class="menu-group cols-1">
                                                    <div class="submenu-wrap">
                                                        <div class="menu-label">{{ $subgroupTitle }}</div>
                                                        <ul class="submenu cols-1">
                                                            @foreach($subgroupItems as $item)
                                                                <li class="menu-item cols-1">
                                                                    <x-filament::dropdown.list.item
                                                                        @class(['fi-topbar-dropdown-list-item' => $itemsCount > 1])
                                                                        :badge="$item->getBadge()"
                                                                        :badge-color="$item->getBadgeColor()"
                                                                        :href="$item->getUrl()"
                                                                        :icon="$item->isActive() ? ($item->getActiveIcon() ?? $item->getIcon()) : $item->getIcon()"
                                                                        tag="a"
                                                                        :target="$item->shouldOpenUrlInNewTab() ? '_blank' : null"
                                                                    >
                                                                        {{ $item->getLabel() }}
                                                                    </x-filament::dropdown.list.item>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                </li>
                                            @endforeach
                                            @if($standaloneItems->isNotEmpty())
                                                <li class="menu-group cols-1">
                                                    <div class="submenu-wrap">
                                                        <div class="menu-label-invisible-spacer"></div>
                                                        <ul class="submenu cols-1">
                                                            @foreach($itemsWithoutSubgroup as $item)
                                                                <li class="menu-item cols-1">
                                                                    <x-filament::dropdown.list.item
                                                                        @class([
                                                                            'fi-topbar-dropdown-list-item' => $itemsCount > 1,
                                                                        ])
                                                                        :badge="$item->getBadge()"
                                                                        :badge-color="$item->getBadgeColor()"
                                                                        :href="$item->getUrl()"
                                                                        :icon="$item->isActive() ? ($item->getActiveIcon() ?? $item->getIcon()) : $item->getIcon()"
                                                                        tag="a"
                                                                        :target="$item->shouldOpenUrlInNewTab() ? '_blank' : null"
                                                                    >
                                                                        {{ $item->getLabel() }}
                                                                    </x-filament::dropdown.list.item>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                </li>
                                            @endif
                                        </ul>
                                    @else
                                        @foreach ($group->getItems() as $item)
                                            <x-filament::dropdown.list.item
                                                @class([
                                                    'fi-topbar-dropdown-list-item' => $itemsCount > 1,
                                                ])
                                                :badge="$item->getBadge()"
                                                :badge-color="$item->getBadgeColor()"
                                                :href="$item->getUrl()"
                                                :icon="$item->isActive() ? ($item->getActiveIcon() ?? $item->getIcon()) : $item->getIcon()"
                                                tag="a"
                                                :target="$item->shouldOpenUrlInNewTab() ? '_blank' : null"
                                            >
                                                {{ $item->getLabel() }}
                                            </x-filament::dropdown.list.item>
                                        @endforeach
                                    @endif
                                </x-filament::dropdown.list>
                            </x-filament::dropdown>
                        @endif
                    @endforeach
                </ul>
            @endif
        @endif

        <div
            x-persist="topbar.end"
            class="ms-auto flex items-center gap-x-4"
        >
            {{ \Filament\Support\Facades\FilamentView::renderHook('panels::global-search.before') }}

            @if (filament()->isGlobalSearchEnabled())
                @livewire(Filament\Livewire\GlobalSearch::class, ['lazy' => true])
            @endif

            {{ \Filament\Support\Facades\FilamentView::renderHook('panels::global-search.after') }}

            @if (filament()->auth()->check())
                @if (filament()->hasDatabaseNotifications())
                    @livewire(Filament\Livewire\DatabaseNotifications::class, ['lazy' => true])
                @endif

                <x-filament-panels::user-menu />
            @endif
        </div>

        {{ \Filament\Support\Facades\FilamentView::renderHook('panels::topbar.end') }}
    </nav>
</div>
