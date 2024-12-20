<x-app-layout>
    @section('title', 'Scheduler System with Automated Nursery System')
    @section('styles')
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">

        <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-timepicker/0.5.2/css/bootstrap-timepicker.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/habibmhamadi/multi-select-tag@3.1.0/dist/css/multi-select-tag.css">
    @endsection
    @section('title-pane', 'Manage Schedules')

    <div class="outer-container flex flex-col md:flex-row items-center justify-end">
        <div class="flex items-center justify-between w-full bg-white px-3 py-2 rounded-lg shadow-sm">
            <div class="flex items-center gap-2 w-full">
                <!-- Teacher sort sort -->
                <select id="teacherSelect" class="form-control w-[16%]">
                    <option value="">Select Teacher</option>
                    @foreach($teachers as $teacher)
                        <option value="{{ $teacher->id }}">{{ $teacher->teacherName }}</option>
                    @endforeach
                </select>

                <!-- Semester sort sort -->
                <select id="semesterSelect" class="form-control w-[16%]">
                    <option value="">All Semesters</option>
                    <option value="1st semester" {{ request('semester') == '1st semester' ? 'selected' : '' }}>1st Semester</option>
                    <option value="2nd semester" {{ request('semester') == '2nd semester' ? 'selected' : '' }}>2nd Semester</option>
                </select>

                <!-- Strand sort -->
                <select id="strandSelect" class="form-control w-[16%]">
                    <option value="">All Strands</option>
                    @foreach($subjects->unique('strand') as $subject)
                        <option value="{{ $subject->strand }}" {{ request('strand') == $subject->strand ? 'selected' : '' }}>
                            {{ $subject->strand }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="buttons flex items-center justify-end gap-2 w-80">
                <button class="button bg-gradient-to-r from-[#d3d3d3] to-[#c0c0c0] text-gray-800 border border-transparent rounded-full flex items-center gap-1.5 px-3 py-2 shadow-custom transition-transform duration-300 hover:border-[#a9a9a9] active:transform active:scale-95 active:shadow-custom-active" id="printButton">
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
                        data-bs-target="#scheduleModal"
                    >
                        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                        </div>
                    </button>
                </div>
            </div>
        </div>

       <!-- Modal for Resolving Schedule Conflicts -->
        <span class="hidden" id="resolveScheduleModal">
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-gray-800 bg-opacity-50" tabindex="-1">
                <div class="bg-white rounded-lg shadow-lg w-full max-w-lg">
                    <div class="flex justify-between items-center p-4 border-b">
                        <div class="flex flex-col">
                            <h5 class="text-lg font-semibold" id="resolveScheduleModalLabel">Resolve Schedule Conflict</h5>
                            <p class="text-sm">There is already a schedule asigned for this specific time and day. You can choose from any these free schedules.</p>
                        </div>
                        <button type="button" class="text-gray-400 hover:text-gray-600" onclick="document.getElementById('resolveScheduleModal').classList.add('hidden')">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <div class="p-4">
                        <!-- Teacher Information and Conflict Details -->
                        <div id="teacherInfo" class="mb-4 p-3 bg-gray-100 rounded">
                            <strong>Teacher:</strong> <span id="teacherName"></span><br>
                            <strong>Conflicted Schedule:</strong> <span id="conflictedSchedule"></span>
                        </div>

                        <!-- Available Time Slots -->
                        <h6 class="mb-3 font-semibold">Suggested Available Slots:</h6>
                        <div class="max-h-60 overflow-y-auto"> <!-- Added scrollable wrapper -->
                            <table class="min-w-full bg-white border border-gray-300">
                                <thead>
                                    <tr class="bg-[#223a5e] text-white">
                                        <th class="py-2 px-4 border">Day</th>
                                        <th class="py-2 px-4 border">Start Time</th>
                                        <th class="py-2 px-4 border">End Time</th>
                                        <th class="py-2 px-4 border">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Rows will be inserted dynamically via JS -->
                                </tbody>
                            </table>
                        </div>

                        <div class="flex justify-end mt-4">
                            <button id="rejectBtn" class="bg-red-500 text-white px-4 py-2 rounded mr-2" onclick="document.getElementById('resolveScheduleModal').classList.add('hidden')">Reject</button>
                            <button id="acceptBtn" class="bg-green-500 text-white px-4 py-2 rounded">Accept</button>
                        </div>
                    </div>
                </div>
            </div>
        </span>

        <!-- Modal for adding Schedule -->
        <div class="modal fade" id="scheduleModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content rounded-lg shadow-xl border-none">
                    <div class="modal-header bg-gradient-to-r from-[#223a5e] to-[#2c4b7b] text-white p-4 rounded-t-lg">
                        <h1 class="modal-title text-xl font-semibold" id="staticBackdropLabel">Create New Schedule</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="filter: brightness(0) invert(1);"></button>
                    </div>
                    <div class="modal-body p-6">
                        <form action="{{ route('admin.createSchedule') }}" method="post" id="schedules-form" class="space-y-4">
                            @csrf
                            @method('post')

                            <div class="grid grid-cols-2 gap-4">
                                <!-- Teacher Dropdown -->
                                <div>
                                    <label for="teacher_id" class="block mb-2 font-medium">Teacher</label>
                                    <select name="teacher_id" id="teacher_id" class="form-control w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#223a5e]">
                                        <option value="">Select Teacher</option>
                                        @foreach($teachers->unique('teacherName') as $teacher)
                                            <option value="{{ $teacher->id }}">{{ $teacher->teacherName }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Semester Dropdown -->
                                <div>
                                    <label for="semester" class="block mb-2 font-medium">Semester</label>
                                    <select id="semester" name="semester" class="form-control w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#223a5e]">
                                        <option value="">Select Semester</option>
                                        @foreach($subjects->unique('semester') as $subject)
                                            <option value="{{ $subject->semester }}">{{ $subject->semester }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Strand Dropdown -->
                                <div>
                                    <label for="strand" class="block mb-2 font-medium">Strand</label>
                                    <select id="strand" name="strand" class="form-control w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#223a5e]">
                                        <option value="">Select Strand</option>
                                        @foreach($subjects->unique('strand') as $subject)
                                            <option value="{{ $subject->strand }}">{{ $subject->strand }}</option>
                                        @endforeach
                                    </select>
                                </div>


                                <!-- Category Dropdown -->
                                <div>
                                    <label for="category-select" class="block mb-2 font-medium">Category</label>
                                    <select id="category-select" name="categoryName" class="form-control w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#223a5e]">
                                        <option value="">Select Category</option>
                                        @foreach($subjects->unique('category') as $subject)
                                            <option value="{{ $subject->category }}">{{ $subject->category }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Days Multiselect -->
                                <div>
                                    <label for="days" class="block mb-2 font-medium">Days</label>
                                    <select id="days" name="days[]" class="form-control w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#223a5e]" multiple>
                                        <option value="M" selected>Monday</option>
                                        <option value="T">Tuesday</option>
                                        <option value="W">Wednesday</option>
                                        <option value="TH">Thursday</option>
                                        <option value="F">Friday</option>
                                    </select>
                                </div>

                                <!-- Subject Dropdown -->
                                <div>
                                    <label for="subject_id" class="block mb-2 font-medium">Subject</label>
                                    <select name="subject_id" id="subject_id" class="form-control w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#223a5e]">
                                        <option value="">Select Subject</option>
                                        @foreach($subjects->unique('subjectName') as $subject)
                                            <option value="{{ $subject->id }}">{{ $subject->subjectName }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Room Dropdown -->
                                <div>
                                    <label for="room_id" class="block mb-2 font-medium">Room</label>
                                    <select name="room_id" id="room_id" class="form-control w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#223a5e]">
                                        <option value="">Select Room</option>
                                        @foreach($classrooms->unique('roomName') as $classroom)
                                            <option value="{{ $classroom->id }}">{{ $classroom->roomName }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Year Dropdown -->
                                <div>
                                    <label for="year" class="block mb-2 font-medium">Year</label>
                                    <select name="year" id="year" class="form-control w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#223a5e]">
                                        <option value="">Select Year</option>
                                        <option value="Grade 11">Grade 11</option>
                                        <option value="Grade 12">Grade 12</option>
                                    </select>
                                </div>

                                <!-- Section Input -->
                                <div>
                                    <label for="section" class="block mb-2 font-medium">Section</label>
                                    <input type="text" name="section" id="section" placeholder="Enter Section" class="form-control w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#223a5e]">
                                </div>

                                <!-- Time Inputs -->
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="start-time" class="block mb-2 text-sm font-medium text-gray-700">Start Time</label>
                                        <input type="text" name="startTime" id="start-time"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg timepicker focus:outline-none focus:ring-2 focus:ring-[#223a5e] focus:border-transparent"
                                            placeholder="Start Time">
                                    </div>
                                    <div>
                                        <label for="end-time" class="block mb-2 text-sm font-medium text-gray-700">End Time</label>
                                        <input type="text" name="endTime" id="end-time"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg timepicker focus:outline-none focus:ring-2 focus:ring-[#223a5e] focus:border-transparent"
                                            placeholder="End Time">
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex justify-end gap-4 mt-6">
                                <button type="button" class="border-[#223a5e] border-2 p-2 w-[120px] text-[#223a5e] rounded-lg transition duration-300 hover:bg-[#223a5e] hover:text-white" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="bg-[#223a5e] p-2 w-[120px] text-white rounded-lg transition duration-300 hover:bg-[#2c4b7b]">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>


        @include('admin-modals.editSchedule')
    </div>

    <table id="schedulesTable" class="bg-white my-5">
        <thead>
            <tr>
                <th>Teacher</th>
                <th>Semester</th>
                <th>Strand</th>
                <th>Category</th>
                <th>Subject</th>
                <th class="text-center">Room</th>
                <th>Grade</th>
                <th>Section</th>
                <th class="text-center">Day/s</th>
                <th>Start Time</th>
                <th>End Time</th>
                <th class="text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($schedules as $schedule)
                {{-- <tr style="background-color: {{ $schedule->is_conflicted ? 'rgba(255, 0, 0, 0.6)' : 'white' }};"> --}}
                <tr>
                    <td class="text-md font-light">{{ $schedule->teacher->teacherName }}</td>
                    <td class="text-md font-light">{{ $schedule->semester }}</td>
                    <td class="text-md font-light">{{ $schedule->strand }}</td>
                    <td class="text-md font-light">{{ $schedule->categoryName }}</td>
                    <td class="text-md font-light">{{ $schedule->subject->subjectName }}</td>
                    <td class="text-md font-light text-center">{{ $schedule->classroom->roomName }}</td>
                    <td class="text-md font-light">{{ $schedule->year }}</td>
                    <td class="text-md font-light">{{ $schedule->section }}</td>
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
        <!-- Data tables and jquery scripts scripts -->
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
        <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
        <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

        <!-- Buttons extension scripts -->
        <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

        <!-- Sweet alert and other necessary scripts -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-timepicker/0.5.2/js/bootstrap-timepicker.min.js"></script>
        <script src="https://cdn.jsdelivr.net/gh/habibmhamadi/multi-select-tag@3.1.0/dist/js/multi-select-tag.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.all.min.js"></script>
            <script>
                $(document).ready(function() {
                    let table;

                    // Ensure DataTables is loaded before initializing
                    if ($.fn.DataTable) {
                        table = $('#schedulesTable').DataTable({
                            responsive: true,
                            pageLength: 10,
                            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
                            columnDefs: [
                                {
                                    targets: -1,
                                    orderable: false,
                                    className: 'actions-column'
                                }
                            ],
                            language: {
                                search: "_INPUT_",
                                searchPlaceholder: "Search schedules...",
                                lengthMenu: "Show _MENU_ entries",
                                paginate: {
                                    first: "First",
                                    last: "Last",
                                    next: "Next",
                                    previous: "Previous"
                                }
                            },
                            dom: 'frtip',
                            drawCallback: function() {
                                $('.dataTables_wrapper').addClass('my-3 p-4 bg-gray-50 rounded-lg shadow-sm');
                            }
                        });
                    } else {
                        console.error('DataTables not loaded correctly');
                    }

                    // Function to set dropdown values from URL parameters
                    function setDropdownValuesFromURL() {
                        // Get URL parameters
                        const urlParams = new URLSearchParams(window.location.search);

                        // Set teacher dropdown
                        const teacherId = urlParams.get('teacher');
                        if (teacherId) {
                            $('#teacherSelect').val(teacherId);
                        }

                        // Set semester dropdown
                        const semester = urlParams.get('semester');
                        if (semester) {
                            $('#semesterSelect').val(semester);
                            console.log(semester)
                        }

                        // Set strand dropdown
                        const strand = urlParams.get('strand');
                        if (strand) {
                            $('#strandSelect').val(strand);
                        }
                    }

                    // Call the function when page loads
                    setDropdownValuesFromURL();

                    // Modify the filtering event listeners to use a combined approach
                    function applyFilters() {
                        let formData = {};

                        // Get values from dropdowns
                        const teacherVal = $('#teacherSelect').val();
                        const semesterVal = $('#semesterSelect').val();
                        const strandVal = $('#strandSelect').val();

                        // Add values to formData if they exist
                        if (teacherVal) formData['teacher'] = teacherVal;
                        if (semesterVal) formData['semester'] = semesterVal;
                        if (strandVal) formData['strand'] = strandVal;

                        // Construct query string
                        let baseUrl = window.location.pathname;
                        let queryString = $.param(formData);

                        // Navigate to the URL with all selected filters
                        window.location.href = baseUrl + (queryString ? '?' + queryString : '');
                    }

                    // Event listeners for dropdowns
                    $('#teacherSelect, #semesterSelect, #strandSelect').on('change', function() {
                        applyFilters();
                    });

                    $('.timepicker').timepicker({
                        showMeridian: true,
                        defaultTime: false,
                        minuteStep: 1
                    });

                    function safeMultiSelectInit(selector) {
                        try {
                            const element = document.getElementById(selector);
                            if (element) {
                                new MultiSelectTag(selector);
                            } else {
                                console.warn(`Element with id ${selector} not found`);
                            }
                        } catch (error) {
                            console.error(`Error initializing MultiSelectTag for ${selector}:`, error);
                        }
                    }

                    // Initialize days multi-select
                    safeMultiSelectInit('days');

                    // Safely initialize edit-days multi-select for existing schedules
                    @foreach ($schedules as $schedule)
                        safeMultiSelectInit('edit-days-{{ $schedule->id }}');
                    @endforeach

                    // Function to handle subject filtering based on category selection of the subjects
                    function setupSubjectFiltering(categorySelect, subjectSelect) {
                        categorySelect.addEventListener('change', function() {
                            const categoryId = encodeURIComponent(this.value);
                            subjectSelect.innerHTML = '<option value="">Fetching subjects..</option>';

                            if (categoryId) {
                                fetch(`${window.location.origin}/api/subjects/by_category/${categoryId}`)
                                    .then(response => response.json())
                                    .then(data => {
                                        subjectSelect.innerHTML = '<option value="">Select Subject</option>';
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

                    // Get the elements responsible for the category and subject selection
                    const createCategorySelect = document.getElementById('category-select');
                    const createSubjectSelect = document.getElementById('subject_id');
                    setupSubjectFiltering(createCategorySelect, createSubjectSelect);

                    // Loop through all the schedules for edit modal subject filtering
                    @foreach ($schedules as $schedule)
                        const editCategorySelect{{ $schedule->id }} = document.getElementById('edit-category-select-{{ $schedule->id }}');
                        const editSubjectSelect{{ $schedule->id }} = document.getElementById('subject_id-{{ $schedule->id }}');

                        if (editCategorySelect{{ $schedule->id }} && editSubjectSelect{{ $schedule->id }}) {
                            setupSubjectFiltering(editCategorySelect{{ $schedule->id }}, editSubjectSelect{{ $schedule->id }});
                        }
                    @endforeach

                    // Sort filtering event listener
                    $('#semesterSelect').on('change', function() {
                        let formData = {};
                        let semesterVal = $(this).val();
                        if (semesterVal) {
                            formData['semester'] = semesterVal;
                        }

                        let teacherVal = $('#teacherSelect').val();
                        if (teacherVal) {
                            formData['teacher'] = teacherVal;
                        }

                        let baseUrl = window.location.pathname;
                        let queryString = $.param(formData);

                        window.location.href = baseUrl + (queryString ? '?' + queryString : '');
                    });

                    // Strand dropdown sorting event listener
                    $('#strandSelect').on('change', function() {
                        let formData = {};
                        let strandVal = $(this).val();

                        if (strandVal) {
                            formData['strand'] = strandVal;
                        }

                        let teacherVal = $('#teacherSelect').val();
                        if (teacherVal) {
                            formData['teacher'] = teacherVal;
                        }

                        let semesterVal = $('#semesterSelect').val();
                        if (semesterVal) {
                            formData['semester'] = semesterVal;
                        }

                        let baseUrl = window.location.pathname;
                        let queryString = $.param(formData);

                        window.location.href = baseUrl + (queryString ? '?' + queryString : '');
                    });

                    $('#printButton').on('click', function() {
                        const teacherId = $('#teacherSelect').val();
                        const semester = $('#semesterSelect').val();

                        if (!teacherId || !semester) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Incomplete Selection',
                                text: 'Please select both a teacher and a semester to print the schedule.',
                                confirmButtonColor: '#223a5e'
                            });
                            return;
                        }

                        // Construct the print URL with query parameters
                        const printUrl = "{{ route('print') }}" +
                            `?teacher=${teacherId}&semester=${semester}`;

                        // Open print window
                        const printWindow = window.open(printUrl, '_blank');

                        // Add error handling
                        if (!printWindow) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Pop-up Blocked',
                                text: 'Please allow pop-ups for this website to print schedules.',
                                confirmButtonColor: '#223a5e'
                            });
                            return;
                        }

                        printWindow.onload = function() {
                            try {
                                printWindow.print();
                            } catch (error) {
                                console.error('Print error:', error);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Print Error',
                                    text: 'An error occurred while trying to print the schedule.',
                                    confirmButtonColor: '#223a5e'
                                });
                            }

                            printWindow.onafterprint = function() {
                                $('#teacherSelect').val('');
                                $('#semesterSelect').val('');
                                printWindow.close();
                            };
                        };

                        printWindow.onerror = function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Print Error',
                                text: 'Unable to open print window. Please check your browser settings.',
                                confirmButtonColor: '#223a5e'
                            });
                        };
                    });

                    // Time formatting function
                    function formatTimeForServer(time) {
                        // If time is already in HH:mm:ss format, return as is
                        if (/^\d{2}:\d{2}:\d{2}$/.test(time)) {
                            return time;
                        }

                        // Parsing with multiple formats
                        const formats = [
                            'h:mm A',   // 12-hour format with AM/PM
                            'H:mm',     // 24-hour format
                            'HH:mm',    // Padded 24-hour format
                            'hh:mm A',  // Padded 12-hour format
                            'h:mm a',   // Lowercase am/pm
                        ];

                        // Try parsing the time using moment.js library
                        const parsedTime = moment(time, formats);

                        // Check if parsing was successful
                        if (parsedTime.isValid()) {
                            return parsedTime.format('HH:mm:ss');
                        }

                        // Fallback or error handling
                        console.error('Unable to parse time:', time);

                        // If parsing fails, show an alert to the user
                        Swal.fire({
                            icon: 'error',
                            title: 'Invalid Time Format',
                            text: `Unable to parse the time: ${time}. Please use formats like 9:30 AM, 14:30, etc.`
                        });

                        return moment().format('HH:mm:ss'); // Return current time if parsing fails
                    }

                    // Update the error handling in the form submission
                    $('#schedules-form').on('submit', function(event) {
                        event.preventDefault(); // Prevent default form submission

                        // Get raw time values
                        const rawStartTime = $('#start-time').val();
                        const rawEndTime = $('#end-time').val();

                        // Get and format times
                        const startTime = formatTimeForServer(rawStartTime);
                        const endTime = formatTimeForServer(rawEndTime);

                        // Check if time formatting failed
                        if (!startTime || !endTime) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Invalid Time Format',
                                text: 'Please enter valid start and end times.'
                            });
                            return;
                        }

                        // Create a copy of the form data
                        const formData = new FormData(this);
                        formData.set('startTime', startTime);
                        formData.set('endTime', endTime);

                        $.ajax({
                            url: $(this).attr('action'),
                            method: 'POST',
                            data: formData,
                            processData: false,
                            contentType: false,
                            dataType: 'json',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            },
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success',
                                    text: response.message || 'Schedule created successfully'
                                }).then(() => {
                                    window.location.reload();
                                });
                            },
                            error: function(xhr) {
                                console.log('Full Error Response:', xhr);

                                // Check specifically for conflict status
                                if (xhr.status === 409 ||
                                    (xhr.responseJSON && xhr.responseJSON.status === 'conflict')) {

                                    // Use responseJSON to get conflict details
                                    const response = xhr.responseJSON;

                                    // Ensure modal is hidden first
                                    $('#scheduleModal').modal('hide');

                                    // Slight delay to ensure modal is closed
                                    setTimeout(() => {
                                        // Open conflict resolution modal
                                        openConflictResolveModal(response);
                                    }, 300);
                                } else {
                                    // Handle other types of errors
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: xhr.responseJSON?.message || 'An unexpected error occurred',
                                        confirmButtonColor: '#223a5e'
                                    });
                                }
                            }
                        });
                    });

                    //function to open the resolve modal if there is a conflict
                    function openConflictResolveModal(response) {
                        try {
                            console.log('Opening Conflict Resolve Modal', response);

                            // Ensure the response has the expected structure
                            if (!response || !response.original_schedule || !response.available_slots) {
                                throw new Error('Invalid conflict response structure');
                            }

                            const originalSchedule = response.original_schedule;
                            const availableSlots = response.available_slots;

                            // Update modal content
                            $('#teacherName').text(originalSchedule.teacher_name || 'Unknown Teacher');

                            // Format schedule details
                            const conflictDetails = `
                                Semester: ${originalSchedule.semester || 'N/A'}<br>
                                Subject: ${originalSchedule.subject_name || 'N/A'}<br>
                                Days: ${originalSchedule.days || 'N/A'}<br>
                                Time: ${formatTime(originalSchedule.start_time)} - ${formatTime(originalSchedule.end_time)}
                            `;
                            $('#conflictedSchedule').html(conflictDetails);

                            // Clear and populate available slots
                            const slotsTableBody = $('#resolveScheduleModal tbody');
                            slotsTableBody.empty();

                            // Store original schedule data globally
                            window.originalScheduleData = originalSchedule;

                            // Populate available slots
                            if (availableSlots && availableSlots.length > 0) {
                                availableSlots.forEach(slot => {
                                    const row = `
                                        <tr data-day="${slot.day}"
                                            data-start-time="${slot.start_time}"
                                            data-end-time="${slot.end_time}">
                                            <td class="text-center">${getDayFullName(slot.day)}</td>
                                            <td class="text-center">${formatTime(slot.start_time)}</td>
                                            <td class="text-center">${formatTime(slot.end_time)}</td>
                                            <td class="text-center">
                                                <input type="checkbox" class="select-slot-checkbox"
                                                    data-day="${slot.day}"
                                                    data-start-time="${slot.start_time}"
                                                    data-end-time="${slot.end_time}">
                                            </td>
                                        </tr>
                                    `;
                                    slotsTableBody.append(row);
                                });
                            } else {
                                slotsTableBody.html(`
                                    <tr>
                                        <td colspan="4" class="text-center text-danger">
                                            No alternative slots available. Please choose a different time or teacher.
                                        </td>
                                    </tr>
                                `);
                            }

                            // Show the modal
                            const resolveModal = document.getElementById('resolveScheduleModal');

                            // Remove hidden class and make visible
                            resolveModal.classList.remove('hidden');
                            resolveModal.style.display = 'block';
                            resolveModal.setAttribute('aria-hidden', 'false');

                            // Ensure modal is on top of other elements
                            resolveModal.style.zIndex = '1050';
                            resolveModal.style.position = 'fixed';
                            resolveModal.style.top = '0';
                            resolveModal.style.left = '0';
                            resolveModal.style.width = '100%';
                            resolveModal.style.height = '100%';
                            resolveModal.style.backgroundColor = 'rgba(0,0,0,0.5)';

                        } catch (error) {
                            console.error('Error in openConflictResolveModal:', error);

                            Swal.fire({
                                icon: 'error',
                                title: 'Modal Error',
                                text: 'An error occurred while preparing the conflict resolution modal.',
                                footer: error.message,
                                confirmButtonColor: '#223a5e'
                            });
                        }
                    }

                    // Checkbox selection logic
                    $(document).on('change', '.select-slot-checkbox', function() {
                        $('.select-slot-checkbox').not(this).prop('checked', false);
                    });

                    // Accept button logic for resolving conflicts
                    $('#acceptBtn').click(function() {
                        const selectedSlots = [];
                        $('.select-slot-checkbox:checked').each(function() {
                            const day = $(this).data('day');
                            const startTime = $(this).data('start-time');
                            const endTime = $(this).data('end-time');

                            selectedSlots.push({
                                day: day,
                                startTime: formatTimeForServer(startTime + ':00'),
                                endTime: formatTimeForServer(endTime + ':00')
                            });
                        });

                        if (selectedSlots.length > 0 && window.originalScheduleData) {
                            const formData = new FormData();

                            console.log('Original Schedule Data:', window.originalScheduleData);

                            const requiredFields = [
                                'teacher_id',
                                'semester',
                                'strand',
                                'categoryName',
                                'subject_id',
                                'room_id',
                                'year',
                                'section'
                            ];

                            requiredFields.forEach(field => {
                                // Fallback to form input if not in original data
                                let value = window.originalScheduleData[field] ||
                                            $(`#${field}`).val() ||
                                            $(`input[name="${field}"]`).val() ||
                                            $(`select[name="${field}"]`).val();

                                if (field === 'section' && !value) {
                                    // Explicitly try to get section value
                                    value = $('#section').val() ||
                                            $('input[name="section"]').val() ||
                                            'Default Section';
                                }

                                if (value) {
                                    formData.append(field, value);
                                    console.log(`Appending ${field}:`, value);
                                } else {
                                    console.warn(`No value found for ${field}`);
                                }
                            });

                            // Ensure days are added
                            selectedSlots.forEach((slot, index) => {
                                formData.append(`days[${index}]`, slot.day);
                            });

                            // Set start and end times from the first selected slot
                            formData.append('startTime', selectedSlots[0].startTime);
                            formData.append('endTime', selectedSlots[0].endTime);

                            // Add selected slots as JSON
                            formData.append('selected_slots', JSON.stringify(selectedSlots));

                            // Add CSRF token
                            formData.append('_token', $('meta[name="csrf-token"]').attr('content') ||
                                $('input[name="_token"]').val());

                            // Debug: Log all form data
                            for (let pair of formData.entries()) {
                                console.log(pair[0] + ': ' + pair[1]);
                            }

                            $.ajax({
                                url: "{{ route('admin.createSchedule') }}",
                                method: 'POST',
                                data: formData,
                                processData: false,
                                contentType: false,
                                success: function(response) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Schedule Created Successfully!',
                                        text: response.message || 'Your schedule has been successfully created.',
                                        timer: 2000,
                                        timerProgressBar: true,
                                        didClose: () => {
                                            window.location.href = "{{ route('admin.schedules') }}";
                                        }
                                    });
                                },
                                error: function(xhr, status, error) {
                                    console.error('Full Error Response:', xhr);

                                    // More detailed error handling
                                    let errorMessage = 'An error occurred while creating the schedule.';

                                    if (xhr.responseJSON) {
                                        if (xhr.responseJSON.errors) {
                                            // Laravel validation errors
                                            const errors = xhr.responseJSON.errors;
                                            errorMessage = Object.values(errors).flat().join('\n');
                                        } else if (xhr.responseJSON.message) {
                                            // Custom error message
                                            errorMessage = xhr.responseJSON.message;
                                        }
                                    }

                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: errorMessage,
                                        footer: `
                                            <div>
                                                <p>Troubleshooting Tips:</p>
                                                <ul>
                                                    <li>Ensure all required fields are filled</li>
                                                    <li>Check that you've selected a valid section</li>
                                                    <li>Verify all schedule details</li>
                                                </ul>
                                            </div>
                                        `
                                    });
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'warning',
                                title: 'No Slot Selected',
                                text: 'Please select at least one alternative time slot.'
                            });
                        }
                    });

                    // Reject button logic
                    $('#rejectBtn').click(function() {
                        Swal.fire({
                            title: 'Discard Schedule?',
                            text: 'The schedule will not be saved due to conflicts.',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#3085d6',
                            confirmButtonText: 'Yes, discard',
                            cancelButtonText: 'Keep Editing'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Close the conflict resolution modal
                                $('#resolveScheduleModal').addClass('hidden');
                                $('#resolveScheduleModal').hide();
                                $('body').removeClass('modal-open');
                                $('.modal-backdrop').remove();
                                $('#schedules-form')[0].reset();
                            } else if (result.dismiss === Swal.DismissReason.cancel) {
                                // Close the conflict resolution modal
                                $('#resolveScheduleModal').addClass('hidden');

                                // Reopen the create schedule modal
                                $('#scheduleModal').modal('show');
                            }
                        });
                    });

                    // Helper function to get full day name
                    function getDayFullName(shortDay) {
                        const dayMap = {
                            'M': 'Monday',
                            'T': 'Tuesday',
                            'W': 'Wednesday',
                            'TH': 'Thursday',
                            'F': 'Friday'
                        };
                        return dayMap[shortDay] || shortDay;
                    }

                    // Helper function to format time
                    function formatTime(time) {
                        if (!time) return 'N/A';

                        // Remove seconds if present
                        const timeParts = time.split(':');
                        const hours = parseInt(timeParts[0]);
                        const minutes = timeParts[1];

                        // Convert to 12-hour format
                        const period = hours >= 12 ? 'PM' : 'AM';
                        const formattedHours = hours % 12 || 12;

                        return `${formattedHours}:${minutes} ${period}`;
                    }

                    // Global error handling
                    window.addEventListener('error', function(event) {
                        console.error('Uncaught error:', event.error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Unexpected Error',
                            text: 'An unexpected error occurred. Please try again.',
                            footer: 'Check console for more details'
                        });
                    });
                });

                // Confirmation function for deletion
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
