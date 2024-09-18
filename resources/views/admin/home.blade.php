<x-app-layout>
    @section('title', 'Scheduler System with Automated Nursery System')
    @section('title-pane', 'S.Y 2024-2025 Calendar of Activities')

    @section('styles')
        <style>
            .buttonDownload {
                display: inline-block;
                position: relative;
                padding: 10px 25px;
                background-color: #3fa90e;
                color: white;
                font-family: sans-serif;
                text-decoration: none;
                font-size: 0.9em;
                text-align: center;
                text-indent: 15px;
                border: none;
            }
            .buttonDownload:hover {
                background-color: #2e720f;
                color: white;
            }
            .buttonDownload:before, .buttonDownload:after {
                content: ' ';
                display: block;
                position: absolute;
                left: 15px;
                top: 52%;
            }
            .buttonDownload:before {
                width: 10px;
                height: 2px;
                border-style: solid;
                border-width: 0 2px 2px;
            }
            .buttonDownload:after {
                width: 0;
                height: 0;
                margin-left: 1px;
                margin-top: -7px;
                border-style: solid;
                border-width: 4px 4px 0 4px;
                border-color: transparent;
                border-top-color: inherit;
                animation: downloadArrow 1s linear infinite;
                animation-play-state: paused;
            }
            .buttonDownload:hover:before {
                border-color: #cdefbd;
            }
            .buttonDownload:hover:after {
                border-top-color: #cdefbd;
                animation-play-state: running;
            }
            @keyframes downloadArrow {
                0% {
                    margin-top: -7px;
                    opacity: 1;
                }
                0.001% {
                    margin-top: -15px;
                    opacity: 0.4;
                }
                50% {
                    opacity: 1;
                }
                100% {
                    margin-top: 0;
                    opacity: 0.4;
                }
            }
        </style>
    @endsection

    <div class="row w-full">
            <div class="input-control md:ml-[24px] ml-[14px] mr-4 flex items-center justify-between gap-2 w-[calc(100%-24px)] bg-white rounded-md">
                {{-- Search input box--}}
                <div class="flex items-center relative md:w-3/12 my-2">
                    <svg class="absolute left-4 w-4 h-4 text-gray-500" aria-hidden="true" viewBox="0 0 24 24">
                    <g><path d="M21.53 20.47l-3.66-3.66C19.195 15.24 20 13.214 20 11c0-4.97-4.03-9-9-9s-9 4.03-9 9 4.03 9 9 9c2.215 0 4.24-.804 5.808-2.13l3.66 3.66c.147.146.34.22.53.22s.385-.073.53-.22c.295-.293.295-.767.002-1.06zM3.5 11c0-4.135 3.365-7.5 7.5-7.5s7.5 3.365 7.5 7.5-3.365 7.5-7.5 7.5-7.5-3.365-7.5-7.5z"></path></g>
                    </svg>
                    <input type="search" id="search" placeholder="search event" class="w-full h-10 pl-10 pr-4 px-1.5 rounded-md text-gray-900 bg-white focus:outline-none focus:bg-[#223a5e] transition duration-300">
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
            <div class="modal-content">
                <div class="modal-header bg-[#223a5e] text-neutral-100">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Create Calendar Activities</h1>
                </div>
                <div class="modal-body w-full">
                    {{-- Event title input --}}
                    <input type="text" name="eventTitle" id="event-title" placeholder="Event Title:" class="form-control p-2 w-full">
                    <span class="bg-red-600" id="titleError"></span>
                    <div class="grid grid-cols-2 gap-2 mt-3">
                        {{-- Start date input --}}
                        <div class="containers">
                            <label for="start-date" class="font-medium">Start Date:</label>
                            <input type="date" name="startDate" id="start-date" class="form-control">
                        </div>

                        {{-- End date input --}}
                        <div class="containers">
                            <label for="end-date" class="font-medium">End Date:</label></label>
                            <input type="date" name="endDate" id="end-date" class="form-control">
                        </div>
                    </div>

                    {{-- Buttons --}}
                    <div class="flex justify-end gap-2 col-span-2 mt-3">
                        <button type="button" class="border-[#223a5e] border-2 p-2 w-[120px] text-[#223a5e] rounded-lg" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="bg-[#223a5e] p-2 w-[120px] text-white rounded-lg" id="save" data-bs-dismiss="modal">Save Event</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container mt-2 max-w-screen">
        <div class="card overflow-hidden" style="border: 1px solid #141313;">
            <div class="card-body p-0 overflow-y-auto md:h-[510px]">
                <div class="w-full min-h-[430px] max-h-[800px] cursor-pointer" id="calendar"></div>
            </div>
        </div>
    </div>


    @section('scripts')
        {{-- JQuery script --}}
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

        {{-- Full calendar script --}}
        <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>

        {{-- Exporting to excel script --}}
        <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>
        
        <script>
            $(document).ready(function() {
                const printBtn = document.getElementById('print-button');
                const calendarElement = document.getElementById('calendar');

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                // Initialize the full calendar
                const calendar = new FullCalendar.Calendar(calendarElement, {
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,timeGridDay'
                    },
                    initialView: 'dayGridMonth',
                    timeZone: 'UTC',
                    events: '/calendar/events',
                    editable: true,
                    selectable: true,
                    aspectRatio: 2,

                    // Function for the resizing of calendar based on the screen
                    windowResize() {
                        calendar.updateSize();
                    },

                    // Function for toggling the modal when a date is clicked in the calendar
                    select(start, end, all) {
                        $('#calendar-events').modal('toggle');
                    },
                    
                    // Function for deleting events in the calendar
                    eventContent(info) {
                        const eventTitle = info.event.title;
                        const eventElement = document.createElement('div');
                        eventElement.innerHTML = `<span style="cursor:pointer;"><i class="fas fa-trash"></i></span> ${eventTitle}`;

                        eventElement.querySelector('span').addEventListener('click', () => {
                            if (confirm('Are you sure you want to delete this event?')) {
                                const eventId = info.event.id;
                                $.ajax({
                                    method: 'DELETE',
                                    url: `/calendar/event/${eventId}`,
                                    success() {
                                        alert('Event deleted successfully!');
                                        calendar.refetchEvents();
                                    },
                                    error(error) {
                                        alert('Error deleting event.', error);
                                    }
                                });
                            }
                        });

                        return { domNodes: [eventElement] };
                    },

                    // Function for dragging and dropping of calendar events
                    eventDrop(info) {
                        const eventId = info.event.id;
                        const newStartDate = info.event.start;
                        const newEndDate = info.event.end || newStartDate;
                        const newStartDateUTC = newStartDate.toISOString().slice(0, 10);
                        const newEndDateUTC = newEndDate.toISOString().slice(0, 10);

                        $.ajax({
                            method: 'PUT',
                            url: `/calendar/event/${eventId}`,
                            data: { start_date: newStartDateUTC, end_date: newEndDateUTC },
                            success() {
                                alert('Event re-assigned successfully!');
                            },
                            error(error) {
                                alert('Error re-assigning event.', error);
                            }
                        });
                    },

                    // Function for resizing events
                    eventResize(info) {
                        const eventId = info.event.id;
                        const newEndDate = info.event.end;
                        const newEndDateUTC = newEndDate.toISOString().slice(0, 10);

                        $.ajax({
                            method: 'PUT',
                            url: `/calendar/${eventId}/resize`,
                            data: { end_date: newEndDateUTC },
                            success() {
                                console.log('Event resized');
                            },
                            error(error) {
                                console.log('Error resizing current event.', error);
                            }
                        });

                    }
                });

                calendar.render();
                // Fetching searched events on the search bar
                $('#search').on('keypress', function (e) {
                    if (e.which === 13) { 
                        const searchEvent = $(this).val().toLowerCase();
                        displaySearchedEvents(searchEvent);
                    }
                });


                // Function for search
                const displaySearchedEvents = searchEvent => {
                    const searchedEvent = encodeURIComponent(searchEvent);

                    $.ajax({
                        method: 'GET',
                        url: `/calendar/search?eventTitle=${searchedEvent}`,
                        success(response) {
                            if (calendar) {
                                calendar.removeAllEvents();
                                calendar.addEventSource(response);
                            } else {
                                console.error('Calendar event not defined.');
                            }
                        },
                        error(jqXHR, status, error) {
                            console.error('Error searching events: ', status, error);
                        }
                    });
                }

                printBtn.addEventListener('click', () => {
                    const events = calendar.getEvents();
                    
                    if (events.length === 0) {
                        alert('No events to export.');
                        return;
                    }

                    const formattedEvents = events.map(event => ({
                        title: event.title,
                        start: event.start ? event.start.toISOString().slice(0, 10) : '',
                        end: event.end ? event.end.toISOString().slice(0, 10) : 'N/A',
                        color: event.backgroundColor,
                    }));

                    const wb = XLSX.utils.book_new();
                    const ws = XLSX.utils.json_to_sheet(formattedEvents);
                    XLSX.utils.book_append_sheet(wb, ws, 'Events');

                    XLSX.writeFile(wb, 'events.xlsx');
                });
            });
        </script>
    @endsection
</x-app-layout>
