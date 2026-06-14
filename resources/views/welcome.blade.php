<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Welcome Page</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="m-0 p-0">

  <div class="flex h-screen w-full flex-col md:flex-row">
    
    <div class="flex w-full items-center justify-center bg-green-600 text-white md:w-1/2 p-8">
      <div class="text-center">
        <h1 class="text-4xl font-extrabold mb-4 md:text-6xl">Selamat</h1>
        <p class="text-lg opacity-90">Datang di RME PORMIKI JAWA BARAT.</p>
      </div>
    </div>

    

    <div class="flex w-full items-center justify-center bg-white text-gray-900 md:w-1/2 p-8">
      <div class="text-center">
            <h2 class="text-3xl font-bold mb-6">Mulai Sekarang</h2>
            <a href="{{ route('login') }}" class="bg-green-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-green-700 transition duration-300 shadow-lg">
            Masuk / Daftar
            </a>
      </div>
    </div>

  </div>

</body>
</html>