<x-app-layout>
    @section('title', 'Scheduler System with Automated Nursery System')

    @section('title-pane', 'Manage Schedules')

    @section('styles')
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
        <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-timepicker/0.5.2/css/bootstrap-timepicker.min.css">
    @endsection

    <div class="container mx-auto px-4 py-6">
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex justify-between items-center mb-4">
                <div class="flex space-x-2">
                    <button id="printButton" class="btn btn-secondary bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300 transition">
                        <i class="fas fa-download mr-2"></i>Print
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table id="schedulesTable" class="min-w-full bg-white">
                    <thead>
                        <tr class="bg-[#223a5e] text-white">
                            <th>Teacher</th>
                            <th>Semester</th>
                            <th>Strand</th>
                            <th>Category</th>
                            <th>Subject</th>
                            <th>Room</th>
                            <th>Grade</th>
                            <th>Section</th>
                            <th>Day/s</th>
                            <th>Start Time</th>
                            <th>End Time</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($schedules as $schedule)
                            <tr class="hover:bg-gray-100">
                                <td>{{ $schedule->teacher->teacherName }}</td>
                                <td>{{ $schedule->semester }}</td>
                                <td>{{ $schedule->strand }}</td>
                                <td>{{ $schedule->categoryName }}</td>
                                <td>{{ $schedule->subject->subjectName }}</td>
                                <td>{{ $schedule->classroom->roomName }}</td>
                                <td>{{ $schedule->year }}</td>
                                <td>{{ $schedule->section }}</td>
                                <td>{{ $schedule->days }}</td>
                                <td>{{ \Carbon\Carbon::parse($schedule->startTime)->format('g:i A') }}</td>
                                <td>{{ \Carbon\Carbon::parse($schedule->endTime)->format('g:i A') }}</td>
                                <td class="flex items-center space-x-2">
                                    <a href="{{ route('admin.editSchedule', $schedule->id) }}"
                                       class="text-green-600 hover:text-green-800"
                                       data-bs-toggle="modal"
                                       data-bs-target="#editScheduleModal-{{ $schedule->id }}">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.deleteSchedule', $schedule->id) }}" method="POST"
                                          id="delete-form-{{ $schedule->id }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button"
                                                class="text-red-600 hover:text-red-800"
                                                onclick="confirmDeletion(event, 'delete-form-{{ $schedule->id }}')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @section('scripts')
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
        <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.all.min.js"></script>

        <script>
            $(document).ready(function() {
                $('#schedulesTable').DataTable({
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

                // Filter functionality
                $('#teacherSelect, #semesterSelect, #strandSelect').on('change', function() {
                    let teacherId = $('#teacherSelect').val();
                    let semester = $('#semesterSelect').val();
                    let strand = $('#strandSelect').val();

                    $('#schedulesTable').DataTable().columns(0).search(teacherId)
                        .columns(1).search(semester)
                        .columns(2).search(strand)
                        .draw();
                });

                // Confirmation deletion function
                window.confirmDeletion = function(event, formId) {
                    event.preventDefault();
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You won't be able to revert this!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            document.getElementById(formId).submit();
                        }
                    });
                };
            });

            // Print button functionality
            $('#printButton').on('click', function() {
                window.print();
            });
        </script>

        {{-- Sweet Alert Notifications --}}
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
                    timerProgressBar: true
                });
            </script>
        @endif

        @if(session('error'))
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: "{{ session('error') }}"
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
