<?php

$basePath = __DIR__;

$migrations = [
    '2026_01_01_000001_create_locations_table.php' => <<<PHP
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('locations', function (Blueprint \$table) {
            \$table->id();
            \$table->string('name');
            \$table->enum('type', ['warehouse', 'store']);
            \$table->text('address');
            \$table->unsignedInteger('province_id');
            \$table->unsignedInteger('city_id');
            \$table->unsignedInteger('subdistrict_id');
            \$table->string('postal_code', 10);
            \$table->boolean('is_active')->default(true);
            \$table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
PHP,

    '2026_01_01_000002_add_location_id_to_users_table.php' => <<<PHP
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint \$table) {
            \$table->foreignId('location_id')->nullable()->constrained('locations');
            \$table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint \$table) {
            \$table->dropForeign(['location_id']);
            \$table->dropColumn('location_id');
            \$table->dropSoftDeletes();
        });
    }
};
PHP,

    '2026_01_01_000003_create_categories_table.php' => <<<PHP
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint \$table) {
            \$table->id();
            \$table->foreignId('parent_id')->nullable()->constrained('categories');
            \$table->string('name');
            \$table->string('slug')->unique();
            \$table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
PHP,

    '2026_01_01_000004_create_products_table.php' => <<<PHP
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint \$table) {
            \$table->id();
            \$table->foreignId('category_id')->constrained();
            \$table->string('name');
            \$table->string('slug')->unique();
            \$table->longText('description')->nullable();
            \$table->unsignedInteger('weight_gram');
            \$table->boolean('is_active')->default(true);
            \$table->timestamps();
            \$table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
PHP,

    '2026_01_01_000005_create_product_variants_table.php' => <<<PHP
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_variants', function (Blueprint \$table) {
            \$table->id();
            \$table->foreignId('product_id')->constrained()->cascadeOnDelete();
            \$table->string('sku')->unique();
            \$table->string('barcode')->unique()->nullable();
            \$table->json('attribute_json')->nullable();
            \$table->decimal('cost_price', 15, 2);
            \$table->decimal('selling_price', 15, 2);
            \$table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
PHP,

    '2026_01_01_000006_create_inventory_stocks_table.php' => <<<PHP
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_stocks', function (Blueprint \$table) {
            \$table->id();
            \$table->foreignId('location_id')->constrained();
            \$table->foreignId('product_variant_id')->constrained();
            \$table->integer('qty')->default(0);
            \$table->integer('min_qty')->default(0);
            \$table->timestamps();
            
            \$table->unique(['location_id', 'product_variant_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_stocks');
    }
};
PHP,

    '2026_01_01_000007_create_inventory_movements_table.php' => <<<PHP
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_movements', function (Blueprint \$table) {
            \$table->id();
            \$table->foreignId('location_id')->constrained();
            \$table->foreignId('product_variant_id')->constrained();
            \$table->enum('type', ['in', 'out', 'transfer', 'adjustment']);
            \$table->integer('qty');
            \$table->nullableMorphs('reference');
            \$table->string('remarks')->nullable();
            \$table->foreignId('created_by')->constrained('users');
            \$table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_movements');
    }
};
PHP,

    '2026_01_01_000008_create_orders_table.php' => <<<PHP
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint \$table) {
            \$table->uuid('id')->primary();
            \$table->string('invoice_number')->unique();
            \$table->foreignId('user_id')->nullable()->constrained();
            \$table->string('customer_name')->nullable();
            \$table->string('customer_phone')->nullable();
            \$table->foreignId('location_id')->constrained();
            \$table->enum('source', ['online', 'pos']);
            \$table->enum('status', ['pending', 'processing', 'shipped', 'completed', 'cancelled']);
            \$table->decimal('subtotal', 15, 2);
            \$table->decimal('discount_amount', 15, 2)->default(0);
            \$table->decimal('tax_amount', 15, 2)->default(0);
            \$table->decimal('shipping_cost', 15, 2)->default(0);
            \$table->decimal('grand_total', 15, 2);
            \$table->string('shipping_courier')->nullable();
            \$table->string('shipping_service')->nullable();
            \$table->string('tracking_number')->nullable();
            \$table->timestamps();
            \$table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
PHP,

    '2026_01_01_000009_create_order_items_table.php' => <<<PHP
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint \$table) {
            \$table->id();
            \$table->foreignUuid('order_id')->constrained('orders')->cascadeOnDelete();
            \$table->foreignId('product_variant_id')->constrained();
            \$table->unsignedInteger('qty');
            \$table->decimal('unit_price', 15, 2);
            \$table->decimal('subtotal', 15, 2);
            \$table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
PHP,

    '2026_01_01_000010_create_payments_table.php' => <<<PHP
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint \$table) {
            \$table->id();
            \$table->foreignUuid('order_id')->constrained('orders')->cascadeOnDelete();
            \$table->decimal('amount', 15, 2);
            \$table->string('payment_method');
            \$table->enum('status', ['unpaid', 'paid', 'failed', 'refunded']);
            \$table->string('payment_gateway_ref')->nullable();
            \$table->json('payload_log')->nullable();
            \$table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
PHP,
];

