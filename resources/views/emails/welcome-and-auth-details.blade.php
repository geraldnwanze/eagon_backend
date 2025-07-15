<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estate Invitation</title>
    <link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans">
<table class="w-full max-w-md mx-auto my-12">
    <tbody>
    <tr>
        <td class="bg-white p-8 rounded-lg shadow-md">
            <h2 class="text-xl font-bold text-center mb-4">Hello {{ $user->full_name }}</h2>
            <p class="text-center mb-2">You have been invited by {{ ucwords(config('app.estate_name')) }} as a {{ ucfirst($user->role) }}. Use the credentials provided for your first login, ensure to change your password.</p>
            <p class="font-medium text-center bg-gray-200 py-6 rounded">Email: {{ $user->email }}</p>
            <p class="font-medium text-center bg-gray-200 py-6 rounded">Password: {{ $password }}</p>
        </td>
    </tr>
    </tbody>
</table>
</body>
</html>
