<x-app-layout>
    @section('title', 'Scheduler System with Automated Nursery System')
    @section('styles')
        {{-- Sweet alert 2 css link --}}
        <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.min.css" rel="stylesheet">
    @endsection
    @section('title-pane', "Teacher's Loading")

    <div class="outer-container flex flex-col md:flex-row items-center justify-between bg-white px-2 rounded-md">
        {{-- Search input box--}}
        <div class="flex items-center relative md:w-3/12 my-2">
            <svg class="absolute left-4 w-4 h-4 text-gray-500" aria-hidden="true" viewBox="0 0 24 24">
            <g><path d="M21.53 20.47l-3.66-3.66C19.195 15.24 20 13.214 20 11c0-4.97-4.03-9-9-9s-9 4.03-9 9 4.03 9 9 9c2.215 0 4.24-.804 5.808-2.13l3.66 3.66c.147.146.34.22.53.22s.385-.073.53-.22c.295-.293.295-.767.002-1.06zM3.5 11c0-4.135 3.365-7.5 7.5-7.5s7.5 3.365 7.5 7.5-3.365 7.5-7.5 7.5-7.5-3.365-7.5-7.5z"></path></g>
            </svg>
            <input type="search" placeholder="search event" class="w-full h-10 pl-10 pr-4 px-1.5 rounded-md text-gray-900 bg-white focus:outline-none focus:bg-[#223a5e] transition duration-300">
        </div>

        <div class="buttons flex items-center justify-evenly w-80">
            <div class="buttons flex items-center justify-end gap-2 w-80">
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
                                <select name="subjectName" id="subject-name" class="form-control col-span-2 w-full p-2 rounded-md">
                                    <option value="">Subjects</option>
                                    @foreach($subjects as $subject)
                                        <option value="{{ $subject->subjectName }}">{{ $subject->subjectName }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <input type="text" name="numberHours" id="number-hours" class="form-control col-span-2 w-full p-2 rounded-md" placeholder="No. of Hours: ">
                            </div>

                            {{-- Modal buttons --}}
                            <div class="modal-button flex items-center justify-end gap-2 mt-3">
                                <button type="button" class="border-[#223a5e] border-2 p-2 w-[120px] text-[#223a5e] rounded-lg" data-bs-dismiss="modal">Cancel</button>
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
                    <th scope="col">Subject Name</th>
                    <th scope="col">No. of Hours</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($teachers as $teacher)
                    <tr>
                        <td>{{ $teacher->teacherName }}</td>
                        <td>{{ $teacher->subjectName }}</td> 
                        <td>{{ $teacher->numberHours }}</td>
                        <td class="flex items-center justify-start">
                            <a href="{{ route('admin.editLoad', $teacher->id) }}" class="btn btn-success bg-transparent text-green-600 text-xl mr-2 hover:border-green-200 hover:text-green-900" data-bs-toggle="modal" data-bs-target="#editTeacher-{{ $teacher->id }}">
                                <i class="fas fa-gear"></i>
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
    </span>

    {{-- Table for mobile--}}
    <span class="block md:hidden">
        <table class="table shadow-sm">
            <thead>
                <tr>
                <th scope="col">Teacher's Name</th>
                <th scope="col">Subject Name</th>
                <th scope="col"># of Hours</th>
                <th scope="col"></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="font-light">Eric Coles</td>
                    <td class="font-light">Event Driven Programming</td>
                    <td class="font-light">2 Hours</td>
                    <td>
                        <div class="dropdown">
                            <button type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-ellipsis-h"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="">Edit</a></li>
                                <li><a class="dropdown-item" href="">Delete</a></li>
                            </ul>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </span>

    @section('scripts')
        {{-- Sweet alert 2 script --}}
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
                    icon: 'success',
                    title: 'Success',
                    text: "{{ session('success') }}"
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
            const typeSubject = document.getElementById('subject-name');

            typeSubject.addEventListener('input', function(e) {
                const filter = e.target.value.toLowerCase();
                const options = e.target.querySelectorAll('option');

                options.forEach(option => {
                    const text = option.textContent.toLowerCase();
                    option.style.display = text.includes(filter) ? 'block' : 'none';
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
    @endsection
</x-app-layout>