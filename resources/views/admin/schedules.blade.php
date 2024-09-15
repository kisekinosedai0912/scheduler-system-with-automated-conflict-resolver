<x-app-layout>
    @section('title', 'Scheduler System with Automated Nursery System')
    @section('title-pane', 'Manage Schedules')


    <div class="outer-container flex flex-col md:flex-row items-center justify-between">
        <div class="flex items-center relative md:w-3/12 my-2">
            <svg class="absolute left-4 w-4 h-4 text-gray-500" aria-hidden="true" viewBox="0 0 24 24">
            <g><path d="M21.53 20.47l-3.66-3.66C19.195 15.24 20 13.214 20 11c0-4.97-4.03-9-9-9s-9 4.03-9 9 4.03 9 9 9c2.215 0 4.24-.804 5.808-2.13l3.66 3.66c.147.146.34.22.53.22s.385-.073.53-.22c.295-.293.295-.767.002-1.06zM3.5 11c0-4.135 3.365-7.5 7.5-7.5s7.5 3.365 7.5 7.5-3.365 7.5-7.5 7.5-7.5-3.365-7.5-7.5z"></path></g>
            </svg>
            <input type="search" placeholder="search schedule" class="w-full h-10 pl-10 pr-4 px-1.5 text-gray-900 bg-white focus:outline-none focus:bg-[#223a5e] transition duration-300">
        </div>

        <div class="buttons flex items-center justify-evenly w-80 mt-2">
            <div class="buttons flex items-center justify-end gap-2 w-80 mt-2">
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
        </div>

        <!-- Modal -->
        <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header text-center bg-[#223a5e]">
                        <h1 class="modal-title fs-5 text-center text-neutral-100" id="staticBackdropLabel">Create New Schedule</h1>
                    </div>
                    <div class="modal-body">
                        {{-- Modal form --}}
                        <form action="" method="post" name="schedulesForm" id="schedules-form" class="inputs grid grid-cols-2 gap-4">
                            @csrf
                            @method('post')

                            {{-- Input controls --}}
                            <input type="text" name="teacherName" id="teacher-name" class="form-control col-span-2 w-full p-2 rounded-xl" placeholder="Teacher Name: ">
                            <input type="text" name="subject" id="subject" class="form-control w-full p-2 rounded-xl" placeholder="Subject: ">
                            <input type="text" name="studentNum" id="student-number" class="form-control w-full p-2 rounded-xl" placeholder="Student No.: ">
                            <input type="text" name="yearSection" id="year-section" class="form-control w-full p-2 rounded-xl" placeholder="Year & Section: ">
                            <input type="text" name="room" id="room" class="form-control w-full p-2 rounded-xl" placeholder="Room: ">
                            
                            <div class="col-span-2 grid grid-cols-2 gap-4">
                                <input type="date" name="date" id="date" class="form-control w-full p-2 rounded-xl">
                                <input type="time" name="time" id="time" class="form-control w-full p-2 rounded-xl">
                            </div>

                             {{-- Buttons --}}
                            <div class="flex justify-end gap-2 col-span-2">
                                <button type="button" class="border-[#223a5e] border-2 p-2 w-[120px] text-[#223a5e] rounded-lg" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="bg-[#223a5e] p-2 w-[120px] text-white rounded-lg" data-bs-dismiss="modal">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Data table delete lng comment ko lng kay basi gusto mo liwaton ang table--}}
    <div class="datatable w-full mx-auto md:mt-4">
        <table id="schedulesTable" class="table table-bordered table-sm mt-4">
            <thead>
                <tr>
                    <th>Teacher</th>
                    <th>Subject</th>
                    <th>Student Number</th>
                    <th>Year/Section</th>
                    <th>Room</th>
                    <th>Date</th>
                    <th>Time</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>John Doe</td>
                    <td>Science</td>
                    <td>12345</td>
                    <td>Grade 10 - A</td>
                    <td>Room 101</td>
                    <td>2023-09-04</td>
                    <td>10:00 AM</td>
                </tr>
                <tr>
                    <td>John Cena</td>
                    <td>Programming</td>
                    <td>75164</td>
                    <td>Grade 10 - A</td>
                    <td>Room 102</td>
                    <td>2023-09-04</td>
                    <td>10:00 AM</td>
                </tr>
                <tr>
                    <td>Coles John</td>
                    <td>Programming</td>
                    <td>75164</td>
                    <td>Grade 10 - A</td>
                    <td>Room 102</td>
                    <td>2023-09-04</td>
                    <td>10:00 AM</td>
                </tr>
                <tr>
                    <td>Ishida Uryu</td>
                    <td>Science</td>
                    <td>12345</td>
                    <td>Grade 10 - A</td>
                    <td>Room 101</td>
                    <td>2023-09-04</td>
                    <td>10:00 AM</td>
                </tr>
                <tr>
                    <td>John Ish</td>
                    <td>Entrepreneurship</td>
                    <td>75164</td>
                    <td>Grade 10 - A</td>
                    <td>Room 102</td>
                    <td>2023-09-04</td>
                    <td>10:00 AM</td>
                </tr>
                <tr>
                    <td>Hay Maker</td>
                    <td>Programming</td>
                    <td>75164</td>
                    <td>Grade 10 - A</td>
                    <td>Room 102</td>
                    <td>2023-09-04</td>
                    <td>10:00 AM</td>
                </tr>
            </tbody>
        </table>
    </div>

    @section('scripts')
        <!-- jQuery cdn link-->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <!-- DataTables JS library script -->
        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
        <script>
            $(document).ready(function() {
                $('#schedulesTable').DataTable();
            });
        </script>
    @endsection
</x-app-layout>


