<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Buku - Siswa</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">

    <nav class="bg-blue-600 p-4 shadow-md">
        <div class="container mx-auto flex justify-between items-center text-white">
            <h1 class="text-xl font-bold tracking-wider">📚 E-Perpus</h1>
            <div class="space-x-4 font-semibold">
                <a href="/" class="hover:text-blue-200">Katalog</a>
                <a href="#" class="hover:text-blue-200">Pinjaman Saya</a>
                
                @auth
                    <span class="bg-blue-700 px-3 py-1 rounded-full text-sm">{{ auth()->user()->name }}</span>
                @else
                    <a href="/admin/login" class="bg-white text-blue-600 px-4 py-1 rounded shadow hover:bg-gray-100">Login</a>
                @endauth
            </div>
        </div>
    </nav>

    <div class="container mx-auto mt-8 px-4 pb-12">
        
        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm font-bold">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm font-bold">
                {{ session('error') }}
            </div>
        @endif

        <h2 class="text-2xl font-extrabold mb-6 text-gray-800 border-l-4 border-blue-600 pl-3">Katalog Buku Terbaru</h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            
            @foreach ($bukus as $buku)
            <div class="bg-white rounded-xl shadow-sm hover:shadow-xl transition-shadow duration-300 overflow-hidden border border-gray-100">
                <img src="{{ asset('storage/' . $buku->cover) }}" alt="{{ $buku->judul }}" class="w-full h-64 object-cover">
                
                <div class="p-5">
                    <h3 class="font-bold text-lg text-gray-800 line-clamp-1" title="{{ $buku->judul }}">{{ $buku->judul }}</h3>
                    <p class="text-sm text-gray-500 mb-3">{{ $buku->penulis }}</p>
                    
                    <div class="flex justify-between items-center mt-4">
                        @if($buku->stok > 0)
                            <span class="bg-green-100 text-green-700 text-xs font-bold px-2 py-1 rounded-md">Stok: {{ $buku->stok }}</span>
                            
                            <form action="{{ route('siswa.pinjam', $buku->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="bg-blue-600 text-white text-sm font-semibold px-4 py-1.5 rounded hover:bg-blue-700 transition" onclick="return confirm('Yakin ingin meminjam buku ini?')">
                                    Pinjam
                                </button>
                            </form>

                        @else
                            <span class="bg-red-100 text-red-700 text-xs font-bold px-2 py-1 rounded-md">Habis</span>
                            <button class="bg-gray-300 text-gray-500 text-sm font-semibold px-4 py-1.5 rounded cursor-not-allowed" disabled>Pinjam</button>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach

        </div>
    </div>

</body>
</html>