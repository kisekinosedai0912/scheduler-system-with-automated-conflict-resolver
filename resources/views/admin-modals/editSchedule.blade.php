{{-- Edit schedule modal  --}}
@foreach ($schedules as $schedule)
    <div class="modal fade" id="editScheduleModal-{{ $schedule->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="editModal-{{ $schedule->id }}" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header text-center bg-[#223a5e]">
                    <h1 class="modal-title fs-5 text-center text-neutral-100" id="editScheduleModal-{{ $schedule->id }}">Edit Schedule</h1>
                </div>
                <div class="modal-body">
                    {{-- Modal form --}}
                    <form action="{{ route('admin.updateSchedule', $schedule->id) }}" method="POST" name="schedulesEditForm" id="schedules-edit-form-{{ $schedule->id }}" class="inputs grid grid-cols-2 gap-4">
                        @csrf
                        @method('PUT')

                        {{-- Input controls --}}
                        <input type="text" name="teacherName" id="teacher-name-{{ $schedule->id }}" class="form-control col-span-2 w-full p-2 rounded-xl" value="{{ $schedule->teacherName }}">
                        <input type="text" name="subject" id="subject-{{ $schedule->id }}" class="form-control w-full p-2 rounded-xl" value="{{ $schedule->subject }}">
                        <input type="text" name="studentNum" id="student-number-{{ $schedule->id }}" class="form-control w-full p-2 rounded-xl" value="{{ $schedule->studentNum }}">
                        <input type="text" name="yearSection" id="year-section-{{ $schedule->id }}" class="form-control w-full p-2 rounded-xl" value="{{ $schedule->yearSection }}">
                        <input type="text" name="room" id="room-{{ $schedule->id }}" class="form-control w-full p-2 rounded-xl" value="{{ $schedule->room }}">
                        
                        <div class="col-span-2 grid grid-cols-2 gap-4">
                            <input type="text" name="startTime" id="start-time-{{ $schedule->id }}" class="form-control w-full p-2 rounded-xl timepicker" value="{{ $schedule->startTime }}">
                            <input type="text" name="endTime" id="end-time-{{ $schedule->id }}" class="form-control w-full p-2 rounded-xl timepicker" value="{{ $schedule->endTime }}">
                        </div>                            

                        {{-- Buttons --}}
                        <div class="flex justify-end gap-2 col-span-2">
                            <button type="button" class="border-[#223a5e] border-2 p-2 w-[120px] text-[#223a5e] rounded-lg" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="bg-[#223a5e] p-2 w-[120px] text-white rounded-lg">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endforeach
