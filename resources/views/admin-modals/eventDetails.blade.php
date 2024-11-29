<!-- Modal for displaying event details -->
<div class="modal fade" id="event-details" tabindex="-1" aria-labelledby="eventDetailsLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-lg shadow-xl border-none">
            <div class="modal-header bg-gradient-to-r from-[#223a5e] to-[#2c4b7b] text-white p-4 rounded-t-lg flex items-center justify-between">
                <h1 class="modal-title text-xl font-semibold" id="eventDetailsLabel">Event Details</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="filter: brightness(0) invert(1);"></button>
            </div>
            <div class="modal-body p-6 space-y-4">
                <div class="bg-gray-50 p-4 rounded-lg shadow-sm">
                    <div class="flex items-center mb-2">
                        <span class="font-semibold mr-2 text-[#223a5e] w-24">Title:</span>
                        <span id="modal-event-title" class="text-gray-800"></span>
                    </div>
                    <div class="flex items-center mb-2">
                        <span class="font-semibold mr-2 text-[#223a5e] w-24">Start:</span>
                        <span id="modal-event-start" class="text-gray-800"></span>
                    </div>
                    <div class="flex items-center">
                        <span class="font-semibold mr-2 text-[#223a5e] w-24">End:</span>
                        <span id="modal-event-end" class="text-gray-800"></span>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-gray-100 p-4 rounded-b-lg">
                <button type="button" class="bg-[#223a5e] text-white px-4 py-2 rounded-lg hover:bg-[#2c4b7b] transition duration-300" data-bs-dismiss="modal">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>
