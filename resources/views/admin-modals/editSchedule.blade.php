{{-- Edit schedule modal  --}}
@foreach ($schedules as $schedule)
    <div class="modal fade" id="editScheduleModal-{{ $schedule->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="editModal-{{ $schedule->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content rounded-lg shadow-xl border-none">
                <div class="modal-header bg-gradient-to-r from-[#223a5e] to-[#2c4b7b] text-white p-4 rounded-t-lg">
                    <h1 class="modal-title text-xl font-semibold" id="editScheduleModal-{{ $schedule->id }}">Edit Schedule</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="filter: brightness(0) invert(1);"></button>
                </div>
                <div class="modal-body p-6">
                    {{-- Modal form --}}
                    <form action="{{ route('admin.updateSchedule', $schedule->id) }}" method="POST" name="schedulesEditForm" id="schedules-edit-form-{{ $schedule->id }}" class="space-y-4">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-2 gap-4">
                            <!-- Teacher Dropdown -->
                            <div>
                                <label for="teacher_id-{{ $schedule->id }}" class="block mb-2 font-medium">Select Teacher</label>
                                <select name="teacher_id" id="teacher_id-{{ $schedule->id }}" class="form-control w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#223a5e]">
                                    <option value="">Select Teacher</option>
                                    @foreach($teachers as $teacher)
                                        <option value="{{ $teacher->id }}" {{ $teacher->id == $schedule->teacher_id ? 'selected' : '' }}>
                                            {{ $teacher->teacherName }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Semester Dropdown -->
                            <div>
                                <label for="edit-semester-select-{{ $schedule->id }}" class="block mb-2 font-medium">Semester</label>
                                <select id="edit-semester-select-{{ $schedule->id }}" name="semester" class="form-control w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#223a5e]">
                                    <option value="">Select Semester</option>
                                    @foreach($subjects->unique('semester') as $subject)
                                        <option value="{{ $subject->semester }}" {{ $subject->semester == $schedule->semester ? 'selected' : '' }}>
                                            {{ $subject->semester }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Strand Dropdown -->
                            <div>
                                <label for="edit-strand-select-{{ $schedule->id }}" class="block mb-2 font-medium">Strand</label>
                                <select id="edit-strand-select-{{ $schedule->id }}" name="strand" class="form-control w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#223a5e]">
                                    <option value="">Select strand</option>
                                    @foreach($schedules->unique('strand') as $schedule)
                                        <option value="{{ $schedule->strand }}" {{ $schedule->strand == $schedule->strand ? 'selected' : '' }}>
                                            {{ $schedule->strand }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Category Dropdown -->
                            <div>
                                <label for="edit-category-select-{{ $schedule->id }}" class="block mb-2 font-medium">Category</label>
                                <select id="edit-category-select-{{ $schedule->id }}" name="categoryName" class="form-control w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#223a5e]">
                                    <option value="">Select Category</option>
                                    @foreach($subjects->unique('category') as $subject)
                                        <option value="{{ $subject->category }}" {{ $subject->category == $schedule->categoryName ? 'selected' : '' }}>
                                            {{ $subject->category }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Days Multiselect -->
                            <div>
                                <label for="edit-days-{{ $schedule->id }}" class="block mb-2 font-medium">Days</label>
                                <select id="edit-days-{{ $schedule->id }}" name="days[]" class="form-control w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#223a5e]" multiple>
                                    @php
                                        $selectedDays = explode('-', $schedule->days);
                                    @endphp
                                    <option value="M" {{ in_array('M', $selectedDays) ? 'selected' : '' }}>Monday</option>
                                    <option value="T" {{ in_array('T', $selectedDays) ? 'selected' : '' }}>Tuesday</option>
                                    <option value="W" {{ in_array('W', $selectedDays) ? 'selected' : '' }}>Wednesday</option>
                                    <option value="TH" {{ in_array('TH', $selectedDays) ? 'selected' : '' }}>Thursday</option>
                                    <option value="F" {{ in_array('F', $selectedDays) ? 'selected' : '' }}>Friday</option>
                                </select>
                            </div>

                            <!-- Subject Dropdown -->
                            <div>
                                <label for="subject_id-{{ $schedule->id }}" class="block mb-2 font-medium">Subject</label>
                                <select name="subject_id" id="subject_id-{{ $schedule->id }}" class="form-control w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#223a5e]">
                                    <option value="">Select Subject</option>
                                    @foreach($subjects->where('category', $schedule->categoryName) as $subject)
                                        <option value="{{ $subject->id }}" {{ $subject->id == $schedule->subject_id ? 'selected' : '' }}>
                                            {{ $subject->subjectName }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Room Dropdown -->
                            <div>
                                <label for="room-{{ $schedule->id }}" class="block mb-2 font-medium">Room</label>
                                <select name="room_id" id="room-{{ $schedule->id }}" class="form-control w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#223a5e]">
                                    <option value="">Select Room</option>
                                    @foreach($classrooms as $classroom)
                                        <option value="{{ $classroom->id }}" {{ $classroom->id == $schedule->room_id ? 'selected' : '' }}>
                                            {{ $classroom->roomName }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Year Dropdown -->
                            <div>
                                <label for="edit-year" class="block mb-2 font-medium">Year</label>
                                <select name="year" id="edit-year" class="form-control w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#223a5e]">
                                    <option value="">Select Year</option>
                                    @foreach($schedules->unique('year') as $yearOption)
                                        <option value="{{ $yearOption->year }}" {{ $yearOption->year == $schedule->year ? 'selected' : '' }}>
                                            {{ $yearOption->year }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Section Input -->
                            <div>
                                <label for="edit-section" class="block mb-2 font-medium">Section</label>
                                <input type="text" name="section" id="edit-section" value="{{ $schedule->section }}" class="form-control w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#223a5e]">
                            </div>

                            <!-- Time Inputs -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="start-time-{{ $schedule->id }}" class="block mb-2 font-medium">Start Time</label>
                                    <input type="text" name="startTime" id="start-time-{{ $schedule->id }}"
                                        class="form-control w-full p-2 rounded-lg timepicker focus:outline-none focus:ring-2 focus:ring-[#223a5e]"
                                        value="{{ $schedule->startTime }}"
                                        placeholder="Start Time (e.g. 02:30 PM)">
                                </div>
                                <div>
                                    <label for="end-time-{{ $schedule->id }}" class="block mb-2 font-medium">End Time</label>
                                    <input type="text" name="endTime" id="end-time-{{ $schedule->id }}"
                                        class="form-control w-full p-2 rounded-lg timepicker focus:outline-none focus:ring-2 focus:ring-[#223a5e]"
                                        value="{{ $schedule->endTime }}"
                                        placeholder="End Time (e.g. 03:30 PM)">
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
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
