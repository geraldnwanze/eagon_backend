<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guest Invitation</title>
    <link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans">
<table class="w-full max-w-md mx-auto my-12">
    <tbody>
    <tr>
        <td class="bg-white p-8 rounded-lg shadow-md">
            <h2 class="text-xl font-bold text-center mb-4">Hello {{ ucwords($guest['full_name']) }}</h2>
            <p class="text-center mb-2">You have been invited by {{ ucwords($user['full_name']) }} as a Guest to {{ ucwords(config('app.estate_name')) }}. .</p>
            <p class="font-medium text-center bg-gray-200 py-6 rounded">Your Invitation Code is</p>
            <p class="font-medium text-3xl text-center bg-gray-200 py-6 rounded">{{ $guest['invitation_code'] }}</p>
            <p class="font-medium text-center bg-gray-200 py-6 rounded">Follow this <a href="#">link</a> to download the app and accept the invite</p>
        </td>
    </tr>
    </tbody>
</table>
</body>
</html>
