<x-app-layout>
    @section('title', 'Scheduler System with Automated Nursery System')
    @section('title-pane', 'S.Y 2024-2025 Calendar of Activities')

    @section('styles')
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.4.0/fullcalendar.css" />
        <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endsection

    <div class="row w-full">
        <div class="input-control md:ml-[24px] ml-[14px] mr-4 flex items-center justify-between gap-2 w-[calc(100%-24px)] bg-white rounded-md">
            {{-- Search input box--}}
            <div class="flex items-center relative md:w-3/12 my-2">
                <svg class="absolute left-4 w-4 h-4 text-gray-500" aria-hidden="true" viewBox="0 0 24 24">
                <g><path d="M21.53 20.47l-3.66-3.66C19.195 15.24 20 13.214 20 11c0-4.97-4.03-9-9-9s-9 4.03-9 9 4.03 9 9 9c2.215 0 4.24-.804 5.808-2.13l3.66 3.66c.147.146.34.22.53.22s.385-.073.53-.22c.295-.293.295-.767.002-1.06zM3.5 11c0-4.135 3.365-7.5 7.5-7.5s7.5 3.365 7.5 7.5-3.365 7.5-7.5 7.5-7.5-3.365-7.5-7.5z"></path></g>
                </svg>
                <input type="search" id="search-event" placeholder="search event" class="w-full h-10 pl-10 pr-4 px-1.5 rounded-md text-gray-900 bg-white focus:outline-none focus:bg-[#223a5e] transition duration-300">
            </div>

            <div class="flex items-center justify-end gap-2">
                <!-- Month Selection Dropdown -->
                <select id="month-select" class="form-control mr-2 w-[150px]">
                    <option value="">All Months</option>
                    <option value="01">January</option>
                    <option value="02">February</option>
                    <option value="03">March</option>
                    <option value="04">April</option>
                    <option value="05">May</option>
                    <option value="06">June</option>
                    <option value="07">July</option>
                    <option value="08">August</option>
                    <option value="09">September</option>
                    <option value="10">October</option>
                    <option value="11">November</option>
                    <option value="12">December</option>
                </select>

                <!-- From Date Dropdown -->
                <select id="from-date" class="form-control mr-2 w-[150px]">
                    <option value="">From Month</option>
                    @foreach(range(1, 12) as $month)
                        <option value="{{ $month }}">{{ date('F', mktime(0, 0, 0, $month, 1)) }}</option>
                    @endforeach
                </select>

                <!-- Until Date Dropdown -->
                <select id="until-date" class="form-control mr-2 w-[150px]">
                    <option value="">Until Month</option>
                    @foreach(range(1, 12) as $month)
                        <option value="{{ $month }}">{{ date('F', mktime(0, 0, 0, $month, 1)) }}</option>
                    @endforeach
                </select>

                <button class="button bg-gradient-to-r from-[#d3d3d3] to-[#c0c0c0] text-gray-800 border border-transparent rounded-lg flex items-center gap-1.5 px-3 py-2 shadow-custom transition-transform duration-300 hover:border-[#a9a9a9] active:transform active:scale-95 active:shadow-custom-active" id="print-button">
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

                {{-- For large screens button --}}
                {{-- <button class="buttonDownload rounded-md hidden md:block" id="print-button">Export to excel</button> --}}
                {{-- For mobile button --}}
                {{-- <button class="buttonDownload rounded-md block md:hidden" id="print-button">Export</button> --}}
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="calendar-events" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content rounded-lg shadow-xl border-none">
                <div class="modal-header bg-gradient-to-r from-[#223a5e] to-[#2c4b7b] text-white p-4 rounded-t-lg">
                    <h1 class="modal-title text-xl font-semibold" id="exampleModalLabel">Create Calendar Activities</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="filter: brightness(0) invert(1);"></button>
                </div>
                <div class="modal-body p-6">
                    {{-- Event title input --}}
                    <div class="mb-4">
                        <label for="event-title" class="font-medium">Event Title:</label>
                        <input type="text" name="eventTitle" id="event-title" placeholder="Enter Event Title" class="form-control p-2 mt-1 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#223a5e]">
                        <span class="text-red-600" id="titleError"></span>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        {{-- Start date input --}}
                        <div class="containers">
                            <label for="start-date" class="font-medium">Start Date:</label>
                            <input type="date" name="startDate" id="start-date" class="form-control border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#223a5e]">
                        </div>

                        {{-- Start time input --}}
                        <div class="containers">
                            <label for="start-time" class="font-medium">Start Time:</label>
                            <input type="time" name="startTime" id="start-time" class="form-control border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#223a5e]">
                        </div>

                        {{-- End date input --}}
                        <div class="containers">
                            <label for="end-date" class="font-medium">End Date:</label>
                            <input type="date" name="endDate" id="end-date" class="form-control border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#223a5e]">
                        </div>

                        {{-- End time input --}}
                        <div class="containers">
                            <label for="end-time" class="font-medium">End Time:</label>
                            <input type="time" name="endTime" id="end-time" class="form-control border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#223a5e]">
                        </div>
                    </div>

                    {{-- Buttons --}}
                    <div class="flex justify-end gap-4 mt-6">
                        <button type="button" class="border-[#223a5e] border-2 p-2 w-[120px] text-[#223a5e] rounded-lg transition duration-300 hover:bg-[#223a5e] hover:text-white" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="bg-[#223a5e] p-2 w-[120px] text-white rounded-lg transition duration-300 hover:bg-[#2c4b7b]" id="save-btn">Save Event</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Event details modal --}}
    <div class="modal fade" id="event-details" tabindex="-1" aria-labelledby="eventDetailsLabel">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content rounded-lg shadow-xl border-none">
                <div class="modal-header bg-gradient-to-r from-[#223a5e] to-[#2c4b7b] text-white p-4 rounded-t-lg">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <h1 class="modal-title text-xl font-semibold" id="eventDetailsLabel">Event Details</h1>
                    </div>
                    <button type="button" class="close text-white hover:text-gray-200 transition duration-300" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" class="text-2xl">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-6">
                    <div class="space-y-4">
                        <div class="bg-gray-100 p-4 rounded-lg">
                            <h2 class="text-lg font-bold text-[#223a5e] mb-2">Event Title</h2>
                            <p class="text-gray-800" id="modal-event-title"></p>
                        </div>

                        <div class="grid md:grid-cols-2 gap-4">
                            <div class="bg-gray-50 p-3 rounded-lg">
                                <h3 class="text-sm font-semibold text-gray-600 mb-1">Start Date</h3>
                                <p class="text-gray-800" id="modal-event-start"></p>
                            </div>
                            <div class="bg-gray-50 p-3 rounded-lg">
                                <h3 class="text-sm font-semibold text-gray-600 mb-1">Start Time</h3>
                                <p class="text-gray-800" id="modal-event-timeStart"></p>
                            </div>
                            <div class="bg-gray-50 p-3 rounded-lg">
                                <h3 class="text-sm font-semibold text-gray-600 mb-1">End Date</h3>
                                <p class="text-gray-800" id="modal-event-end"></p>
                            </div>
                            <div class="bg-gray-50 p-3 rounded-lg">
                                <h3 class="text-sm font-semibold text-gray-600 mb-1">End Time</h3>
                                <p class="text-gray-800" id="modal-event-timeEnd"></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-gray-100 p-4 rounded-b-lg flex justify-between items-center">
                    <button type="button" class="text-gray-600 hover:text-gray-800 transition duration-300" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button
                        type="submit"
                        id="delete-btn"
                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md transition duration-300 flex items-center"
                        data-bs-dismiss="modal"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        Delete Event
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="container mt-2 max-w-screen">
        <div class="card" style="border: 1px solid #141313;">
            <div class="card-body p-0 h-[calc(100vh-16rem)] overflow-y-auto">
                <div class="w-full h-full cursor-pointer" id="calendar"></div>
            </div>
        </div>
    </div>


    @section('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.4.0/fullcalendar.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.all.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
        <script>
           $(document).ready(function() {
                // Stack sorting preparation
                let events = @json($events).sort((a, b) => {
                    return moment(a.start).diff(moment(b.start)) || a.title.localeCompare(b.title);
                });
                const prevEventStartTime = "{{ $prevEventStartTime }}";

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $('#calendar-events').on('hidden.bs.modal', function () {
                    $('#save-btn').unbind();
                });

                // Full calendar modification functionalities
                $('#calendar').fullCalendar({
                    header: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'month,agendaWeek,agendaDay'
                    },

                    // Call the events and stack them
                    events,
                    editable: true,
                    selectable: true,
                    selectHelper: true,
                    aspectRatio: 2,
                    select (start, end, allDay) {
                        $('#calendar-events').modal('toggle');

                        $('#save-btn').off('click').on('click', function() {
                            let eventTitle = $('#event-title').val();
                            let startDate = moment($('#start-date').val() + 'T' + $('#start-time').val()).format('Y-MM-DDTHH:mm:ss');
                            let endDate = moment($('#end-date').val() + 'T' + $('#end-time').val()).format('Y-MM-DDTHH:mm:ss');
                            let newEventColor = startDate === prevEventStartTime ? 'red' : 'green';

                            $.ajax({
                                url: "{{ route('admin.createEvent') }}",
                                type: "POST",
                                dataType: "json",
                                data: {
                                    title: eventTitle,
                                    start: startDate,
                                    end: endDate,
                                    startTime: $('#start-time').val(),
                                    endTime: $('#end-time').val()
                                },
                                success: function(response) {
                                    $('#calendar-events').modal('hide');
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Success',
                                        text: response.success || 'Added successfully'
                                    });

                                    $('#calendar').fullCalendar('renderEvent', {
                                        title: eventTitle,
                                        start: startDate,
                                        end: endDate,
                                        allDay: false,
                                        color: newEventColor,
                                    }, false);

                                    events.push({
                                        title: eventTitle,
                                        start: startDate,
                                        end: endDate,
                                        allDay: false,
                                        color: newEventColor,
                                    });

                                    $('#calendar').fullCalendar('removeEventSources');
                                    $('#calendar').fullCalendar('addEventSource', events.concat([{
                                        title: eventTitle,
                                        start: startDate,
                                        end: endDate,
                                        allDay: false
                                    }]));

                                    $('#event-title').val('');
                                    $('#start-date').val('');
                                    $('#start-time').val('');
                                    $('#end-date').val('');
                                    $('#end-time').val('');
                                },
                                error: function(error) {
                                    if (error.responseJSON.errors) {
                                        $('#titleError').html(error.responseJSON.errors.title || '');
                                    }
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: error.responseJSON.message || 'An unexpected error occurred.'
                                    });
                                }
                            });
                        });
                    },

                    // Function to show the modal about the events
                    eventClick: function(event) {
                        let eventId = event.id;
                        const url = `{{ url('/admin') }}/${eventId}/delete-event`;
                        if (!event) {
                            console.error('Event is undefined');
                            return;
                        }

                        $('#modal-event-title').text(event.title);
                        $('#modal-event-start').text(moment(event.start).format('MMMM Do YYYY'));
                        $('#modal-event-end').text(event.end ? moment(event.end).format('MMMM Do YYYY') : 'No end date');
                        $('#modal-event-timeStart').text(moment(event.start).format('h:mm A'));
                        $('#modal-event-timeEnd').text(event.end ? moment(event.end).format('h:mm A') : 'No end time');

                        $('#event-details').modal('show');

                        $('#delete-btn').off('click').on('click', function() {
                            confirmDeletion(event, eventId, url);
                        });

                        // Function to confirm before deletion of event
                        function confirmDeletion(event, eventId, url) {
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
                                    $.ajax({
                                        url: url,
                                        type: "DELETE",
                                        dataType: "json",
                                        success: function(response) {
                                            Swal.fire({
                                                toast: true,
                                                position: 'top-end',
                                                icon: 'success',
                                                title: 'Event deleted successfully!',
                                                showConfirmButton: false,
                                                timer: 1000,
                                                timerProgressBar: true
                                            });
                                            $('#event-details').modal('hide');
                                            $('#calendar').fullCalendar('removeEvents', eventId);
                                        },
                                        error: function(error) {
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'Error',
                                                text: error.responseJSON.message || 'An unexpected error occurred.'
                                            });
                                        }
                                    });
                                }
                            });
                        }
                    },

                    // Function for the resizing of calendar based on the screen
                    eventResize: function(event) {
                        let eventId = event.id;
                        let newEndDate = event.end.format();

                        $.ajax({
                            url: `/admin/${eventId}/resize`,
                            type: 'PUT',
                            data: {
                                endDate: newEndDate
                            },
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success',
                                    text: response.message
                                });
                            },
                            error: function(error) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: error.responseJSON.message || 'An unexpected error occurred.'
                                });
                            }
                        });
                    },

                    // Function for event drag and drop
                    eventDrop(event) {
                        const eventId = event.id;
                        const startDate = moment(event.start).format('YYYY-MM-DD');
                        const endDate = moment(event.end).format('YYYY-MM-DD');
                        const url = `{{ url('/admin') }}/${eventId}/drag-drop`;

                        $.ajax({
                            url: url,
                            method: 'PATCH',
                            data: { startDate, endDate },
                            success(response) {
                                Swal.fire({
                                    toast: true,
                                    position: 'top-end',
                                    icon: 'success',
                                    title: 'Event updated successfully!',
                                    showConfirmButton: false,
                                    timer: 1000,
                                    timerProgressBar: true,
                                });
                            },
                            error(error) {
                                Swal.fire({
                                    toast: true,
                                    position: 'top-end',
                                    icon: 'error',
                                    title: 'Error updating event',
                                    showConfirmButton: false,
                                    timer: 1000,
                                    timerProgressBar: true
                                });
                            }
                        });
                    },
                });

                // Fetching searched events on the search input
                $('#search-event').on('input', function() {
                    let searchTerm = $(this).val().toLowerCase();
                    let filteredEvents = events.filter(event => event.title.toLowerCase().includes(searchTerm));

                    $('#calendar').fullCalendar('removeEventSources');
                    $('#calendar').fullCalendar('addEventSource', filteredEvents);
                });

                $('#print-button').on('click', function() {
                    let fromDate = $('#from-date').val();
                    let untilDate = $('#until-date').val();
                    let selectedMonth = $('#month-select').val();

                    console.log("From Date:", fromDate);
                    console.log("Until Date:", untilDate);

                    // Case 1: Date range selected
                    if (fromDate && untilDate) {
                        // Add the current year to the selected month (e.g., 08 => 2024-08-01)
                        const currentYear = new Date().getFullYear();

                        const formattedFromDate = `${currentYear}-${fromDate}-01`;  // "01" sets the date to the first of the month
                        const formattedUntilDate = `${currentYear}-${untilDate}-01`;

                        // Set untilDate to last day of the month
                        const untilDateObj = new Date(formattedUntilDate);
                        untilDateObj.setMonth(untilDateObj.getMonth() + 1);  // Move to next month
                        untilDateObj.setDate(0);  // Set it to the last day of the previous month

                        // Reformat the "until" date to "YYYY-MM-DD" format
                        const finalUntilDate = untilDateObj.toISOString().split('T')[0];

                        // Open the URL with the query parameters
                        window.open(`{{ route('print-calendar') }}?from=${formattedFromDate}&until=${finalUntilDate}`, '_blank');
                    }
                    // Case 2: Specific month selected
                    else if (selectedMonth) {
                        window.open(`{{ route('print-calendar') }}?month=${selectedMonth}`, '_blank');
                    }
                    // Case 3: Incomplete date range
                    else if (fromDate || untilDate) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Incomplete Date Range',
                            text: 'Please select both "From" and "Until" dates.'
                        });
                    }
                    // Case 4: No selection, print all events
                    else {
                        window.open("{{ route('print-calendar') }}", '_blank');
                    }
                });
            });
        </script>
    @endsection
</x-app-layout>
