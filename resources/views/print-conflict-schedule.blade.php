<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Conflicted Schedules</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .container {
            margin: 0 auto;
            border: 1px solid #000;
        }
        table {
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #000;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        td {
            font-size: 14px;
        }
        .signatures div {
            text-align: center;
            width: 45%;
        }
        @media print {
            body {
                margin: 0;
            }
            .container {
                border: none;
            }
            button {
                display: none;
            }
        }
    </style>
</head>
<body class="m-0 p-[20px] w-screen">
    <div class="container max-w-[800px] p-[20px]">
        <h1 class="text-2xl font-semibold text-center">Conflicted Schedules Report</h1>

        <div class="table-container w-full mt-[20px]">
            <h2 class="mb-2 font-semibold">User: {{ Auth::user()->name }} (Role: {{ Auth::user()->user_role }})</h2>

            <table class="w-full">
                <thead>
                    <tr>
                        <th>Category</th>
                        <th>Subject</th>
                        <th>Room</th>
                        <th>No. of Students</th>
                        <th>Sec/Year</th>
                        <th>Day/s</th>
                        <th>Start</th>
                        <th>End</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($conflictedSchedules as $schedule)
                        <tr>
                            <td>{{ $schedule->categoryName }}</td>
                            <td>{{ $schedule->subject->subjectName }}</td>
                            <td>{{ $schedule->classroom->roomName }}</td>
                            <td class="text-center">{{ $schedule->studentNum }}</td>
                            <td>{{ $schedule->yearSection }}</td>
                            <td class="text-center">{{ $schedule->days }}</td>
                            <td>{{ \Carbon\Carbon::parse($schedule->startTime)->format('h:i A') }}</td>
                            <td>{{ \Carbon\Carbon::parse($schedule->endTime)->format('h:i A') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="summary mt-[20px] w-full">
            <h5 class="text-left"><b>Summary of Reports</b></h5>
            <p class="font-medium mb-2">Total Schedule Conflict: {{ $conflictCount }}</p>
            <p class="font-medium mb-2" id="appeal-date">Date of appeal:</p>
            <p class="font-medium mb-2" id="appeal-time">Time:</p>
        </div>

        <div class="signatures w-full mt-[40px] flex justify-between">
            <div class="user">
                <p>_______________________</p>
                <p class="font-semibold">Signature over printed <br> name of the faculty</p>
            </div>
            <div class="admin">
                <p>_______________________</p>
                <p class="font-semibold">Signature over printed <br> name of the admin</p>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const appealDate = document.getElementById('appeal-date');
            const appealTime = document.getElementById('appeal-time');
            const now = new Date();

            const dateOptions = { year: 'numeric', month: 'long', day: 'numeric' };
            const timeOptions = { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true };

            appealDate.textContent += ' ' + now.toLocaleDateString(undefined, dateOptions);
            appealTime.textContent += ' ' + now.toLocaleTimeString(undefined, timeOptions);
        });
    </script>
</body>
</html>
