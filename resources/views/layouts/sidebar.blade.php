<!-- Sidebar navigation menu -->
<nav id="nav-bar" class="fixed top-0 left-0 h-full w-64 bg-[#fad302] flex flex-col text-white transition-all duration-200">
    <div id="nav-header" class="flex items-center justify-start p-2 min-h-[60px]">
        <!-- System Logo -->
        <a id="nav-title" href="{{ Auth::user()->user_role == 'admin' ? route('admin.home') : route('dashboard') }}">
            <x-application-logo/>
        </a>
    </div>
    <div id="nav-content" class="flex-1 overflow-y-auto pt-2">
        <hr class="border-t border-gray-950">
        <!-- Navigation menu links -->
        <div class="nav-button flex items-center py-3 pl-4 transition-colors duration-200 cursor-pointer">
            <i class="fas fa-home text-gray-950 w-6 ml-4"></i>
            <x-nav-link :href="route('admin.home')" :active="request()->routeIs('admin.home')" class="transition duration-150 ease-in-out">
                {{ __('Home') }}
            </x-nav-link>
        </div>
        <div class="nav-button flex items-center py-3 pl-4 transition-colors duration-200 cursor-pointer">
            <i class="fas fa-calendar-day text-gray-950 w-6 ml-4"></i>
            <x-nav-link :href="route('admin.schedules')" :active="request()->routeIs('admin.schedules')" class="transition duration-150 ease-in-out">
                {{ __('Schedules') }}
            </x-nav-link>
        </div>
        <div class="nav-button flex items-center py-3 pl-4 transition-colors duration-200 cursor-pointer">
            <i class="fas fa-chalkboard-teacher text-gray-950 w-6 ml-4"></i>
            <x-nav-link :href="route('admin.subjects')" :active="request()->routeIs('admin.subjects')" class="transition duration-150 ease-in-out">
                {{ __('Subjects') }}
            </x-nav-link>
        </div>

        <hr class="border-t border-gray-950">

        <div class="nav-button flex items-center py-3 pl-4 transition-colors duration-200 cursor-pointer">
            <i class="fas fa-chalkboard-teacher text-gray-950 w-6 ml-4"></i>
            <x-nav-link :href="route('admin.teacher')" :active="request()->routeIs('admin.teacher')" class="transition duration-150 ease-in-out">
                {{ __('Teachers') }}
            </x-nav-link>
        </div>
        <div class="nav-button flex items-center py-3 pl-4 transition-colors duration-200 cursor-pointer">
            <i class="fas fa-chalkboard-teacher text-gray-950 w-6 ml-4"></i>
            <x-nav-link :href="route('admin.classroom')" :active="request()->routeIs('admin.classroom')" class="transition duration-150 ease-in-out">
                {{ __('Classroom') }}
            </x-nav-link>
        </div>
        <div class="nav-button flex items-center py-3 pl-4 transition-colors duration-200 cursor-pointer">
            <i class="fas fa-chalkboard-teacher text-gray-950 w-6 ml-4"></i>
            <x-nav-link :href="route('admin.users')" :active="request()->routeIs('admin.users')" class="transition duration-150 ease-in-out">
                {{ __('Users') }}
            </x-nav-link>
        </div>
        
        <hr class="border-t border-gray-950">
    </div>
    <!-- Footer section for credits -->
    <footer id="nav-footer" class="text-center text-xs p-4 bg-gray-800">
        <p>&copy; 2024 Sagay City National High School Stand Alone. All rights reserved.</p>
    </footer>
</nav>
