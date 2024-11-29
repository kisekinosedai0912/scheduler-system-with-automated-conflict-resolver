<!-- Subject edit modal -->
@foreach ($paginateRooms as $room)
    <div class="modal fade" id="classroomEdit-{{ $room->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="editModalLabel-{{ $room->id }}" aria-hidden="true">
        <!-- Modal content -->
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content rounded-lg shadow-xl border-none">
                <div class="modal-header bg-gradient-to-r from-[#223a5e] to-[#2c4b7b] text-white p-4 rounded-t-lg">
                    <h1 class="modal-title text-xl font-semibold" id="editModalLabel-{{ $room->id }}">Edit Classroom</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="filter: brightness(0) invert(1);"></button>
                </div>
                <div class="modal-body p-6">
                    <form action="{{ route('admin.updateRoom', $room->id) }}" method="POST" name="classroomEditForm" id="classroom-edit-form-{{ $room->id }}" class="space-y-4">
                        @csrf
                        @method('PUT')

                        <div>
                            <label for="classroom-input-{{ $room->id }}" class="block mb-2 font-medium">Classroom/Laboratory</label>
                            <input type="text"
                                class="form-control w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#223a5e]"
                                name="roomName"
                                id="classroom-input-{{ $room->id }}"
                                value="{{ $room->roomName }}"
                                placeholder="Enter Classroom/Laboratory"
                                required>
                        </div>

                        <div>
                            <label for="building-input-{{ $room->id }}" class="block mb-2 font-medium">Building #</label>
                            <input type="text"
                                class="form-control w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#223a5e]"
                                name="buildingNumber"
                                id="building-input-{{ $room->id }}"
                                value="{{ $room->buildingNumber }}"
                                placeholder="Enter Building #">
                        </div>

                        <div>
                            <label for="floor-input-{{ $room->id }}" class="block mb-2 font-medium">Floor #</label>
                            <input type="text"
                                class="form-control w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#223a5e]"
                                name="floorNumber"
                                id="floor-input-{{ $room->id }}"
                                value="{{ $room->floorNumber }}"
                                placeholder="Enter Floor #">
                        </div>

                        <div class="flex justify-end gap-4 mt-6">
                            <button type="button"
                                class="border-[#223a5e] border-2 p-2 w-[120px] text-[#223a5e] rounded-lg transition duration-300 hover:bg-[#223a5e] hover:text-white"
                                data-bs-dismiss="modal">
                                Cancel
                            </button>
                            <button type="submit"
                                class="bg-[#223a5e] p-2 w-[120px] text-white rounded-lg transition duration-300 hover:bg-[#2c4b7b]">
                                Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endforeach
