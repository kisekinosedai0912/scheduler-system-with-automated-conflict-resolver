<x-app-layout>
    @section('title', 'Scheduler System with Automated Nursery System')
    @section('styles')
        <link rel="stylesheet" href="//cdn.datatables.net/2.1.6/css/dataTables.dataTables.min.css">
        {{-- Sweet alert 2 css link --}}
        <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-timepicker/0.5.2/css/bootstrap-timepicker.min.css">
    @endsection
    @section('title-pane', 'Event Notifications')

    <div class="icons mt-3 mr-2 flex items-center justify-end">
        <i class="fas fa-bell text-gray-950 text-[#223a5e] md:text-2xl lg:text-2xl"></i>
    </div>

    <div class="notification-container w-full flex items-center justify-center mt-3 gap-2">
        <div class="flex-col w-full">
            @foreach ($notifications as $notification)
                <div class="notification-item {{ $notification->is_read ? 'bg-white' : 'bg-[#a2d9f7]' }} mb-3 flex items-center justify-between px-4 py-3 rounded-xl">
                    <div class="details">
                        <h3 class="font-medium mb-3">{{ $notification->event->eventTitle }}</h3>
                        <p>
                            Event will start at {{ \Carbon\Carbon::parse($notification->event->startTime)->format('g:i A') }} until
                            {{ \Carbon\Carbon::parse($notification->event->endTime)->format('g:i A') }}
                        </p>
                        <p>
                            Event Date: {{ \Carbon\Carbon::parse($notification->event->startDate)->format('F j, Y') }} -
                            {{ \Carbon\Carbon::parse($notification->event->endDate)->format('F j, Y') }}
                        </p>
                    </div>
                    <div class="icon">
                        <i class="fas {{ $notification->is_read ? 'fa-eye' : 'fa-eye-slash' }}" id="read" data-id="{{ $notification->id }}"></i>
                    </div>
                </div>
            @endforeach
        </div>
    </div>


    @section('scripts')
        <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
        <script src="//cdn.datatables.net/2.1.6/js/dataTables.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-timepicker/0.5.2/js/bootstrap-timepicker.min.js"></script>
        <script>
            $(document).ready(function() {
                $('.notification-item').each(function() {
                    const startDate = $(this).data('start-date');
                    const endDate = $(this).data('end-date');
                    const startTime = $(this).data('start-time');
                    const endTime = $(this).data('end-time');

                    // Format the date
                    const formattedStartDate = new Date(startDate).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
                    const formattedEndDate = new Date(endDate).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });

                    // Format the time
                    const startTimeObj = new Date(`1970-01-01T${startTime}`);
                    const endTimeObj = new Date(`1970-01-01T${endTime}`);
                    const formattedTime = `${startTimeObj.toLocaleTimeString('en-US', { hour: 'numeric', minute: 'numeric', hour12: true })} until ${endTimeObj.toLocaleTimeString('en-US', { hour: 'numeric', minute: 'numeric', hour12: true })}`;

                    // Setting the formatted time and date to display in the frontend
                    $(this).find('.formatted-time').text(`Event schedule will start at ${formattedTime}`);
                    $(this).find('.formatted-date').text(`Event Date: ${formattedStartDate} - ${formattedEndDate}`);
                });

                $('.notification-item .icon #read').click(function() {
                    const notificationId = $(this).data('id'); // Get the notification ID

                    // AJAX url to send in the backend to process passing the notification ID as he parameter to query
                    $.ajax({
                        url: `/faculty/notifications/marked-read/${notificationId}`,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                        },
                        success: function(response) {
                            // Change icon to 'eye' and update styles
                            $(this).removeClass('fa-eye-slash').addClass('fa-eye');
                            $(this).closest('.notification-item').removeClass('bg-[#a2d9f7]').addClass('bg-white');
                        }.bind(this),
                        error: function(xhr) {
                            console.error(xhr.responseText);
                        }
                    });
                });
            });
        </script>
    @endsection
</x-app-layout>
