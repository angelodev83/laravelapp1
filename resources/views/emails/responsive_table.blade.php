<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        /* General resets and styles for email clients */
        body, table, td, a {
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }
        table, td {
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }
        img {
            -ms-interpolation-mode: bicubic;
        }

        /* Responsive styles */
        @media only screen and (max-width: 600px) {
            .table-container {
                width: 100%;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }
        }
    </style>
</head>
<body>

<div style="width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch;">
    <table role="presentation" style="width: 100%; border-collapse: collapse; border: 1px solid #ddd;">
        <thead>
            <tr>
                <th style="padding: 8px; border: 1px solid #ddd; background-color: #f4f4f4;">Header 1</th>
                <th style="padding: 8px; border: 1px solid #ddd; background-color: #f4f4f4;">Header 2</th>
                <th style="padding: 8px; border: 1px solid #ddd; background-color: #f4f4f4;">Header 3</th>
                <!-- Add more headers as needed -->
            </tr>
        </thead>
        <tbody>
            @foreach($tableData as $row)
                <tr>
                    <td style="padding: 8px; border: 1px solid #ddd;">{{ $row['column1'] }}</td>
                    <td style="padding: 8px; border: 1px solid #ddd;">{{ $row['column2'] }}</td>
                    <td style="padding: 8px; border: 1px solid #ddd;">{{ $row['column3'] }}</td>
                    <!-- Add more columns as needed -->
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

</body>
</html>
