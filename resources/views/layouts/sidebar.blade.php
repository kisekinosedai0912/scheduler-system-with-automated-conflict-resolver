<!-- Sidebar navigation menu -->
<nav id="nav-bar" class="fixed top-0 left-0 h-full w-64 lg:w-80 bg-[#fad302] flex flex-col text-white transition-all duration-200">
    <div id="nav-header" class="flex items-center justify-between p-2 min-h-[60px]">
        <!-- System Logo -->
        <a id="nav-title" href="{{ Auth::user()->user_role == 'admin' ? route('admin.home') : route('event-calendar') }}">
            <x-application-logo/>
        </a>
        <!-- Mobile Menu Toggle Button -->
        <button id="sidebar-toggle" class="lg:hidden text-white p-2">
            <i class="fas fa-bars"></i>
        </button>
    </div>

    <div id="nav-content" class="flex-1 overflow-y-auto pt-2 w-full">
        <hr class="border-t border-gray-950">

        <!-- Navigation menu links -->
        <div class="nav-button flex items-center py-3 pl-4 transition-colors duration-200 cursor-pointer">
            <i class="fas fa-home text-gray-950 w-6 ml-4"></i>
            <x-nav-link
                :href="Auth::user()->user_role == 'admin' ? route('admin.home') : route('event-calendar')"
                :active="Auth::user()->user_role == 'admin' ? request()->routeIs('admin.home') : request()->routeIs('event-calendar')"
                class="transition duration-150 ease-in-out">
                {{ __('Events') }}
            </x-nav-link>
        </div>

        <div class="nav-button flex items-center py-3 pl-4 transition-colors duration-200 cursor-pointer">
            <i class="fas fa-calendar-day text-gray-950 w-6 ml-4"></i>
            <x-nav-link
                :href="Auth::user()->user_role == 'admin' ? route('admin.schedules') : route('schedule')"
                :active="Auth::user()->user_role == 'admin' ? request()->routeIs('admin.schedules') : request()->routeIs('schedule')"
                class="transition duration-150 ease-in-out">
                {{ __('Schedules') }}
            </x-nav-link>
        </div>

        <div class="nav-button flex items-center py-3 pl-4 transition-colors duration-200 cursor-pointer">
            @if (Auth::user()->user_role == 'admin')
                <i class="fas fa-chalkboard-teacher text-gray-950 w-6 ml-4"></i>
                <x-nav-link
                    :href="route('admin.subjects')"
                    :active="request()->routeIs('admin.subjects')"
                    class="transition duration-150 ease-in-out">
                    {{ __('Subjects') }}
                </x-nav-link>
            @else
                <i class="fas fa-bell text-gray-950 w-6 ml-4"></i>
                <x-nav-link
                    :href="route('notification')"
                    :active="request()->routeIs('notification')"
                    class="transition duration-150 ease-in-out">
                    {{ __('Notifications') }}
            </x-nav-link>
            @endif
        </div>

        <hr class="border-t border-gray-950">

        <div class="nav-button flex items-center py-3 pl-4 transition-colors duration-200 cursor-pointer">
            @if (Auth::user()->user_role == 'admin')
                <i class="fas fa-user text-gray-950 w-6 ml-4"></i>
                <x-nav-link :href="route('admin.teacher')" :active="request()->routeIs('admin.teacher')" class="transition duration-150 ease-in-out">
                    {{ __('Teachers') }}
                </x-nav-link>
            @else
                <i class="fas fa-calendar-times text-gray-950 w-6 ml-4"></i>
                <x-nav-link :href="route('conflicted_schedule')" :active="request()->routeIs('conflicted_schedule')" class="transition duration-150 ease-in-out">
                    {{ __('Conflicted Schedules') }}
                </x-nav-link>
            @endif
        </div>

        <div class="nav-button flex items-center py-3 pl-4 transition-colors duration-200 cursor-pointer">
            @if (Auth::user()->user_role == 'admin')
                <i class="fas fa-door-open text-gray-950 w-6 ml-4"></i>
                <x-nav-link :href="route('admin.classroom')" :active="request()->routeIs('admin.classroom')" class="transition duration-150 ease-in-out">
                    {{ __('Classroom') }}
                </x-nav-link>
            @else
                <i class="fas fa-user-circle text-gray-950 w-6 ml-4"></i>
                <x-nav-link :href="route('profile.edit')" :active="request()->routeIs('profile.edit')" class="transition duration-150 ease-in-out">
                    {{ __('Profile') }}
                </x-nav-link>
            @endif
        </div>

        <div class="nav-button flex items-center py-3 pl-4 transition-colors duration-200 cursor-pointer">
            @if (Auth::user()->user_role == 'admin')
                <i class="fas fa-cogs text-gray-950 w-6 ml-4"></i>
                <x-nav-link :href="route('admin.users')" :active="request()->routeIs('admin.users')" class="transition duration-150 ease-in-out">
                    {{ __('Users') }}
                </x-nav-link>
            @else
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <i class="fas fa-sign-out-alt text-gray-950 w-6 ml-4"></i>
                    <x-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();" class="transition duration-150 ease-in-out">
                        {{ __('Logout') }}
                    </x-nav-link>
                </form>
            @endif

        </div>

        <hr class="border-t border-gray-950">
    </div>

    <!-- Footer section for credits -->
    <footer id="nav-footer" class="text-center text-xs p-4 bg-gray-800">
        <p>&copy; 2024 Sagay City National High School Stand Alone. All rights reserved.</p>
    </footer>
</nav>

<script>
    const toggleButton = document.getElementById('sidebar-toggle');
    const sidebar = document.getElementById('nav-bar');

    toggleButton.addEventListener('click', () => {
        sidebar.classList.toggle('lg:w-80');
        sidebar.classList.toggle('lg:w-0');
    });
</script>
