<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Classroom Inventory - Sagay City Senior High School</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @media print {
            @page {
                size: portrait;
                margin: 10mm;
            }

            body {
                font-family: 'Roboto', Arial, sans-serif;
                font-size: 10pt;
                line-height: 1.4;
                color: #333;
            }

            .print-container {
                width: 100%;
                max-width: 210mm;
                margin: 0 auto;
            }

            .header {
                display: flex;
                flex-direction: column;
                align-items: center;
                margin-bottom: 10mm;
                padding-bottom: 3mm;
                border-bottom: 1.5px solid #223a5e;
            }

            .header-content {
                display: flex;
                align-items: center;
                gap: 10mm;
            }

            .logo {
                width: 25mm;
                height: 25mm;
                object-fit: contain;
            }

            .school-info {
                text-align: center;
            }

            .school-name {
                font-size: 14pt;
                font-weight: bold;
                color: #223a5e;
                margin-bottom: 2mm;
                text-transform: uppercase;
            }

            .document-title {
                font-size: 12pt;
                color: #666;
                text-transform: uppercase;
            }

            .classroom-table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 10mm;
            }

            .classroom-table th,
            .classroom-table td {
                border: 1px solid #223a5e;
                padding: 2mm;
                text-align: left;
            }

            .classroom-table thead {
                background-color: #f0f4f8;
            }

            .classroom-table th {
                font-weight: bold;
                color: #223a5e;
                font-size: 10pt;
            }

            .classroom-table tr:nth-child(even) {
                background-color: #f9f9f9;
            }

            .footer {
                display: flex;
                justify-content: space-between;
                margin-top: 10mm;
                border-top: 1px solid #223a5e;
                padding-top: 5mm;
            }

            .footer-section {
                text-align: center;
                flex: 1;
            }

            .footer-name {
                font-weight: bold;
                margin-bottom: 2mm;
            }

            .footer-title {
                font-size: 9pt;
                color: #666;
            }

            .prepared-by {
                margin-top: 5mm;
                font-style: italic;
                color: #666;
            }
        }

        body {
            visibility: visible !important;
        }
    </style>
</head>
<body>
    <div class="print-container">
        <header class="header">
            <div class="header-content">
                <img src="{{ asset('/images/depedLogo.png') }}" alt="DepEd Logo" class="logo">

                <div class="school-info">
                    <div class="school-name">Sagay City Senior High School</div>
                    <div class="document-title">Classroom List Report</div>
                </div>
            </div>
        </header>

        <main>
            <table class="classroom-table">
                <thead>
                    <tr>
                        <th>Classroom/Laboratory</th>
                        <th>Building Number</th>
                        <th>Floor Number</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($classrooms as $classroom)
                        <tr>
                            <td>{{ $classroom->roomName }}</td>
                            <td>{{ $classroom->buildingNumber }}</td>
                            <td>{{ $classroom->floorNumber }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <p class="prepared-by">Prepared by: Classroom Management Team</p>
        </main>

        <footer class="footer">
            <div class="footer-section">
                <div class="footer-name">JONA A. ESMALLA</div>
                <div class="footer-title">Principal II</div>
            </div>

            <div class="footer-section">
                <div class="footer-name">MARILYN B. GAMBOA, Ph.D.</div>
                <div class="footer-title">PSDS, District X</div>
            </div>

            <div class="footer-section">
                <div class="footer-name">NENITA P. GAMAO, Ph.D.</div>
                <div class="footer-title">CID Chief</div>
            </div>
        </footer>
    </div>

    <script>
        window.onload = function() {
            window.print();
            window.onafterprint = function() {
                window.close();
            };
        };
    </script>
</body>
</html>
