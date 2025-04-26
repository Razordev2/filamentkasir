<div>
<div class="grid grid-cols-1 dark:bg-gray-900 md:grid-cols-3 gap-4">
    <div class="md:col-span-2 bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="relative w-full md:w-1/2">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                </div>
                <input wire:model.live.debounce.300ms='search' type="text" placeholder="Cari produk..."
                    class="w-full pl-10 p-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200">
            </div>
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition duration-200 shadow">
                    <span>Kategori</span>
                </button>
                
                <div x-show="open" @click.away="open = false" 
                    class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg py-1 z-10 border border-gray-200 dark:border-gray-700"
                    x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="transform opacity-0 scale-95"
                    x-transition:enter-end="transform opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-75"
                    x-transition:leave-start="transform opacity-100 scale-100"
                    x-transition:leave-end="transform opacity-0 scale-95">
                    <div class="px-4 py-2 border-b border-gray-200 dark:border-gray-700">
                        <span class="font-medium text-gray-800 dark:text-gray-200">Pilih Kategori</span>
                    </div>
                    <button wire:click="$set('selectedCategory', null)" class="w-full text-left px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700">
                        <span class="inline-flex items-center">
                            <span class="h-2 w-2 rounded-full mr-2 {{ is_null($selectedCategory) ? 'bg-blue-600' : 'bg-transparent' }}"></span>
                            Semua
                        </span>
                    </button>
                    @foreach($categories as $category)
                        <button wire:click="$set('selectedCategory', '{{ $category->id }}')" class="w-full text-left px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700">
                            <span class="inline-flex items-center">
                                <span class="h-2 w-2 rounded-full mr-2 {{ $selectedCategory === $category->id ? 'bg-blue-600' : 'bg-transparent' }}"></span>
                                {{ $category->name }}
                            </span>
                        </button>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="mb-4">
            @if(!is_null($selectedCategory))
                @foreach($categories as $category)
                    @if($selectedCategory === $category->id)
                        <div class="inline-flex items-center bg-blue-100 dark:bg-blue-900/30 px-3 py-1 rounded-full">
                            <span class="text-blue-800 dark:text-blue-200 text-sm font-medium">{{ $category->name }}</span>
                            <button wire:click="$set('selectedCategory', null)" class="ml-1 text-blue-600 dark:text-blue-300 hover:text-blue-800 dark:hover:text-blue-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    @endif
                @endforeach
            @else
            @endif
        </div>
        <div class="flex-grow">
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                @forelse($products as $item)
                    <div wire:click="addToOrder({{$item->id}})" 
                        class="bg-white dark:bg-gray-700 rounded-xl shadow-md hover:shadow-xl cursor-pointer transform hover:scale-105 transition-all duration-200 overflow-hidden border border-gray-100 dark:border-gray-600">
                        <div class="relative">
                            <img src="{{$item->image_url}}" alt="{{$item->name}}" 
                                class="w-full h-32 object-cover">
                            @if($item->discount)
                                <div class="absolute top-0 right-0 bg-red-500 text-white px-2 py-1 text-xs font-bold rounded-bl-lg">
                                    SALE
                                </div>
                            @endif
                        </div>
                        <div class="p-4">   
                            <h3 class="font-medium text-gray-800 dark:text-gray-200 truncate">{{$item->name}}</h3>
                            <div class="mt-1 flex justify-between items-center">
                                <div>
                                    @if($item->discount)
                                        <p class="text-xs text-gray-500 dark:text-gray-400 line-through">Harga : Rp {{number_format($item->price, 0, ',', '.')}}</p>
                                        <span class="text-xs px-2 py-1 bg-blue-100 dark:bg-gray-600 text-black-700 dark:text-gray-300 rounded-full">
                                            Stock: {{$item->stock}}
                                        </span>
                                    @else
                                    <p class="text-xs text-gray-500 dark:text-gray-400 line-through">Harga : Rp
                                            {{number_format($item->price, 0, ',', '.')}}
                                        </p>
                                        <span class="text-xs px-2 py-1 bg-blue-100 dark:bg-gray-600 text-black-700 dark:text-gray-300 rounded-full">
                                            Stock: {{$item->stock}}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center text-gray-500 dark:text-gray-400">Produk tidak ditemukan.</div>
                @endforelse
            </div>
            <div class="py-6">
                {{ $products->links() }}
            </div>
        </div>
    </div>
    <div class="md:col-span-1 bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
    @if(count($order_items) > 0)
    @endif
        @foreach($order_items as $item)
            <div class="mb-4">
                <div class="flex justify-between items-center bg-gray-100 dark:bg-gray-700 p-4 rounded-lg shadow">
                    <div class="flex items-center">
                        <img src="{{$item['image_url']}}" alt="Product Image"
                            class="w-10 h-10 object-cover rounded-lg mr-2">
                        <div class="px-2">
                            <h3 class="text-sm font-semibold">{{$item['name']}}</h3>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <x-filament::button color="warning" wire:click="decreaseQuantity({{$item['product_id']}})">-</x-filament::button>
                        <span class="px-4">{{$item['quantity']}}</span>
                        <x-filament::button color="success" wire:click="increaseQuantity({{$item['product_id']}})">+</x-filament::button>
                    </div>
                </div>
            </div>
        @endforeach 
        <form wire:submit="checkout">
            {{$this->form}}
            <x-filament::button
                type="submit"
                class="w-full bg-red-500 mt-3 text-white py-2 rounded">Checkout</x-filament::button>
                
        </form>
    </div>
    
