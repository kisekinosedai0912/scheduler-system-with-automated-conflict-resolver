<x-app-layout>
    @section('title', 'Scheduler System with Automated Nursery System')
    @section('styles')
        <link rel="stylesheet" href="//cdn.datatables.net/2.1.6/css/dataTables.dataTables.min.css">
        <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-timepicker/0.5.2/css/bootstrap-timepicker.min.css">
    @endsection
    @section('title-pane', 'Schedule Conflicts')

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4 mt-4">
        <div class="bg-green-600 p-4 rounded-lg">
            <div class="wrapper flex items-center justify-between">
                <h3 class="text-lg font-semibold">Unique Schedules</h3>
                <i class="fas fa-clipboard-list mr-3 text-xl"></i>
            </div>
            <p class="text-2xl text-right mr-3 mt-2">{{ $subjectCount }}</p>
        </div>
        <div class="bg-red-600 p-4 rounded-lg">
            <div class="wrapper flex items-center justify-between">
                <h3 class="text-lg font-semibold">Conflicted Schedules</h3>
                <i class="fas fa-exclamation-triangle mr-3 text-xl"></i>
            </div>
            <p class="text-2xl text-right mr-3 mt-2">{{ $conflictCount }}</p>
        </div>
        {{-- <div class="bg-yellow-300 p-4 rounded-lg">
            <div class="wrapper flex items-center justify-between">
                <h3 class="text-lg font-semibold">Total Loaded Hours</h3>
                <i class="fas fa-clock mr-3 text-xl"></i>
            </div>
            <p class="text-2xl text-right mr-3 mt-2">{{ $totalLoadedHours }}</p>
        </div> --}}
    </div>

    <div class="flex justify-end mb-4">
        {{-- Print button --}}
        <button onclick="window.open('{{ route('print_conflicted_schedules') }}', '_blank')"
                class="button bg-gradient-to-r from-[#d3d3d3] to-[#c0c0c0] text-gray-800 border border-transparent rounded-full flex items-center gap-1.5 px-2 py-2 shadow-custom transition-transform duration-300 hover:border-[#a9a9a9] active:transform active:scale-95 active:shadow-custom-active"
                title="Print your conflicted scheds">
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
    </div>

    {{-- Conflicted Schedule Table --}}
    <span class="hidden md:block mt-3">
        <table class="table table-hover cursor-pointer border border-slate-950">
            <thead>
                <tr>
                    <th>Category</th>
                    <th>Subject</th>
                    <th class="text-center">Room</th>
                    <th class="text-center">No. of Students</th>
                    <th>Section/Year</th>
                    <th>Days</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($conflictedSchedules as $schedule)
                    <tr>
                        <td class="text-md font-light">{{ $schedule->categoryName }}</td>
                        <td class="text-md font-light">{{ $schedule->subject->subjectName }}</td>
                        <td class="text-md font-light text-center">{{ $schedule->classroom->roomName }}</td>
                        <td class="text-md font-light text-center">{{ $schedule->studentNum }}</td>
                        <td class="text-md font-light">{{ $schedule->yearSection }}</td>
                        <td>{{ $schedule->days }}</td>
                        <td class="text-md font-light">{{ \Carbon\Carbon::parse($schedule->startTime)->format('h:i A') }}</td>
                        <td class="text-md font-light">{{ \Carbon\Carbon::parse($schedule->endTime)->format('h:i A') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <div class="alert alert-info">
                                No conflicting schedules found.
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <!-- Pagination Links -->
        <div class="mt-4">
            {{-- {{ $paginateSubjects->links() }} --}}
        </div>
    </span>

    @section('scripts')
        <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
        <script src="//cdn.datatables.net/2.1.6/js/dataTables.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-timepicker/0.5.2/js/bootstrap-timepicker.min.js"></script>
        <script>
            $(document).ready(function() {
                // Initialize any scripts you need here
            });
        </script>
    @endsection
</x-app-layout>
