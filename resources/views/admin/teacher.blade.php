<x-app-layout>
    @section('title', 'Scheduler System with Automated Nursery System')
    @section('styles')
        {{-- Sweet alert 2 css link --}}
        <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.min.css" rel="stylesheet">
    @endsection
    @section('title-pane', "Teacher's Information Management")

    <div class="outer-container flex items-center justify-between px-4 py-2 rounded-lg bg-white shadow-md border border-gray-100">
        {{-- Search input box--}}
        <form class="flex items-center relative md:w-3/12" id="search-teacher-form">
            <svg class="absolute left-4 w-4 h-4 text-gray-500" aria-hidden="true" viewBox="0 0 24 24">
                <g>
                    <path d="M21.53 20.47l-3.66-3.66C19.195 15.24 20 13.214 20 11c0-4.97-4.03-9-9-9s-9 4.03-9 9 4.03 9 9 9c2.215 0 4.24-.804 5.808-2.13l3.66 3.66c.147.146.34.22.53.22s.385-.073.53-.22c.295-.293.295-.767.002-1.06zM3.5 11c0-4.135 3.365-7.5 7.5-7.5s7.5 3.365 7.5 7.5-3.365 7.5-7.5 7.5-7.5-3.365-7.5-7.5z"></path>
                </g>
            </svg>
            <input type="search" id="search-teacher" name="searchTeacher" placeholder="Search teachers..."
                class="w-full h-10 pl-10 pr-4 rounded-md text-gray-900 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#223a5e] focus:border-transparent transition duration-300"
                value="{{ request('searchTeacher') }}">
        </form>

        <div class="flex items-center justify-end gap-2">
            <select id="teacherSelect" class="form-control w-full md:w-48 rounded-md border border-gray-300 text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#223a5e] transition duration-300">
                <option value="">Select Teacher</option>
                @foreach($paginateLoads as $teacher)
                    <option value="{{ $teacher->id }}">{{ $teacher->teacherName }}</option>
                @endforeach
            </select>

            <button class="button bg-gradient-to-r from-[#d3d3d3] to-[#c0c0c0] text-gray-800 border border-transparent rounded-full flex items-center gap-1.5 px-3 py-2 shadow-custom transition-transform duration-300 hover:border-[#a9a9a9] active:transform active:scale-95 active:shadow-custom-active" id="printBtn">
                <span class="font-medium">Print</span>
                <svg stroke-linejoin="round" stroke-linecap="round" fill="none" stroke="currentColor" stroke-width="1.5"
                    viewBox="0 0 24 24"
                    height="40"
                    width="40"
                    class="w-6 h-6"
                    xmlns="http://www.w3.org/2000/svg">
                    <path fill="none" d="M0 0h24v24H0z" stroke="none"></path>
                    <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2"></path>
                    <path d="M7 11l5 5l5 -5"></path>
                    <path d="M12 4l0 12"></path>
                </svg>
            </button>

            <div>
                <button
                    class="group relative w-10 h-10 rounded-full bg-[#223a5e] text-white hover:bg-[#2c4b7b] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#223a5e] transition duration-300 ease-in-out"
                    title="Add New User"
                    data-bs-toggle="modal"
                    data-bs-target="#staticBackdrop"
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
        <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content rounded-lg shadow-xl border-none">
                    {{-- Modal header --}}
                    <div class="modal-header bg-gradient-to-r from-[#223a5e] to-[#2c4b7b] text-white p-4 rounded-t-lg">
                        <h1 class="modal-title text-xl font-semibold" id="staticBackdropLabel">Add New Load</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="filter: brightness(0) invert(1);"></button>
                    </div>

                    {{-- Modal body --}}
                    <div class="modal-body p-6">
                        <form action="{{ route('admin.createLoad') }}" method="post" name="teachersForm" id="teachers-form" class="space-y-4">
                            @csrf
                            @method('post')

                            <div>
                                <label for="teacher-name" class="block mb-2 font-medium">Teacher's Name</label>
                                <input type="text"
                                    name="teacherName"
                                    id="teacher-name"
                                    class="form-control w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#223a5e]"
                                    placeholder="Enter teacher's full name"
                                    required>
                            </div>

                            <div>
                                <label for="email" class="block mb-2 font-medium">Email Address</label>
                                <input type="email"
                                    id="email"
                                    name="email"
                                    class="form-control w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#223a5e]"
                                    placeholder="Enter email address"
                                    required>
                            </div>

                            <div>
                                <label for="contact" class="block mb-2 font-medium">Contact Number</label>
                                <input type="tel"
                                    id="contact"
                                    name="contact"
                                    class="form-control w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#223a5e]"
                                    placeholder="Enter contact number"
                                    pattern="[0-9]{10}"
                                    required>
                            </div>

                            {{-- Modal buttons --}}
                            <div class="flex justify-end gap-4 mt-6">
                                <button type="button"
                                    class="border-[#223a5e] border-2 p-2 w-[120px] text-[#223a5e] rounded-lg transition duration-300 hover:bg-[#223a5e] hover:text-white"
                                    data-bs-dismiss="modal"
                                    id="dismiss">
                                    Cancel
                                </button>
                                <button type="submit"
                                    class="bg-[#223a5e] p-2 w-[120px] text-white rounded-lg transition duration-300 hover:bg-[#2c4b7b]">
                                    Add Load
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        </div>
        @include('admin-modals.editTeacher')
    </div>

    <hr class="my-2">

    {{-- Table --}}
    <span class="hidden md:block">
        <table class="min-w-full bg-white shadow-sm rounded-lg overflow-hidden border border-gray-200">
            <thead class="bg-gradient-to-r from-[#223a5e] to-[#2c4b7b] text-white">
                <tr>
                    <th scope="col" class="px-4 py-2 text-left text-sm font-medium uppercase tracking-wider">Teacher's Name</th>
                    <th scope="col" class="px-4 py-2 text-left text-sm font-medium uppercase tracking-wider">Email</th>
                    <th scope="col" class="px-4 py-2 text-left text-sm font-medium uppercase tracking-wider">Contact Number</th>
                    <th scope="col" class="px-4 py-2 text-center text-sm font-medium uppercase tracking-wider">Total Load Hours</th>
                    <th scope="col" class="px-4 py-2 text-center text-sm font-medium uppercase tracking-wider">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach ($paginateLoads as $teacher)
                    <tr class="hover:bg-gray-50 transition duration-200 ease-in-out">
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $teacher->teacherName }}</div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $teacher->email }}</div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $teacher->contact }}</div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap text-center">
                            <div class="text-sm text-gray-900">{{ $teacher->numberHours }}</div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap text-center">
                            <div class="flex items-center justify-center space-x-2">
                                <a
                                    href="{{ route('admin.editLoad', $teacher->id) }}"
                                    class="text-green-500 hover:text-green-700 transition duration-200 ease-in-out"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editTeacher-{{ $teacher->id }}"
                                >
                                    <i class="fas fa-edit text-xl"></i>
                                </a>
                                <form action="{{ route('admin.deleteLoad', $teacher->id) }}" method="POST" id="delete-form-{{ $teacher->id }}">
                                    @csrf
                                    @method('DELETE')
                                    <a
                                        href="#"
                                        class="text-red-500 hover:text-red-700 transition duration-200 ease-in-out"
                                        onclick="confirmDeletion(event, 'delete-form-{{ $teacher->id }}')"
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
            {{ $paginateLoads->links() }}
        </div>
    </span>

    {{-- Table for mobile--}}
    <span class="block md:hidden">
        <table class="table shadow-sm">
            <thead>
                <tr>
                <th scope="col" class="text-sm">Teachers</th>
                <th scope="col" class="text-sm">Email</th>
                <th scope="col" class="text-sm">Contact #</th>
                {{-- <th scope="col" class="text-sm">Total Load Hours</th> --}}
                <th scope="col" class="text-sm"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($paginateLoads as $teacher)
                    <tr>
                        <td class="text-md font-light">{{ $teacher->teacherName }}</td>
                        {{-- <td class="text-md font-light">{{ $teacher->subject ? $teacher->subject->subjectName : 'No subject assigned' }}</td> --}}
                        <td>{{ $teacher->email }}</td>
                        <td>{{ $teacher->contact }}</td>
                        {{-- <td class="text-md font-light">{{ $teacher->numberHours }}</td> --}}
                        <td>
                            <div class="dropdown">
                                <button type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-ellipsis-h"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item text-md" href="{{ route('admin.editLoad', $teacher->id) }}" data-bs-toggle="modal" data-bs-target="#editTeacher-{{ $teacher->id }}">Edit</a>
                                    </li>
                                    <li>
                                        <form action="{{ route('admin.deleteLoad', $teacher->id) }}" method="POST" id="delete-form-{{ $teacher->id }}">
                                            @csrf
                                            @method('DELETE')
                                            <a href="#" class="dropdown-item text-md" onclick="confirmDeletion(event, 'delete-form-{{ $teacher->id }}')">
                                                Delete
                                            </a>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="mt-2">
            {{ $paginateLoads->links() }}
        </div>
    </span>

    @section('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.all.min.js"></script>
        <script>
            $('#search-teacher').on('keypress', function (e) {
                if (e.which === 13) {
                    e.preventDefault();
                    $('#search-teacher-form').submit();
                }
            });

            $('#search-teacher').on('input', function () {
                if ($(this).val().trim() === "") {
                    $('#search-teacher-form').submit();
                }
            });

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
