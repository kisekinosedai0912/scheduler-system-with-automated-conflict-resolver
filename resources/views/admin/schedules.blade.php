<x-app-layout>
    @section('title', 'Scheduler System with Automated Nursery System')

    @section('styles')
        <link rel="stylesheet" href="//cdn.datatables.net/2.1.6/css/dataTables.dataTables.min.css">
        {{-- Sweet alert 2 css link --}}
        <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-timepicker/0.5.2/css/bootstrap-timepicker.min.css">
    @endsection

    @section('title-pane', 'Manage Schedules')


    <div class="outer-container flex flex-col md:flex-row items-center justify-end">
        <div class="buttons flex items-center justify-end gap-2 w-80">
            {{-- Print button --}}
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
                
            {{-- Add button with modal trigger --}}
            <button class="group cursor-pointer outline-none hover:rotate-90 duration-300" title="Add New" data-bs-toggle="modal" data-bs-target="#scheduleModal">
                <svg class="stroke-blue-950 fill-none group-hover:fill-blue-100 group-active:stroke-blue-900 group-active:fill-blue-950 group-active:duration-0 duration-300" viewBox="0 0 24 24"
                    height="50px" width="50px" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-width="1" d="M12 22C17.5 22 22 17.5 22 12C22 6.5 17.5 2 12 2C6.5 2 2 6.5 2 12C2 17.5 6.5 22 12 22Z"></path>
                    <path stroke-width="1" d="M8 12H16"></path>
                    <path stroke-width="1" d="M12 16V8"></path>
                </svg>
            </button>
        </div>       
        

        <!-- Modal -->
        <div class="modal fade" id="scheduleModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header text-center bg-[#223a5e]">
                        <h1 class="modal-title fs-5 text-center text-neutral-100" id="staticBackdropLabel">Create New Schedule</h1>
                    </div>
                    <div class="modal-body">
                        {{-- Modal form --}}
                        <form action="{{ route('admin.createSchedule') }}" method="post" name="schedulesForm" id="schedules-form" class="inputs grid grid-cols-2 gap-4">
                            @csrf
                            @method('post')

                            {{-- Input controls --}}
                            <input type="text" name="teacherName" id="teacher-name" class="form-control col-span-2 w-full p-2 rounded-xl" placeholder="Teacher Name: ">
                            <input type="text" name="subject" id="subject" class="form-control w-full p-2 rounded-xl" placeholder="Subject: ">
                            <input type="text" name="studentNum" id="student-number" class="form-control w-full p-2 rounded-xl" placeholder="Student No.: ">
                            <input type="text" name="yearSection" id="year-section" class="form-control w-full p-2 rounded-xl" placeholder="Year & Section: ">
                            <input type="text" name="room" id="room" class="form-control w-full p-2 rounded-xl" placeholder="Room: ">
                            
                            <div class="col-span-2 grid grid-cols-2 gap-4">
                                <input type="text" name="startTime" id="start-time" class="form-control w-full p-2 rounded-xl timepicker" placeholder="Start Time (e.g. 02:30 PM)">
                                <input type="text" name="endTime" id="end-time" class="form-control w-full p-2 rounded-xl timepicker" placeholder="End Time (e.g. 03:30 PM)">
                            </div>                            

                             {{-- Buttons --}}
                            <div class="flex justify-end gap-2 col-span-2">
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
                <th>Subject</th>
                <th>No. of Student</th>
                <th>Section/Year</th>
                <th>Room</th>
                <th>Start Time</th>
                <th>End Time</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($schedules as $schedule)
                <tr>
                    <td>{{ $schedule->teacherName }}</td>
                    <td>{{ $schedule->subject }}</td>
                    <td>{{ $schedule->studentNum }}</td>
                    <td>{{ $schedule->yearSection }}</td>
                    <td>{{ $schedule->room }}</td>
                    <td>{{ $schedule->startTime }}</td>
                    <td>{{ $schedule->endTime }}</td>
                    <td class="flex items-center justify-start">
                        <a href="{{ route('admin.editSchedule', $schedule->id) }}" class="btn btn-success bg-transparent text-green-600 text-xl mr-2 hover:border-green-200 hover:text-green-900" data-bs-toggle="modal" data-bs-target="#editScheduleModal-{{ $schedule->id }}">
                            <i class="fas fa-edit"></i>
                        </a>

                        <form action="{{ route('admin.deleteSchedule', $schedule->id) }}" method="POST" id="delete-form-{{ $schedule->id }}">
                            @csrf
                            @method('DELETE')
                            <a href="#" class="btn btn-danger bg-transparent text-red-600 text-xl hover:border-red-200 hover:text-red-700" onclick="confirmDeletion(event, 'delete-form-{{ $schedule->id }}')">
                                <i class="fas fa-trash"></i>
                            </a>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @section('scripts')
        <!-- jQuery cdn link-->
        <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
        <script src="//cdn.datatables.net/2.1.6/js/dataTables.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-timepicker/0.5.2/js/bootstrap-timepicker.min.js"></script>
        <script>
            $(document).ready(function() {
                $('#schedulesTable').DataTable();
            });

            $('.timepicker').timepicker({
                showMeridian: true, 
                defaultTime: false, 
                minuteStep: 1 
            });
        </script>
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
    @endsection
    <script>
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
</x-app-layout>


