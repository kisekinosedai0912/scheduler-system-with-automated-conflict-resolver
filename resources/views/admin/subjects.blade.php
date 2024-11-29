<x-app-layout>
    @section('title', 'Scheduler System with Automated Nursery System')
    @section('styles')
        <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.min.css" rel="stylesheet">
    @endsection
    @section('title-pane', 'Manage Subjects')

    <div class="outer-container flex items-center justify-between px-4 py-2 rounded-lg bg-white shadow-md border border-gray-100">
        <form class="flex items-center relative md:w-3/12" id="subjects-search-form">
            <div class="absolute left-3 text-gray-500">
                <svg class="w-5 h-5" aria-hidden="true" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M21.53 20.47l-3.66-3.66C19.195 15.24 20 13.214 20 11c0-4.97-4.03-9-9-9s-9 4.03-9 9 4.03 9 9 9c2.215 0 4.24-.804 5.808-2.13l3.66 3.66c.147.146.34.22.53.22s.385-.073.53-.22c.295-.293.295-.767.002-1.06zM3.5 11c0-4.135 3.365-7.5 7.5-7.5s7.5 3.365 7.5 7.5-3.365 7.5-7.5 7.5-7.5-3.365-7.5-7.5z"></path>
                </svg>
            </div>
            <input
                type="search"
                name="searchSubject"
                id="search-subject"
                placeholder="Search subjects..."
                class="w-full h-10 pl-10 pr-4 rounded-md text-gray-900 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#223a5e] focus:border-transparent transition duration-300"
                value="{{ request('searchSubject') }}"
            >
        </form>

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

        <!-- Subject modal -->
        <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content rounded-lg shadow-xl border-none">
                    {{-- Modal header --}}
                    <div class="modal-header bg-gradient-to-r from-[#223a5e] to-[#2c4b7b] text-white p-4 rounded-t-lg">
                        <h1 class="modal-title text-xl font-semibold" id="staticBackdropLabel">Add New Subject</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="filter: brightness(0) invert(1);"></button>
                    </div>

                    {{-- Modal body --}}
                    <div class="modal-body p-6">
                        <div class="inputs">
                            <form action="{{ route('admin.createSubject') }}" method="post" name="subjectsForm" id="subjects-form" class="space-y-4">
                                @csrf
                                @method('post')

                                <div>
                                    <label for="semester" class="block mb-2 font-medium">Select Semester</label>
                                    <select name="semester" id="semester" class="form-control w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#223a5e]">
                                        <option disabled selected value="">Select Semester</option>
                                        <option value="1st semester">1st Semester</option>
                                        <option value="2nd semester">2nd Semester</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="category" class="block mb-2 font-medium">Select Subject Category</label>
                                    <select name="category" id="category" class="form-control w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#223a5e]">
                                        <option disabled selected value="">Select Subject Category</option>
                                        <option value="Grade-11 Subjects">Grade-11 Subjects</option>
                                        <option value="Grade-12 Subjects">Grade-12 Subjects</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="subject-name" class="block mb-2 font-medium">Subject Name</label>
                                    <input type="text"
                                        class="form-control w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#223a5e]"
                                        name="subjectName"
                                        id="subject-name"
                                        placeholder="Enter Subject Name">
                                </div>

                                <div>
                                    <label for="description" class="block mb-2 font-medium">Description</label>
                                    <textarea
                                        name="description"
                                        id="description"
                                        placeholder="Enter subject description..."
                                        class="form-control w-full p-2 border rounded-lg min-h-[100px] focus:outline-none focus:ring-2 focus:ring-[#223a5e]"></textarea>
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
        </div>
        @include('admin-modals.editSubject')
    </div>

    <hr class="my-2">

    {{-- Subject Table --}}
    <span class="hidden md:block">
        <table class="min-w-full bg-white shadow-sm rounded-lg overflow-hidden border border-slate-950">
            <thead class="bg-gradient-to-r from-[#223a5e] to-[#2c4b7b] text-white">
                <tr>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Semester</th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Category</th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Subject Name</th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Subject Description</th>
                    <th scope="col" class="px-4 py-3 text-center text-xs font-medium uppercase tracking-wider">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach ($paginateSubjects as $subject)
                    <tr class="hover:bg-gray-50 transition duration-200 ease-in-out">
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $subject->semester }}</div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $subject->category }}</div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $subject->subjectName }}</div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $subject->description }}</div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap text-center">
                            <div class="flex items-center justify-center space-x-2">
                                <a href="{{ route('admin.editSubject', $subject->id) }}"
                                class="text-green-500 hover:text-green-700 transition duration-200 ease-in-out"
                                data-bs-toggle="modal"
                                data-bs-target="#editModal-{{ $subject->id }}">
                                    <i class="fas fa-edit text-xl"></i>
                                </a>
                                <form action="{{ route('admin.deleteSubject', $subject->id) }}" method="POST" id="delete-form-{{ $subject->id }}">
                                    @csrf
                                    @method('DELETE')
                                    <a href="#"
                                    class="text-red-500 hover:text-red-700 transition duration-200 ease-in-out"
                                    onclick="confirmDeletion(event, 'delete-form-{{ $subject->id }}')">
                                        <i class="fas fa-trash text-xl"></i>
                                    </a>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Pagination Links -->
        <div class="mt-4">
            {{ $paginateSubjects->links() }}
        </div>
    </span>

    {{-- Subject table for mobile --}}
    <span class="block md:hidden">
        <table class="min-w-full bg-white shadow-md rounded-lg border border-gray-200">
            <thead class="bg-gradient-to-r from-[#223a5e] to-[#2c4b7b] text-white">
                <tr>
                    <th scope="col" class="px-4 py-2 text-left text-sm font-medium">Semester</th>
                    <th scope="col" class="px-4 py-2 text-left text-sm font-medium">Category</th>
                    <th scope="col" class="px-4 py-2 text-left text-sm font-medium">Subject Name</th>
                    <th scope="col" class="px-4 py-2 text-left text-sm font-medium">Description</th>
                    <th scope="col" class="px-4 py-2 text-center text-sm font-medium">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach ($paginateSubjects as $subject)
                    <tr class="hover:bg-gray-100 transition duration-200 ease-in-out">
                        <td class="px-4 py-3 text-sm font-light">{{ $subject->semester }}</td>
                        <td class="px-4 py-3 text-sm font-light">{{ $subject->category }}</td>
                        <td class="px-4 py-3 text-sm font-light">{{ $subject->subjectName }}</td>
                        <td class="px-4 py-3 text-sm font-light">{{ $subject->description }}</td>
                        <td class="px-4 py-3 text-center">
                            <div class="dropdown inline-block relative">
                                <button
                                    type="button"
                                    class="bg-transparent text-gray-600 hover:text-gray-900 focus:outline-none"
                                    data-bs-toggle="dropdown"
                                    aria-expanded="false"
                                >
                                    <i class="fas fa-ellipsis-h"></i>
                                </button>
                                <ul class="dropdown-menu absolute hidden bg-white shadow-lg rounded-lg mt-1 z-10">
                                    <li>
                                        <a
                                            href="{{ route('admin.editSubject', $subject->id) }}"
                                            class="block px-4 py-2 text-sm text-green-600 hover:bg-green-100"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editModal-{{ $subject->id }}"
                                        >
                                            Edit
                                        </a>
                                    </li>
                                    <li>
                                        <form action="{{ route('admin.deleteSubject', $subject->id) }}" method="POST" id="delete-form-{{ $subject->id }}">
                                            @csrf
                                            @method('DELETE')
                                            <a
                                                href="#"
                                                class="block px-4 py-2 text-red-600 hover:bg-red-100"
                                                onclick="confirmDeletion(event, 'delete-form-{{ $subject->id }}')"
                                            >
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

        {{-- Pagination Links --}}
        <div class="mt-4">
            {{ $paginateSubjects->links() }}
        </div>
    </span>

    @section('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.all.min.js"></script>
        <script>
            $(document).ready(function () {
                $('#search-subject').on('keypress', function (e) { // Searching event listner for searches
                if (e.which === 13) {
                        e.preventDefault();
                        $('#subjects-search-form').submit();
                    }
                });

                $('#search-subject').on('input', function () { // Searching event listener when the search input is empty
                    if ($(this).val().trim() === "") {
                        $('#subjects-search-form').submit();
                    }
                });
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
