<x-app-layout>
    @section('title', 'Scheduler System with Automated Nursery System')
    @section('styles')
        <link rel="stylesheet" href="//cdn.datatables.net/2.1.6/css/dataTables.dataTables.min.css">
        {{-- Sweet alert 2 css link --}}
        <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-timepicker/0.5.2/css/bootstrap-timepicker.min.css">
    @endsection

    <div class="spacer mb-10"></div>
    <table id="schedulesTable" class="bg-white">
        <thead>
            <tr>
                <th>Category</th>
                <th>Subject</th>
                <th>Room</th>
                <th>No. of Students</th>
                <th>Section/Year</th>
                <th>Days</th>
                <th>Start Time</th>
                <th>End Time</th>
                <th class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($schedules as $schedule)
                <tr style="background-color: {{ $schedule->is_conflicted ? 'rgba(255, 0, 0, 0.5)' : 'white' }}">
                    <td class="text-md font-light">{{ $schedule->categoryName }}</td>
                    <td class="text-md font-light">{{ $schedule->subject->subjectName }}</td>
                    <td class="text-md font-light">{{ $schedule->classroom->roomName }}</td>
                    <td class="text-md font-light">{{ $schedule->studentNum }}</td>
                    <td class="text-md font-light">{{ $schedule->yearSection }}</td>
                    <td class="text-md font-light">{{ $schedule->days }}</td>
                    <td class="text-md font-light">{{ \Carbon\Carbon::parse($schedule->startTime)->format('h:i A') }}</td>
                    <td class="text-md font-light">{{ \Carbon\Carbon::parse($schedule->endTime)->format('h:i A') }}</td>
                    <td class="text-md font-light">
                        @if($schedule->is_conflicted)
                            <span class="text-red-600 font-bold">Conflicting</span>
                        @else
                            <span class="text-green-600 font-bold">No Conflict</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center py-4">
                        <div class="alert alert-info">
                            No schedules found for your current assignment.
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @section('scripts')
        <!-- jQuery cdn link-->
        <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
        <script src="//cdn.datatables.net/2.1.6/js/dataTables.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-timepicker/0.5.2/js/bootstrap-timepicker.min.js"></script>
        <script>
            $(document).ready(function() {
                $('#schedulesTable').DataTable({
                    responsive: true,
                    pageLength: 10,
                    lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
                    language: {
                        emptyTable: "No schedules available",
                        zeroRecords: "No matching schedules found"
                    }
                });

                $('#schedulesTable').DataTable();

                $('.timepicker').timepicker({
                    showMeridian: true,
                    defaultTime: false,
                    minuteStep: 1
                });
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


