<div class="modal fade" id="edit-user-{{ $user->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="editUser Label-{{ $user->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-lg shadow-xl border-none">
            <div class="modal-header bg-gradient-to-r from-[#223a5e] to-[#2c4b7b] text-white p-4 rounded-t-lg">
                <h1 class="modal-title text-xl font-semibold" id="editUser Label-{{ $user->id }}">Edit User Credentials</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="filter: brightness(0) invert(1);"></button>
            </div>
            <div class="modal-body p-6">
                <form method="post" action="{{ route('admin.update_user', $user->id) }}" id="user-form" class="space-y-4">
                    @csrf
                    @method('patch')

                    <!-- Name -->
                    <div>
                        <label for="name-{{ $user->id }}" class="block mb-2 font-medium">Name</label>
                        <input id="name-{{ $user->id }}"
                            class="form-control w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#223a5e]"
                            type="text"
                            name="name"
                            value="{{ $user->name }}"
                            required
                            autocomplete="name" />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <!-- User Role -->
                    <div>
                        <label for="roleSelect-{{ $user->id }}" class="block mb-2 font-medium">Select Role</label>
                        <select name="user_role" id="roleSelect-{{ $user->id }}" class="form-select w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#223a5e]" aria-label="Role Select">
                            <option value="faculty" {{ $user->user_role == 'faculty' ? 'selected' : '' }}>Faculty</option>
                            <option value="admin" {{ $user->user_role == 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                    </div>

                    <div class="flex items-center justify-end mt-6">
                        <x-primary-button class="ms-4">
                            {{ __('Update') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
