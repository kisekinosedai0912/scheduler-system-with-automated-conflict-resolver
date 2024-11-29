<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schedule Print</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        /* Custom Print CSS */
        @media print {
            @page {
                size: portrait A4; /* Set portrait format for A4 paper */
                margin: 10mm; /* Set margins for print */
            }

            body {
                font-size: 10pt; /* Default print font size */
                line-height: 1.2; /* Adjust line height for better readability */
            }

            /* Print Table Styling */
            .schedule-table {
                width: 100%; /* Ensure the table takes full width */
                table-layout: auto; /* Allow the table to adjust based on content */
                border-collapse: collapse;
            }

            .schedule-table th, .schedule-table td {
                border: 1px solid #000;
                padding: 5px; /* Adjust padding for better fit */
                text-align: left;
                word-wrap: break-word;
                vertical-align: middle;
            }

            .schedule-table th {
                background-color: #f2f2f2;
                font-weight: bold;
            }

            /* Page Breaks for Large Tables */
            .page-break {
                page-break-before: always;
            }

            /* Ensure the table fits within the print area */
            h1 {
                text-align: center;
                font-size: 14pt; /* Adjusted font size */
                margin-bottom: 10px; /* Adjusted margin */
            }

            /* Hide elements you don't want to print (like buttons) */
            .no-print {
                display: none;
            }
        }

        /* Additional Custom Tailwind Styling for Printing */
        .no-print {
            display: block;
            margin: 20px;
            text-align: center;
        }
    </style>
</head>
<body class="bg-gray-50">
    <header>
        <img src="{{ asset('/images/depedLogo.png') }}" alt="deped logo" width="90" height="90" class="block mx-auto mb-4">
        <h1 class="text-xl font-bold text-center mb-6">
            SAGAY CITY SENIOR HIGH SCHOOL <br>
            HUMMS {{ strtoupper($year) }} SCHEDULE OF CLASSES <br>
            {{ strtoupper($semester) }} - SY <span id="year"></span> - <span id="next-year"></span>
        </h1>
    </header>

    <!-- Schedule Content -->
    <div class="px-4" id="schedule-container">
        <table class="schedule-table mx-auto">
            <thead>
                <tr class="bg-gray-200">
                    <th class="px-4 py-2 border">Teacher</th>
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
                    <td colspan="4" class="text-center bg-yellow-300"><b>FLAG CEREMONY</b></td>
                    <td class="px-4 py-2 border">7:45 AM</td>
                    <td class="px-4 py-2 border">8:00 AM</td>
                    <td class="px-4 py-2 border text-center">M-T-W-TH-F</td>
                </tr>
                @foreach ($schedules as $schedule)
                    <tr>
                        <td class="px-4 py-2 border">{{ $schedule->teacher->teacherName }}</td>
                        <td class="px-4 py-2 border">{{ $schedule->year }}</td>
                        <td class="px-4 py-2 border text-center">{{ $schedule->section }}</td>
                        <td class="px-4 py-2 border">{{ $schedule->subject->subjectName }}</td>
                        <td class="px-4 py-2 border">{{ \Carbon\Carbon::parse($schedule->startTime)->format('g:i A') }}</td>
                        <td class="px-4 py-2 border">{{ \Carbon\Carbon::parse($schedule->endTime)->format('g:i A') }}</td>
                        <td class="px-4 py-2 border text-center">{{ $schedule->days }}</td>
                    </tr>

                    @php
                        // Convert start and end times to Carbon instances for comparison
                        $endTime = \Carbon\Carbon::parse($schedule->endTime);
                        $recessStart = \Carbon\Carbon::createFromTime(10, 0); // 10:00 AM
                        $recessEnd = \Carbon\Carbon::createFromTime(10, 15); // 10:15 AM
                        $lunchStart = \Carbon\Carbon::createFromTime(12, 15); // 12:15 PM
                        $lunchEnd = \Carbon\Carbon::createFromTime(13, 0); // 1:00 PM
                    @endphp

                    <!-- Insert Recess Break -->
                    @if ($endTime->equalTo($recessStart))
                        <tr class="break">
                            <td colspan="4" class="text-center bg-yellow-300"><b>RECESS</b></td>
                            <td class="px-4 py-2 border">10:00 AM</td>
                            <td class="px-4 py-2 border">10:15 AM</td>
                            <td class="px-4 py-2 border text-center">M-T-W-TH-F</td>
                        </tr>
                    @endif

                    <!-- Insert Lunch Break -->
                    @if ($endTime->equalTo($lunchStart))
                        <tr class="break">
                            <td colspan="4" class="text-center bg-yellow-300"><b>LUNCH BREAK</b></td>
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
        document.addEventListener('DOMContentLoaded', function() {
            const currentYear = new Date().getFullYear();

            // Set current year and next year
            document.getElementById('current-year').textContent = currentYear;
            document.getElementById('next-year').textContent = currentYear + 1;

            // Print button functionality
            const printBtn = document.getElementById('print');
            if (printBtn) {
                printBtn.addEventListener('click', function() {
                    window.print();
                    this.classList.add('hidden');
                });
            }
        });
    </script>
</body>
</html>
