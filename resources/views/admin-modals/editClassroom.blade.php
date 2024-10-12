<!-- Subject edit modal -->
@foreach ($paginateRooms as $room)
    <div class="modal fade" id="classroomEdit-{{ $room->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="editModalLabel-{{ $room->id }}" aria-hidden="true">
        <!-- Modal content -->
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header text-center bg-[#223a5e]">
                    <h1 class="modal-title fs-5 text-center text-neutral-100" id="editModalLabel-{{ $room->id }}">Edit Subject</h1>
                </div>
                <div class="modal-body">
                    <form action="{{ route('admin.updateRoom', $room->id) }}" method="POST" name="classroomEditForm" id="classroom-edit-form-{{ $room->id }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <input type="text" class="form-control" name="roomName" id="classroom-input-{{ $room->id }}" value="{{ $room->roomName }}" required>
                        </div>
                        <div class="mb-3">
                            <input type="text" class="form-control" name="buildingNumber" id="building-input-{{ $room->id }}" value="{{ $room->buildingNumber }}">
                        </div>
                        <div class="mb-3">
                            <input type="text" class="form-control" name="floorNumber" id="floor-input-{{ $room->id }}" value="{{ $room->floorNumber }}">
                        </div>

                        <div class="modal-button flex items-center justify-end gap-2 mt-3">
                            <button type="button" class="border-[#223a5e] border-2 p-2 w-[120px] text-[#223a5e] rounded-lg" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="bg-[#223a5e] p-2 w-[120px] text-white rounded-lg">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endforeach
