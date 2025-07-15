<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Verification</title>
    <link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans">
<table class="w-full max-w-md mx-auto my-12">
    <tbody>
    <tr>
        <td class="bg-white p-8 rounded-lg shadow-md">
            <p class="text-center mb-2">To complete your verification, please use the following OTP code:</p>
            <p class="text-3xl font-bold text-center bg-gray-200 py-6 rounded">{{ $code }}</p>
            <p class="text-center mt-4 text-gray-500">
                This code will expire in 10 minutes.
            </p>
        </td>
    </tr>
    </tbody>
</table>
</body>
</html>
