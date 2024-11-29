<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>S.Y. {{ $schoolYear }} Calendar of Activities</title>
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
            margin: 0; /* Remove default margin */
            line-height: 1.2;
            font-size: 0.9rem;
            font-family: Cambria, Cochin, Georgia, Times, 'Times New Roman', serif;
        }
        .header-title {
            text-align: center;
            margin-top: 10px;
            font-size: 1.5em;
            font-weight: 700;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.2);
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
        @media print {
            * {
                -webkit-print-color-adjust: exact !important;
                color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            body {
                background-color: white !important;
            }
            .container {
                box-shadow: none;
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            header {
                background: linear-gradient(135deg, #223a5e 0%, #223a5e 100%) !important;
                color: white !important;
                -webkit-print-color-adjust: exact !important;
            }
            thead {
                background-color: #223a5e !important;
                color: white !important;
            }
            tbody tr:nth-child(even) {
                background-color: #f9f9f9 !important;
            }
            table, th, td {
                border: 1px solid #e0e0e0 !important;
            }
            footer {
                background: linear-gradient(135deg, #223a5e 0%, #223a5e 100%) !important;
                color: white !important;
                -webkit-print-color-adjust: exact !important;
                margin-top: 10px;
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
                SENIOR HIGH SCHOOL CALENDAR OF EVENTS <br>(S.Y {{ $schoolYear }})
            </h2>
        </header>

        @if($events->isEmpty())
            <div class="no-events">
                <p>No events scheduled for this school year.</p>
            </div>
        @else
            <table>
                <thead>
                    <tr>
                        <th style="width: 70%;">School Year Events</th>
                        <th style="width: 30%" class="text-right">Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($events as $event)
                        <tr>
                            <td>{{ $event->eventTitle }}</td>
                            <td class="text-right">
                                @if ($event->startDate == $event->endDate)
                                    {{ \Carbon\Carbon::parse($event->startDate)->format('F j, Y') }}
                                @else
                                    {{ \Carbon\Carbon::parse($event->startDate)->format('F j') }} - {{ \Carbon\Carbon::parse($event->endDate)->format('F j, Y') }}
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <footer>
            <p>&copy; 2024 Sagay City National High School Stand Alone. All rights reserved.</p>
        </footer>
    </div>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
