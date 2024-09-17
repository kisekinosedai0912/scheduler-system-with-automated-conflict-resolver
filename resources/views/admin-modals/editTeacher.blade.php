@foreach ($teachers as $teacher)
    <div class="modal fade" id="editTeacher-{{ $teacher->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="editModalLabel-{{ $teacher->id }}" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                {{-- Modal header --}}
                <div class="modal-header text-center bg-[#223a5e]">
                    <h1 class="modal-title fs-5 text-center text-neutral-100" id="editModalLabel-{{ $teacher->id }}">Edit Load</h1>
                </div>
                {{-- Modal body --}}
                <div class="modal-body">
                    <form action="{{ route('admin.updateLoad', $teacher->id) }}" method="post" name="teachersForm" id="teachers-form">
                        @csrf
                        @method('put')

                        <div class="mb-3">
                            <input type="text" name="teacherName" id="teacher-name-{{ $teacher->id }}" class="form-control col-span-2 w-full p-2 rounded-md" value="{{ $teacher->teacherName }}" required>
                        </div>

                        <div class="mb-3">
                            <select name="subjectName" id="subject-name" class="form-control col-span-2 w-full p-2 rounded-md" required>
                                <option value="">Subjects</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->subjectName }}" {{ $subject->subjectName == $teacher->subjectName ? 'selected' : '' }}>
                                        {{ $subject->subjectName }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <input type="text" name="numberHours" id="number-hours-{{ $teacher->id }}" class="form-control col-span-2 w-full p-2 rounded-md" value="{{ $teacher->numberHours }}" required>
                        </div>

                        {{-- Modal buttons --}}
                        <div class="modal-button flex items-center justify-end gap-2 mt-3">
                            <button type="button" class="border-[#223a5e] border-2 p-2 w-[120px] text-[#223a5e] rounded-lg" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="bg-[#223a5e] p-2 w-[120px] text-white rounded-lg">Update Load</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endforeach
