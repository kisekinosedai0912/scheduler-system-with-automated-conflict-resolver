@foreach ($paginateLoads as $teacher)
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

                        {{-- Display Current Teacher Name --}}
                        <div class="mb-3">
                            <input type="text" name="teacherName" id="teacher-name-{{ $teacher->id }}" class="form-control col-span-2 w-full p-2 rounded-md" value="{{ $teacher->teacherName }}" required>
                        </div>

                        {{-- Display Currentt Category --}}
                        <div class="mb-3">
                            <select id="category-select-{{ $teacher->id }}" name="categoryName" class="form-control">
                                <option value="">Select Category</option>
                                @foreach($subjects->unique('category') as $subject)
                                    <option value="{{ $subject->category }}" {{ $subject->category == $teacher->category ? 'selected' : '' }}>
                                        {{ $subject->category }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Pre-select Subject --}}
                        <div class="mb-3">
                            <select name="subjectName" id="subject-name-{{ $teacher->id }}" class="form-control col-span-2 w-full p-2 rounded-md" required>
                                <option value="">Subjects</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->subjectName }}" {{ $subject->subjectName == $teacher->subjectName ? 'selected' : '' }}>
                                        {{ $subject->subjectName }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Display Current Number of Hours --}}
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

<script>
   document.addEventListener('DOMContentLoaded', function () {
        const editTeacherModals = document.querySelectorAll('.modal');

        editTeacherModals.forEach((modal) => {
            const modalId = modal.id.split('-')[1];
            const categorySelect = modal.querySelector(`#category-select-${modalId}`);
            const subjectSelect = modal.querySelector(`#subject-name-${modalId}`);

            if (categorySelect && subjectSelect) {
                categorySelect.addEventListener('change', (event) => {
                    const selectedCategory = encodeURIComponent(event.target.value);
                    subjectSelect.innerHTML = '<option value="">Fetching subjects..</option>';

                    if (selectedCategory) {
                        fetch(`${window.location.origin}/api/subjects/by_category/${selectedCategory}`)
                        .then(response => response.json())
                        .then(data => {
                            subjectSelect.innerHTML = '<option value="">Select Subject</option>';
                            data.forEach(subject => {
                                const option = document.createElement('option');
                                option.value = subject.subjectName;
                                option.textContent = subject.subjectName;

                                if (subject.subjectName === subjectSelect.getAttribute('data-current-subject')) {
                                    option.selected = true;
                                }
                                subjectSelect.appendChild(option);
                            });
                        })
                        .catch((error) => console.error('Error fetching subjects:', error));
                    }
                });

                $(modal).on('show.bs.modal', function () {
                    categorySelect.dispatchEvent(new Event('change'));
                });
            }
        });
    });

</script>