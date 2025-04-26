<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;
use App\Models\Order;
use App\Models\Category;
use App\Models\PaymentMethod;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;

class Pos extends Component implements HasForms
{
    use InteractsWithForms;

    public $search = '';
    public $selectedCategory = null;
    public $payment_methods;
    public $order_items = [];
    public $member_id = null;
    public $name_customer = '';
    public $gender = '';
    public $payment_method_id = null;
    public $total_price = 0;
    public $discount = 0;
    public $members = [];
    public $discount_value = 0;  
    public $discounted_price = 0;
    public $showReceiptModal = false;
    public $lastOrderDetails = null;

    public function mount()
    {
        if (session()->has('orderItems')) {
            $this->order_items = array_map(function ($item) {
                $item['discount'] = $item['discount'] ?? 0;
                $item['discounted_price'] = $item['discounted_price'] ?? ($item['price'] - $item['discount']);
                return $item;
            }, session('orderItems'));
        }

        $this->payment_methods = PaymentMethod::all();
        $this->updateDiscountedPrice();
        $this->form->fill([
            'discounted_price' => $this->priceAfterDiscount(),
            'discount' => $this->priceAfterDiscount(),
        ]);
    }

    public function updateDiscountedPrice()
    {
        $this->discounted_price = $this->calculateTotal();
    }

