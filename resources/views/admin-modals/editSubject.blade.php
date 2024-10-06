<!-- Subject edit modal -->
@foreach ($paginateSubjects as $subject)
    <div class="modal fade" id="editModal-{{ $subject->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="editModalLabel-{{ $subject->id }}" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                {{-- Modal header --}}
                <div class="modal-header text-center bg-[#223a5e]">
                    <h1 class="modal-title fs-5 text-center text-neutral-100" id="editModalLabel-{{ $subject->id }}">Edit Subject</h1>
                </div>
                {{-- Modal body --}}
                <div class="modal-body">
                    <div class="inputs">
                        <form action="{{ route('admin.updateSubject', $subject->id) }}" method="post" name="subjectsEditForm" id="subjects-edit-form-{{ $subject->id }}">
                            @csrf
                            @method('put')

                            <div class="mb-3">
                                <select name="category" id="category" class="form-control col-span-2 w-full p-2 rounded-md">
                                    <option disabled selected value="">Subject Category</option>
                                    <option value="Grade-11 Subjects">Grade-11 Subjects</option>
                                    <option value="Grade-12 Subjects">Grade-12 Subjects</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <input type="text" class="form-control" name="subjectName" id="subject-name-{{ $subject->id }}" value="{{ $subject->subjectName }}" required>
                            </div>
                            <div class="mb-3">
                                <textarea class="form-control col-span-2 w-full mt-4 pl-2 rounded-md bg-stone-200 scroll-py-1.5" name="description" id="description-{{ $subject->id }}">{{ $subject->description }}</textarea>
                            </div>
                            
                            {{-- Modal buttons --}}
                            <div class="modal-button flex items-center justify-end gap-2 mt-3">
                                <button type="button" class="border-[#223a5e] border-2 p-2 w-[120px] text-[#223a5e] rounded-lg" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="bg-[#223a5e] p-2 w-[120px] text-white rounded-lg">Update</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endforeach
