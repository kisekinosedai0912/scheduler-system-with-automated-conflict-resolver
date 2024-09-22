<x-app-layout>
    @section('title', 'Scheduler System with Automated Nursery System')
    @section('styles')
        {{-- Sweet alert 2 css link --}}
        <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.min.css" rel="stylesheet">
    @endsection
    @section('title-pane', 'User Management')

    <div class="outer-container flex items-center justify-evenly md:justify-between bg-white rounded-sm md:px-2">
        {{-- Search input box--}}
        <form class="flex items-center relative md:w-3/12 my-2" id="search-form">
            <svg class="absolute left-4 w-4 h-4 text-gray-500" aria-hidden="true" viewBox="0 0 24 24">
            <g><path d="M21.53 20.47l-3.66-3.66C19.195 15.24 20 13.214 20 11c0-4.97-4.03-9-9-9s-9 4.03-9 9 4.03 9 9 9c2.215 0 4.24-.804 5.808-2.13l3.66 3.66c.147.146.34.22.53.22s.385-.073.53-.22c.295-.293.295-.767.002-1.06zM3.5 11c0-4.135 3.365-7.5 7.5-7.5s7.5 3.365 7.5 7.5-3.365 7.5-7.5 7.5-7.5-3.365-7.5-7.5z"></path></g>
            </svg>
            <input type="search" id="search" name="search" placeholder="search event" class="w-full h-10 pl-10 pr-4 px-1. rounded-md text-gray-900 bg-white focus:outline-none focus:bg-[#223a5e] transition duration-300" value="{{ request('search') }}">
        </form>

        <div class="buttons flex items-center justify-evenly w-80">
            <div class="buttons flex items-center justify-end gap-2 w-80">
                {{-- Add button with modal trigger --}}
                <button class="group cursor-pointer outline-none hover:rotate-90 duration-300" title="Add New" data-bs-toggle="modal" data-bs-target="#create-user-modal">
                    <svg class="stroke-blue-950 fill-none group-hover:fill-blue-100 group-active:stroke-blue-900 group-active:fill-blue-950 group-active:duration-0 duration-300" viewBox="0 0 24 24"
                        height="50px" width="50px" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-width="1" d="M12 22C17.5 22 22 17.5 22 12C22 6.5 17.5 2 12 2C6.5 2 2 6.5 2 12C2 17.5 6.5 22 12 22Z"></path>
                        <path stroke-width="1" d="M8 12H16"></path>
                        <path stroke-width="1" d="M12 16V8"></path>
                    </svg>
                </button>
            </div>    
        </div>
        <!-- Modal -->
        <div class="modal fade" id="create-user-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    {{-- Modal header --}}
                    <div class="modal-header text-center bg-[#223a5e]">
                        <h1 class="modal-title fs-5 text-center text-neutral-100" id="staticBackdropLabel">Register New User</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="filter: brightness(0) invert(1);"></button>
                    </div>
                    {{-- Modal body --}}
                    <div class="modal-body">
                        <form method="post" action="{{ route('auth.store_user') }}">
                            @csrf
                            @method('post')
                            <!-- Name -->
                            <div>
                                <x-input-label for="name" :value="__('Name')" />
                                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autocomplete="name" />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>
                    
                            <!-- Email Address -->
                            <div class="mt-4">
                                <x-input-label for="email" :value="__('Email')" />
                                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>
                            
                            <!-- User Role -->
                            <div class="mt-4">
                                <x-input-label for="roleSelect" :value="__('Select Role')" />
                                <select name="user_role" id="roleSelect" class="form-select rounded-md" aria-label="Role Select">
                                    <option value="faculty">Faculty</option>
                                    <option value="admin">Admin</option>
                                </select>
                            </div>

                            <!-- Password -->
                            <div class="mt-4">
                                <x-input-label for="password" :value="__('Password')" />
                    
                                <x-text-input id="password" class="block mt-1 w-full"
                                                type="password"
                                                name="password"
                                                required autocomplete="new-password" />
                    
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>
                    
                            <!-- Confirm Password -->
                            <div class="mt-4">
                                <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                    
                                <x-text-input id="password_confirmation" class="block mt-1 w-full"
                                                type="password"
                                                name="password_confirmation" required autocomplete="new-password" />
                    
                                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                            </div>
                    
                            <div class="flex items-center justify-end mt-4">
                                <x-primary-button class="ms-4">
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
        <table class="table table-hover cursor-pointer border border-slate-950">
            <thead>
                <tr>
                    <th scope="col">User</th>
                    <th scope="col">Email</th>
                    <th scope="col">Role</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody id="userTableBody">
                @foreach ($users as $user)
                    @include('admin-modals.editUser', ['user' => $user])
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->user_role }}</td>
                        <td class="flex items-start justify-start">
                            <a href="#" class="btn btn-success bg-transparent text-green-600 text-xl mr-2 hover:border-green-200 hover:text-green-900" data-bs-toggle="modal" data-bs-target="#edit-user-{{ $user->id }}">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.delete_user', $user->id) }}" method="post" id="delete-form-{{ $user->id }}">
                                @csrf
                                @method('DELETE')
                                <a href="#" class="btn btn-danger bg-transparent text-red-600 text-xl hover:border-red-200 hover:text-red-700" onclick="confirmDeletion(event, 'delete-form-{{ $user->id }}')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="mt-4" id="paginationLinks">
            {{ $users->links() }}
        </div>        
    </span>
    {{-- Table for mobile--}}
    <span class="block md:hidden">
        <table class="table shadow-sm">
            <thead>
                <tr>
                    <th scope="col">User</th>
                    <th scope="col">Email</th>
                    <th scope="col">Role</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody id="mobileUserTableBody">
                @foreach ($users as $user)
                    @include('admin-modals.editUser', ['user' => $user])
                    <tr>
                        <td class="font-normal">{{ $user->name }}</td>
                        <td class="font-normal">{{ $user->email }}</td>
                        <td class="font-normal">{{ $user->user_role }}</td>
                        <td>
                            <div class="dropdown">
                                <button type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-ellipsis-h"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a href="#" class="text-md ml-4" data-bs-toggle="modal" data-bs-target="#edit-user-{{ $user->id }}">
                                            Edit
                                        </a>
                                    </li>
                                    <li>
                                        <form action="{{ route('admin.delete_user', $user->id) }}" method="post" id="delete-form-{{ $user->id }}">
                                            @csrf
                                            @method('DELETE')
                                            <a href="#" class="text-md ml-4" onclick="confirmDeletion(event, 'delete-form-{{ $user->id }}')">
                                                Delete
                                            </a>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                </tbody>
            @endforeach
        </table>
        <div class="mt-2" id="mobilePaginationLinks">
            {{ $users->links() }}
        </div>
    </span>

    @section('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.all.min.js"></script>
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
        {{-- Validation error handling --}}
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
                    // Check if the search input is empty then automatically submit an empty search to revert the table list back
                    if ($(this).val().trim() === "") {
                        $('#search-form').submit();
                    }
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
    @endsection
</x-app-layout>