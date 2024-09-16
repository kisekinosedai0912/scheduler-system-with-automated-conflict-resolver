<x-app-layout>
    @section('title', 'Scheduler System with Automated Nursery System')
    @section('title-pane', 'Manage Subjects')

    <div class="outer-container flex flex-col md:flex-row items-center justify-between bg-white px-2 rounded-sm">
        <div class="flex items-center relative md:w-3/12 my-2">
            <svg class="absolute left-4 w-4 h-4 text-gray-500" aria-hidden="true" viewBox="0 0 24 24">
            <g><path d="M21.53 20.47l-3.66-3.66C19.195 15.24 20 13.214 20 11c0-4.97-4.03-9-9-9s-9 4.03-9 9 4.03 9 9 9c2.215 0 4.24-.804 5.808-2.13l3.66 3.66c.147.146.34.22.53.22s.385-.073.53-.22c.295-.293.295-.767.002-1.06zM3.5 11c0-4.135 3.365-7.5 7.5-7.5s7.5 3.365 7.5 7.5-3.365 7.5-7.5 7.5-7.5-3.365-7.5-7.5z"></path></g>
            </svg>
            <input type="search" placeholder="search subject" class="w-full h-10 pl-10 pr-4 px-1.5 rounded-sm text-gray-900 bg-white focus:outline-none focus:bg-[#223a5e] transition duration-300">
        </div>

        <div class="buttons flex items-center justify-end gap-2 w-80">
            {{-- Print button --}}
            <button class="button bg-gradient-to-r from-[#d3d3d3] to-[#c0c0c0] text-gray-800 border border-transparent rounded-full flex items-center gap-1.5 px-2 py-2 shadow-custom transition-transform duration-300 hover:border-[#a9a9a9] active:transform active:scale-95 active:shadow-custom-active">
                <svg stroke-linejoin="round" stroke-linecap="round" fill="none" stroke="currentColor" stroke-width="1.5"
                  viewBox="0 0 24 24"
                  height="40"
                  width="40"
                  class="w-6 h-6"
                  xmlns="http://www.w3.org/2000/svg">
                  <path fill="none" d="M0 0h24v24H0z" stroke="none"></path>
                  <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2"></path>
                  <path d="M7 11l5 5l5 -5"></path>
                  <path d="M12 4l0 12"></path>
                </svg>
            </button>
              
            {{-- Add button with modal trigger --}}
            <button class="group cursor-pointer outline-none hover:rotate-90 duration-300" title="Add New" data-bs-toggle="modal" data-bs-target="#staticBackdrop">
                <svg class="stroke-blue-950 fill-none group-hover:fill-blue-100 group-active:stroke-blue-900 group-active:fill-blue-950 group-active:duration-0 duration-300" viewBox="0 0 24 24"
                    height="50px" width="50px" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-width="1" d="M12 22C17.5 22 22 17.5 22 12C22 6.5 17.5 2 12 2C6.5 2 2 6.5 2 12C2 17.5 6.5 22 12 22Z"></path>
                    <path stroke-width="1" d="M8 12H16"></path>
                    <path stroke-width="1" d="M12 16V8"></path>
                </svg>
            </button>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    {{-- Modal header --}}
                    <div class="modal-header text-center bg-[#223a5e]">
                        <h1 class="modal-title fs-5 text-center text-neutral-100" id="staticBackdropLabel">Add New Subject</h1>
                    </div>
                    {{-- Modal body --}}
                    <div class="modal-body">
						<div class="inputs">
                            <form action="" method="post" name="subjectsForm" id="subjects-form">
                                @csrf
                                @method('post')

                                <div class="mb-3">
                                  <input type="text" class="form-control" id="subjectInput" placeholder="Subject: ">
                                </div>
                                <div class="mb-3">
                                    <textarea name="description" id="description" placeholder="Description.." class="form-control col-span-2 w-full mt-4 pl-2 rounded-md bg-stone-200 scroll-py-1.5"></textarea>
                                </div>
                                
                                {{-- Modal buttons --}}
                                <div class="modal-button flex items-center justify-end gap-2 mt-3">
                                    <button type="button" class="border-[#223a5e] border-2 p-2 w-[120px] text-[#223a5e] rounded-lg" data-bs-dismiss="modal">Cancel</button>
                                    <button type="button" class="bg-[#223a5e] p-2 w-[120px] text-white rounded-lg">Save</button>
                                </div>
                            </form>
						</div>
					</div>
                </div>
            </div>
        </div>
    </div>

    <hr class="my-2">
    {{-- Subject Table --}}
    <span class="hidden md:block">
        <table class="table shadow=sm">
            <thead>
                <tr>
                <th scope="col">Subject Name</th>
                <th scope="col">Subject Description</th>
                <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                <td scope="row">Event Driven Programming</td>
                <td>Programming lesson focused on the events occuring in the system</td>
                <td>
                    <a href="" class="btn btn-success bg-transparent text-green-600 text-xl mr-2 hover:border-green-200 hover:text-green-900"><i class="fas fa-gear"></i></a>
                    <a href="" class="btn btn-danger bg-transparent text-red-600 text-xl hover:border-red-200 hover:text-red-700"><i class="fas fa-trash"></i></a>
                </td>
                </tr>
            </tbody>
        </table>
    </span>

    {{-- Subject table for mobile --}}
    <span class="block md:hidden">
        <table class="table shadow-sm">
            <thead>
                <tr>
                <th scope="col">Subject Name</th>
                <th scope="col">Subject Description</th>
                <th scope="col"></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                <td scope="row">Event Driven Programming</td>
                <td>Programming lesson focused on the events occuring in the system</td>
                <td>
                    <div class="dropdown">
                        <button type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-ellipsis-h"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="">Edit</a></li>
                            <li><a class="dropdown-item" href="">Delete</a></li>
                        </ul>
                    </div>
                </td>
                </tr>
            </tbody>
        </table>
    </span>
</x-app-layout>