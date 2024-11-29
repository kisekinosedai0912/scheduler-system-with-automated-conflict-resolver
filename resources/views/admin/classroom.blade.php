<x-app-layout>
    @section('title', 'Scheduler System with Automated Nursery System')

    @section('styles')
        {{-- Sweet alert 2 css link --}}
        <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.min.css" rel="stylesheet">
    @endsection

    @section('title-pane', 'Manage Classroom')

    <div class="outer-container flex items-center justify-between bg-white rounded-lg shadow-md p-3">
        {{-- Search input box --}}
        <form class="flex items-center relative md:w-3/12" id="search-room-form">
            <svg class="absolute left-3 w-5 h-5 text-gray-500" aria-hidden="true" viewBox="0 0 24 24">
                <g>
                    <path d="M21.53 20.47l-3.66-3.66C19.195 15.24 20 13.214 20 11c0-4.97-4.03-9-9-9s-9 4.03-9 9 4.03 9 9 9c2.215 0 4.24-.804 5.808-2.13l3.66 3.66c.147.146.34.22.53.22s.385-.073.53-.22c.295-.293.295-.767.002-1.06zM3.5 11c0-4.135 3.365-7.5 7.5-7.5s7.5 3.365 7.5 7.5-3.365 7.5-7.5 7.5-7.5-3.365-7.5-7.5z"></path>
                </g>
            </svg>
            <input type="search" name="searchRoom" id="search-room" placeholder="Search Classroom" class="w-full h-10 pl-10 pr-4 rounded-md text-gray-900 bg-gray-50 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#223a5e] transition duration-300" value="{{ request('searchRoom') }}">
        </form>

        {{-- Add button with modal trigger --}}
        <div class="flex items-center justify-end">
            <button
                class="group relative w-10 h-10 rounded-full bg-[#223a5e] text-white hover:bg-[#2c4b7b] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#223a5e] transition duration-300 ease-in-out"
                title="Add New Classroom"
                data-bs-toggle="modal"
                data-bs-target="#classroomModal"
            >
                <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                </div>
            </button>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="classroomModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content rounded-lg shadow-xl border-none">
                    {{-- Modal header --}}
                    <div class="modal-header bg-gradient-to-r from-[#223a5e] to-[#2c4b7b] text-white p-4 rounded-t-lg">
                        <h1 class="modal-title text-xl font-semibold" id="staticBackdropLabel">Add New Classroom</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="filter: brightness(0) invert(1);"></button>
                    </div>

                    {{-- Modal body --}}
                    <div class="modal-body p-6">
                        <form action="{{ route('admin.createRoom') }}" method="post" name="classroomForm" id="classroom-form" class="space-y-4">
                            @csrf
                            @method('post')

                            <div>
                                <label for="classroom-input" class="block mb-2 font-medium">Classroom/Laboratory</label>
                                <input type="text"
                                    name="roomName"
                                    class="form-control w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#223a5e]"
                                    id="classroom-input"
                                    placeholder="Enter Classroom/Laboratory"
                                    required>
                            </div>

                            <div>
                                <label for="building-input" class="block mb-2 font-medium">Building #</label>
                                <input type="text"
                                    name="buildingNumber"
                                    class="form-control w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#223a5e]"
                                    id="building-input"
                                    placeholder="Enter Building #">
                            </div>

                            <div>
                                <label for="floor-input" class="block mb-2 font-medium">Floor #</label>
                                <input type="text"
                                    name="floorNumber"
                                    class="form-control w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#223a5e]"
                                    id="floor-input"
                                    placeholder="Enter Floor #">
                            </div>

                            {{-- Modal buttons --}}
                            <div class="flex justify-end gap-4 mt-6">
                                <button type="button"
                                    class="border-[#223a5e] border-2 p-2 w-[120px] text-[#223a5e] rounded-lg transition duration-300 hover:bg-[#223a5e] hover:text-white"
                                    data-bs-dismiss="modal">
                                    Cancel
                                </button>
                                <button type="submit"
                                    class="bg-[#223a5e] p-2 w-[120px] text-white rounded-lg transition duration-300 hover:bg-[#2c4b7b]">
                                    Save
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @include('admin-modals.editClassroom')
    </div>

    <hr class="my-2">

    <span class="hidden md:block">
        <table class="min-w-full bg-white shadow-sm rounded-lg overflow-hidden border border-gray-200">
            <thead class="bg-gradient-to-r from-[#223a5e] to-[#2c4b7b] text-white">
                <tr>
                    <th scope="col" class="px-4 py-2 text-left text-sm font-medium uppercase tracking-wider">Classroom/Laboratory</th>
                    <th scope="col" class="px-4 py-2 text-left text-sm font-medium uppercase tracking-wider">Building</th>
                    <th scope="col" class="px-4 py-2 text-left text-sm font-medium uppercase tracking-wider">Floor Number</th>
                    <th scope="col" class="px-4 py-2 text-center text-sm font-medium uppercase tracking-wider">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach ($paginateRooms as $room)
                    <tr class="hover:bg-gray-50 transition duration-200 ease-in-out">
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $room->roomName }}</div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $room->buildingNumber }}</div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $room->floorNumber }}</div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap text-center">
                            <div class="flex items-center justify-center space-x-2">
                                <a
                                    href="{{ route('admin.editRoom', $room->id) }}"
                                    class="text-green-500 hover:text-green-700 transition duration-200 ease-in-out"
                                    data-bs-toggle="modal"
                                    data-bs-target="#classroomEdit-{{ $room->id }}"
                                >
                                    <i class="fas fa-edit text-xl"></i>
                                </a>
                                <form action="{{ route('admin.deleteRoom', $room->id) }}" method="POST" id="delete-room-{{ $room->id }}">
                                    @csrf
                                    @method('DELETE')
                                    <a
                                        href="#"
                                        class="text-red-500 hover:text-red-700 transition duration-200 ease-in-out"
                                        onclick="confirmDeletion(event, 'delete-room-{{ $room->id }}')"
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
        <div class="mt-4">
            {{ $paginateRooms->links() }}
        </div>
    </span>

    {{-- Table for mobile --}}
    <span class="block md:hidden">
        <div class="bg-white shadow-sm rounded-lg overflow-hidden border border-gray-200">
            <div class="divide-y divide-gray-200">
                @foreach ($paginateRooms as $room)
                    <div class="px-4 py-4 hover:bg-gray-50 transition duration-200 ease-in-out">
                        <div class="flex justify-between items-center">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3">
                                    <div class="flex-shrink-0 bg-[#223a5e] text-white rounded-full w-10 h-10 flex items-center justify-center font-bold">
                                        {{ substr($room->roomName, 0, 2) }}
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">
                                            Room: {{ $room->roomName }}
                                        </div>
                                        <div class="text-sm text-gray-500 flex space-x-2">
                                            <span>Bldg: {{ $room->buildingNumber }}</span>
                                            <span>•</span>
                                            <span>Floor: {{ $room->floorNumber }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="relative">
                                <button
                                    type="button"
                                    class="text-gray-500 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#223a5e] rounded-full p-2"
                                    onclick="toggleDropdown(this)"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                    </svg>
                                </button>

                                <div class="absolute right-0 z-10 hidden mt-2 w-48 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none dropdown-menu">
                                    <div class="py-1">
                                        <a
                                            href="{{ route('admin.editRoom', $room->id) }}"
                                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                            data-bs-toggle="modal"
                                            data-bs-target="#classroomEdit-{{ $room->id }}"
                                        >
                                            <i class="fas fa-edit mr-2 text-green-500"></i>Edit
                                        </a>
                                        <form
                                            action="{{ route('admin.deleteRoom', $room->id) }}"
                                            method="post"
                                            id="delete-room-{{ $room->id }}"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <a
                                                href="#"
                                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                                onclick="confirmDeletion(event, 'delete-room-{{ $room->id }}')"
                                            >
                                                <i class="fas fa-trash mr-2 text-red-500"></i>Delete
                                            </a>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="mt-4">
            {{ $paginateRooms->links() }}
        </div>
    </span>

    @section('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.all.min.js"></script>
        <script>
            $(document).ready(function () {
                $('#search-room').on('keypress', function (e) {
                if (e.which === 13) {
                        e.preventDefault();
                        $('#search-room-form').submit();
                    }
                });

                $('#search-room').on('input', function () {
                    if ($(this).val().trim() === "") {
                        $('#search-room-form').submit();
                    }
                });
            });
            function confirmDeletion(event, formId) {
                event.preventDefault();

                Swal.fire({
                    title: 'Are you sure you want to delete this?',
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
            function toggleDropdown(button) {
                const dropdown = button.nextElementSibling;
                dropdown.classList.toggle('hidden');
            }

            // Close dropdowns when clicking outside
            document.addEventListener('click', function(event) {
                const dropdowns = document.querySelectorAll('.dropdown-menu');
                dropdowns.forEach(function(dropdown) {
                    if (!dropdown.classList.contains('hidden') &&
                        !dropdown.previousElementSibling.contains(event.target)) {
                        dropdown.classList.add('hidden');
                    }
                });
            });
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