$models = [
    'Location.php' => <<<PHP
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    use HasFactory;

    protected \$fillable = [
        'name', 'type', 'address', 'province_id', 'city_id', 
        'subdistrict_id', 'postal_code', 'is_active'
    ];

    protected \$casts = [
        'is_active' => 'boolean',
    ];

    public function users(): HasMany
    {
        return \$this->hasMany(User::class);
    }

    public function inventoryStocks(): HasMany
    {
        return \$this->hasMany(InventoryStock::class);
    }

    public function inventoryMovements(): HasMany
    {
        return \$this->hasMany(InventoryMovement::class);
    }

    public function orders(): HasMany
    {
        return \$this->hasMany(Order::class);
    }
}
PHP,

    'Category.php' => <<<PHP
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected \$fillable = ['parent_id', 'name', 'slug'];

    public function parent(): BelongsTo
    {
        return \$this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return \$this->hasMany(Category::class, 'parent_id');
    }

    public function products(): HasMany
    {
        return \$this->hasMany(Product::class);
    }
}
PHP,

    'Product.php' => <<<PHP
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected \$fillable = [
        'category_id', 'name', 'slug', 'description', 
        'weight_gram', 'is_active'
    ];

    protected \$casts = [
        'is_active' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return \$this->belongsTo(Category::class);
    }

    public function variants(): HasMany
    {
        return \$this->hasMany(ProductVariant::class);
    }
}
PHP,

    'ProductVariant.php' => <<<PHP
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductVariant extends Model
{
    use HasFactory;

    protected \$fillable = [
        'product_id', 'sku', 'barcode', 'attribute_json', 
        'cost_price', 'selling_price'
    ];

    protected \$casts = [
        'attribute_json' => 'array',
        'cost_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
    ];

    public function product(): BelongsTo
    {
        return \$this->belongsTo(Product::class);
    }

    public function inventoryStocks(): HasMany
    {
        return \$this->hasMany(InventoryStock::class);
    }

    public function inventoryMovements(): HasMany
    {
        return \$this->hasMany(InventoryMovement::class);
    }

    public function orderItems(): HasMany
    {
        return \$this->hasMany(OrderItem::class);
    }
}
PHP,

    'InventoryStock.php' => <<<PHP
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryStock extends Model
{
    use HasFactory;

    protected \$fillable = [
        'location_id', 'product_variant_id', 'qty', 'min_qty'
    ];

    public function location(): BelongsTo
    {
        return \$this->belongsTo(Location::class);
    }

    public function productVariant(): BelongsTo
    {
        return \$this->belongsTo(ProductVariant::class);
    }
}
PHP,

    'InventoryMovement.php' => <<<PHP
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class InventoryMovement extends Model
{
    use HasFactory;

    public \$timestamps = false; // Immutable ledger

    protected \$fillable = [
        'location_id', 'product_variant_id', 'type', 'qty',
        'reference_type', 'reference_id', 'remarks', 'created_by', 'created_at'
    ];

    protected \$casts = [
        'created_at' => 'datetime',
    ];

    public function location(): BelongsTo
    {
        return \$this->belongsTo(Location::class);
    }

    public function productVariant(): BelongsTo
    {
        return \$this->belongsTo(ProductVariant::class);
    }

    public function createdBy(): BelongsTo
    {
        return \$this->belongsTo(User::class, 'created_by');
    }

    public function reference(): MorphTo
    {
        return \$this->morphTo();
    }
}
PHP,

    'Order.php' => <<<PHP
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Order extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    protected \$fillable = [
        'invoice_number', 'user_id', 'customer_name', 'customer_phone',
        'location_id', 'source', 'status', 'subtotal', 'discount_amount',
        'tax_amount', 'shipping_cost', 'grand_total', 'shipping_courier',
        'shipping_service', 'tracking_number'
    ];

    protected \$casts = [
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'grand_total' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return \$this->belongsTo(User::class);
    }

    public function location(): BelongsTo
    {
        return \$this->belongsTo(Location::class);
    }

    public function items(): HasMany
    {
        return \$this->hasMany(OrderItem::class);
    }

    public function payments(): HasMany
    {
        return \$this->hasMany(Payment::class);
    }

    public function inventoryMovements(): MorphMany
    {
        return \$this->morphMany(InventoryMovement::class, 'reference');
    }
}
PHP,

    'OrderItem.php' => <<<PHP
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    protected \$fillable = [
        'order_id', 'product_variant_id', 'qty', 'unit_price', 'subtotal'
    ];

    protected \$casts = [
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function order(): BelongsTo
    {
        return \$this->belongsTo(Order::class);
    }

    public function productVariant(): BelongsTo
    {
        return \$this->belongsTo(ProductVariant::class);
    }
}
PHP,

    'Payment.php' => <<<PHP
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected \$fillable = [
        'order_id', 'amount', 'payment_method', 'status', 
        'payment_gateway_ref', 'payload_log'
    ];

    protected \$casts = [
        'amount' => 'decimal:2',
        'payload_log' => 'array',
    ];

    public function order(): BelongsTo
    {
        return \$this->belongsTo(Order::class);
    }
}
PHP,
];

