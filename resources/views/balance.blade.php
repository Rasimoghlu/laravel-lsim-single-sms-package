<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Rasimoghlu L-sim Sms Package</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css"
          integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>
<body class="antialiased">
<div class="relative flex items-top justify-center min-h-screen bg-gray-100 sm:items-center py-4 sm:pt-0">
    <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
        <div class="row justify-content-center">
            <div class="col-auto">
                <table class="table table-striped table-responsive">
                    <thead>
                    <tr>
                        <th scope="col">Success Message</th>
                        <th scope="col">Balance</th>
                        <th scope="col">Error Message</th>
                        <th scope="col">Error Code</th>
                        <th scope="col">Status</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if($balanceResponse)
                        <tr>
                            <td>{{ $balanceResponse['response']['successMessage'] ?? 'null' }}</td>
                            <td>{{ $balanceResponse['response']['obj'] }}</td>
                            <td>{{ $balanceResponse['response']['errorMessage'] ?? 'null' }}</td>
                            <td>{{ $balanceResponse['response']['errorCode'] ?? 'null' }}</td>
                            <td>{{ $balanceResponse['status_code'] }}</td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
</body>
</html>