</div>
@if($showReceiptModal)
<div class="fixed inset-0 bg-black bg-opacity-60 backdrop-blur-sm flex items-center justify-center z-50">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-lg w-full overflow-hidden">
        <!-- Header -->
        <div class="bg-blue-600 text-white p-4">
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-bold">Konfirmasi Cetak Struk</h2>
                <button wire:click="closeReceiptModal" class="text-white hover:text-gray-200 transition duration-150">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
        
        <div class="p-6">
            <div class="flex items-center mb-6 bg-green-100 dark:bg-green-900/30 p-4 rounded-lg">
                <div class="rounded-full bg-green-500 p-2 mr-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <p class="text-green-800 dark:text-green-200 font-medium">Transaksi berhasil diproses! Apakah Anda ingin mencetak struk?</p>
            </div>
            
            <!-- Purchase Summary -->
            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg mb-6 border border-gray-200 dark:border-gray-600 overflow-auto max-h-96">
                <h3 class="font-bold text-gray-800 dark:text-gray-200 mb-3 border-b border-gray-200 dark:border-gray-600 pb-2">Ringkasan Pembelian</h3>
                
                <!-- Informasi Pelanggan -->
                <div class="mb-3">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 dark:text-gray-400">Pelanggan</span>
                        <span class="font-medium text-gray-800 dark:text-gray-200">{{ $lastOrderDetails['name_customer'] }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 dark:text-gray-400">Metode Pembayaran</span>
                        <span class="font-medium text-gray-800 dark:text-gray-200">{{ $lastOrderDetails['payment_method'] }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 dark:text-gray-400">Tanggal</span>
                        <span class="font-medium text-gray-800 dark:text-gray-200">{{ $lastOrderDetails['date'] }}</span>
                    </div>
                </div>
                
                <!-- Detail Item Pembelian -->
                <div class="mt-4 border-t border-gray-200 dark:border-gray-600 pt-3">
                    <h4 class="font-semibold text-gray-800 dark:text-gray-200 mb-2">Detail Item</h4>
                    
                    <div class="space-y-3">
                        @foreach($lastOrderDetails['items'] as $item)
                        <div class="flex flex-col p-2 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                            <div class="flex justify-between">
                                <span class="font-medium">{{ $item['name'] }}</span>
                                <span class="text-sm">x{{ $item['quantity'] }}</span>
                            </div>
                            <div class="flex justify-between text-sm mt-1">
                                <span class="text-gray-600 dark:text-gray-400">Unit Price</span>
                                <span>Rp {{ number_format($item['unit_price'], 0, ',', '.') }}</span>
                            </div>
                            @if($item['discount'] > 0)
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-400">Diskon</span>
                                <span class="text-red-500">- Rp {{ number_format($item['discount'], 0, ',', '.') }}</span>
                            </div>
                            @endif
                            <div class="flex justify-between text-sm font-semibold mt-1 pt-1 border-t border-gray-100 dark:border-gray-700">
                                <span>Subtotal</span>
                                <span>Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t-2 border-gray-300 dark:border-gray-600">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-700 dark:text-gray-300">Total Sebelum Diskon</span>
                        <span class="font-medium">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</span>
                    </div>
                    
                    @if($lastOrderDetails['discount'] > 0)
                    <div class="flex justify-between items-center">
                        <span class="text-gray-700 dark:text-gray-300">Total Diskon</span>
                        <span class="font-medium text-red-500">- Rp {{ number_format($lastOrderDetails['discount'], 0, ',', '.') }}</span>
                    </div>
                    @endif
                    
                    <div class="flex justify-between items-center mt-2 pt-2 border-t border-gray-200 dark:border-gray-600 font-bold text-lg">
                        <span class="text-gray-800 dark:text-gray-200">Total</span>
                        <span class="text-green-600 dark:text-green-400">Rp  {{ number_format($item['subtotal'], 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
            
            <!-- Buttons -->
            <div class="flex justify-between space-x-4">
                <button wire:click="closeReceiptModal" class="flex items-center justify-center w-full px-4 py-3 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition duration-200 font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Tidak
                </button>
                <button wire:click="printReceipt" class="flex items-center justify-center w-full px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200 font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Cetak Struk
                </button>
            </div>
        </div>
    </div>
</div>
@endif

</div>