<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schedule Print - {{ $teacher->teacherName }}</title>
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        @media print {
            @page {
                size: portrait A4;
                margin: 10mm;
            }

            body {
                font-size: 10pt;
                line-height: 1.2;
            }

            .schedule-table {
                width: 100%;
                table-layout: auto;
                border-collapse: collapse;
            }

            .schedule-table th, .schedule-table td {
                border: 1px solid #000;
                padding: 5px;
                text-align: left;
                word-wrap: break-word;
                vertical-align: middle;
            }

            .schedule-table th {
                background-color: #f2f2f2;
                font-weight: bold;
            }

            h1 {
                text-align: center;
                font-size: 14pt;
                margin-bottom: 10px;
            }
        }
    </style>
</head>
<body class="bg-gray-50">
    <header>
        <img src="{{ asset('/images/depedLogo.png') }}" alt="deped logo" width="90" height="90" class="block mx-auto mb-4">
        <h1 class="text-xl font-bold text-center mb-6">
            SAGAY CITY SENIOR HIGH SCHOOL <br>
            HUMMS {{ strtoupper($year) }} SCHEDULE OF CLASSES <br>
            {{ strtoupper($semester) }} - {{ $schoolYear }} <br>
        </h1>
    </header>

    <div class="px-4" id="schedule-container">
        <div class="flex items-center justify-between">
            <p class="mb-3">
                <b>TEACHER:</b> {{ strtoupper($teacher->teacherName) }}
            </p>
            <p>
                <b>TOTAL LOADED HOURS:</b> {{ strtoupper($teacher->numberHours) }}
            </p>
        </div>
        <table class="schedule-table mx-auto">
            <thead>
                <tr class="bg-gray-200">
                    <th class="px-4 py-2 border">Grade</th>
                    <th class="px-4 py-2 border">Section</th>
                    <th class="px-4 py-2 border">Subject</th>
                    <th class="px-4 py-2 border">Start Time</th>
                    <th class="px-4 py-2 border">End Time</th>
                    <th class="px-4 py-2 border">Day</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="3" class="text-center bg-yellow-300"><b>FLAG CEREMONY</b></td>
                    <td class="px-4 py-2 border">7:45 AM</td>
                    <td class="px-4 py-2 border">8:00 AM</td>
                    <td class="px-4 py-2 border text-center">M-T-W-TH-F</td>
                </tr>
                @foreach ($schedules as $schedule)
                    <tr>
                        <td class="px-4 py-2 border">{{ $schedule->year }}</td>
                        <td class="px-4 py-2 border text-center">{{ $schedule->section }}</td>
                        <td class="px-4 py-2 border">{{ $schedule->subject->subjectName }}</td>
                        <td class="px-4 py-2 border">{{ \Carbon\Carbon::parse($schedule->startTime)->format('g:i A') }}</td>
                        <td class="px-4 py-2 border">{{ \Carbon\Carbon::parse($schedule->endTime)->format('g:i A') }}</td>
                        <td class="px-4 py-2 border text-center">{{ $schedule->days }}</td>
                    </tr>

                    @php
                        $endTime = \Carbon\Carbon::parse($schedule->endTime);
                        $recessStart = \Carbon\Carbon::createFromTime(10, 0);
                        $recessEnd = \Carbon\Carbon::createFromTime(10, 15);
                        $lunchStart = \Carbon\Carbon::createFromTime(12, 15);
                        $lunchEnd = \Carbon\Carbon::createFromTime(13, 0);
                    @endphp

                    @if ($endTime->equalTo($recessStart))
                        <tr class="break">
                            <td colspan="3" class="text-center bg-yellow-300"><b>RECESS</b></td>
                            <td class="px-4 py-2 border">10:00 AM</td>
                            <td class="px-4 py-2 border">10:15 AM</td>
                            <td class="px-4 py-2 border text-center">M-T-W-TH-F</td>
                        </tr>
                    @endif

                    @if ($endTime->equalTo($lunchStart))
                        <tr class="break">
                            <td colspan="3" class="text-center bg-yellow-300"><b>LUNCH BREAK</b></td>
                            <td class="px-4 py-2 border">12:15 PM</td>
                            <td class="px-4 py-2 border">1:00 PM</td>
                            <td class="px-4 py-2 border text-center">M-T-W-TH-F</td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
        <p class="text-left font-medium">Prepared by:</p>
    </div>

    <footer>
        <div class="flex items-center justify-between px-10 pt-10">
            <div class="directives">
                <h5><b>JONA A. ESMALLA</b></h5>
                <p class="text-center">Principal II</p>
            </div>

            <div class="directives">
                <h5><b>MARILYN B. GAMBOA, Ph.D.</b></h5>
                <p class="text-center">PSDS, District X</p>
            </div>

            <div class="directives">
                <h5><b>NENITA P. GAMAO, Ph.D.</b></h5>
                <p class="text-center">CID Chief</p>
            </div>
        </div>
    </footer>

    <script>
        window.onafterprint = function() {
            window.close();
        };

        window.addEventListener('beforeunload', function() {
            window.opener.focus();
        });

        window.print();
    </script>
</body>
</html>
