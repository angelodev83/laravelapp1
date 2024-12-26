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
            font-family: 'Poppins', sans-serif;
        }
        table, td {
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
            font-family: 'Poppins', sans-serif;
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

            .summary-table {
                width: 100% !important;
            }
        }
    </style>
</head>
<body>

<div style="width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch;">
    <div style="text-align: center; margin-bottom: 5px; padding-bottom: 0;">
        <img src="https://home.mgmt88.com/images/mgmt88-logo.png" style="max-width: 100%; max-height: 100px;"></img>
    </div>
    <p style="font-size: 30px; color: black; text-align: center; margin-bottom: 0; padding-bottom: 0;">{{ $emailSubject }}</p>
    <p style="font-size: 12px; color: gray; text-align: center; margin-top: 0; padding-top: 0;">by {{ $createdBy }}</p>
    <div style="background-color: #c2f4f58c; color: #0d677a; margin-bottom: 20px; padding: 20px 40px 20px 40px; text-align: center;">
        <!-- <a href="https://home.mgmt88.com/store/clinical/1/rx-daily-census" target="_blank">TRP: Clinical > TRHC px+Rx Daily Census</a> -->
        <small>TRP: Clinical > TRHC px+Rx Daily Census</small>
    </div>
</div>

<div style="width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch;">
    <table role="presentation" class="summary-table" style="width: 50%; border-collapse: collapse; border: 1px solid #ddd;">
        <tbody>
            <tr>
                <td style="padding: 8px; border: 1px solid #ddd;">Live Patients</td>
                <td style="padding: 8px; border: 1px solid #ddd; background-color: #f4f4f4;"><b>{{ $data['summary']->count_patient_names }}</b></td>
            </tr>
            <tr>
                <td style="padding: 8px; border: 1px solid #ddd;">Live Patients w/ scripts</td>
                <td style="padding: 8px; border: 1px solid #ddd; background-color: #f4f4f4;"><b>{{ $data['summary']->summary_unique_patient_with_scripts }}</b></td>
            </tr>
            <tr>
                <td style="padding: 8px; border: 1px solid #ddd;" colspan="2">Top 3 Providers:</td>
            </tr>
            @if(!empty($data['summary']->summary_top_providers))
                @foreach($data['summary']->summary_top_providers as $row)
                    <tr>
                        <td style="padding: 8px; border: 1px solid #ddd; background-color: #f4f4f4;">{{ $row->provider }}</td>
                        <td style="padding: 8px; border: 1px solid #ddd; background-color: #f4f4f4;"><b>{{ $row->sum_scripts_received }}</b></td>
                    </tr>
                @endforeach
            @else
                <td style="padding: 8px; border: 1px solid #ddd;" colspan="2">N/A</td>
            @endif
        </tbody>
    </table>
</div>

<br>

<div style="width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch;">
    <table role="presentation" style="width: 100%; border-collapse: collapse; border: 1px solid #ddd;">
        <thead>
            <tr>
                <th style="padding: 8px; border: 1px solid #ddd; background-color: #f4f4f4;">Patient</th>
                <th style="padding: 8px; border: 1px solid #ddd; background-color: #f4f4f4;">DOB</th>
                <th style="padding: 8px; border: 1px solid #ddd; background-color: #f4f4f4;"># of scripts</th>
                <th style="padding: 8px; border: 1px solid #ddd; background-color: #f4f4f4;">Provider</th>
                <th style="padding: 8px; border: 1px solid #ddd; background-color: #f4f4f4;">Remarks</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data['collections'] as $row)
                @php
                    $patient = isset($row->patient) ? $row->patient : null;

                    $patient_fullname = '';
                    if(isset($patient->firstname)) {
                        $patient_fullname = $patient->getDecryptedLastname().', '.$patient->getDecryptedFirstname();
                    } else {
                        $patient_fullname = $row->patient_name;
                    }

                    $birth_date = '';
                    if(!empty($row->birth_date)) {
                        $birth_date = date('F d, Y', strtotime($row->birth_date));
                    }
                @endphp
                <tr>
                    <td style="padding: 8px; border: 1px solid #ddd;">{{ $patient_fullname }}</td>
                    <td style="padding: 8px; border: 1px solid #ddd;">{{ $birth_date }}</td>
                    <td style="padding: 8px; border: 1px solid #ddd;">{{ $row->scripts_received }}</td>
                    <td style="padding: 8px; border: 1px solid #ddd;">{{ $row->provider }}</td>
                    <td style="padding: 8px; border: 1px solid #ddd;">{{ $row->remarks }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<br>

<div style="width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch;">
    <div style="background-color: #edf2f7; color: #000; margin-bottom: 20px; padding: 20px 40px 20px 40px; text-align: center;">
        <small>© 2024 MGMT88 Intranet. All rights reserved.</small>
    </div>
</div>

</body>
</html>
