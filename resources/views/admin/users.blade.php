<x-app-layout>
    @section('title', 'Scheduler System with Automated Nursery System')
    @section('styles')
        {{-- Sweet alert 2 css link --}}
        <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.min.css" rel="stylesheet">
    @endsection
    @section('title-pane', 'User Management')

    <div class="bg-white shadow-sm rounded-lg border border-gray-200 px-4 py-3">
        <div class="flex items-center space-x-4">
            <div class="relative flex-grow">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>

                <form id="search-form" class="w-full">
                    <input
                        type="search"
                        id="search"
                        name="search"
                        placeholder="Search user..."
                        value="{{ request('search') }}"
                        class="w-full pl-10 pr-4 py-2 text-gray-900 bg-gray-50 border border-gray-300 rounded-md
                                focus:ring-2 focus:ring-[#223a5e] focus:border-transparent
                                transition duration-300 ease-in-out
                                placeholder-gray-500"
                    />
                </form>
            </div>

            {{-- Add Button --}}
            <div>
                <button
                    class="group relative w-10 h-10 rounded-full bg-[#223a5e] text-white hover:bg-[#2c4b7b] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#223a5e] transition duration-300 ease-in-out"
                    title="Add New User"
                    data-bs-toggle="modal"
                    data-bs-target="#create-user-modal"
                >
                    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                    </div>
                </button>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="create-user-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content rounded-lg shadow-xl border-none">
                    {{-- Modal header --}}
                    <div class="modal-header bg-gradient-to-r from-[#223a5e] to-[#2c4b7b] text-white p-4 rounded-t-lg">
                        <h1 class="modal-title text-xl font-semibold">Register New User</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" aria-hidden="false" style="filter: brightness(0) invert(1);"></button>
                    </div>

                    {{-- Modal body --}}
                    <div class="modal-body p-6">
                        <form id="create-user-form" method="post" class="space-y-4">
                            @csrf
                            @method('post')

                            <!-- Teacher Selection -->
                            <div>
                                <label for="teacher_id" class="block mb-2 font-medium">Select Teacher</label>
                                <select name="teacher_id" id="teacher_id" class="form-select w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#223a5e]" required>
                                    <option value="">Select a Teacher</option>
                                    @foreach($teachers as $teacher)
                                        <option value="{{ $teacher->id }}">{{ $teacher->teacherName }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Email Address -->
                            <div>
                                <label for="email" class="block mb-2 font-medium">Email</label>
                                <input type="email"
                                    id="email"
                                    class="form-control w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#223a5e]"
                                    name="email"
                                    :value="old('email')"
                                    required
                                    autocomplete="username" />
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>

                            <!-- User Role -->
                            <div>
                                <label for="roleSelect" class="block mb-2 font-medium">Select Role</label>
                                <select name="user_role" id="roleSelect" class="form-select w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#223a5e]" aria-label="Role Select">
                                    <option value="faculty">Faculty</option>
                                    <option value="admin">Admin</option>
                                </select>
                            </div>

                            <!-- Password -->
                            <div>
                                <label for="password" class="block mb-2 font-medium">Password</label>
                                <input type="password"
                                    id="password"
                                    class="form-control w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#223a5e]"
                                    name="password"
                                    required
                                    autocomplete="new-password" />
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>

                            <!-- Confirm Password -->
                            <div>
                                <label for="password_confirmation" class="block mb-2 font-medium">Confirm Password</label>
                                <input type="password"
                                    id="password_confirmation"
                                    class="form-control w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#223a5e]"
                                    name="password_confirmation"
                                    required
                                    autocomplete="new-password" />
                                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                            </div>

                            <div class="flex items-center justify-end mt-6">
                                <x-primary-button id="submit-user-btn" class="ms-4">
                                    {{ __('Register') }}
                                </x-primary-button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <hr class="my-2">
    {{-- Table --}}
    <span class="hidden md:block">
        <table class="min-w-full bg-white shadow-sm rounded-lg overflow-hidden border border-gray-200">
            <thead class="bg-gradient-to-r from-[#223a5e] to-[#2c4b7b] text-white">
                <tr>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">User</th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Email</th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Role</th>
                    <th scope="col" class="px-4 py-3 text-center text-xs font-medium uppercase tracking-wider">Action</th>
                </tr>
            </thead>
            <tbody id="userTableBody" class="divide-y divide-gray-200">
                @foreach ($users as $user)
                    @include('admin-modals.editUser', ['user' => $user])
                    <tr class="hover:bg-gray-50 transition duration-200 ease-in-out">
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 bg-[#223a5e] text-white rounded-full flex items-center justify-center font-bold">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-500">{{ $user->email }}</div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                @if($user->user_role == 'admin') bg-green-100 text-green-800
                                @elseif($user->user_role == 'teacher') bg-blue-100 text-blue-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ $user->user_role }}
                            </span>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap text-center">
                            <div class="flex items-center justify-center space-x-3">
                                <a
                                    href="#"
                                    class="text-green-500 hover:text-green-700 transition duration-200 ease-in-out"
                                    data-bs-toggle="modal"
                                    data-bs-target="#edit-user-{{ $user->id }}"
                                    title="Edit User"
                                >
                                    <i class="fas fa-edit text-xl"></i>
                                </a>
                                <form
                                    action="{{ route('admin.delete_user', $user->id) }}"
                                    method="post"
                                    id="delete-form-{{ $user->id }}"
                                >
                                    @csrf
                                    @method('DELETE')
                                    <a
                                        href="#"
                                        class="text-red-500 hover:text-red-700 transition duration-200 ease-in-out"
                                        onclick="confirmDeletion(event, 'delete-form-{{ $user->id }}')"
                                        title="Delete User"
                                    >
                                        <i class="fas fa-trash text-xl"></i>
                                    </a>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="mt-4" id="paginationLinks">
            {{ $users->links() }}
        </div>
    </span>

    {{-- Table for mobile --}}
    <span class="block md:hidden">
        <div class="bg-white shadow-sm rounded-lg overflow-hidden border border-gray-200">
            <div class="divide-y divide-gray-200">
                @foreach ($users as $user)
                    @include('admin-modals.editUser ', ['user' => $user])
                    <div class="px-4 py-4 hover:bg-gray-50 transition duration-200 ease-in-out">
                        <div class="flex justify-between items-center">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3">
                                    <div class="flex-shrink-0 h-10 w-10 bg-[#223a5e] text-white rounded-full flex items-center justify-center font-bold">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            @if($user->user_role == 'admin') bg-green-100 text-green-800
                                            @elseif($user->user_role == 'teacher') bg-blue-100 text-blue-800
                                            @else bg-gray-100 text-gray-800
                                            @endif">
                                            {{ $user->user_role }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="relative">
                                <button
                                    type="button"
                                    class="text-gray-500 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#223a5e] rounded-full p-2"
                                    data-bs-toggle="dropdown"
                                    aria-expanded="false"
                                >
                                    <i class="fas fa-ellipsis-h"></i>
                                </button>

                                <ul class="absolute right-0 z-10 hidden mt-2 w-48 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none dropdown-menu">
                                    <li>
                                        <a
                                            href="#"
                                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                            data-bs-toggle="modal"
                                            data-bs-target="#edit-user-{{ $user->id }}"
                                        >
                                            <i class="fas fa-edit mr-2 text-green-500"></i>Edit
                                        </a>
                                    </li>
                                    <li>
                                        <form action="{{ route('admin.delete_user', $user->id) }}" method="post" id="delete-form-{{ $user->id }}">
                                            @csrf
                                            @method('DELETE')
                                            <a
                                                href="#"
                                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                                onclick="confirmDeletion(event, 'delete-form-{{ $user->id }}')"
                                            >
                                                <i class="fas fa-trash mr-2 text-red-500"></i>Delete
                                            </a>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="mt-4" id="mobilePaginationLinks">
            {{ $users->links() }}
        </div>
    </span>

    @section('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.all.min.js"></script>
        <script>
            $(document).ready(function () {
                // Event listener for searching
                $('#search').on('keypress', function (e) {
                if (e.which === 13) {
                        e.preventDefault();
                        $('#search-form').submit();
                    }
                });
                // Revert the table back after the search input is empty
                $('#search').on('input', function () {
                    // Checks if the search input is empty then automatically submit an empty search to revert the table list back
                    if ($(this).val().trim() === "") {
                        $('#search-form').submit();
                    }
                });

                // Form submission via AJAX
                $('#create-user-form').on('submit', function(e) {
                    e.preventDefault();

                    // Disable submit button to prevent multiple submissions
                    $('#submit-user-btn').prop('disabled', true);

                    $.ajax({
                        url: "{{ route('auth.store_user') }}",
                        method: 'POST',
                        data: $(this).serialize(),
                        success: function(response) {
                            // Check if success is true or message exists
                            if (response.success || response.message) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success',
                                    text: response.message || 'User created successfully',
                                    timer: 2000,
                                    timerProgressBar: true,
                                }).then(() => {
                                    // Redirect if a redirect URL is provided
                                    if (response.redirect) {
                                        window.location.href = response.redirect;
                                    } else {
                                        // Optionally reload the page
                                        location.reload();
                                    }
                                });

                                // Reset the form
                                $('#create-user-form')[0].reset();

                                // Close the modal
                                $('#create-user-modal').modal('hide');
                            }
                        },
                        error: function(xhr) {
                            // Enable submit button
                            $('#submit-user-btn').prop('disabled', false);

                            // Handle different types of errors
                            let errorMessage = 'An unexpected error occurred';

                            if (xhr.responseJSON) {
                                // Check for specific error messages
                                errorMessage = xhr.responseJSON.error ||
                                            xhr.responseJSON.message ||
                                            errorMessage;

                                // Handle validation errors
                                if (xhr.status === 422) {
                                    let errors = xhr.responseJSON.errors;
                                    if (errors) {
                                        errorMessage = Object.values(errors).flat().join('<br>');
                                    }
                                }
                            }

                            // Show error
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                html: errorMessage
                            });
                        }
                    });
                });
            });
            // Delete confirmation function with sweet alert library pop up
            function confirmDeletion(event, formId) {
                    event.preventDefault();

                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You won't be able to revert this!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#223a5e',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            document.getElementById(formId).submit();
                        }
                    });
                }
        </script>
        @if(session('error'))
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: "{{ session('error') }}"
                });
            </script>
        @endif
        @if(session('success'))
            <script>
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'Success!',
                    text: "{{ session('success') }}",
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                });
            </script>
        @endif
        @if($errors->any())
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    html: `
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    `
                });
            </script>
        @endif
    @endsection
</x-app-layout>
