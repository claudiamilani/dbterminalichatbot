<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #4a5568;
            background-color: #f2f4f6;
            margin: 0;
            padding: 0;
        }

        .email-container {
            width: 100%;
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 5px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            border: 1px solid #e2e8f0;
        }

        th {
            background-color: #f7fafc;
            color: #2d3748;
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }

        td {
            padding: 10px;
            border-bottom: 1px solid #edf2f7;
            color: #4a5568;
        }

        tr:nth-child(even) td {
            background-color: #f7fafc;
        }

        tr:last-child td {
            border-bottom: none;
        }
    </style>
</head>
<body>
<div class="email-container">
    <h2>L'elaborazione del file di ingestion @lang('DBT/ingestions.sources.'.$ingestion->ingestion_source_id) è stata completata.</h2>
    <p>Dettagli dell'elaborazione per ingestion ID: {{$ingestion->id}}.</p>
    <table>
        <thead>
        <tr>
            <th>TERMINAL_ID</th>
            <th>UPDATED</th>
            <th>CREATED</th>
            <th>ERRORED</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($results as $key => $item)
            <tr>
                <td>{{ $key }}</td>
                <td>{{ $item['updated'] }}</td>
                <td>{{ $item['created'] }}</td>
                <td>{{ $item['errored'] }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <p></p>
    <p>Il team di {{config('app.name')}}</p>
</div>
</body>
</html>
