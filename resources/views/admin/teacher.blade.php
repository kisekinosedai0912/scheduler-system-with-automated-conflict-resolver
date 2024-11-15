<x-app-layout>
    @section('title', 'Scheduler System with Automated Nursery System')
    @section('styles')
        {{-- Sweet alert 2 css link --}}
        <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.min.css" rel="stylesheet">
    @endsection
    @section('title-pane', "Teacher's Information Management")

    <div class="outer-container flex items-center justify-between bg-white px-2 rounded-md">
        {{-- Search input box--}}
        <form class="flex items-center relative md:w-3/12 my-2" id="search-teacher-form">
            <svg class="absolute left-4 w-4 h-4 text-gray-500" aria-hidden="true" viewBox="0 0 24 24">
            <g><path d="M21.53 20.47l-3.66-3.66C19.195 15.24 20 13.214 20 11c0-4.97-4.03-9-9-9s-9 4.03-9 9 4.03 9 9 9c2.215 0 4.24-.804 5.808-2.13l3.66 3.66c.147.146.34.22.53.22s.385-.073.53-.22c.295-.293.295-.767.002-1.06zM3.5 11c0-4.135 3.365-7.5 7.5-7.5s7.5 3.365 7.5 7.5-3.365 7.5-7.5 7.5-7.5-3.365-7.5-7.5z"></path></g>
            </svg>
            <input type="search" id="search-teacher" name="searchTeacher" placeholder="search event" class="w-full h-10 pl-10 pr-4 px-1.5 rounded-md text-gray-900 bg-white focus:outline-none focus:bg-[#223a5e] transition duration-300" value="{{ request('searchTeacher') }}">
        </form>

        <div class="buttons flex items-center justify-evenly">
            {{-- Add button with modal trigger --}}
            <button class="group cursor-pointer outline-none hover:rotate-90 duration-300" title="Add New" data-bs-toggle="modal" data-bs-target="#staticBackdrop">
                <svg class="stroke-blue-950 fill-none group-hover:fill-blue-100 group-active:stroke-blue-900 group-active:fill-blue-950 group-active:duration-0 duration-300" viewBox="0 0 24 24"
                    height="50px" width="50px" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-width="1" d="M12 22C17.5 22 22 17.5 22 12C22 6.5 17.5 2 12 2C6.5 2 2 6.5 2 12C2 17.5 6.5 22 12 22Z"></path>
                    <path stroke-width="1" d="M8 12H16"></path>
                    <path stroke-width="1" d="M12 16V8"></path>
                </svg>
            </button>
        </div>
        <!-- Modal -->
        <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    {{-- Modal header --}}
                    <div class="modal-header text-center bg-[#223a5e]">
                        <h1 class="modal-title fs-5 text-center text-neutral-100" id="staticBackdropLabel">Add New Load</h1>
                    </div>
                    {{-- Modal body --}}
                    <div class="modal-body">
                        <form action="{{ route('admin.createLoad') }}" method="post" name="teachersForm" id="teachers-form">
                            @csrf
                            @method('post')

                            <div class="mb-3">
                                <input type="text" name="teacherName" id="teacher-name" class="form-control col-span-2 w-full p-2 rounded-md" placeholder="Teacher's Name: ">
                            </div>

                            <div class="mb-3">
                                <input type="email" id="email" name="email" class="form-control" placeholder="Email" required />
                            </div>

                            <div class="mb-3">
                                <input type="text" id="contact" name="contact" class="form-control" placeholder="Contact Number" required />
                            </div>

                            {{-- <div class="mb-3">
                                <input type="text" name="numberHours" id="number-hours" class="form-control col-span-2 w-full p-2 rounded-md" placeholder="Total Load Hours">
                            </div> --}}

                            {{-- Modal buttons --}}
                            <div class="modal-button flex items-center justify-end gap-2 mt-3">
                                <button type="button" class="border-[#223a5e] border-2 p-2 w-[120px] text-[#223a5e] rounded-lg" data-bs-dismiss="modal" id="dismiss">Cancel</button>
                                <button type="submit" class="bg-[#223a5e] p-2 w-[120px] text-white rounded-lg">Add Load</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @include('admin-modals.editTeacher')
    </div>

    <hr class="my-2">
    {{-- Table --}}
    <span class="hidden md:block">
        <table class="table table-hover cursor-pointer border border-slate-950">
            <thead>
                <tr>
                    <th scope="col">Teacher's Name</th>
                    <th scope="col">Email</th>
                    <th scope="col">Contact Number</th>
                    <th scope="col" class="text-center">Total Load Hours</th>
                    <th scope="col" class="text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($paginateLoads as $teacher)
                    <tr>
                        <td class="text-md font-light">{{ $teacher->teacherName }}</td>
                        {{-- <td class="text-md font-light">{{ $teacher->subject ? $teacher->subject->subjectName : 'No subject assigned' }}</td>  --}}
                        <td>{{ $teacher->email }}</td>
                        <td>{{ $teacher->contact }}</td>
                        <td class="text-md font-light text-center">{{ $teacher->numberHours }}</td>
                        <td class="flex items-center justify-center">
                            <a href="{{ route('admin.editLoad', $teacher->id) }}" class="btn btn-success bg-transparent text-green-600 text-xl mr-2 hover:border-green-200 hover:text-green-900" data-bs-toggle="modal" data-bs-target="#editTeacher-{{ $teacher->id }}">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.deleteLoad', $teacher->id) }}" method="POST" id="delete-form-{{ $teacher->id }}">
                                @csrf
                                @method('DELETE')
                                <a href="#" class="btn btn-danger bg-transparent text-red-600 text-xl hover:border-red-200 hover:text-red-700" onclick="confirmDeletion(event, 'delete-form-{{ $teacher->id }}')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </form>
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
