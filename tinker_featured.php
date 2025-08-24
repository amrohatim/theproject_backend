use App\Models\Product;

echo "Total products: " . Product::count() . "\n";
echo "Featured products: " . Product::where('featured', true)->count() . "\n";

if (Product::where('featured', true)->count() == 0) {
    echo "Marking first 5 products as featured...\n";
    Product::limit(5)->update(['featured' => true]);
    echo "Featured products after update: " . Product::where('featured', true)->count() . "\n";
}

$featured = Product::where('featured', true)->with(['branch', 'category'])->get();
foreach ($featured as $product) {
    echo "- {$product->name} (ID: {$product->id}) - \${$product->price}\n";
}

exit;
