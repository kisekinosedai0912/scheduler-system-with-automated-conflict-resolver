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

                {{-- For large screens button --}}
                <button class="buttonDownload rounded-md hidden md:block" id="print-button">Export to excel</button>
                {{-- For mobile button --}}
                <button class="buttonDownload rounded-md block md:hidden" id="print-button">Export</button>
            </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="calendar-events" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content" aria-hidden="false">
                <div class="modal-header bg-[#223a5e] text-neutral-100">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Create Calendar Activities</h1>
                </div>
                <div class="modal-body w-full">
                    {{-- Event title input --}}
                    <input type="text" name="eventTitle" id="event-title" placeholder="Event Title:" class="form-control p-2 w-full">
                    <span class="text-red-600" id="titleError"></span>
                    <div class="grid grid-cols-2 gap-2 mt-3">
                        {{-- Start date input --}}
                        <div class="containers">
                            <label for="start-date" class="font-medium">Start Date:</label>
                            <input type="date" name="startDate" id="start-date" class="form-control">
                        </div>

                        {{-- Start time input --}}
                        <div class="containers">
                            <label for="start-time" class="font-medium">Start Time:</label>
                            <input type="time" name="startTime" id="start-time" class="form-control">
                        </div>

                        {{-- End date input --}}
                        <div class="containers">
                            <label for="end-date" class="font-medium">End Date:</label>
                            <input type="date" name="endDate" id="end-date" class="form-control">
                        </div>

                        {{-- End time input --}}
                        <div class="containers">
                            <label for="end-time" class="font-medium">End Time:</label>
                            <input type="time" name="endTime" id="end-time" class="form-control">
                        </div>
                    </div>

                    {{-- Buttons --}}
                    <div class="flex justify-end gap-2 col-span-2 mt-3">
                        <button type="button" class="border-[#223a5e] border-2 p-2 w-[120px] text-[#223a5e] rounded-lg" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="bg-[#223a5e] p-2 w-[120px] text-white rounded-lg" id="save-btn">Save Event</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- Event details modal --}}
    <div class="modal fade" id="event-details" tabindex="-1" aria-labelledby="eventDetailsLabel">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-[#223a5e] text-neutral-100">
                    <h1 class="modal-title fs-5" id="eventDetailsLabel">Event Details</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="filter: brightness(0) invert(1);"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Event Title:</strong> <span id="modal-event-title"></span></p>
                    <div class=" grid grid-cols-2 gap-2 mt-2">
                        <p><strong>Start Date:</strong> <span id="modal-event-start"></span></p>
                        <p><strong>Start Time:</strong> <span id="modal-event-timeStart"></span></p>
                        <p><strong>End Date:</strong> <span id="modal-event-end"></span></p>
                        <p><strong>End Time:</strong> <span id="modal-event-timeEnd"></span></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="delete-btn" class="bg-red-600 p-2 w-[120px] text-white rounded-lg" id="delete-btn" data-bs-dismiss="modal">Delete Event</button>
                </div>
            </div>
        </div>
    </div>    

    <div class="container mt-2 max-w-screen">
        <div class="card overflow-hidden" style="border: 1px solid #141313;">
            <div class="card-body p-0 md:overflow-y-auto h-screen md:h-[510px]">
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
                // Function to export calendar data in excel file
                $('#print-button').on('click', function() {
                    if (events.length === 0) {
                        alert('No events to export.');
                        return;
                    }

                    let eventList = events.map(event => ({
                        Title: event.title,
                        StartDate: moment(event.start).format('YYYY-MM-DD'),
                        EndDate: event.end ? moment(event.end).format('YYYY-MM-DD') : 'No end date',
                        StartTime: moment(event.start).format('HH:mm'),
                        EndTime: event.end ? moment(event.end).format('HH:mm') : 'No end time'
                    }));

                    let ws = XLSX.utils.json_to_sheet(eventList);
                    let wb = XLSX.utils.book_new();
                    XLSX.utils.book_append_sheet(wb, ws, "Calendar Events");
                    XLSX.writeFile(wb, "Calendar_Events.xlsx");
                });
            });
        </script>
    @endsection
</x-app-layout>