$factories = [
    'LocationFactory.php' => <<<PHP
<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class LocationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => \$this->faker->company() . ' ' . \$this->faker->randomElement(['Store', 'Warehouse']),
            'type' => \$this->faker->randomElement(['warehouse', 'store']),
            'address' => \$this->faker->address(),
            'province_id' => \$this->faker->numberBetween(1, 34),
            'city_id' => \$this->faker->numberBetween(1, 500),
            'subdistrict_id' => \$this->faker->numberBetween(1, 5000),
            'postal_code' => \$this->faker->postcode(),
            'is_active' => true,
        ];
    }
}
PHP,

    'CategoryFactory.php' => <<<PHP
<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CategoryFactory extends Factory
{
    public function definition(): array
    {
        \$name = \$this->faker->unique()->word();
        return [
            'parent_id' => null,
            'name' => ucfirst(\$name),
            'slug' => Str::slug(\$name),
        ];
    }
}
PHP,

    'ProductFactory.php' => <<<PHP
<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Category;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        \$name = \$this->faker->unique()->words(3, true);
        return [
            'category_id' => Category::factory(),
            'name' => ucfirst(\$name),
            'slug' => Str::slug(\$name),
            'description' => \$this->faker->paragraph(),
            'weight_gram' => \$this->faker->numberBetween(100, 5000),
            'is_active' => true,
        ];
    }
}
PHP,

    'ProductVariantFactory.php' => <<<PHP
<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Product;