    public function filterCategory($id)
    {
        $this->selectedCategory = $id;
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Form Checkout')->schema([
                TextInput::make('name_customer')
                    ->required()
                    ->maxLength(255)
                    ->nullable()
                    ->label('Customer Name (Optional)')
                    ->default(fn () => $this->name_customer),
                
                TextInput::make('discounted_price')
                    ->label('Unit Price')
                    ->numeric()
                    ->required()
                    ->disabled(),
                
                Select::make('payment_method_id')
                    ->required()
                    ->label('Payment Method')
                    ->options($this->payment_methods->pluck('paymentmethods', 'id')),
            ])
        ]);
    }

    public function updated($propertyName)
    {
        if (str_starts_with($propertyName, 'order_items') || $propertyName === 'discount_value') {
            $this->discount = $this->priceAfterDiscount();
            $this->form->fill([
                'discounted_price' => $this->discount,
                'discount' => $this->discount,
                'total_price' => $this->calculateTotal(),
            ]);
        }
    }
    
    public function printReceipt()
    {
        session()->put('receipt', [
            'items' => $this->lastOrderDetails['items'],
            'name_customer' => $this->lastOrderDetails['name_customer'] ?: 'Umum',
            'payment_method' => $this->lastOrderDetails['payment_method'],
            'total' => $this->lastOrderDetails['total'],
            'discount' => $this->lastOrderDetails['discount'],
            'total_price' => $this->lastOrderDetails['total_price'],
            'date' => $this->lastOrderDetails['date'],
        ]);

        $this->showReceiptModal = false;
        return redirect()->route('receipt.print');
    }
    
    public function closeReceiptModal()
    {
        $this->showReceiptModal = false;
    }
    
    public function priceAfterDiscount()
    {
        return $this->calculateTotal();
    }

    public function render()
    {
        $totalBeforeDiscount = collect($this->order_items)->sum(function ($item) {
            return $item['price'] * $item['quantity'];
        });

        $productsQuery = Product::where('stock', '>', 0)
            ->when($this->search, fn($query) => $query->where('name', 'like', '%' . $this->search . '%'))
            ->when($this->selectedCategory, fn($query) => $query->where('category_id', $this->selectedCategory));

        $totalAfterProductDiscount = collect($this->order_items)->sum(function ($item) {
            $discounted = $item['discounted_price'] ?? ($item['price'] - ($item['discount'] ?? 0));
            return $discounted * $item['quantity'];
        });

        $finalTotal = $totalAfterProductDiscount - ($totalAfterProductDiscount * ($this->discount_value / 100));

        return view('livewire.pos', [
            'products' => $productsQuery->paginate(12),
            'categories' => Category::all(),
            'total_price_after_discount' => $this->calculateTotal(),
            'totalBeforeDiscount' => $totalBeforeDiscount,
            'totalAfterProductDiscount' => $totalAfterProductDiscount,
            'finalTotal' => $finalTotal,
            'discountValue' => $this->discount_value,
        ]);
    }

    public function calculateTotal()
    {
        $totalAfterDiscount = collect($this->order_items)->sum(function ($item) {
            $discountedPrice = $item['discounted_price'] ?? ($item['price'] - ($item['discount'] ?? 0));
            return $discountedPrice * $item['quantity'];
        });

        return $totalAfterDiscount - ($totalAfterDiscount * ($this->discount_value / 100));
    }

    public function addToOrder($productId)
    {
        $product = Product::find($productId);
        if (!$product || $product->stock <= 0) {
            Notification::make()->title('Stok habis / tidak ditemukan')->danger()->send();
            return;
        }

        $discountAmount = $product->discount?->amount ?? 0;
        $priceAfterDiscount = $product->price - $discountAmount;

        $existingIndex = collect($this->order_items)->search(fn($item) => $item['product_id'] == $productId);

        if ($existingIndex !== false) {
            if ($this->order_items[$existingIndex]['quantity'] < $product->stock) {
                $this->order_items[$existingIndex]['quantity']++;
            } else {
                Notification::make()->title('Jumlah melebihi stok')->danger()->send();
            }
        } else {
            $this->order_items[] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'discount' => $discountAmount,
                'discounted_price' => $priceAfterDiscount,
                'total_price' => $priceAfterDiscount,
                'image_url' => $product->image_url,
                'quantity' => 1,
            ];
        }

        session()->put('orderItems', $this->order_items);
        $this->updateDiscountedPrice();
        $this->form->fill([]);
    }

    public function increaseQuantity($product_id)
    {
        $product = Product::find($product_id);
        if (!$product) return;

        foreach ($this->order_items as $key => $item) {
            if ($item['product_id'] == $product_id) {
                if ($item['quantity'] + 1 <= $product->stock) {
                    $this->order_items[$key]['quantity']++;
                } else {
                    Notification::make()->title('Stok barang tidak mencukupi')->danger()->send();
                }
                break;
            }
        }

        session()->put('orderItems', $this->order_items);
        $this->updateDiscountedPrice();
        $this->form->fill([
            'discount' => $this->priceAfterDiscount(),
        ]);
    }

    public function decreaseQuantity($product_id)
    {
        foreach ($this->order_items as $key => $item) {
            if ($item['product_id'] == $product_id) {
                if ($item['quantity'] > 1) {
                    $this->order_items[$key]['quantity']--;
                } else {
                    unset($this->order_items[$key]);
                }
                break;
            }
        }

        $this->order_items = array_values($this->order_items);
        session()->put('orderItems', $this->order_items);
        $this->updateDiscountedPrice();
    }

    public function updatedDiscountValue()
    {
        $this->updateDiscountedPrice();
    }

    public function checkout()
    {
        if (empty($this->order_items)) {
            Notification::make()->title('Keranjang kosong')->danger()->send();
            return;
        }
    
        // Check product availability and stock
        foreach ($this->order_items as $item) {
            $product = Product::with('discount')->find($item['product_id']);
    
            if (!$product || $item['quantity'] > $product->stock) {
                Notification::make()->title("Produk '{$item['name']}' tidak tersedia atau stok tidak mencukupi")->danger()->send();
                return;
            }
        }
    
       
        $orderItems = $this->order_items;
        $paymentMethod = PaymentMethod::find($this->payment_method_id)?->paymentmethods ?? 'Tunai';
    
     
        $totalBeforeDiscount = collect($orderItems)->sum(function ($item) {
            return $item['price'] * $item['quantity'];
        });
    
        $totalAfterProductDiscount = collect($orderItems)->sum(function ($item) {
            return $item['discounted_price'] * $item['quantity'];
        });
    
  
        $totalAfterGlobalDiscount = $totalAfterProductDiscount - ($totalAfterProductDiscount * ($this->discount_value / 100));
    
        $discountAmount = $totalBeforeDiscount - $totalAfterGlobalDiscount;
    
        $itemDetails = [];
        $totalOrderPrice = 0;
    
        foreach ($this->order_items as $item) {
            $product = Product::with('discount')->find($item['product_id']);
            $discounted_price = $product->discounted_price;
    
            $itemTotal = $discounted_price * $item['quantity'];
            $totalOrderPrice += $itemTotal;
    
            $itemDetails[] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'quantity' => $item['quantity'],
                'unit_price' => $product->price,
                'discounted_price' => $discounted_price,
                'discount' => $product->price - $discounted_price,
                'subtotal' => $itemTotal
            ];
    
            Order::create([
                'customer_name' => $this->name_customer,
                'payment_method_id' => $this->payment_method_id,
                'product_id' => $product->id,
                'quantity' => $item['quantity'],
                'discount' => $product->price - $discounted_price,
                'total_price' => $discounted_price * $item['quantity'],
                'dateorder' => now(),
                'member_id' => $this->member_id,
            ]);
    
            $product->stock -= $item['quantity'];
            $product->save();
        }
        $this->lastOrderDetails = [
            'items' => $itemDetails,
            'name_customer' => $this->name_customer ?: 'Umum',
            'payment_method' => $paymentMethod,
            'total' => $totalBeforeDiscount,
            'discount' => $discountAmount,
            'total_price' => $totalAfterGlobalDiscount,
            'subtotal' => $itemTotal,
            'date' => now()->format('d-m-Y H:i:s'),
        ];
    
        $this->showReceiptModal = true;
        $this->order_items = [];
        session()->forget('orderItems');
        Notification::make()->title('Pesanan berhasil diproses!')->success()->send();
    }
    
}
