<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $monthName ?? '' }} Calendar of Events - S.Y. {{ $schoolYear }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Roboto', Arial, sans-serif;
            line-height: 1.6;
            background-color: #f4f6f9;
            color: #333;
            padding: 20px;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background-color: white;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            border-radius: 8px;
            overflow: hidden;
        }
        header {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
            background: linear-gradient(135deg, #223a5e 0%, #223a5e 100%);
            color: white;
            text-align: center;
        }
        .header-content {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        .header-content img {
            margin-right: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.2);
        }
        header p {
            margin: 0;
            font-family: Cambria, Cochin, Georgia, Times, 'Times New Roman', serif;
        }
        .header-title {
            font-size: 1.5em;
            font-weight: 700;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.2);
            margin-top: 10px;
        }
        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-top: 20px;
        }
        thead {
            background-color: #223a5e;
            color: white;
        }
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }
        tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tbody tr:hover {
            background-color: #f1f1f1;
            transition: background-color 0.3s ease;
        }
        .no-events {
            text-align: center;
            padding: 30px;
            background-color: #f8f9fa;
            color: #6c757d;
        }
        footer {
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 15px;
            background-color: #223a5e;
            color: white;
            font-size: 0.8rem;
            margin-top: 20px;
            width: 100%;
        }
        footer p {
            max-width: 600px;
            line-height: 1.5;
        }

        .month-header {
            background-color: #e6e6e6;
            color: #223a5e;
            font-size: 1.2em;
            font-weight: bold;
        }

        @media print {
            @page {
                size: A4 portrait;
                margin: 20mm 15mm 20mm 15mm;
            }
            * {
                -webkit-print-color-adjust: exact !important;
                color-adjust: exact !important;
                print-color-adjust: exact !important;
                font-family: 'Roboto', sans-serif;
            }
            body {
                background-color: white !important;
                font-size: 12px;
            }
            .container {
                max-width: 100%;
                margin: 0;
                padding: 0;
                box-shadow: none;
            }
            header {
                background: linear-gradient(135deg, #223a5e 0%, #223a5e 100%) !important;
                color: white !important;
                padding: 10px;
            }
            h2.header-title {
                font-size: 1.3em;
                font-weight: 700;
                margin: 10px 0;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                font-size: 11px;
            }
            th, td {
                padding: 10px 12px;
                text-align: left;
                border: 1px solid #e0e0e0;
            }
            .month-header {
                background-color: #e6e6e6 !important;
                color: #223a5e !important;
            }
            footer {
                background: linear-gradient(135deg, #223a5e 0%, #223a5e 100%) !important;
                color: white !important;
                padding: 10px;
                font-size: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <div class="header-content">
                <img src="{{ asset('images/schsLogo.jfif') }}" alt="SCHS Logo" width="70" height="70">
                <div>
                    <p><b>Department of Education</b></p>
                    <p><b>Region VI - Western Visayas</b></p>
                    <p><b>Schools Division of Sagay City</b></p>
                    <p><b>Sagay City National High School</b></p>
                </div>
            </div>
            <h2 class="header-title">
                @if($fromDate && $untilDate)
                    SENIOR HIGH SCHOOL CALENDAR OF EVENTS<br>
                    ({{ Carbon\Carbon::parse($fromDate)->format('F Y') }} - {{ Carbon\Carbon::parse($untilDate)->format('F Y') }})<br>
                    S.Y {{ $schoolYear }}
                @else
                    SENIOR HIGH SCHOOL CALENDAR OF EVENTS <br>(S.Y {{ $schoolYear }})
                @endif
            </h2>
        </header>

        <div class="table-container">
            @if($eventsByMonth->isEmpty())
                <div class="no-events">
                    <p>No events scheduled for the selected period.</p>
                </div>
            @else
                <table>
                    <thead>
                        <tr>
                            <th style="width: 70%;">Event</th>
                            <th style="width: 30%;" class="text-right">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($eventsByMonth as $month => $monthEvents)
                            <tr class="month-header">
                                <td colspan="2">{{ $month }}</td>
                            </tr>
                            @foreach($monthEvents as $event)
                                <tr class="event-row">
                                    <td>{{ $event->eventTitle }}</td>
                                    <td class="text-right">
                                        @if ($event->startDate == $event->endDate)
                                            {{ Carbon\Carbon::parse($event->startDate)->format('F j, Y') }}
                                        @else
                                            {{ Carbon\Carbon::parse($event->startDate)->format('F j') }} - {{ Carbon\Carbon::parse($event->endDate)->format('F j, Y') }}
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        <footer>
            <p>&copy; 2024 Sagay City National High School Stand Alone. All rights reserved.</p>
        </footer>
    </div>

    <script>
        (function() {
            function safeWindowClose() {
                try {
                    window.close();
                } catch (error) {
                    if (window.opener) {
                        window.opener.focus();
                    }
                    alert('Please close this window manually.');
                }
            }

            window.onload = function() {
                if (window.opener && window.opener.monthEvents) {
                    fetch("{{ route('print-calendar') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                        body: JSON.stringify({ monthEvents: window.opener.monthEvents })
                    })
                    .then(response => response.text())
                    .then(html => {
                        document.body.innerHTML = html;
                        window.print();

                        window.onafterprint = safeWindowClose;

                        setTimeout(function() {
                            if (!window.matchMedia('print').matches) {
                                safeWindowClose();
                            }
                        }, 500);
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        window.print();

                        setTimeout(safeWindowClose, 1000);
                    });
                } else {
                    window.print();

                    window.onafterprint = safeWindowClose;

                    setTimeout(function() {
                        if (!window.matchMedia('print').matches) {
                            safeWindowClose();
                        }
                    }, 500);
                }
            }
        })();
    </script>
</body>
</html>
