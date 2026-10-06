@if(config('menu'))
    @foreach(config('menu') as $groupName => $group)
    <div class="menu-group">
        <div class="menu-group-title">{{ $group['title'] }}</div>
        
        @foreach($group['items'] as $itemKey => $item)
            @if(isset($item['submenu']))
                <!-- Menu with submenu -->
                @php
                    $isSubmenuActive = false;
                    $hasActiveSubitem = false;
                    foreach ($item['submenu'] as $subItem) {
                        if (Route::has($subItem['route']) && request()->routeIs($subItem['route'])) {
                            $isSubmenuActive = true;
                            $hasActiveSubitem = true;
                            break;
                        }
                    }
                @endphp
                
                <div class="menu-item menu-parent {{ $hasActiveSubitem ? 'active' : '' }}" 
                     data-menu="{{ $itemKey }}">
                    <div class="menu-icon" style="color: {{ $item['color'] ?? '#e2e8f0' }};">
                        <i class="{{ $item['icon'] }}"></i>
                    </div>
                    <span class="menu-text">{{ $item['title'] }}</span>
                    @if(isset($item['badge']))
                        <span class="menu-badge">{{ $item['badge'] }}</span>
                    @endif
                    <div class="menu-toggle {{ $hasActiveSubitem ? 'rotated' : '' }}">
                        <i class="fas fa-chevron-down"></i>
                    </div>
                </div>
                <div class="submenu {{ $hasActiveSubitem ? 'open' : '' }}" id="submenu-{{ $itemKey }}">
                    @foreach($item['submenu'] as $subKey => $subItem)
                        @if(Route::has($subItem['route']))
                            <a href="{{ route($subItem['route']) }}" 
                               class="submenu-item {{ request()->routeIs($subItem['route']) ? 'active' : '' }}"
                               data-route="{{ $subItem['route'] }}">
                                {{ $subItem['title'] }}
                            </a>
                        @else
                            <a href="#" class="submenu-item" data-route="{{ $subItem['route'] }}">
                                {{ $subItem['title'] }} <small class="text-muted">(Route not defined)</small>
                            </a>
                        @endif
                    @endforeach
                </div>
            @else
                <!-- Single menu item -->
                @if(Route::has($item['route']))
                    <a href="{{ route($item['route']) }}" 
                       class="menu-item {{ request()->routeIs($item['route']) ? 'active' : '' }}"
                       data-route="{{ $item['route'] }}">
                        <div class="menu-icon" style="color: {{ $item['color'] ?? '#e2e8f0' }};">
                            <i class="{{ $item['icon'] }}"></i>
                        </div>
                        <span class="menu-text">{{ $item['title'] }}</span>
                        @if(isset($item['badge']))
                            <span class="menu-badge">{{ $item['badge'] }}</span>
                        @endif
                    </a>
                @else
                    <div class="menu-item" data-route="{{ $item['route'] }}">
                        <div class="menu-icon" style="color: {{ $item['color'] ?? '#e2e8f0' }};">
                            <i class="{{ $item['icon'] }}"></i>
                        </div>
                        <span class="menu-text">{{ $item['title'] }} <small class="text-muted">(Route not defined)</small></span>
                        @if(isset($item['badge']))
                            <span class="menu-badge">{{ $item['badge'] }}</span>
                        @endif
                    </div>
                @endif
            @endif
        @endforeach
    </div>
    @endforeach
@else
    <!-- Fallback: No menu config found, show empty state -->
    <div class="menu-group">
        <div class="menu-group-title">NAVIGATION</div>
        <div class="menu-item">
            <div class="menu-icon">
                <i class="fas fa-info-circle"></i>
            </div>
            <span class="menu-text">Menu configuration not found</span>
        </div>
    </div>
@endif