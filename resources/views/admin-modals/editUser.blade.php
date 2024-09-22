<div class="modal fade" id="edit-user-{{ $user->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="editUserLabel-{{ $user->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header text-center bg-[#223a5e]">
                <h1 class="modal-title fs-5 text-center text-neutral-100" id="editUserLabel-{{ $user->id }}">Edit User Credentials</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="filter: brightness(0) invert(1);"></button>
            </div>
            <div class="modal-body">
                <form method="post" action="{{ route('admin.update_user', $user->id) }}" id="user-form">
                    @csrf
                    @method('patch')

                    <!-- Name -->
                    <div>
                        <x-input-label for="name-{{ $user->id }}" :value="__('Name')" />
                        <x-text-input id="name-{{ $user->id }}" class="block mt-1 w-full" type="text" name="name" value="{{ $user->name }}" required autocomplete="name" />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <!-- User Role -->
                    <div class="mt-4">
                        <x-input-label for="roleSelect-{{ $user->id }}" :value="__('Select Role')" />
                        <select name="user_role" id="roleSelect-{{ $user->id }}" class="form-select rounded-md" aria-label="Role Select">
                            <option value="faculty" {{ $user->user_role == 'faculty' ? 'selected' : '' }}>Faculty</option>
                            <option value="admin" {{ $user->user_role == 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <x-primary-button class="ms-4">
                            {{ __('Update') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
