<!-- Subject edit modal -->
@foreach ($paginateSubjects as $subject)
    <div class="modal fade" id="editModal-{{ $subject->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="editModalLabel-{{ $subject->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content rounded-lg shadow-xl border-none">
                {{-- Modal header --}}
                <div class="modal-header bg-gradient-to-r from-[#223a5e] to-[#2c4b7b] text-white p-4 rounded-t-lg">
                    <h1 class="modal-title text-xl font-semibold" id="editModalLabel-{{ $subject->id }}">Edit Subject</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="filter: brightness(0) invert(1);"></button>
                </div>

                {{-- Modal body --}}
                <div class="modal-body p-6">
                    <div class="inputs">
                        <form action="{{ route('admin.updateSubject', $subject->id) }}" method="post" name="subjectsEditForm" id="subjects-edit-form-{{ $subject->id }}" class="space-y-4">
                            @csrf
                            @method('put')

                            <div>
                                <label for="edit-semester-select-{{ $subject->id }}" class="block mb-2 font-medium">Select Semester</label>
                                <select id="edit-semester-select-{{ $subject->id }}" name="semester"
                                    class="form-control w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#223a5e]">
                                    <option value="">Select Semester</option>
                                    @foreach($paginateSubjects->unique('semester') as $semesterSubject)
                                        <option value="{{ $semesterSubject->semester }}"
                                            {{ $semesterSubject->semester == $subject->semester ? 'selected' : '' }}>
                                            {{ $semesterSubject->semester }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="edit-category" class="block mb-2 font-medium">Select Subject Category</label>
                                <select name="category" id="edit-category"
                                    class="form-control w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#223a5e]">
                                    <option value="">Select Subject Category</option>
                                    @foreach ($paginateSubjects->unique('category') as $categorySubject)
                                        <option value="{{ $categorySubject->category }}"
                                            {{ $categorySubject->category == $subject->category ? 'selected' : '' }}>
                                            {{ $categorySubject->category }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="subject-name-{{ $subject->id }}" class="block mb-2 font-medium">Subject Name</label>
                                <input type="text"
                                    class="form-control w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#223a5e]"
                                    name="subjectName"
                                    id="subject-name-{{ $subject->id }}"
                                    value="{{ $subject->subjectName }}"
                                    placeholder="Enter Subject Name"
                                    required>
                            </div>

                            <div>
                                <label for="description-{{ $subject->id }}" class="block mb-2 font-medium">Description</label>
                                <textarea
                                    class="form-control w-full p-2 border rounded-lg min-h-[100px] focus:outline-none focus:ring-2 focus:ring-[#223a5e]"
                                    name="description"
                                    id="description-{{ $subject->id }}"
                                    placeholder="Enter subject description...">{{ $subject->description }}</textarea>
                            </div>

                            {{-- Modal buttons --}}
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
    </div>
@endforeach
