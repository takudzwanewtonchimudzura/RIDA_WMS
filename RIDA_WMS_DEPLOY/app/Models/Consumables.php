<?php
// app/Models/Consumables.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * SUPPLIER MODEL
 */
class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'contact_person', 'email', 'phone', 'address',
        'description', 'status'
    ];

    public function consumables(): HasMany
    {
        return $this->hasMany(Consumable::class);
    }

    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class);
    }
}

/**
 * CONSUMABLE CATEGORY MODEL
 */
class ConsumableCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description'];

    public function consumables(): HasMany
    {
        return $this->hasMany(Consumable::class, 'category_id');
    }
}

/**
 * CONSUMABLE MODEL
 */
class Consumable extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'code', 'category_id', 'description', 'unit',
        'unit_cost', 'minimum_stock', 'status'
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(ConsumableCategory::class, 'category_id');
    }

    public function inventory(): HasOne
    {
        return $this->hasOne(Inventory::class);
    }

    public function stockRequests(): HasMany
    {
        return $this->hasMany(StockRequestItem::class);
    }

    public function usageReports(): HasMany
    {
        return $this->hasMany(UsageReportItem::class);
    }

    public function getCurrentStockAttribute()
    {
        return $this->inventory ? $this->inventory->quantity : 0;
    }

    public function getIsLowStockAttribute()
    {
        return $this->current_stock <= $this->minimum_stock;
    }
}

/**
 * INVENTORY MODEL
 */
class Inventory extends Model
{
    use HasFactory;

    protected $table = 'inventory';

    protected $fillable = [
        'consumable_id', 'quantity', 'location', 'batch_number',
        'expiry_date', 'supplier_id', 'notes'
    ];

    protected $casts = [
        'expiry_date' => 'date',
    ];

    public function consumable(): BelongsTo
    {
        return $this->belongsTo(Consumable::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function movements(): HasMany
    {
        return $this->hasMany(InventoryMovement::class);
    }

    public function getIsExpiredAttribute()
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    public function getWillExpireSoonAttribute()
    {
        if (!$this->expiry_date) return false;
        
        $thirtyDaysFromNow = now()->addDays(30);
        return $this->expiry_date->isBetween(now(), $thirtyDaysFromNow);
    }
}

/**
 * STOCK REQUEST MODEL
 */
class StockRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_number', 'user_id', 'project_type', 'project_name',
        'village', 'ward', 'purpose', 'status', 'approved_by',
        'approved_at', 'rejection_reason'
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(StockRequestItem::class);
    }

    public function getTotalRequestedAttribute()
    {
        return $this->items->sum('requested_quantity');
    }

    public function getTotalApprovedAttribute()
    {
        return $this->items->sum('approved_quantity');
    }

    public function getTotalIssuedAttribute()
    {
        return $this->items->sum('issued_quantity');
    }
}

/**
 * STOCK REQUEST ITEM MODEL
 */
class StockRequestItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_request_id', 'consumable_id', 'requested_quantity',
        'approved_quantity', 'issued_quantity', 'notes'
    ];

    public function stockRequest(): BelongsTo
    {
        return $this->belongsTo(StockRequest::class);
    }

    public function consumable(): BelongsTo
    {
        return $this->belongsTo(Consumable::class);
    }
}

/**
 * USAGE REPORT MODEL
 */
class UsageReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_number', 'user_id', 'project_type', 'project_name',
        'village', 'ward', 'work_description', 'maintenance_type',
        'date_of_work', 'remarks'
    ];

    protected $casts = [
        'date_of_work' => 'date',
    ];

    const PROJECT_TYPES = [
        'borehole' => 'Borehole',
        'piped_scheme' => 'Piped Water Scheme',
        'irrigation' => 'Irrigation Scheme',
        'dam' => 'Dam'
    ];

    const MAINTENANCE_TYPES = [
        'repair' => 'Repair',
        'maintenance' => 'Routine Maintenance',
        'rehabilitation' => 'Rehabilitation',
        'installation' => 'Installation'
    ];

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(UsageReportItem::class);
    }

    public function getProjectTypeLabelAttribute()
    {
        return self::PROJECT_TYPES[$this->project_type] ?? $this->project_type;
    }

    public function getMaintenanceTypeLabelAttribute()
    {
        return self::MAINTENANCE_TYPES[$this->maintenance_type] ?? $this->maintenance_type;
    }

    public function getTotalItemsUsedAttribute()
    {
        return $this->items->sum('quantity_used');
    }
}

/**
 * USAGE REPORT ITEM MODEL
 */
class UsageReportItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'usage_report_id', 'consumable_id', 'quantity_used',
        'batch_number', 'purpose'
    ];

    public function usageReport(): BelongsTo
    {
        return $this->belongsTo(UsageReport::class);
    }

    public function consumable(): BelongsTo
    {
        return $this->belongsTo(Consumable::class);
    }
}

/**
 * PURCHASE ORDER MODEL (if needed)
 */
class PurchaseOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number', 'supplier_id', 'user_id', 'status',
        'order_date', 'expected_delivery_date', 'notes'
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }
}

/**
 * PURCHASE ORDER ITEM MODEL (if needed)
 */
class PurchaseOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_order_id', 'consumable_id', 'quantity',
        'unit_price', 'total'
    ];

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function consumable(): BelongsTo
    {
        return $this->belongsTo(Consumable::class);
    }
}