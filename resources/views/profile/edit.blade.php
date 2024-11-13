<x-app-layout>

    @section('title', 'Scheduler System with Automated Conflict Resolver')

    @if (Auth::user()->user_role == 'faculty')

        <div class="h-screen overflow-y-auto">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
                <!-- Profile information and update password container form -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                    <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                        <div class="max-w-xl">
                            @include('profile.partials.update-profile-information-form')
                        </div>
                    </div>

                    <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                        <div class="max-w-xl">
                            @include('profile.partials.update-password-form')
                        </div>
                    </div>
                </div>

                <!-- Delete user container form -->
                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                    <div class="max-w-2xl text-left">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>
            </div>
        </div>

    @else

        <div class="h-screen w-full overflow-y-auto">
            <div class="w-full mx-auto sm:px-6 lg:px-8 space-y-4">
                <!-- Profile information and update password container form -->
                <div class="flex flex-col items-center justify-center gap-4 w-full">
                    <!-- Profile Information Section -->
                    <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg w-full">
                        <div class="w-full">
                            @include('profile.partials.update-profile-information-form')
                        </div>
                    </div>

                    <!-- Update Password Section -->
                    <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg w-full">
                        <div class="w-full">
                            @include('profile.partials.update-password-form')
                        </div>
                    </div>
                </div>
            </div>
        </div>

    @endif

</x-app-layout>
