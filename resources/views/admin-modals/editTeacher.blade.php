@foreach ($paginateLoads as $teacher)
    <div class="modal fade" id="editTeacher-{{ $teacher->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="editModalLabel-{{ $teacher->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content rounded-lg shadow-xl border-none">
                {{-- Modal header --}}
                <div class="modal-header bg-gradient-to-r from-[#223a5e] to-[#2c4b7b] text-white p-4 rounded-t-lg">
                    <h1 class="modal-title text-xl font-semibold" id="editModalLabel-{{ $teacher->id }}">Edit Load</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="filter: brightness(0) invert(1);"></button>
                </div>

                {{-- Modal body --}}
                <div class="modal-body p-6">
                    <form action="{{ route('admin.updateLoad', $teacher->id) }}" method="post" name="teachersForm" id="teachers-form" class="space-y-4">
                        @csrf
                        @method('put')

                        <div>
                            <label for="teacher-name-{{ $teacher->id }}" class="block mb-2 font-medium">Teacher's Name</label>
                            <input type="text"
                                name="teacherName"
                                id="teacher-name-{{ $teacher->id }}"
                                class="form-control w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#223a5e]"
                                value="{{ $teacher->teacherName }}"
                                placeholder="Enter teacher's full name"
                                required>
                        </div>

                        <div>
                            <label for="email-{{ $teacher->id }}" class="block mb-2 font-medium">Email Address</label>
                            <input type="email"
                                id="email-{{ $teacher->id }}"
                                name="email"
                                class="form-control w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#223a5e]"
                                value="{{ $teacher->email }}"
                                placeholder="Enter email address"
                                required>
                        </div>

                        <div>
                            <label for="contact-{{ $teacher->id }}" class="block mb-2 font-medium">Contact Number</label>
                            <input type="tel"
                                id="contact-{{ $teacher->id }}"
                                name="contact"
                                class="form-control w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#223a5e]"
                                value="{{ $teacher->contact }}"
                                placeholder="Enter contact number"
                                required>
                        </div>

                        <div>
                            <label for="number-hours-{{ $teacher->id }}" class="block mb-2 font-medium">Total Load Hours</label>
                            <input type="text"
                                name="numberHours"
                                id="number-hours-{{ $teacher->id }}"
                                class="form-control w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#223a5e]"
                                value="{{ $teacher->numberHours }}"
                                placeholder="Enter total load hours"
                                readonly>
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
                                Update Load
                            </button>
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
