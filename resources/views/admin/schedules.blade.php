<x-app-layout>
    @section('title', 'Scheduler System with Automated Nursery System')
    @section('styles')
        <link rel="stylesheet" href="//cdn.datatables.net/2.1.6/css/dataTables.dataTables.min.css">
        <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-timepicker/0.5.2/css/bootstrap-timepicker.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/habibmhamadi/multi-select-tag@3.1.0/dist/css/multi-select-tag.css">
    @endsection
    @section('title-pane', 'Manage Schedules')

    <div class="outer-container flex flex-col md:flex-row items-center justify-end">
        <div class="buttons flex items-center justify-end gap-2 w-80">
            <button class="button bg-gradient-to-r from-[#d3d3d3] to-[#c0c0c0] text-gray-800 border border-transparent rounded-full flex items-center gap-1.5 px-2 py-2 shadow-custom transition-transform duration-300 hover:border-[#a9a9a9] active:transform active:scale-95 active:shadow-custom-active">
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

            <button class="group cursor-pointer outline-none hover:rotate-90 duration-300" title="Add New" data-bs-toggle="modal" data-bs-target="#scheduleModal">
                <svg class="stroke-blue-950 fill-none group-hover:fill-blue-100 group-active:stroke-blue-900 group-active:fill-blue-950 group-active:duration-0 duration-300" viewBox="0 0 24 24"
                    height="50px" width="50px" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-width="1" d="M12 22C17.5 22 22 17.5 22 12C22 6.5 17.5 2 12 2C6.5 2 2 6.5 2 12C2 17.5 6.5 22 12 22Z"></path>
                    <path stroke-width="1" d="M8 12H16"></path>
                    <path stroke-width="1" d="M12 16V8"></path>
                </svg>
            </button>
        </div>

        <div class="modal fade" id="scheduleModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header text-center bg-[#223a5e]">
                        <h1 class="modal-title fs-5 text-center text-neutral-100" id="staticBackdropLabel">Create New Schedule</h1>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('admin.createSchedule') }}" method="post" id="schedules-form" class="grid grid-cols-1 gap-4">
                            @csrf
                            @method('post')

                            <!-- Dropdown selections -->
                            <select name="teacher_id" id="teacher_id" class="form-control col-span-1">
                                <option value="">Select Teacher</option>
                                @foreach($teachers->unique('teacherName') as $teacher)
                                    <option value="{{ $teacher->id }}">{{ $teacher->teacherName }}</option>
                                @endforeach
                            </select>

                            <select id="category-select" name="categoryName" class="form-control col-span-1">
                                <option value="">Select Category</option>
                                @foreach($subjects->unique('category') as $subject)
                                    <option value="{{ $subject->category }}">{{ $subject->category }}</option>
                                @endforeach
                            </select>

                            <select id="days" name="days[]" class="form-control col-span-1" multiple>
                                <option value="M" selected>Monday</option>
                                <option value="T">Tuesday</option>
                                <option value="W">Wednesday</option>
                                <option value="TH">Thursday</option>
                                <option value="F">Friday</option>
                            </select>

                            <!-- Two-column grid for the small elements -->
                            <div class="grid grid-cols-2 gap-4 col-span-1">
                                <select name="subject_id" id="subject_id" class="form-control">
                                    <option value="">Select Subject</option>
                                    @foreach($subjects->unique('subjectName') as $subject)
                                        <option value="{{ $subject->id }}">{{ $subject->subjectName }}</option>
                                    @endforeach
                                </select>

                                <select name="room_id" id="room_id" class="form-control">
                                    <option value="">Select Room</option>
                                    @foreach($classrooms->unique('roomName') as $classroom)
                                        <option value="{{ $classroom->id }}">{{ $classroom->roomName }}</option>
                                    @endforeach
                                </select>

                                <input type="text" name="studentNum" id="student-number" class="form-control w-full p-2 rounded-md" placeholder="Student No.">
                                <input type="text" name="yearSection" id="year-section" class="form-control w-full p-2 rounded-md" placeholder="Year & Section">

                                <div class="col-span-2 grid grid-cols-2 gap-4">
                                    <input type="text" name="startTime" id="start-time" class="form-control w-full p-2 rounded-md timepicker" placeholder="Start Time (e.g. 02:30 PM)">
                                    <input type="text" name="endTime" id="end-time" class="form-control w-full p-2 rounded-md timepicker" placeholder="End Time (e.g. 03:30 PM)">
                                </div>
                            </div>

                            <div class="flex justify-end gap-2 col-span-1">
                                <button type="button" class="border-[#223a5e] border-2 p-2 w-[120px] text-[#223a5e] rounded-lg" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="bg-[#223a5e] p-2 w-[120px] text-white rounded-lg">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        @include('admin-modals.editSchedule')
    </div>

    <table id="schedulesTable" class="bg-white">
        <thead>
            <tr>
                <th>Teacher Name</th>
                <th>Category</th>
                <th>Subject</th>
                <th class="text-center">Room</th>
                <th class="text-center">No. of Student</th>
                <th>Section/Year</th>
                <th class="text-center">Day/s</th>
                <th>Start Time</th>
                <th>End Time</th>
                <th class="text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($schedules as $schedule)
                <tr style="background-color: {{ $schedule->is_conflicted ? 'rgba(255, 0, 0, 0.6)' : 'white' }};">
                    <td class="text-md font-light">{{ $schedule->teacher->teacherName }}</td>
                    <td class="text-md font-light">{{ $schedule->categoryName }}</td>
                    <td class="text-md font-light">{{ $schedule->subject->subjectName }}</td>
                    <td class="text-md font-light text-center">{{ $schedule->classroom->roomName }}</td>
                    <td class="text-md font-light text-center">{{ $schedule->studentNum }}</td>
                    <td class="text-md font-light">{{ $schedule->yearSection }}</td>
                    <td class="text-md font-light text-center">{{ $schedule->days }}</td>
                    <td class="text-md font-light">{{ \Carbon\Carbon::parse($schedule->startTime)->format('g:i A') }}</td>
                    <td class="text-md font-light">{{ \Carbon\Carbon::parse($schedule->endTime)->format('g:i A') }}</td>
                    <td class="flex items-center justify-start">
                        <a href="{{ route('admin.editSchedule', $schedule->id) }}" class="btn btn-success bg-transparent text-green-600 text-xl mr-2" data-bs-toggle="modal" data-bs-target="#editScheduleModal-{{ $schedule->id }}">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('admin.deleteSchedule', $schedule->id) }}" method="POST" id="delete-form-{{ $schedule->id }}">
                            @csrf
                            @method('DELETE')
                            <a href="#" class="btn btn-danger bg-transparent text-red-600 text-xl" onclick="confirmDeletion(event, 'delete-form-{{ $schedule->id }}')">
                                <i class="fas fa-trash"></i>
                            </a>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @section('scripts')
        <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
        <script src="//cdn.datatables.net/2.1.6/js/dataTables.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-timepicker/0.5.2/js/bootstrap-timepicker.min.js"></script>
        <script src="https://cdn.jsdelivr.net/gh/habibmhamadi/multi-select-tag@3.1.0/dist/js/multi-select-tag.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.all.min.js"></script>
        <script>
            $(document).ready(function() {
                $('#schedulesTable').DataTable();

                $('.timepicker').timepicker({
                    showMeridian: true,
                    defaultTime: false,
                    minuteStep: 1
                });

                new MultiSelectTag('days');
                @foreach ($schedules as $schedule)
                    new MultiSelectTag('edit-days-{{ $schedule->id }}');
                @endforeach

                // Function to handle subject filtering based on category selection of the subjects
                function setupSubjectFiltering(categorySelect, subjectSelect) {
                    categorySelect.addEventListener('change', function() {
                        const categoryId = encodeURIComponent(this.value);
                        subjectSelect.innerHTML = '<option value="">Fetching subjects..</option>';

                        if (categoryId) {
                            fetch(`${window.location.origin}/api/subjects/by_category/${categoryId}`) // Fetch the API endpoint from the backend to get the response
                                .then(response => response.json())
                                .then(data => {
                                    subjectSelect.innerHTML = '<option value="">Select Subject</option>'; // Reset options back to default
                                    data.forEach(subject => {
                                        const option = document.createElement('option');
                                        option.value = subject.id;
                                        option.textContent = subject.subjectName;
                                        subjectSelect.appendChild(option);
                                    });
                                })
                                .catch(error => console.error('Error fetching subjects:', error));
                        }
                    });
                }

                // Get the elements responsible for the category and subject selection then pass these elements as parameters to the function
                const createCategorySelect = document.getElementById('category-select');
                const createSubjectSelect = document.getElementById('subject_id');
                setupSubjectFiltering(createCategorySelect, createSubjectSelect);

                // Loop through all the schedules and take their value from the edit modal then pass their variables as parameters for the subject filtering
                @foreach ($schedules as $schedule)
                    const editCategorySelect{{ $schedule->id }} = document.getElementById('edit-category-select-{{ $schedule->id }}');
                    const editSubjectSelect{{ $schedule->id }} = document.getElementById('subject_id-{{ $schedule->id }}');

                    if (editCategorySelect{{ $schedule->id }} && editSubjectSelect{{ $schedule->id }}) {
                        setupSubjectFiltering(editCategorySelect{{ $schedule->id }}, editSubjectSelect{{ $schedule->id }});
                    }
                @endforeach

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
