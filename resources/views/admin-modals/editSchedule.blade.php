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
                    <form action="{{ route('admin.updateSchedule', $schedule->id) }}" method="POST" name="schedulesEditForm" id="schedules-edit-form-{{ $schedule->id }}" class="grid grid-cols-1 gap-4">
                        @csrf
                        @method('PUT')

                        <!-- Full-width dropdowns -->
                        <select name="teacher_id" id="teacher_id-{{ $schedule->id }}" class="form-control col-span-1">
                            <option value="">Select Teacher</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}" {{ $teacher->id == $schedule->teacher_id ? 'selected' : '' }}>
                                    {{ $teacher->teacherName }}
                                </option>
                            @endforeach
                        </select>

                        <select id="edit-category-select-{{ $schedule->id }}" name="categoryName" class="form-control col-span-1">
                            <option value="">Select Category</option>
                            @foreach($subjects->unique('category') as $subject)
                                <option value="{{ $subject->category }}" {{ $subject->category == $schedule->categoryName ? 'selected' : '' }}>
                                    {{ $subject->category }}
                                </option>
                            @endforeach
                        </select>

                        <select id="edit-days-{{ $schedule->id }}" name="days[]" class="form-control col-span-1" multiple>
                            @php
                                $selectedDays = explode('-', $schedule->days);
                            @endphp
                            <option value="M" {{ in_array('M', $selectedDays) ? 'selected' : '' }}>Monday</option>
                            <option value="T" {{ in_array('T', $selectedDays) ? 'selected' : '' }}>Tuesday</option>
                            <option value="W" {{ in_array('W', $selectedDays) ? 'selected' : '' }}>Wednesday</option>
                            <option value="TH" {{ in_array('TH', $selectedDays) ? 'selected' : '' }}>Thursday</option>
                            <option value="F" {{ in_array('F', $selectedDays) ? 'selected' : '' }}>Friday</option>
                        </select>

                        <!-- Two-column grid for the rest -->
                        <div class="grid grid-cols-2 gap-4 col-span-1">
                            <select name="subject_id" id="subject_id-{{ $schedule->id }}" class="form-control">
                                <option value="">Select Subject</option>
                                @foreach($subjects->where('category', $schedule->categoryName) as $subject)
                                    <option value="{{ $subject->id }}" {{ $subject->id == $schedule->subject_id ? 'selected' : '' }}>
                                        {{ $subject->subjectName }}
                                    </option>
                                @endforeach
                            </select>

                            <select name="room_id" id="room-{{ $schedule->id }}" class="form-control">
                                <option value="">Select Room</option>
                                @foreach($classrooms as $classroom)
                                    <option value="{{ $classroom->id }}" {{ $classroom->id == $schedule->room_id ? 'selected' : '' }}>
                                        {{ $classroom->roomName }}
                                    </option>
                                @endforeach
                            </select>

                            <input type="text" name="studentNum" id="student-number-{{ $schedule->id }}" class="form-control w-full p-2 rounded-md" value="{{ $schedule->studentNum }}" placeholder="Student No.">
                            <input type="text" name="yearSection" id="year-section-{{ $schedule->id }}" class="form-control w-full p-2 rounded-md" value="{{ $schedule->yearSection }}" placeholder="Year & Section">

                            <div class="col-span-2 grid grid-cols-2 gap-4">
                                <input type="text" name="startTime" id="start-time-{{ $schedule->id }}" class="form-control w-full p-2 rounded-md timepicker" value="{{ $schedule->startTime }}" placeholder="Start Time (e.g. 02:30 PM)">
                                <input type="text" name="endTime" id="end-time-{{ $schedule->id }}" class="form-control w-full p-2 rounded-md timepicker" value="{{ $schedule->endTime }}" placeholder="End Time (e.g. 03:30 PM)">
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="flex justify-end gap-2 col-span-1">
                            <button type="button" class="border-[#223a5e] border-2 p-2 w-[120px] text-[#223a5e] rounded-lg" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="bg-[#223a5e] p-2 w-[120px] text-white rounded-lg">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endforeach