class ProductVariantFactory extends Factory
{
    public function definition(): array
    {
        \$cost = \$this->faker->randomFloat(2, 10000, 500000);
        return [
            'product_id' => Product::factory(),
            'sku' => strtoupper(\$this->faker->unique()->bothify('SKU-####-????')),
            'barcode' => \$this->faker->unique()->ean13(),
            'attribute_json' => ['color' => \$this->faker->safeColorName(), 'size' => \$this->faker->randomElement(['S', 'M', 'L', 'XL'])],
            'cost_price' => \$cost,
            'selling_price' => \$cost * 1.5,
        ];
    }
}
PHP,

    'InventoryStockFactory.php' => <<<PHP
<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Location;
use App\Models\ProductVariant;

class InventoryStockFactory extends Factory
{
    public function definition(): array
    {
        return [
            'location_id' => Location::factory(),
            'product_variant_id' => ProductVariant::factory(),
            'qty' => \$this->faker->numberBetween(10, 100),
            'min_qty' => \$this->faker->numberBetween(5, 10),
        ];
    }
}
PHP,

    'InventoryMovementFactory.php' => <<<PHP
<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Location;
use App\Models\ProductVariant;
use App\Models\User;

class InventoryMovementFactory extends Factory
{
    public function definition(): array
    {
        return [
            'location_id' => Location::factory(),
            'product_variant_id' => ProductVariant::factory(),
            'type' => \$this->faker->randomElement(['in', 'out', 'transfer', 'adjustment']),
            'qty' => \$this->faker->numberBetween(-50, 50),
            'remarks' => \$this->faker->sentence(),
            'created_by' => User::factory(),
            'created_at' => now(),
        ];
    }
}
PHP,

    'OrderFactory.php' => <<<PHP
<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Location;

class OrderFactory extends Factory
{
    public function definition(): array
    {
        \$subtotal = \$this->faker->randomFloat(2, 50000, 1000000);
        return [
            'id' => \$this->faker->uuid(),
            'invoice_number' => 'INV-' . strtoupper(\$this->faker->unique()->bothify('????-####')),
            'user_id' => User::factory(),
            'customer_name' => \$this->faker->name(),
            'customer_phone' => \$this->faker->phoneNumber(),
            'location_id' => Location::factory(),
            'source' => \$this->faker->randomElement(['online', 'pos']),
            'status' => \$this->faker->randomElement(['pending', 'processing', 'shipped', 'completed', 'cancelled']),
            'subtotal' => \$subtotal,
            'discount_amount' => 0,
            'tax_amount' => \$subtotal * 0.11,
            'shipping_cost' => 15000,
            'grand_total' => \$subtotal + (\$subtotal * 0.11) + 15000,
            'shipping_courier' => 'JNE',
            'shipping_service' => 'REG',
            'tracking_number' => strtoupper(\$this->faker->bothify('TRACK#####')),
        ];
    }
}
PHP,

    'OrderItemFactory.php' => <<<PHP
<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Order;
use App\Models\ProductVariant;

class OrderItemFactory extends Factory
{
    public function definition(): array
    {
        \$qty = \$this->faker->numberBetween(1, 5);
        \$price = \$this->faker->randomFloat(2, 10000, 200000);
        return [
            'order_id' => Order::factory(),
            'product_variant_id' => ProductVariant::factory(),
            'qty' => \$qty,
            'unit_price' => \$price,
            'subtotal' => \$qty * \$price,
        ];
    }
}
PHP,

    'PaymentFactory.php' => <<<PHP
<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Order;

class PaymentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'amount' => \$this->faker->randomFloat(2, 50000, 1000000),
            'payment_method' => \$this->faker->randomElement(['cash', 'qris', 'midtrans_bca_va']),
            'status' => \$this->faker->randomElement(['unpaid', 'paid', 'failed', 'refunded']),
            'payment_gateway_ref' => \$this->faker->uuid(),
        ];
    }
}
PHP,
];

foreach ($migrations as $file => $content) {
    file_put_contents($basePath . '/database/migrations/' . $file, $content);
}

foreach ($models as $file => $content) {
    file_put_contents($basePath . '/app/Models/' . $file, $content);
}

foreach ($factories as $file => $content) {
    file_put_contents($basePath . '/database/factories/' . $file, $content);
}

echo "Files generated successfully.\n";
